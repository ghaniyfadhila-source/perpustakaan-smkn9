<?php
$requestSummary = $requestSummary ?? [
  'PENDING' => 0, 'APPROVED' => 0, 'REJECTED' => 0, 'CANCELLED' => 0, 'TOTAL' => 0
];
$requestVerified = $requestVerified ?? false;
$returnCount = $returnCount ?? 0;
?>

<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

  <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3 gap-2">
    <div>
      <h4 class="mb-0">Dashboard Admin</h4>
      <div class="text-muted small">Ringkasan aktivitas perpustakaan SMKN 9 Semarang</div>
    </div>
    <div class="text-muted small">
      <i class="bi bi-calendar3 me-1"></i><?= date('d M Y') ?>
    </div>
  </div>

  <?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type']) ?> alert-dismissible fade show" role="alert">
      <?= htmlspecialchars($_SESSION['flash']['msg']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <ul class="nav nav-tabs mb-3" id="dashboardTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="overview-tab" type="button" role="tab" data-target="#overview">
        <i class="bi bi-speedometer2 me-1"></i>Overview
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="requests-tab" type="button" role="tab" data-target="#requests">
        <i class="bi bi-journal-check me-1"></i>Request Verifikasi
        <span class="badge bg-warning text-dark ms-1"><?= $requestSummary['PENDING'] ?? 0 ?></span>
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="returns-tab" type="button" role="tab" data-target="#returns">
        <i class="bi bi-arrow-return-left me-1"></i>Pengembalian
        <span class="badge bg-info text-dark ms-1"><?= $returnCount ?></span>
      </button>
    </li>
  </ul>

  <div class="tab-content" id="dashboardTabsContent">

    <!-- OVERVIEW TAB -->
    <div class="tab-pane fade show active" id="overview" role="tabpanel">
      <div class="row g-3">
        <div class="col-12 col-md-4">
          <div class="card shadow-sm"><div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
              <div><div class="text-muted small">Pinjaman Aktif</div><div class="fs-2 fw-bold"><?= (int)($activeLoans['c'] ?? 0) ?></div></div>
              <span class="badge bg-primary-subtle text-primary border border-primary-subtle"><i class="bi bi-box-arrow-in-right"></i></span>
            </div>
          </div></div>
        </div>
        <div class="col-12 col-md-4">
          <div class="card shadow-sm"><div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
              <div><div class="text-muted small">Terlambat</div><div class="fs-2 fw-bold"><?= (int)($overdue['c'] ?? 0) ?></div></div>
              <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="bi bi-exclamation-triangle"></i></span>
            </div>
          </div></div>
        </div>
        <div class="col-12 col-md-4">
          <div class="card shadow-sm"><div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
              <div><div class="text-muted small">Denda Hari Ini</div><div class="fs-2 fw-bold"><?= number_format((int)($finesToday['s'] ?? 0)) ?></div></div>
              <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-cash-coin"></i></span>
            </div>
          </div></div>
        </div>
      </div>

      <div class="card shadow-sm mt-3"><div class="card-body">
        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
          <div><div class="fw-semibold">Quick Actions</div><div class="text-muted small">Akses cepat untuk petugas</div></div>
          <div class="d-flex gap-2 flex-wrap">
            <a class="btn btn-outline-primary btn-sm" href="index.php?r=loans/borrow"><i class="bi bi-upc-scan me-1"></i>Pinjam</a>
            <a class="btn btn-outline-secondary btn-sm" href="index.php?r=loans/returnBook"><i class="bi bi-arrow-return-left me-1"></i>Kembali</a>
            <a class="btn btn-outline-dark btn-sm" href="index.php?r=books/index"><i class="bi bi-search me-1"></i>Cari Buku</a>
          </div>
        </div>
      </div></div>

      <?php
      $monthlyLoans = $monthlyLoans ?? []; $topBooks = $topBooks ?? []; $memberTypes = $memberTypes ?? [];
      $labelsMonths = array_map(fn($x) => (string)($x['label'] ?? ''), $monthlyLoans);
      $dataBorrowed = array_map(fn($x) => (int)($x['borrowed'] ?? 0), $monthlyLoans);
      $dataReturned = array_map(fn($x) => (int)($x['returned'] ?? 0), $monthlyLoans);
      $labelsTopBooks = array_map(fn($x) => (string)($x['title'] ?? ''), $topBooks);
      $dataTopBooks = array_map(fn($x) => (int)($x['cnt'] ?? 0), $topBooks);
      $labelsMember = array_map(fn($x) => (string)($x['label'] ?? ''), $memberTypes);
      $dataMember = array_map(fn($x) => (int)($x['cnt'] ?? 0), $memberTypes);
      ?>

      <div class="row g-3 mt-3">
        <div class="col-12 col-lg-8">
          <div class="card shadow-sm"><div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div><div class="fw-semibold">Rekap Pinjam vs Kembali</div><div class="text-muted small">Ringkasan per bulan</div></div>
              <span class="badge bg-light text-dark border"><i class="bi bi-graph-up me-1"></i>Chart</span>
            </div>
            <?php if (empty($monthlyLoans)): ?>
              <div class="alert alert-light border mb-0"><div class="fw-semibold">Data chart belum tersedia</div></div>
            <?php else: ?>
              <div style="height:260px"><canvas id="chartLoans"></canvas></div>
            <?php endif; ?>
          </div></div>
        </div>
        <div class="col-12 col-lg-4">
          <div class="card shadow-sm h-100"><div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div><div class="fw-semibold">Komposisi Member</div><div class="text-muted small">Berdasarkan tipe</div></div>
              <span class="badge bg-light text-dark border"><i class="bi bi-pie-chart me-1"></i>Chart</span>
            </div>
            <?php if (empty($memberTypes)): ?>
              <div class="alert alert-light border mb-0"><div class="fw-semibold">Data belum tersedia</div></div>
            <?php else: ?>
              <div style="height:260px"><canvas id="chartMembers"></canvas></div>
            <?php endif; ?>
          </div></div>
        </div>
      </div>

      <div class="row g-3 mt-3">
        <div class="col-12 col-lg-7">
          <div class="card shadow-sm"><div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div><div class="fw-semibold">Top Buku Dipinjam</div><div class="text-muted small">Paling sering dipinjam</div></div>
              <span class="badge bg-light text-dark border"><i class="bi bi-bar-chart me-1"></i>Chart</span>
            </div>
            <?php if (empty($topBooks)): ?>
              <div class="alert alert-light border mb-0"><div class="fw-semibold">Data belum tersedia</div></div>
            <?php else: ?>
              <div style="height:260px"><canvas id="chartTopBooks"></canvas></div>
            <?php endif; ?>
          </div></div>
        </div>
        <div class="col-12 col-lg-5">
          <div class="card shadow-sm"><div class="card-body">
            <div class="fw-semibold mb-2">Rekap Cepat</div>
            <div class="row g-3">
              <div class="col-12 col-md-6"><div class="p-3 border rounded-3 bg-light"><div class="text-muted small">Total Pinjaman Aktif</div><div class="fs-4 fw-bold"><?= (int)($activeLoans['c'] ?? 0) ?></div></div></div>
              <div class="col-12 col-md-6"><div class="p-3 border rounded-3 bg-light"><div class="text-muted small">Total Terlambat</div><div class="fs-4 fw-bold"><?= (int)($overdue['c'] ?? 0) ?></div></div></div>
              <div class="col-12"><div class="p-3 border rounded-3"><div class="d-flex justify-content-between align-items-center"><div><div class="text-muted small">Denda Hari Ini</div><div class="fs-4 fw-bold"><?= number_format((int)($finesToday['s'] ?? 0)) ?></div></div><a href="index.php?r=reports/fines" class="btn btn-outline-success btn-sm"><i class="bi bi-receipt me-1"></i>Lihat Denda</a></div></div></div>
            </div>
          </div></div>
        </div>
      </div>
    </div>

    <!-- REQUESTS TAB -->
    <div class="tab-pane fade" id="requests" role="tabpanel">
      <div class="card shadow-sm mb-3">
        <div class="card-header d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2">
          <h5 class="mb-0"><i class="bi bi-journal-check me-2"></i>Verifikasi Request Peminjaman Siswa</h5>
          <div class="d-flex gap-2 flex-wrap">
            <span class="badge bg-warning text-dark">Menunggu: <?= $requestSummary['PENDING'] ?? 0 ?></span>
            <span class="badge bg-success">Disetujui: <?= $requestSummary['APPROVED'] ?? 0 ?></span>
            <span class="badge bg-danger">Ditolak: <?= $requestSummary['REJECTED'] ?? 0 ?></span>
            <span class="badge bg-secondary">Dibatalkan: <?= $requestSummary['CANCELLED'] ?? 0 ?></span>
          </div>
        </div>
        <div class="card-body">
          <?php if (!$requestVerified): ?>
            <div id="verifyFormContainer">
              <div class="text-center py-4">
                <i class="bi bi-lock-fill display-4 text-muted"></i>
                <h5 class="text-muted mt-2">Area Terproteksi</h5>
                <p class="text-muted">Masukkan password admin untuk mengakses verifikasi request peminjaman.</p>
              </div>
              <form id="verifyPasswordForm" class="mx-auto" style="max-width:400px;">
                <div class="mb-3">
                  <label for="verifyPassword" class="form-label fw-semibold">Password Admin</label>
                  <div class="input-group">
                    <input type="password" class="form-control" id="verifyPassword" name="password" placeholder="Masukkan password" required>
                    <button class="btn btn-outline-secondary" type="button" id="toggleVerifyPassword"><i class="bi bi-eye"></i></button>
                  </div>
                </div>
                <div id="verifyError" class="alert alert-danger d-none" role="alert"></div>
                <button type="submit" class="btn btn-primary w-100" id="verifySubmitBtn">
                  <i class="bi bi-shield-lock me-1"></i> Verifikasi
                </button>
              </form>
            </div>
          <?php else: ?>
            <div id="requestsContent">
              <?php require __DIR__ . '/requests_tab.php'; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- RETURNS TAB -->
    <div class="tab-pane fade" id="returns" role="tabpanel">
      <div class="card shadow-sm mb-3">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="mb-0"><i class="bi bi-arrow-return-left me-2"></i>Pengembalian Buku</h5>
          <span class="badge bg-info text-dark"><?= $returnCount ?> menunggu konfirmasi</span>
        </div>
        <div class="card-body">
          <?php if (!$requestVerified): ?>
            <div id="verifyFormContainerReturns">
              <div class="text-center py-4">
                <i class="bi bi-lock-fill display-4 text-muted"></i>
                <h5 class="text-muted mt-2">Area Terproteksi</h5>
                <p class="text-muted">Masukkan password admin untuk mengakses menu pengembalian.</p>
              </div>
              <form id="verifyPasswordFormReturns" class="mx-auto" style="max-width:400px;">
                <div class="mb-3">
                  <label class="form-label fw-semibold">Password Admin</label>
                  <div class="input-group">
                    <input type="password" class="form-control" name="password" placeholder="Masukkan password" required>
                    <button class="btn btn-outline-secondary toggle-pass-btn" type="button"><i class="bi bi-eye"></i></button>
                  </div>
                </div>
                <div class="alert alert-danger d-none verify-error" role="alert"></div>
                <button type="submit" class="btn btn-primary w-100 verify-submit-btn">
                  <i class="bi bi-shield-lock me-1"></i> Verifikasi
                </button>
              </form>
            </div>
          <?php else: ?>
            <div id="returnsContent">
              <?php require __DIR__ . '/returns_tab.php'; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

  </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const monthsLabels = <?= json_encode($labelsMonths, JSON_UNESCAPED_UNICODE) ?>;
  const borrowedData = <?= json_encode($dataBorrowed, JSON_UNESCAPED_UNICODE) ?>;
  const returnedData = <?= json_encode($dataReturned, JSON_UNESCAPED_UNICODE) ?>;
  const topBookLabels = <?= json_encode($labelsTopBooks, JSON_UNESCAPED_UNICODE) ?>;
  const topBookData = <?= json_encode($dataTopBooks, JSON_UNESCAPED_UNICODE) ?>;
  const memberLabels = <?= json_encode($labelsMember, JSON_UNESCAPED_UNICODE) ?>;
  const memberData = <?= json_encode($dataMember, JSON_UNESCAPED_UNICODE) ?>;

  if (document.getElementById('chartLoans') && monthsLabels.length) {
    new Chart(document.getElementById('chartLoans'), {
      type: 'line',
      data: { labels: monthsLabels, datasets: [{ label: 'Dipinjam', data: borrowedData, tension: 0.35 }, { label: 'Dikembalikan', data: returnedData, tension: 0.35 }] },
      options: { responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
    });
  }
  if (document.getElementById('chartMembers') && memberLabels.length) {
    new Chart(document.getElementById('chartMembers'), {
      type: 'doughnut', data: { labels: memberLabels, datasets: [{ label: 'Member', data: memberData }] },
      options: { responsive: true, maintainAspectRatio: false }
    });
  }
  if (document.getElementById('chartTopBooks') && topBookLabels.length) {
    new Chart(document.getElementById('chartTopBooks'), {
      type: 'bar', data: { labels: topBookLabels, datasets: [{ label: 'Jumlah', data: topBookData }] },
      options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
    });
  }

  // ===== Verify Form (Requests Tab) =====
  const verifyForm = document.getElementById('verifyPasswordForm');
  if (verifyForm) {
    const verifyError = document.getElementById('verifyError');
    const verifySubmit = document.getElementById('verifySubmitBtn');
    const toggleBtn = document.getElementById('toggleVerifyPassword');
    const passInput = document.getElementById('verifyPassword');

    if (toggleBtn && passInput) {
      toggleBtn.addEventListener('click', () => {
        const isPass = passInput.type === 'password';
        passInput.type = isPass ? 'text' : 'password';
        toggleBtn.innerHTML = isPass ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
      });
    }

    verifyForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const password = passInput.value.trim();
      if (!password) return;

      verifySubmit.disabled = true;
      verifySubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memverifikasi...';
      verifyError.classList.add('d-none');

      try {
        const res = await fetch('index.php?r=dashboard/verifyRequestPassword', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams({ password })
        });
        const data = await res.json();
        if (data.ok) {
          window.location.reload();
        } else {
          verifyError.textContent = data.msg || 'Verifikasi gagal';
          verifyError.classList.remove('d-none');
        }
      } catch (err) {
        verifyError.textContent = 'Terjadi kesalahan: ' + err.message;
        verifyError.classList.remove('d-none');
      } finally {
        verifySubmit.disabled = false;
        verifySubmit.innerHTML = '<i class="bi bi-shield-lock me-1"></i> Verifikasi';
      }
    });
  }

  // ===== Verify Form (Returns Tab) =====
  const verifyFormReturns = document.getElementById('verifyPasswordFormReturns');
  if (verifyFormReturns) {
    verifyFormReturns.addEventListener('submit', async (e) => {
      e.preventDefault();
      const passInput = verifyFormReturns.querySelector('input[name="password"]');
      const password = passInput.value.trim();
      if (!password) return;

      const submitBtn = verifyFormReturns.querySelector('.verify-submit-btn');
      const errorDiv = verifyFormReturns.querySelector('.verify-error');

      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memverifikasi...';
      errorDiv.classList.add('d-none');

      try {
        const res = await fetch('index.php?r=dashboard/verifyRequestPassword', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams({ password })
        });
        const data = await res.json();
        if (data.ok) {
          window.location.reload();
        } else {
          errorDiv.textContent = data.msg || 'Verifikasi gagal';
          errorDiv.classList.remove('d-none');
        }
      } catch (err) {
        errorDiv.textContent = 'Terjadi kesalahan: ' + err.message;
        errorDiv.classList.remove('d-none');
      } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-shield-lock me-1"></i> Verifikasi';
      }
    });
  }

  // ===== Toggle password buttons =====
  document.querySelectorAll('.toggle-pass-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const input = btn.closest('.input-group').querySelector('input');
      if (!input) return;
      const isPass = input.type === 'password';
      input.type = isPass ? 'text' : 'password';
      btn.innerHTML = isPass ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
    });
  });

  // ===== Tab Switching =====
  const allTabs = document.querySelectorAll('#dashboardTabs .nav-link');
  const allPanes = document.querySelectorAll('#dashboardTabsContent .tab-pane');

  function switchTab(targetId) {
    allTabs.forEach(t => t.classList.remove('active'));
    allPanes.forEach(p => { p.classList.remove('show', 'active'); });
    const btn = document.querySelector(`#dashboardTabs [data-target="${targetId}"]`);
    const pane = document.querySelector(targetId);
    if (btn) btn.classList.add('active');
    if (pane) pane.classList.add('show', 'active');
  }

  allTabs.forEach(tab => {
    tab.addEventListener('click', function(e) {
      e.preventDefault();
      switchTab(this.getAttribute('data-target'));
    });
  });

  // ===== AJAX Filter =====
  const requestsContent = document.getElementById('requestsContent');
  function loadRequests(status) {
    if (!requestsContent) return;
    requestsContent.innerHTML = '<div class="text-center py-5 text-muted"><div class="spinner-border spinner-border-sm me-2" role="status"></div>Memuat data...</div>';
    const url = 'index.php?r=dashboard/requests' + (status ? '&status=' + encodeURIComponent(status) : '');
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.text(); })
      .then(html => { requestsContent.innerHTML = html; attachFilterListeners(); })
      .catch(err => { requestsContent.innerHTML = '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>Gagal memuat: ' + err.message + '</div>'; });
  }

  function attachFilterListeners() {
    if (!requestsContent) return;
    requestsContent.querySelectorAll('.filter-btn').forEach(btn => {
      btn.addEventListener('click', function() { loadRequests(this.getAttribute('data-status')); });
    });
  }
  attachFilterListeners();
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
