<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<?php if (!empty($_SESSION['flash'])): $f=$_SESSION['flash']; unset($_SESSION['flash']); ?>
  <div class="alert alert-<?= htmlspecialchars($f['type']) ?> alert-dismissible fade show">
    <?= htmlspecialchars($f['msg']) ?><button class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<div class="d-flex flex-wrap justify-content-between align-items-end mb-3">
  <div>
    <h4 class="mb-0">Peminjaman</h4>
    <div class="text-muted small">Masukkan Member ID lalu scan barcode buku (item_code)</div>
  </div>
</div>

<?php if (!empty($error)): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="row g-3">
  <div class="col-12 col-lg-5">
    <div class="card shadow-sm">
      <div class="card-body">
        <form method="get" class="row g-2">
          <input type="hidden" name="r" value="loans/borrow">
          <div class="col-12">
            <label class="form-label">Member ID</label>
            <input class="form-control" name="member_id" value="<?= htmlspecialchars($memberId) ?>" placeholder="contoh: 12345" required>
          </div>
          <div class="col-12 d-grid">
            <button class="btn btn-outline-primary"><i class="bi bi-search me-1"></i>Cek Anggota</button>
          </div>
        </form>

        <hr>

        <?php if ($member): ?>
          <div class="p-3 bg-light rounded">
            <div class="fw-semibold"><?= htmlspecialchars($member['member_name']) ?></div>
            <div class="small text-muted">ID: <?= htmlspecialchars($member['member_id']) ?></div>
            <div class="small text-muted">Expire: <?= htmlspecialchars($member['expire_date']) ?></div>
          </div>
        <?php else: ?>
          <div class="text-muted small">Silakan cek anggota terlebih dahulu.</div>
        <?php endif; ?>
      </div>
    </div>

    <div class="card shadow-sm mt-3">
      <div class="card-body">
        <div class="fw-semibold mb-2">Pinjaman Aktif Anggota</div>
        <div class="table-responsive">
          <table class="table table-sm table-striped align-middle mb-0">
            <thead class="table-light">
              <tr><th>Barcode</th><th>Jatuh Tempo</th></tr>
            </thead>
            <tbody>
              <?php if (empty($activeLoans)): ?>
                <tr><td colspan="2" class="text-muted">Tidak ada pinjaman aktif</td></tr>
              <?php else: foreach ($activeLoans as $x): ?>
                <tr>
                  <td><?= htmlspecialchars($x['item_code']) ?><div class="small text-muted"><?= htmlspecialchars($x['title'] ?? '-') ?></div></td>
                  <td><?= htmlspecialchars($x['due_date']) ?></td>
                </tr>
              <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-7">
    <div class="card shadow-sm">
      <div class="card-body">
        <form method="post">
          <div class="mb-2">
            <label class="form-label">Member ID</label>
            <input class="form-control" name="member_id" value="<?= htmlspecialchars($memberId) ?>" required>
          </div>

          <div class="mb-2">
            <label class="form-label">Scan Barcode Buku (1 per baris atau pisahkan dengan koma)</label>
            <textarea class="form-control" name="item_codes" rows="8" placeholder="BK-0001&#10;BK-0002"></textarea>
            <div class="form-text">Tips: scanner biasanya otomatis enter → cocok 1 baris per barcode.</div>
          </div>

          <div class="d-flex gap-2">
            <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Peminjaman</button>
            <a class="btn btn-outline-secondary" href="index.php?r=loans/borrow">Reset</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
