<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h4 class="mb-0">Tambah Eksemplar</h4>
    <div class="text-muted small">Buku: <?= htmlspecialchars($book['title']) ?></div>
  </div>
  <a class="btn btn-outline-secondary" href="index.php?r=books/show&id=<?= (int)$book['biblio_id'] ?>">
    <i class="bi bi-arrow-left me-1"></i>Kembali
  </a>
</div>

<?php if (!empty($error)): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card shadow-sm">
  <div class="card-body">
    <form method="post" class="row g-3">
      <div class="col-12 col-md-6">
        <label class="form-label">Barcode (item_code)</label>
        <input class="form-control" name="item_code" required placeholder="contoh: BK-0001">
        <div class="form-text">Harus unik. Biasanya dari barcode scanner.</div>
      </div>

      <div class="col-12 col-md-6">
        <label class="form-label">Collection Type</label>
        <select class="form-select" name="coll_type_id">
          <?php foreach ($masters['colls'] as $x): ?>
            <option value="<?= (int)$x['coll_type_id'] ?>"><?= htmlspecialchars($x['coll_type_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-12 col-md-6">
        <label class="form-label">Lokasi</label>
        <select class="form-select" name="location_id">
          <?php foreach ($masters['locs'] as $x): ?>
            <option value="<?= htmlspecialchars($x['location_id']) ?>"><?= htmlspecialchars($x['location_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-12 col-md-6">
        <label class="form-label">Status Item</label>
        <select class="form-select" name="item_status_id">
          <?php foreach ($masters['stats'] as $x): ?>
            <option value="<?= htmlspecialchars($x['item_status_id']) ?>"><?= htmlspecialchars($x['item_status_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-12 d-flex gap-2">
        <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
        <a class="btn btn-outline-secondary" href="index.php?r=books/show&id=<?= (int)$book['biblio_id'] ?>">Batal</a>
      </div>
    </form>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
