<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="d-flex justify-content-between align-items-end mb-3">
  <div><h4 class="mb-0">Reset Password</h4><div class="text-muted small">User: <?= htmlspecialchars($user['username']) ?></div></div>
  <a class="btn btn-outline-secondary" href="index.php?r=users/index"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
</div>

<?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="card shadow-sm">
  <div class="card-body">
    <form method="post" class="row g-3">
      <div class="col-12 col-md-4">
        <label class="form-label">Password Baru</label>
        <input class="form-control" type="password" name="password" required>
      </div>
      <div class="col-12">
        <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
      </div>
    </form>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
