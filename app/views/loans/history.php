<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<?php if (!empty($_SESSION['flash'])): $f=$_SESSION['flash']; unset($_SESSION['flash']); ?>
  <div class="alert alert-<?= htmlspecialchars($f['type']) ?> alert-dismissible fade show">
    <?= htmlspecialchars($f['msg']) ?><button class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-end mb-3">
  <div>
    <h4 class="mb-0">History Peminjaman</h4>
    <div class="text-muted small">Filter periode + cari member/barcode/judul</div>
  </div>
</div>

<div class="card shadow-sm mb-3">
  <div class="card-body">
    <form class="row g-2" method="get">
      <input type="hidden" name="r" value="loans/history">
      <div class="col-12 col-md-3">
        <label class="form-label">Dari</label>
        <input class="form-control" type="date" name="from" value="<?= htmlspecialchars($from) ?>">
      </div>
      <div class="col-12 col-md-3">
        <label class="form-label">Sampai</label>
        <input class="form-control" type="date" name="to" value="<?= htmlspecialchars($to) ?>">
      </div>
      <div class="col-12 col-md-4">
        <label class="form-label">Cari</label>
        <input class="form-control" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="member_id / item_code / judul">
      </div>
      <div class="col-12 col-md-2 d-grid">
        <label class="form-label d-none d-md-block">&nbsp;</label>
        <button class="btn btn-outline-primary"><i class="bi bi-search me-1"></i>Filter</button>
      </div>
    </form>
  </div>
</div>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-striped table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>ID</th><th>Member</th><th>Barcode</th><th>Judul</th>
          <th>Pinjam</th><th>Jatuh Tempo</th><th>Status</th><th>Petugas</th>
          <?php if (ACL::isAdmin()): ?><th class="text-end">Aksi</th><?php endif; ?>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($rows)): ?>
          <tr><td colspan="<?= ACL::isAdmin()?9:8 ?>" class="text-center text-muted py-4">Data kosong</td></tr>
        <?php else: foreach ($rows as $r): ?>
          <tr>
            <td><?= (int)$r['loan_id'] ?></td>
            <td><?= htmlspecialchars($r['member_id']) ?></td>
            <td><?= htmlspecialchars($r['item_code']) ?></td>
            <td><?= htmlspecialchars($r['title'] ?? '-') ?></td>
            <td><?= htmlspecialchars($r['loan_date']) ?></td>
            <td><?= htmlspecialchars($r['due_date']) ?></td>
            <td>
              <?php if ((int)$r['is_return'] === 1): ?>
                <span class="badge text-bg-success">Kembali</span>
              <?php else: ?>
                <span class="badge text-bg-warning">Dipinjam</span>
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($r['staff_name'] ?? '-') ?></td>
            <?php if (ACL::isAdmin()): ?>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-primary" href="index.php?r=loans/edit&id=<?= (int)$r['loan_id'] ?>">
                  <i class="bi bi-pencil"></i>
                </a>
              </td>
            <?php endif; ?>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
