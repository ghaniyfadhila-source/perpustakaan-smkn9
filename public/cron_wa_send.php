<?php
// public/cron_wa_send.php
date_default_timezone_set('Asia/Jakarta');

require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../app/helpers/WhatsApp.php';
require_once __DIR__ . '/../app/helpers/Phone.php';

$now = date('Y-m-d H:i:s');

// Ambil antrian yang sudah waktunya
$items = DB::select("
  SELECT id, member_id, phone, category, message, try_count
  FROM wa_outbox
  WHERE status='PENDING' AND scheduled_at <= ?
  ORDER BY scheduled_at ASC
  LIMIT 50
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
    INSERT INTO wa_log(outbox_id, phone, category, http_code, response, error)
    VALUES(?,?,?,?,?,?)
  ", "ississ", [
    $id,
    $phone,
    $it['category'],
    (int)($res['http_code'] ?? 0),
    (string)($res['response'] ?? ''),
    (string)($res['error'] ?? '')
  ]);

  if (!empty($res['ok'])) {
    DB::exec("UPDATE wa_outbox SET status='SENT', sent_at=?, try_count=try_count+1, last_error=NULL WHERE id=?",
      "si", [date('Y-m-d H:i:s'), $id]
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
      // retry 10 menit
      $nextSchedule = date('Y-m-d H:i:s', time() + 600);
      DB::exec("UPDATE wa_outbox SET try_count=?, last_error=?, scheduled_at=? WHERE id=?",
        "issi", [$nextTry, $errText, $nextSchedule, $id]
      );
    }
    $failed++;
  }
}

echo "OK send: ".count($items)." | SENT=$sent | FAILED=$failed | NOW=$now\n";