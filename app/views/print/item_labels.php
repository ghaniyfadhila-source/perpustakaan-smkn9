<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Print Label - <?= htmlspecialchars($row['item_code']) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
  <style>
    @media print { .no-print { display:none } }
    .label {
      width: 80mm;
      border: 1px solid #ddd;
      border-radius: 10px;
      padding: 10px;
    }
    .cn { font-size: 18px; font-weight: 700; }
    .smalltext { font-size: 12px; }
  </style>
</head>
<body class="p-3">
  <div class="d-flex justify-content-between align-items-center mb-3 no-print">
    <div class="fw-bold">Print Label Item</div>
    <button class="btn btn-primary" onclick="window.print()">Print</button>
  </div>

  <div class="label">
    <div class="smalltext text-muted">Judul</div>
    <div class="fw-semibold"><?= htmlspecialchars($row['title']) ?></div>

    <hr class="my-2">

    <div class="smalltext text-muted">Call Number</div>
    <div class="cn"><?= htmlspecialchars($row['call_number'] ?? '-') ?></div>
    <div class="smalltext text-muted">Klasifikasi: <?= htmlspecialchars($row['classification'] ?? '-') ?></div>

    <hr class="my-2">

    <svg id="barcode"></svg>
    <div class="text-center smalltext"><?= htmlspecialchars($row['item_code']) ?></div>
  </div>

<script>
  JsBarcode("#barcode", "<?= addslashes($row['item_code']) ?>", {
    format: "CODE128",
    width: 2,
    height: 50,
    displayValue: false
  });
</script>
</body>
</html>