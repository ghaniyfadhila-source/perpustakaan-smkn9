<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="d-flex justify-content-between align-items-end mb-3">
  <div><h4 class="mb-0">System Log</h4><div class="text-muted small">Audit: siapa melakukan apa</div></div>
</div>

<div class="card shadow-sm mb-3">
  <div class="card-body">
    <form class="row g-2" method="get">
      <input type="hidden" name="r" value="logs/index">
      <div class="col-12 col-md-10">
        <input class="form-control" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Cari location/action/id/pesan...">
      </div>
      <div class="col-12 col-md-2 d-grid">
        <button class="btn btn-primary"><i class="bi bi-search me-1"></i>Cari</button>
      </div>
    </form>
  </div>
</div>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-striped table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>ID</th><th>Tanggal</th><th>Type</th><th>UserID</th><th>Lokasi</th><th>Action</th><th>Pesan</th></tr>
      </thead>
      <tbody>
        <?php if (empty($rows)): ?>
          <tr><td colspan="7" class="text-center text-muted py-4">Log kosong</td></tr>
        <?php else: foreach ($rows as $r): ?>
          <tr>
            <td><?= (int)$r['log_id'] ?></td>
            <td><?= htmlspecialchars($r['log_date']) ?></td>
            <td><?= htmlspecialchars($r['log_type']) ?></td>
            <td><?= htmlspecialchars($r['id'] ?? '-') ?></td>
            <td><?= htmlspecialchars($r['log_location']) ?></td>
            <td><?= htmlspecialchars($r['action'] ?? '-') ?></td>
            <td class="small"><?= htmlspecialchars($r['log_msg']) ?></td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
