<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Cetak Kartu - <?= htmlspecialchars($member['member_name']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
  <style>
    @media print { .no-print { display:none } }
    .cardbox { width: 86mm; height: 54mm; border: 1px solid #ddd; border-radius: 10px; padding: 10px; }
  </style>
</head>
<body class="p-3">
  <div class="d-flex justify-content-between align-items-center mb-3 no-print">
    <div class="fw-semibold">Cetak Kartu Anggota</div>
    <button class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer"></i> Print</button>
  </div>

  <div class="cardbox">
    <div class="d-flex justify-content-between">
      <div>
        <div class="fw-bold">KARTU ANGGOTA</div>
        <div class="small text-muted"><?= htmlspecialchars(APP_NAME) ?></div>
      </div>
      <div class="text-end small">
        <div class="text-muted">Expire</div>
        <div class="fw-semibold"><?= htmlspecialchars($member['expire_date']) ?></div>
      </div>
    </div>

    <hr class="my-2">

    <div class="fw-semibold"><?= htmlspecialchars($member['member_name']) ?></div>
    <div class="small text-muted">ID: <?= htmlspecialchars($member['member_id']) ?> | Tipe: <?= htmlspecialchars($member['member_type_name'] ?? '-') ?></div>

    <div class="mt-2">
      <svg id="barcode"></svg>
    </div>
  </div>

<script>
  JsBarcode("#barcode", "<?= addslashes($member['member_id']) ?>", {
    format: "CODE128",
    width: 2,
    height: 40,
    displayValue: true
  });
</script>
</body>
</html>
