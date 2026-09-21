<?php
// $title, $subtitle, $printedAt, $contentHtml sudah disediakan
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<style>
  @page { margin: 18mm 12mm 20mm 12mm; }
  body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color:#111; }
  .header { border-bottom: 2px solid #111; padding-bottom: 8px; margin-bottom: 10px; }
  .org { font-size: 13px; font-weight: 700; letter-spacing: .2px; }
  .title { font-size: 12px; font-weight: 700; margin-top: 6px; }
  .subtitle { font-size: 11px; color:#333; margin-top: 2px; }
  .meta { float:right; text-align:right; font-size: 10.5px; color:#333; line-height: 1.4; }
  table { width: 100%; border-collapse: collapse; margin-top: 10px; }
  th, td { border: 1px solid #333; padding: 6px; vertical-align: top; }
  th { background: #f2f2f2; font-size: 10px; text-transform: uppercase; letter-spacing: .3px; }
  .center { text-align: center; }
  .right { text-align: right; white-space: nowrap; }
  .muted { color:#444; }
  .sign-wrap { margin-top: 14px; width: 100%; }
  .sign { width: 33.33%; display: inline-block; text-align: center; font-size: 10.5px; }
  .line { margin: 42px 16px 0 16px; border-top: 1px solid #111; padding-top: 6px; }
</style>
</head>
<body>

<div class="meta">
  Tanggal Cetak: <?= htmlspecialchars($printedAt['date']) ?><br>
  Waktu: <?= htmlspecialchars($printedAt['time']) ?> WIB
</div>

<div class="header">
  <div class="org">Perpustakaan SMKN 9 Semarang</div>
  <div class="title"><?= htmlspecialchars($title) ?></div>
  <div class="subtitle"><?= $subtitle ?></div>
</div>

<?= $contentHtml ?>

</body>
</html>