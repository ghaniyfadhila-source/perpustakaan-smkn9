<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="d-flex justify-content-between align-items-end mb-3">
  <div><h4 class="mb-0">Edit User</h4><div class="text-muted small">Username tidak diubah</div></div>
  <a class="btn btn-outline-secondary" href="index.php?r=users/index"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <form method="post" class="row g-3">
      <div class="col-12 col-md-4">
        <label class="form-label">Username</label>
        <input class="form-control" value="<?= htmlspecialchars($user['username']) ?>" disabled>
      </div>
      <div class="col-12 col-md-8">
        <label class="form-label">Nama</label>
        <input class="form-control" name="realname" value="<?= htmlspecialchars($user['realname']) ?>" required>
      </div>
      <div class="col-12 col-md-6">
        <label class="form-label">Email</label>
        <input class="form-control" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>">
      </div>
      <div class="col-12 col-md-3">
        <label class="form-label">Role (Groups)</label>
        <select class="form-select" name="groups">
          <option value="1" <?= ($user['groups']=='1')?'selected':'' ?>>Admin</option>
          <option value="2" <?= ($user['groups']=='2')?'selected':'' ?>>Operator</option>
        </select>
      </div>
      <div class="col-12">
        <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
      </div>
    </form>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
