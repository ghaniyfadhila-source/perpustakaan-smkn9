<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="d-flex justify-content-between align-items-end mb-3">
  <div><h4 class="mb-0">Tambah Master Data</h4><div class="text-muted small">Type: <?= htmlspecialchars($type) ?></div></div>
  <a class="btn btn-outline-secondary" href="index.php?r=master/index&type=<?= urlencode($type) ?>"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
</div>

<?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="card shadow-sm">
  <div class="card-body">
    <form method="post">
      <label class="form-label">Nama</label>
      <input class="form-control" name="name" required>
      <div class="mt-3 d-flex gap-2">
        <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
        <a class="btn btn-outline-secondary" href="index.php?r=master/index&type=<?= urlencode($type) ?>">Batal</a>
      </div>
    </form>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
