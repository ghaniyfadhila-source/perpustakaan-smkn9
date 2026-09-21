<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="d-flex justify-content-between align-items-end mb-3">
  <div><h4 class="mb-0">User Staff</h4><div class="text-muted small">Admin/Operator (tabel user)</div></div>
  <a class="btn btn-outline-secondary" href="index.php?r=system/changePassword"><i class="bi bi-key me-1"></i>Ganti Password Saya</a>
</div>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-striped table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>ID</th><th>Username</th><th>Nama</th><th>Email</th><th>Group</th><th>Last Login</th></tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= (int)$r['user_id'] ?></td>
            <td><?= htmlspecialchars($r['username']) ?></td>
            <td><?= htmlspecialchars($r['realname']) ?></td>
            <td><?= htmlspecialchars($r['email'] ?? '-') ?></td>
            <td><?= htmlspecialchars($r['groups'] ?? '-') ?></td>
            <td><?= htmlspecialchars($r['last_login'] ?? '-') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
