<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="d-flex justify-content-between align-items-end mb-3">
  <div><h4 class="mb-0">Ganti Password</h4><div class="text-muted small">Untuk akun yang sedang login</div></div>
</div>

<?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="card shadow-sm">
  <div class="card-body">
    <form method="post" class="row g-3">
      <div class="col-12 col-md-4">
        <label class="form-label">Password Lama</label>
        <input class="form-control" type="password" name="old_password" required>
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">Password Baru</label>
        <input class="form-control" type="password" name="new_password" required>
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">Ulangi Password Baru</label>
        <input class="form-control" type="password" name="new_password2" required>
      </div>
      <div class="col-12">
        <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
      </div>
    </form>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
