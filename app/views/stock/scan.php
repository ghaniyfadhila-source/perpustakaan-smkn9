<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="d-flex justify-content-between align-items-end mb-3">
  <div>
    <h4 class="mb-0">Stock Scan</h4>
    <div class="text-muted small">Scan barcode buku untuk melihat stok per judul</div>
  </div>
</div>

<div class="card shadow-sm mb-3">
  <div class="card-body">
    <form class="row g-2" method="get">
      <input type="hidden" name="r" value="stock/scan">
      <div class="col-12 col-md-9">
        <label class="form-label">Barcode (item_code)</label>
        <input class="form-control" name="code" value="<?= htmlspecialchars($code) ?>" autofocus placeholder="scan di sini..." required>
      </div>
      <div class="col-12 col-md-3 d-grid">
        <label class="form-label d-none d-md-block">&nbsp;</label>
        <button class="btn btn-primary"><i class="bi bi-search me-1"></i>Cek</button>
      </div>
    </form>
  </div>
</div>

<?php if ($data && !$data['ok']): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($data['error']) ?></div>
<?php endif; ?>

<?php if ($data && $data['ok']): ?>
  <div class="card shadow-sm">
    <div class="card-body">
      <div class="fw-semibold mb-1"><?= htmlspecialchars($data['item']['title']) ?></div>
      <div class="text-muted small">Barcode discan: <?= htmlspecialchars($data['item']['item_code']) ?></div>

      <hr>

      <div class="row g-2">
        <div class="col-12 col-md-4">
          <div class="p-3 bg-light rounded">
            <div class="text-muted small">Total Copy</div>
            <div class="fs-3 fw-bold"><?= (int)$data['stock']['total_copies'] ?></div>
          </div>
        </div>
        <div class="col-12 col-md-4">
          <div class="p-3 bg-light rounded">
            <div class="text-muted small">Sedang Dipinjam</div>
            <div class="fs-3 fw-bold"><?= (int)$data['stock']['on_loan'] ?></div>
          </div>
        </div>
        <div class="col-12 col-md-4">
          <div class="p-3 bg-light rounded">
            <div class="text-muted small">Tersedia</div>
            <div class="fs-3 fw-bold"><?= (int)$data['stock']['available'] ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
