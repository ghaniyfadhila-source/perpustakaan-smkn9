<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="mb-0"><i class="bi bi-person me-2"></i>Profil Saya</h4>
</div>

<?php if (!empty($_SESSION['flash'])): ?>
  <div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show" role="alert">
    <?= $_SESSION['flash']['msg'] ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<div class="row g-4">
  <div class="col-md-4">
    <div class="card">
      <div class="card-body text-center">
        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px; font-size: 2.5rem;">
          <i class="bi bi-person"></i>
        </div>
        <h5><?= htmlspecialchars($student['student_name'] ?? '-') ?></h5>
        <p class="text-muted mb-1">@<?= htmlspecialchars($student['username'] ?? '-') ?></p>
        <p class="text-muted small">ID: <?= htmlspecialchars($student['student_id'] ?? '-') ?></p>
      </div>
    </div>
  </div>

  <div class="col-md-8">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-key me-2"></i>Ubah Password</h5>
      </div>
      <div class="card-body">
        <form method="POST" action="index.php?r=student/profile/changePassword">
          <div class="row g-3">
            <div class="col-12 col-md-6">
              <label class="form-label">Password Lama <span class="text-danger">*</span></label>
              <input type="password" class="form-control" name="current_password" required autocomplete="current-password">
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label">Password Baru <span class="text-danger">*</span></label>
              <input type="password" class="form-control" name="new_password" required minlength="6" autocomplete="new-password">
              <div class="form-text">Minimal 6 karakter</div>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
              <input type="password" class="form-control" name="confirm_password" required autocomplete="new-password">
            </div>
          </div>
          <hr>
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>