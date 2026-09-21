<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<?php if (!empty($_SESSION['flash'])): $f=$_SESSION['flash']; unset($_SESSION['flash']); ?>
  <div class="alert alert-<?= htmlspecialchars($f['type']) ?> alert-dismissible fade show">
    <?= htmlspecialchars($f['msg']) ?><button class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<div class="d-flex flex-wrap justify-content-between align-items-end mb-3">
  <div>
    <h4 class="mb-0">Anggota</h4>
    <div class="text-muted small">Tambah/edit/hapus anggota + cetak kartu barcode</div>
  </div>
  <a class="btn btn-primary" href="index.php?r=members/create"><i class="bi bi-plus-lg me-1"></i>Tambah</a>
</div>

<div class="card shadow-sm mb-3">
  <div class="card-body">
    <form class="row g-2" method="get">
      <input type="hidden" name="r" value="members/index">
      <div class="col-12 col-md-10">
        <input class="form-control" name="q" placeholder="Cari ID / Nama..." value="<?= htmlspecialchars($q ?? '') ?>">
      </div>
      <div class="col-12 col-md-2 d-grid">
        <button class="btn btn-outline-primary"><i class="bi bi-search me-1"></i>Cari</button>
      </div>
    </form>
  </div>
</div>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-striped table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>ID</th><th>Nama</th><th>Expire</th><th class="text-end">Aksi</th></tr>
      </thead>
      <tbody>
        <?php if (empty($rows)): ?>
          <tr><td colspan="4" class="text-center text-muted py-4">Data kosong</td></tr>
        <?php else: foreach ($rows as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r['member_id']) ?></td>
            <td class="fw-semibold"><?= htmlspecialchars($r['member_name']) ?></td>
            <td><?= htmlspecialchars($r['expire_date']) ?></td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-dark" target="_blank" href="index.php?r=members/printCard&id=<?= urlencode($r['member_id']) ?>">
                <i class="bi bi-printer"></i>
              </a>
              <a class="btn btn-sm btn-outline-primary" href="index.php?r=members/edit&id=<?= urlencode($r['member_id']) ?>">
                <i class="bi bi-pencil"></i>
              </a>
              <a class="btn btn-sm btn-outline-danger" href="index.php?r=members/delete&id=<?= urlencode($r['member_id']) ?>"
                 onclick="return confirm('Yakin hapus anggota ini?')">
                <i class="bi bi-trash"></i>
              </a>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
