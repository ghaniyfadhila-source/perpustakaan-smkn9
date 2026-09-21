<?php
// Variabel untuk request tab
$requestSummary = $requestSummary ?? [
  'PENDING' => 0, 'APPROVED' => 0, 'REJECTED' => 0, 'CANCELLED' => 0, 'TOTAL' => 0
];
$requestVerified = $requestVerified ?? false;
$showVerifyModal = isset($_GET['request_verify']);
?>

<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="content-inner flex-grow-1" style="padding: 1.25rem; width: 100%;">
  <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3 gap-2">
    <div>
      <h4 class="mb-0">Dashboard Admin</h4>
      <div class="text-muted small">Ringkasan aktivitas perpustakaan SMKN 9 Semarang</div>
    </div>
    <div class="text-muted small">
      <i class="bi bi-calendar3 me-1"></i><?= date('d M Y') ?>
    </div>
  </div>

  <!-- Flash messages -->
  <?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show" role="alert">
      <?= $_SESSION['flash']['msg'] ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['flash']); ?>
  <?php endif; ?>

  <!-- Tab Navigation -->
  <ul class="nav nav-tabs mb-3" id="dashboardTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="overview-tab" type="button" role="tab" data-target="#overview">
        <i class="bi bi-speedometer2 me-1"></i>Overview
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="requests-tab" type="button" role="tab"
              data-target="#requests"
              data-verified="<?= $requestVerified ? '1' : '0' ?>">
        <i class="bi bi-journal-check me-1"></i>Request Verifikasi
        <span class="badge bg-warning text-dark ms-1"><?= $requestSummary['PENDING'] ?? 0 ?></span>
      </button>
    </li>
  </ul>

  <!-- Tab Content -->
  <div class="tab-content" id="dashboardTabsContent">

    <!-- OVERVIEW TAB (Existing Dashboard) -->
    <div class="tab-pane fade show active" id="overview" role="tabpanel">
      <!-- Statistik Cards -->
      <div class="row g-3">
        <div class="col-12 col-md-4">
          <div class="card shadow-sm">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <div class="text-muted small">Pinjaman Aktif</div>
                  <div class="fs-2 fw-bold"><?= (int)($activeLoans['c'] ?? 0) ?></div>
                </div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                  <i class="bi bi-box-arrow-in-right"></i>
                </span>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12 col-md-4">
          <div class="card shadow-sm">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <div class="text-muted small">Terlambat</div>
                  <div class="fs-2 fw-bold"><?= (int)($overdue['c'] ?? 0) ?></div>
                </div>
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                  <i class="bi bi-exclamation-triangle"></i>
                </span>
              </div>
            </div>
          </div>
        </div>

        <div class="col-12 col-md-4">
          <div class="card shadow-sm">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <div class="text-muted small">Denda Hari Ini</div>
                  <div class="fs-2 fw-bold"><?= number_format((int)($finesToday['s'] ?? 0)) ?></div>
                </div>
                <span class="badge bg-success-subtle text-success border border-success-subtle">
                  <i class="bi bi-cash-coin"></i>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card shadow-sm mt-3">
        <div class="card-body">
          <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
            <div>
              <div class="fw-semibold">Quick Actions</div>
              <div class="text-muted small">Akses cepat untuk petugas</div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
              <a class="btn btn-outline-primary btn-sm" href="index.php?r=loans/borrow"><i class="bi bi-upc-scan me-1"></i>Pinjam</a>
              <a class="btn btn-outline-secondary btn-sm" href="index.php?r=loans/returnBook"><i class="bi bi-arrow-return-left me-1"></i>Kembali</a>
              <a class="btn btn-outline-dark btn-sm" href="index.php?r=books/index"><i class="bi bi-search me-1"></i>Cari Buku</a>
            </div>
          </div>
        </div>
      </div>

      <?php
      $monthlyLoans = $monthlyLoans ?? [];
      $topBooks = $topBooks ?? [];
      $memberTypes = $memberTypes ?? [];

      $labelsMonths   = array_map(fn($x) => (string)($x['label'] ?? ''), $monthlyLoans);
      $dataBorrowed   = array_map(fn($x) => (int)($x['borrowed'] ?? 0), $monthlyLoans);
      $dataReturned   = array_map(fn($x) => (int)($x['returned'] ?? 0), $monthlyLoans);

      $labelsTopBooks = array_map(fn($x) => (string)($x['title'] ?? ''), $topBooks);
      $dataTopBooks   = array_map(fn($x) => (int)($x['cnt'] ?? 0), $topBooks);

      $labelsMember   = array_map(fn($x) => (string)($x['label'] ?? ''), $memberTypes);
      $dataMember     = array_map(fn($x) => (int)($x['cnt'] ?? 0), $memberTypes);
      ?>

      <div class="row g-3 mt-3">
        <div class="col-12 col-lg-8">
          <div class="card shadow-sm">
            <div class="card-body">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <div>
                  <div class="fw-semibold">Rekap Pinjam vs Kembali</div>
                  <div class="text-muted small">Ringkasan per bulan</div>
                </div>
                <span class="badge bg-light text-dark border"><i class="bi bi-graph-up me-1"></i>Chart</span>
              </div>
              <?php if (empty($monthlyLoans)): ?>
                <div class="alert alert-light border mb-0">
                  <div class="fw-semibold">Data chart belum tersedia</div>
                  <div class="text-muted small">Belum ada data peminjaman bulanan.</div>
                </div>
              <?php else: ?>
                <div style="height: 260px;"><canvas id="chartLoans"></canvas></div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <div class="col-12 col-lg-4">
          <div class="card shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <div>
                  <div class="fw-semibold">Komposisi Member</div>
                  <div class="text-muted small">Berdasarkan tipe</div>
                </div>
                <span class="badge bg-light text-dark border"><i class="bi bi-pie-chart me-1"></i>Chart</span>
              </div>
              <?php if (empty($memberTypes)): ?>
                <div class="alert alert-light border mb-0">
                  <div class="fw-semibold">Data belum tersedia</div>
                  <div class="text-muted small">Belum ada data tipe member.</div>
                </div>
              <?php else: ?>
                <div style="height: 260px;"><canvas id="chartMembers"></canvas></div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-3 mt-3">
        <div class="col-12 col-lg-7">
          <div class="card shadow-sm">
            <div class="card-body">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <div>
                  <div class="fw-semibold">Top Buku Dipinjam</div>
                  <div class="text-muted small">Paling sering dipinjam</div>
                </div>
                <span class="badge bg-light text-dark border"><i class="bi bi-bar-chart me-1"></i>Chart</span>
              </div>
              <?php if (empty($topBooks)): ?>
                <div class="alert alert-light border mb-0">
                  <div class="fw-semibold">Data belum tersedia</div>
                  <div class="text-muted small">Belum ada data peminjaman buku.</div>
                </div>
              <?php else: ?>
                <div style="height: 260px;"><canvas id="chartTopBooks"></canvas></div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <div class="col-12 col-lg-5">
          <div class="card shadow-sm">
            <div class="card-body">
              <div class="fw-semibold mb-2">Rekap Cepat</div>
              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <div class="p-3 border rounded-3 bg-light">
                    <div class="text-muted small">Total Pinjaman Aktif</div>
                    <div class="fs-4 fw-bold"><?= (int)($activeLoans['c'] ?? 0) ?></div>
                  </div>
                </div>
                <div class="col-12 col-md-6">
                  <div class="p-3 border rounded-3 bg-light">
                    <div class="text-muted small">Total Terlambat</div>
                    <div class="fs-4 fw-bold"><?= (int)($overdue['c'] ?? 0) ?></div>
                  </div>
                </div>
                <div class="col-12">
                  <div class="p-3 border rounded-3">
                    <div class="d-flex justify-content-between align-items-center">
                      <div>
                        <div class="text-muted small">Denda Hari Ini</div>
                        <div class="fs-4 fw-bold"><?= number_format((int)($finesToday['s'] ?? 0)) ?></div>
                      </div>
                      <a href="index.php?r=reports/fines" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-receipt me-1"></i>Lihat Denda
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- REQUESTS TAB (New) -->
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
            <!-- Locked State -->
            <div class="text-center py-5">
              <div class="mb-3">
                <i class="bi bi-lock-fill display-1 text-muted"></i>
              </div>
              <h5 class="text-muted">Area Terproteksi</h5>
              <p class="text-muted mb-4">Masukkan password admin untuk mengakses verifikasi request peminjaman.</p>
              <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#verifyPasswordModal">
                <i class="bi bi-key me-1"></i> Verifikasi Password
              </button>
            </div>
          <?php else: ?>
            <!-- Verified State - Load requests via AJAX or include partial -->
            <div id="requestsContent">
              <?php require __DIR__ . '/requests_tab.php'; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

  </div>
</div><!-- /.content-inner -->

<!-- Password Verification Modal -->
<div class="modal fade" id="verifyPasswordModal" tabindex="-1" aria-labelledby="verifyPasswordModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="verifyPasswordModalLabel">
          <i class="bi bi-shield-lock me-2"></i>Verifikasi Akses Admin
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <form id="verifyPasswordForm">
        <div class="modal-body">
          <p class="text-muted mb-3">Masukkan password admin untuk mengakses menu <strong>Request Verifikasi</strong>.</p>
          <div class="mb-3">
            <label for="verifyPassword" class="form-label">Password</label>
            <div class="input-group">
              <input type="password" class="form-control" id="verifyPassword" name="password" placeholder="Masukkan password" required autocomplete="current-password">
              <button class="btn btn-outline-secondary" type="button" id="toggleVerifyPassword">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>
          <div id="verifyError" class="alert alert-danger d-none" role="alert"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary" id="verifySubmitBtn">
            <i class="bi bi-check-circle me-1"></i> Verifikasi
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  // ===== Chart Data =====
  const monthsLabels = <?= json_encode($labelsMonths, JSON_UNESCAPED_UNICODE) ?>;
  const borrowedData = <?= json_encode($dataBorrowed, JSON_UNESCAPED_UNICODE) ?>;
  const returnedData = <?= json_encode($dataReturned, JSON_UNESCAPED_UNICODE) ?>;
  const topBookLabels = <?= json_encode($labelsTopBooks, JSON_UNESCAPED_UNICODE) ?>;
  const topBookData   = <?= json_encode($dataTopBooks, JSON_UNESCAPED_UNICODE) ?>;
  const memberLabels  = <?= json_encode($labelsMember, JSON_UNESCAPED_UNICODE) ?>;
  const memberData    = <?= json_encode($dataMember, JSON_UNESCAPED_UNICODE) ?>;

  // ===== Chart: Pinjam vs Kembali =====
  if (document.getElementById('chartLoans') && monthsLabels.length) {
    new Chart(document.getElementById('chartLoans'), {
      type: 'line',
      data: {
        labels: monthsLabels,
        datasets: [
          { label: 'Dipinjam', data: borrowedData, tension: 0.35 },
          { label: 'Dikembalikan', data: returnedData, tension: 0.35 }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: { legend: { display: true }, tooltip: { enabled: true } },
        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
      }
    });
  }

  // ===== Chart: Komposisi Member =====
  if (document.getElementById('chartMembers') && memberLabels.length) {
    new Chart(document.getElementById('chartMembers'), {
      type: 'doughnut',
      data: { labels: memberLabels, datasets: [{ label: 'Member', data: memberData }] },
      options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: true } } }
    });
  }

  // ===== Chart: Top Buku =====
  if (document.getElementById('chartTopBooks') && topBookLabels.length) {
    new Chart(document.getElementById('chartTopBooks'), {
      type: 'bar',
      data: { labels: topBookLabels, datasets: [{ label: 'Jumlah', data: topBookData }] },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: true } },
        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
      }
    });
  }

  // ===== Password Modal Logic =====
  const verifyForm = document.getElementById('verifyPasswordForm');
  const verifyError = document.getElementById('verifyError');
  const verifySubmit = document.getElementById('verifySubmitBtn');
  const verifyModal = document.getElementById('verifyPasswordModal');
  const togglePassBtn = document.getElementById('toggleVerifyPassword');
  const passInput = document.getElementById('verifyPassword');

  // Toggle password visibility
  if (togglePassBtn && passInput) {
    togglePassBtn.addEventListener('click', () => {
      const isPass = passInput.type === 'password';
      passInput.type = isPass ? 'text' : 'password';
      togglePassBtn.innerHTML = isPass ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
    });
  }

  // Form submit
  if (verifyForm) {
    verifyForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      
      const password = document.getElementById('verifyPassword').value.trim();
      if (!password) return;

      verifySubmit.disabled = true;
      verifySubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></i> Memverifikasi...';
      verifyError.classList.add('d-none');

      try {
        const res = await fetch('index.php?r=dashboard/verifyRequestPassword', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams({ password })
        });
        const data = await res.json();

        if (data.ok) {
          // Success - reload page to show requests tab
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
        verifySubmit.innerHTML = '<i class="bi bi-check-circle me-1"></i> Verifikasi';
      }
    });
  }

  // Auto-show modal if explicitly requested via URL
  <?php if ($showVerifyModal): ?>
    if (verifyModal) {
      const bsModal = new bootstrap.Modal(verifyModal);
      bsModal.show();
    }
  <?php endif; ?>

  // ===== Manual Tab Switching (both tabs) =====
  const requestsTab = document.getElementById('requests-tab');
  const overviewTab = document.getElementById('overview-tab');
  const requestsPane = document.getElementById('requests');
  const overviewPane = document.getElementById('overview');

  function switchToRequests() {
    if (!requestsTab || !overviewTab || !requestsPane || !overviewPane) return;
    overviewTab.classList.remove('active');
    overviewTab.setAttribute('aria-selected', 'false');
    overviewPane.classList.remove('show', 'active');
    requestsTab.classList.add('active');
    requestsTab.setAttribute('aria-selected', 'true');
    requestsPane.classList.add('show', 'active');
    console.log('Switched to Requests tab');
  }

  function switchToOverview() {
    if (!requestsTab || !overviewTab || !requestsPane || !overviewPane) return;
    requestsTab.classList.remove('active');
    requestsTab.setAttribute('aria-selected', 'false');
    requestsPane.classList.remove('show', 'active');
    overviewTab.classList.add('active');
    overviewTab.setAttribute('aria-selected', 'true');
    overviewPane.classList.add('show', 'active');
    console.log('Switched to Overview tab');
  }

  if (requestsTab) {
    requestsTab.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      const verified = this.getAttribute('data-verified') === '1';
      console.log('Requests tab clicked, verified:', verified);
      if (!verified) {
        if (verifyModal) {
          const modal = new bootstrap.Modal(verifyModal);
          modal.show();
        }
      } else {
        switchToRequests();
      }
    });
  }

  if (overviewTab) {
    overviewTab.addEventListener('click', function(e) {
      e.preventDefault();
      switchToOverview();
    });
  }

  // Auto-switch to requests tab if verified and hash in URL
  if (window.location.hash === '#requests' && requestsTab && requestsTab.getAttribute('data-verified') === '1') {
    switchToRequests();
  }

  // ===== AJAX Filter untuk Request Tab =====
  const requestsContent = document.getElementById('requestsContent');

  function loadRequests(status) {
    if (!requestsContent) return;

    // Tampilkan loading spinner
    requestsContent.innerHTML = `
      <div class="text-center py-5 text-muted">
        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
        Memuat data...
      </div>`;

    const url = 'index.php?r=dashboard/requests' + (status ? '&status=' + encodeURIComponent(status) : '');
    fetch(url, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => {
      if (!res.ok) throw new Error('Gagal memuat data (HTTP ' + res.status + ')');
      return res.text();
    })
    .then(html => {
      requestsContent.innerHTML = html;
      // Re-attach filter listeners setelah konten diganti
      attachFilterListeners();
    })
    .catch(err => {
      requestsContent.innerHTML = `
        <div class="alert alert-danger">
          <i class="bi bi-exclamation-triangle me-2"></i>
          Gagal memuat data: ${err.message}
          <button class="btn btn-sm btn-outline-danger ms-3" onclick="loadRequests('')">Coba Lagi</button>
        </div>`;
    });
  }

  function attachFilterListeners() {
    if (!requestsContent) return;
    requestsContent.querySelectorAll('.filter-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const status = this.getAttribute('data-status');
        loadRequests(status);
      });
    });
  }

  // Pasang listener pertama kali (saat konten sudah ada dari server-side render)
  attachFilterListeners();

  console.log('Tab elements:', {requestsTab, overviewTab, requestsPane, overviewPane});
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>