<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<?php if (!empty($_SESSION['flash'])): $f=$_SESSION['flash']; unset($_SESSION['flash']); ?>
  <div class="alert alert-<?= htmlspecialchars($f['type']) ?> alert-dismissible fade show">
    <?= htmlspecialchars($f['msg']) ?><button class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-end mb-3">
  <div><h4 class="mb-0">Manajemen User</h4><div class="text-muted small">Tambah/edit/hapus admin/operator</div></div>
  <a class="btn btn-primary" href="index.php?r=users/create"><i class="bi bi-plus-lg me-1"></i>Tambah User</a>
</div>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-striped table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>ID</th><th>Username</th><th>Nama</th><th>Email</th><th>Groups</th><th>Last Login</th><th class="text-end">Aksi</th></tr>
      </thead>
      <tbody>
        <?php if (empty($rows)): ?>
          <tr><td colspan="7" class="text-center text-muted py-4">Data kosong</td></tr>
        <?php else: foreach ($rows as $r): ?>
          <tr>
            <td><?= (int)$r['user_id'] ?></td>
            <td><?= htmlspecialchars($r['username']) ?></td>
            <td><?= htmlspecialchars($r['realname']) ?></td>
            <td><?= htmlspecialchars($r['email'] ?? '-') ?></td>
            <td><?= htmlspecialchars($r['groups'] ?? '-') ?></td>
            <td><?= htmlspecialchars($r['last_login'] ?? '-') ?></td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-primary" href="index.php?r=users/edit&id=<?= (int)$r['user_id'] ?>"><i class="bi bi-pencil"></i></a>
              <a class="btn btn-sm btn-outline-dark" href="index.php?r=users/resetPassword&id=<?= (int)$r['user_id'] ?>"><i class="bi bi-key"></i></a>
              <a class="btn btn-sm btn-outline-danger" href="index.php?r=users/delete&id=<?= (int)$r['user_id'] ?>"
                 onclick="return confirm('Yakin hapus user ini?')"><i class="bi bi-trash"></i></a>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
