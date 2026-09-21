<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="d-flex justify-content-between align-items-end mb-3">
  <div>
    <h4 class="mb-0">Edit Typo Transaksi</h4>
    <div class="text-muted small">Admin only. Semua perubahan tercatat di system_log.</div>
  </div>
  <a class="btn btn-outline-secondary" href="index.php?r=loans/history"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
</div>

<?php if (!empty($error)): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card shadow-sm">
  <div class="card-body">
    <form method="post" class="row g-3">
      <div class="col-12 col-md-4">
        <label class="form-label">Member ID</label>
        <input class="form-control" name="member_id" value="<?= htmlspecialchars($loan['member_id']) ?>" required>
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">Item Code</label>
        <input class="form-control" name="item_code" value="<?= htmlspecialchars($loan['item_code']) ?>" required>
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">Due Date</label>
        <input class="form-control" type="date" name="due_date" value="<?= htmlspecialchars($loan['due_date']) ?>" required>
      </div>
      <div class="col-12 d-flex gap-2">
        <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
        <a class="btn btn-outline-secondary" href="index.php?r=loans/history">Batal</a>
      </div>
    </form>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
