<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="d-flex justify-content-between align-items-end mb-3">
  <div>
    <h4 class="mb-0">Laporan</h4>
    <div class="text-muted small">Pilih jenis laporan</div>
  </div>
</div>

<div class="row g-3">
  <div class="col-12 col-md-6 col-lg-3">
    <a class="card shadow-sm text-decoration-none" href="index.php?r=reports/loans">
      <div class="card-body">
        <div class="fw-semibold">Laporan Peminjaman</div>
        <div class="text-muted small">Periode pinjam</div>
      </div>
    </a>
  </div>
  <div class="col-12 col-md-6 col-lg-3">
    <a class="card shadow-sm text-decoration-none" href="index.php?r=reports/returns">
      <div class="card-body">
        <div class="fw-semibold">Laporan Pengembalian</div>
        <div class="text-muted small">Periode kembali</div>
      </div>
    </a>
  </div>
  <div class="col-12 col-md-6 col-lg-3">
    <a class="card shadow-sm text-decoration-none" href="index.php?r=reports/overdue">
      <div class="card-body">
        <div class="fw-semibold">Laporan Terlambat</div>
        <div class="text-muted small">Pinjaman aktif telat</div>
      </div>
    </a>
  </div>
  <div class="col-12 col-md-6 col-lg-3">
    <a class="card shadow-sm text-decoration-none" href="index.php?r=reports/fines">
      <div class="card-body">
        <div class="fw-semibold">Laporan Denda</div>
        <div class="text-muted small">Ledger denda</div>
      </div>
    </a>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
