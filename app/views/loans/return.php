<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<?php if (!empty($_SESSION['flash'])): $f=$_SESSION['flash']; unset($_SESSION['flash']); ?>
  <div class="alert alert-<?= htmlspecialchars($f['type']) ?> alert-dismissible fade show shadow-sm">
    <div class="d-flex align-items-center gap-2">
      <i class="bi bi-info-circle"></i>
      <div><?= htmlspecialchars($f['msg']) ?></div>
    </div>
    <button class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-end mb-3">
  <div>
    <h4 class="mb-1">Pengembalian</h4>
    <div class="text-muted small">Scan barcode buku (item_code) untuk memproses pengembalian</div>
  </div>

  <div class="mt-2 mt-md-0">
    <span class="badge text-bg-secondary">
      <i class="bi bi-upc-scan me-1"></i>Mode Scan
    </span>
  </div>
</div>

<?php if (!empty($error)): ?>
  <div class="alert alert-danger shadow-sm">
    <div class="d-flex align-items-center gap-2">
      <i class="bi bi-exclamation-triangle"></i>
      <div><?= htmlspecialchars($error) ?></div>
    </div>
  </div>
<?php endif; ?>

<div class="row g-3">
  <!-- Scan Card -->
  <div class="col-12 col-lg-5">
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <div class="d-flex align-items-center gap-2 mb-2">
          <div class="rounded-3 p-2 bg-primary-subtle text-primary">
            <i class="bi bi-arrow-return-left fs-5"></i>
          </div>
          <div>
            <div class="fw-semibold">Scan Barcode Buku</div>
            <div class="text-muted small">Arahkan scanner ke input, lalu scan</div>
          </div>
        </div>

        <form method="post" class="mt-3" autocomplete="off">
          <label class="form-label">Barcode Buku</label>
          <div class="input-group input-group-lg">
            <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
            <input class="form-control" id="item_code" name="item_code" autofocus required placeholder="scan di sini...">
          </div>

          <div class="d-grid mt-3">
            <button class="btn btn-primary">
              <i class="bi bi-check2-circle me-1"></i>Proses Pengembalian
            </button>
          </div>

          <div class="text-muted small mt-2">
            Tips: setelah berhasil, input akan otomatis fokus lagi untuk scan berikutnya.
          </div>
        </form>
      </div>
    </div>

    <!-- Quick actions -->
    <div class="card shadow-sm border-0 mt-3">
      <div class="card-body">
        <div class="fw-semibold mb-2">Aksi Cepat</div>
        <div class="d-flex gap-2 flex-wrap">
          <a class="btn btn-outline-secondary btn-sm" href="index.php?r=reports/returns">
            <i class="bi bi-clipboard-data me-1"></i>Laporan Pengembalian
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Result Card -->
  <div class="col-12 col-lg-7">
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <div class="fw-semibold">Hasil Pengembalian</div>
            <div class="text-muted small">Ringkasan keterlambatan & denda</div>
          </div>
          <span class="badge text-bg-success-subtle text-success border border-success-subtle">
            <i class="bi bi-shield-check me-1"></i>Validasi
          </span>
        </div>

        <?php if (empty($result)): ?>
          <div class="text-center py-5">
            <div class="display-6 mb-2">📦</div>
            <div class="fw-semibold">Belum ada hasil</div>
            <div class="text-muted small">Scan barcode untuk menampilkan detail pengembalian.</div>
          </div>
        <?php else: ?>
          <div class="row g-3 mt-2">
            <!-- Metric 1 -->
            <div class="col-12 col-md-4">
              <div class="p-3 rounded-4 border h-100">
                <div class="d-flex align-items-center justify-content-between">
                  <div class="text-muted small">Terlambat</div>
                  <i class="bi bi-clock-history text-danger"></i>
                </div>
                <div class="fs-2 fw-bold"><?= (int)$result['late_days'] ?></div>
                <div class="text-muted small">hari</div>
              </div>
            </div>

            <!-- Metric 2 -->
            <div class="col-12 col-md-4">
              <div class="p-3 rounded-4 border h-100">
                <div class="d-flex align-items-center justify-content-between">
                  <div class="text-muted small">Denda</div>
                  <i class="bi bi-cash-coin text-primary"></i>
                </div>
                <div class="fs-2 fw-bold">Rp <?= number_format((int)$result['fine']) ?></div>
                <div class="text-muted small">total</div>
              </div>
            </div>

            <!-- Metric 3 -->
            <div class="col-12 col-md-4">
              <div class="p-3 rounded-4 border h-100">
                <div class="d-flex align-items-center justify-content-between">
                  <div class="text-muted small">Loan ID</div>
                  <i class="bi bi-receipt text-secondary"></i>
                </div>
                <div class="fs-2 fw-bold"><?= (int)$result['loan_id'] ?></div>
                <div class="text-muted small">transaksi</div>
              </div>
            </div>
          </div>

          <!-- Detail (jika controller mengirim data tambahan) -->
          <div class="mt-4">
            <div class="fw-semibold mb-2">Detail</div>
            <div class="row g-2">
              <div class="col-12 col-md-6">
                <div class="p-3 rounded-4 border">
                  <div class="text-muted small">Barcode Buku</div>
                  <div class="fw-semibold"><?= htmlspecialchars($result['item_code'] ?? '-') ?></div>
                  <div class="text-muted small mt-2">Judul Buku</div>
                  <div><?= htmlspecialchars($result['title'] ?? '-') ?></div>
                </div>
              </div>
              <div class="col-12 col-md-6">
                <div class="p-3 rounded-4 border">
                  <div class="text-muted small">Anggota</div>
                  <div class="fw-semibold"><?= htmlspecialchars($result['member_name'] ?? '-') ?></div>
                  <div class="text-muted small mt-2">Member ID</div>
                  <div><?= htmlspecialchars($result['member_id'] ?? '-') ?></div>
                </div>
              </div>
            </div>

            <div class="alert alert-success mt-3 mb-0">
              <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check2-circle"></i>
                <div>Pengembalian berhasil diproses.</div>
              </div>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script>
function focusScan(){ document.getElementById('item_code')?.focus(); }
function clearScan(){
  const el = document.getElementById('item_code');
  if (el){ el.value=''; el.focus(); }
}
// auto fokus setiap page load
document.addEventListener('DOMContentLoaded', focusScan);
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
