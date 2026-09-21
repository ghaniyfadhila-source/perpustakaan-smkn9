<?php
// app/controllers/WaController.php
date_default_timezone_set('Asia/Jakarta');

require_once __DIR__ . '/../../core/db.php';
require_once __DIR__ . '/../helpers/WhatsApp.php';
require_once __DIR__ . '/../helpers/Phone.php';

class WaController {

  /* =========================
     PAGE 1: SCHEDULE TOMORROW
     ========================= */

  public function schedule() {
  $rows = DB::select("
    SELECT id, member_id, phone, category, status, scheduled_at, sent_at, try_count, last_error
    FROM wa_outbox
    ORDER BY id DESC
    LIMIT 25
  ");
  require __DIR__ . '/../views/wa/schedule.php';
}

  // Klik tombol: buat jadwal BESOK jam 07:00 untuk overdue + reminder
  public function scheduleTomorrow() {
    header('Content-Type: application/json; charset=utf-8');

    $tomorrow = date('Y-m-d', strtotime('+1 day'));
    $sendAt   = $tomorrow . ' 07:00:00';

    // anti dobel: kalau sudah pernah dibuat jadwal besok, jangan buat lagi
    $exist = DB::selectOne("
      SELECT id FROM wa_outbox
      WHERE scheduled_at = ?
        AND category IN ('OVERDUE','REMINDER')
      LIMIT 1
    ", "s", [$sendAt]);

    if ($exist) {
      echo json_encode(['ok'=>true,'msg'=>'Jadwal besok sudah ada','send_at'=>$sendAt,'inserted'=>0]);
      return;
    }

    $inserted = 0;

    // ===== REMINDER H-2 & H-1 dari besok =====
    // Kalau jadwal dibuat hari ini untuk besok:
    // Reminder target: due_date = besok+1 (H-1 besok) dan besok+2 (H-2 besok)
    $h1 = date('Y-m-d', strtotime($tomorrow.' +1 day'));
    $h2 = date('Y-m-d', strtotime($tomorrow.' +2 day'));

    $reminders = DB::select("
      SELECT l.member_id, m.member_name, m.member_phone, l.item_code, b.title, l.due_date
      FROM loan l
      JOIN member m ON m.member_id = l.member_id
      JOIN item i ON i.item_code = l.item_code
      JOIN biblio b ON b.biblio_id = i.biblio_id
      WHERE l.is_return = 0
        AND l.due_date IN (?, ?)
    ", "ss", [$h2, $h1]);

    foreach ($reminders as $r) {
      $phone = Phone::normalizeID($r['member_phone'] ?? '');
      if ($phone === '') continue;

      // anti dobel per (besok schedule + member + item + category)
      $dup = DB::selectOne("
        SELECT id FROM wa_outbox
        WHERE scheduled_at=? AND category='REMINDER' AND member_id=? AND message LIKE ?
        LIMIT 1
      ", "sss", [$sendAt, $r['member_id'], '%'.$r['item_code'].'%']);
      if ($dup) continue;

      // hitung sisa hari dari BESOK
      $daysLeft = (int)((strtotime($r['due_date']) - strtotime($tomorrow)) / 86400);

      $msg =
        "📚 PERPUSTAKAAN SMKN 9 SEMARANG\n\n".
        "Yth. ".$r['member_name']."\n".
        "Pengingat pengembalian buku:\n".
        "• Judul : ".$r['title']."\n".
        "• Kode  : ".$r['item_code']."\n".
        "• Jatuh tempo: ".$r['due_date']."\n".
        "• Sisa: ".$daysLeft." hari\n\n".
        "Mohon dikembalikan tepat waktu.\n".
        "Terima kasih.";

      DB::exec("
        INSERT INTO wa_outbox(member_id, phone, category, message, status, scheduled_at, created_at, try_count)
        VALUES(?,?,?,?, 'PENDING', ?, NOW(), 0)
      ", "sssss", [$r['member_id'], $phone, 'REMINDER', $msg, $sendAt]);

      $inserted++;
    }

    // ===== OVERDUE untuk BESOK (yang telat saat besok) =====
    // Artinya: due_date < besok dan belum return
    $overdues = DB::select("
      SELECT l.member_id, m.member_name, m.member_phone, l.item_code, b.title, l.due_date,
             DATEDIFF(?, l.due_date) AS late_days
      FROM loan l
      JOIN member m ON m.member_id = l.member_id
      JOIN item i ON i.item_code = l.item_code
      JOIN biblio b ON b.biblio_id = i.biblio_id
      WHERE l.is_return = 0
        AND ? > l.due_date
      ORDER BY late_days DESC
    ", "ss", [$tomorrow, $tomorrow]);

    foreach ($overdues as $r) {
      $phone = Phone::normalizeID($r['member_phone'] ?? '');
      if ($phone === '') continue;

      $dup = DB::selectOne("
        SELECT id FROM wa_outbox
        WHERE scheduled_at=? AND category='OVERDUE' AND member_id=? AND message LIKE ?
        LIMIT 1
      ", "sss", [$sendAt, $r['member_id'], '%'.$r['item_code'].'%']);
      if ($dup) continue;

      $msg =
        "⏰ PERPUSTAKAAN SMKN 9 SEMARANG\n\n".
        "Yth. ".$r['member_name']."\n".
        "Notifikasi keterlambatan pengembalian:\n".
        "• Judul : ".$r['title']."\n".
        "• Kode  : ".$r['item_code']."\n".
        "• Jatuh tempo: ".$r['due_date']."\n".
        "• Terlambat: ".$r['late_days']." hari\n\n".
        "Mohon segera dikembalikan.\n".
        "Terima kasih.";

      DB::exec("
        INSERT INTO wa_outbox(member_id, phone, category, message, status, scheduled_at, created_at, try_count)
        VALUES(?,?,?,?, 'PENDING', ?, NOW(), 0)
      ", "sssss", [$r['member_id'], $phone, 'OVERDUE', $msg, $sendAt]);

      $inserted++;
    }

    echo json_encode(['ok'=>true,'msg'=>'Jadwal besok dibuat','send_at'=>$sendAt,'inserted'=>$inserted]);
  }

  public function cancel() {
  header('Content-Type: application/json; charset=utf-8');
  ini_set('display_errors', '0');
  error_reporting(E_ALL);

  $id = (int)($_POST['id'] ?? $_REQUEST['id'] ?? 0);
  if ($id <= 0) {
    echo json_encode(['ok'=>false, 'msg'=>'ID tidak valid']);
    return;
  }

  // hanya boleh cancel kalau masih PENDING
  $row = DB::selectOne("SELECT id, status FROM wa_outbox WHERE id=? LIMIT 1", "i", [$id]);
  if (!$row) {
    echo json_encode(['ok'=>false, 'msg'=>'Data tidak ditemukan']);
    return;
  }
  if (($row['status'] ?? '') !== 'PENDING') {
    echo json_encode(['ok'=>false, 'msg'=>'Hanya bisa cancel status PENDING']);
    return;
  }

  DB::exec("
    UPDATE wa_outbox
    SET status='CANCELLED',
        last_error='Dibatalkan oleh admin',
        try_count=try_count+1
    WHERE id=?
  ", "i", [$id]);

  echo json_encode(['ok'=>true, 'msg'=>'Berhasil dibatalkan']);
}

  // Auto sender: kirim semua yang PENDING dan sudah jatuh tempo
  public function sendDue() {
    header('Content-Type: application/json; charset=utf-8');

    $now = date('Y-m-d H:i:s');

    $items = DB::select("
      SELECT id, phone, category, message, try_count
      FROM wa_outbox
      WHERE status='PENDING' AND scheduled_at <= ?
      ORDER BY scheduled_at ASC
      LIMIT 30
    ", "s", [$now]);

    $sent = 0; $failed = 0;

    foreach ($items as $it) {
      $id = (int)$it['id'];
      $phone = Phone::normalizeID($it['phone']);

      if ($phone === '') {
        DB::exec("UPDATE wa_outbox SET status='FAILED', last_error=?, try_count=try_count+1 WHERE id=?",
          "si", ["Nomor HP kosong/tidak valid", $id]
        );
        $failed++;
        continue;
      }

      $res = WhatsApp::send($phone, $it['message']);

      DB::exec("
        INSERT INTO wa_log(outbox_id, phone, category, http_code, response, error, created_at)
        VALUES(?,?,?,?,?,?, NOW())
      ", "ississ", [
        $id, $phone, $it['category'],
        (int)($res['http_code'] ?? 0),
        (string)($res['response'] ?? ''),
        (string)($res['error'] ?? '')
      ]);

      if (!empty($res['ok'])) {
        DB::exec("UPDATE wa_outbox SET status='SENT', sent_at=?, try_count=try_count+1, last_error=NULL WHERE id=?",
          "si", [$now, $id]
        );
        $sent++;
      } else {
        $nextTry = (int)$it['try_count'] + 1;
        $errText = ($res['error'] ?? '') !== '' ? $res['error'] : ("HTTP ".(int)($res['http_code'] ?? 0));

        if ($nextTry >= 3) {
          DB::exec("UPDATE wa_outbox SET status='FAILED', try_count=?, last_error=? WHERE id=?",
            "isi", [$nextTry, $errText, $id]
          );
        } else {
          $nextSchedule = date('Y-m-d H:i:s', time() + 600);
          DB::exec("UPDATE wa_outbox SET try_count=?, last_error=?, scheduled_at=? WHERE id=?",
            "issi", [$nextTry, $errText, $nextSchedule, $id]
          );
        }
        $failed++;
      }
    }

    echo json_encode(['ok'=>true,'now'=>$now,'picked'=>count($items),'sent'=>$sent,'failed'=>$failed]);
  }

  /* =========================
     PAGE 2: BLAST SEND NOW
     ========================= */

  public function blast() {
    require __DIR__ . '/../views/wa/blast.php';
  }


  
  // Kirim detik itu juga (tanpa masuk outbox PENDING)
  public function blastSend() {
    header('Content-Type: application/json; charset=utf-8');

    $mode = $_GET['mode'] ?? 'ALL'; // OVERDUE | REMINDER | ALL
    $today = date('Y-m-d');

    // target reminder (mendekati jatuh tempo): hari ini H-1/H-2
    $h1 = date('Y-m-d', strtotime('+1 day'));
    $h2 = date('Y-m-d', strtotime('+2 day'));

    $targets = [];

    if ($mode === 'REMINDER' || $mode === 'ALL') {
      $rem = DB::select("
        SELECT l.member_id, m.member_name, m.member_phone, l.item_code, b.title, l.due_date
        FROM loan l
        JOIN member m ON m.member_id = l.member_id
        JOIN item i ON i.item_code = l.item_code
        JOIN biblio b ON b.biblio_id = i.biblio_id
        WHERE l.is_return=0 AND l.due_date IN (?,?)
      ", "ss", [$h2,$h1]);

      foreach ($rem as $r) {
        $phone = Phone::normalizeID($r['member_phone'] ?? '');
        if ($phone==='') continue;

        $daysLeft = (int)((strtotime($r['due_date']) - strtotime($today)) / 86400);

        $msg =
          "📚 PERPUSTAKAAN SMKN 9 SEMARANG\n\n".
          "Yth. ".$r['member_name']."\n".
          "Pengingat pengembalian buku:\n".
          "• Judul : ".$r['title']."\n".
          "• Kode  : ".$r['item_code']."\n".
          "• Jatuh tempo: ".$r['due_date']."\n".
          "• Sisa: ".$daysLeft." hari\n\n".
          "Mohon dikembalikan tepat waktu.\n".
          "Terima kasih.";

        $targets[] = ['member_id'=>$r['member_id'], 'phone'=>$phone, 'category'=>'REMINDER', 'message'=>$msg];
      }
    }

    if ($mode === 'OVERDUE' || $mode === 'ALL') {
      $od = DB::select("
        SELECT l.member_id, m.member_name, m.member_phone, l.item_code, b.title, l.due_date,
               DATEDIFF(CURDATE(), l.due_date) AS late_days
        FROM loan l
        JOIN member m ON m.member_id = l.member_id
        JOIN item i ON i.item_code = l.item_code
        JOIN biblio b ON b.biblio_id = i.biblio_id
        WHERE l.is_return=0 AND CURDATE() > l.due_date
        ORDER BY late_days DESC
      ");

      foreach ($od as $r) {
        $phone = Phone::normalizeID($r['member_phone'] ?? '');
        if ($phone==='') continue;

        $msg =
          "⏰ PERPUSTAKAAN SMKN 9 SEMARANG\n\n".
          "Yth. ".$r['member_name']."\n".
          "Notifikasi keterlambatan pengembalian:\n".
          "• Judul : ".$r['title']."\n".
          "• Kode  : ".$r['item_code']."\n".
          "• Jatuh tempo: ".$r['due_date']."\n".
          "• Terlambat: ".$r['late_days']." hari\n\n".
          "Mohon segera dikembalikan.\n".
          "Terima kasih.";

        $targets[] = ['member_id'=>$r['member_id'], 'phone'=>$phone, 'category'=>'OVERDUE', 'message'=>$msg];
      }
    }

    // === kirim sekarang
$sent=0; $failed=0;

foreach ($targets as $t) {
  $res = WhatsApp::send($t['phone'], $t['message']);

  // normalisasi error text
  $errText = ($res['error'] ?? '') !== '' ? $res['error'] : ("HTTP ".(int)($res['http_code'] ?? 0));
  $now = date('Y-m-d H:i:s');

  if (!empty($res['ok'])) {
    // ✅ kalau sukses: sent_at = NOW(), last_error = NULL
    DB::exec("
      INSERT INTO wa_outbox
        (member_id, phone, category, message, status, scheduled_at, created_at, sent_at, try_count, last_error)
      VALUES
        (?, ?, ?, ?, 'SENT', ?, NOW(), ?, 1, NULL)
    ", "ssssss", [
      $t['member_id'],
      $t['phone'],
      $t['category'],
      $t['message'],
      $now,   // scheduled_at
      $now    // sent_at
    ]);

    $sent++;

  } else {
    // ❌ kalau gagal: sent_at = NULL, last_error diisi
    DB::exec("
      INSERT INTO wa_outbox
        (member_id, phone, category, message, status, scheduled_at, created_at, sent_at, try_count, last_error)
      VALUES
        (?, ?, ?, ?, 'FAILED', ?, NOW(), NULL, 1, ?)
    ", "ssssss", [
      $t['member_id'],
      $t['phone'],
      $t['category'],
      $t['message'],
      $now,      // scheduled_at
      $errText   // last_error
    ]);

    $failed++;
  }

  $outboxId = DB::lastId();

  // log
  DB::exec("
    INSERT INTO wa_log(outbox_id, phone, category, http_code, response, error, created_at)
    VALUES(?,?,?,?,?,?, NOW())
  ", "ississ", [
    $outboxId,
    $t['phone'],
    $t['category'],
    (int)($res['http_code'] ?? 0),
    (string)($res['response'] ?? ''),
    (string)($res['error'] ?? '')
  ]);
}

echo json_encode([
  'ok'=>true,
  'mode'=>$mode,
  'total'=>count($targets),
  'sent'=>$sent,
  'failed'=>$failed
]);
exit;
  }
}