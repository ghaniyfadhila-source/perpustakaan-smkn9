<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Print Barcodes - <?= htmlspecialchars($book['title'] ?? 'Mass Print') ?></title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>

  <style>
    /* === ukuran label === */
    :root{
      --label-w: 80mm;
      --label-h: 45mm;
      --gap: 6mm;
      --pad: 6mm;
    }

    @media print {
      .no-print { display:none !important; }
      @page { margin: 8mm; } /* margin kertas */
      body { margin: 0; }
    }

    body { background: #fff; }

    .grid{
      display: flex;
      flex-wrap: wrap;
      gap: var(--gap);
      align-items: flex-start;
    }

    .label{
      width: var(--label-w);
      height: var(--label-h);
      border: 1px solid #d0d0d0;
      border-radius: 10px;
      padding: var(--pad);
      box-sizing: border-box;
      page-break-inside: avoid;
      break-inside: avoid;
      overflow: hidden;
    }

    .title{
      font-size: 12px;
      font-weight: 600;
      line-height: 1.2;
      max-height: 28px;
      overflow: hidden;
    }

    .meta{
      font-size: 11px;
      color: #555;
      line-height: 1.2;
    }

    .cn{
      font-size: 16px;
      font-weight: 800;
      line-height: 1.1;
      margin: 2px 0 0;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .barcode-wrap{
      margin-top: 4mm;
      text-align: center;
    }

    svg{
      width: 100%;
      height: 18mm; /* tinggi barcode */
    }

    .code{
      font-size: 11px;
      margin-top: 2px;
      letter-spacing: .5px;
    }

    hr { margin: 6px 0; }
  </style>
</head>

<body class="p-3">
  <div class="d-flex justify-content-between align-items-center mb-3 no-print">
    <div>
      <div class="fw-bold">Print Label Massal</div>
      <div class="text-muted small"><?= htmlspecialchars($book['title'] ?? '') ?></div>
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-outline-secondary" onclick="window.close()">Tutup</button>
      <button class="btn btn-primary" onclick="window.print()">Print</button>
    </div>
  </div>

  <?php if (empty($items)): ?>
    <div class="alert alert-warning">Tidak ada item untuk diprint.</div>
  <?php else: ?>

    <div class="grid" id="grid">
      <?php foreach ($items as $idx => $it): ?>
        <?php
          // Support 2 bentuk data:
          // - dari bookBarcodes(): item_code saja
          // - dari itemLabels(): item_code + title + call_number + classification
          $itemCode = $it['item_code'];
          $title = $it['title'] ?? ($book['title'] ?? '');
          $callNumber = $it['call_number'] ?? ($book['call_number'] ?? '-');
          $classification = $it['classification'] ?? ($book['classification'] ?? '-');
          $svgId = "bc_" . $idx;
        ?>

        <div class="label">
          <div class="meta">Judul</div>
          <div class="title"><?= htmlspecialchars($title) ?></div>

          <hr>

          <div class="meta">Call Number</div>
          <div class="cn"><?= htmlspecialchars($callNumber ?: '-') ?></div>
          <div class="meta">Klasifikasi: <?= htmlspecialchars($classification ?: '-') ?></div>

          <div class="barcode-wrap">
            <svg id="<?= htmlspecialchars($svgId) ?>"></svg>
            <div class="code"><?= htmlspecialchars($itemCode) ?></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <script>
      (function(){
        const codes = <?= json_encode(array_map(fn($x)=>$x['item_code'], $items), JSON_UNESCAPED_UNICODE) ?>;

        function renderAllBarcodes(){
          codes.forEach((code, idx) => {
            JsBarcode("#bc_" + idx, String(code), {
              format: "CODE128",
              width: 2,
              height: 50,
              margin: 0,
              displayValue: false
            });
          });
        }

        function autoPrint(){
          // Pastikan DOM + SVG sudah ter-render
          // timeout kecil supaya SVG selesai digambar sebelum print dialog muncul
          setTimeout(() => {
            window.print();
          }, 200);
        }

        window.addEventListener('load', () => {
          renderAllBarcodes();
          autoPrint();
        });
      })();
    </script>

  <?php endif; ?>
</body>
</html>