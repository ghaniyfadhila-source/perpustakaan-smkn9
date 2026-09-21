<?php
// public/cron_wa_generate.php
date_default_timezone_set('Asia/Jakarta');

require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../app/helpers/Phone.php';

$today = date('Y-m-d');
$h1 = date('Y-m-d', strtotime('+1 day')); // H-1
$h2 = date('Y-m-d', strtotime('+2 day')); // H-2

// Semua pesan dibuat untuk dikirim jam 07:00 hari ini
$sendAt = date('Y-m-d 08:50:00');

function alreadyQueuedToday(string $category, string $memberId, string $itemCode): bool {
  // anti dobel 1x per hari per kategori+member+item
  $row = DB::selectOne("
    SELECT id
    FROM wa_outbox
    WHERE category=? AND member_id=?
      AND message LIKE ?
      AND DATE(created_at)=CURDATE()
    LIMIT 1
  ", "sss", [$category, $memberId, '%'.$itemCode.'%']);
  return $row ? true : false;
}

/* =========================
   A) REMINDER (H-2 & H-1)
   ========================= */
$reminders = DB::select("
  SELECT l.loan_id, l.member_id, m.member_name, m.member_phone,
         l.item_code, b.title, l.due_date
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

  if (alreadyQueuedToday('REMINDER', $r['member_id'], $r['item_code'])) continue;

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

  DB::exec("
    INSERT INTO wa_outbox(member_id, phone, category, message, scheduled_at)
    VALUES(?,?,?,?,?)
  ", "sssss", [
    $r['member_id'],
    $phone,
    'REMINDER',
    $msg,
    $sendAt
  ]);
}

/* =========================
   B) OVERDUE (TELAT)
   ========================= */
$overdues = DB::select("
  SELECT l.loan_id, l.member_id, m.member_name, m.member_phone,
         l.item_code, b.title, l.due_date,
         DATEDIFF(CURDATE(), l.due_date) AS late_days
  FROM loan l
  JOIN member m ON m.member_id = l.member_id
  JOIN item i ON i.item_code = l.item_code
  JOIN biblio b ON b.biblio_id = i.biblio_id
  WHERE l.is_return = 0
    AND CURDATE() > l.due_date
  ORDER BY late_days DESC
");

foreach ($overdues as $r) {
  $phone = Phone::normalizeID($r['member_phone'] ?? '');
  if ($phone === '') continue;

  if (alreadyQueuedToday('OVERDUE', $r['member_id'], $r['item_code'])) continue;

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
    INSERT INTO wa_outbox(member_id, phone, category, message, scheduled_at)
    VALUES(?,?,?,?,?)
  ", "sssss", [
    $r['member_id'],
    $phone,
    'OVERDUE',
    $msg,
    $sendAt
  ]);
}

echo "OK generate: reminders=".count($reminders)." overdue=".count($overdues)." sendAt=$sendAt\n";