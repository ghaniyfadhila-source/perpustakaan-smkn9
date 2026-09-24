<?php
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/sidebar.php';

$student = $_SESSION['student'] ?? null;
$name = $student['student_name'] ?? 'Siswa';
$activeCount = $activeLoans['c'] ?? 0;
$overdueCount = $overdue['c'] ?? 0;
$fineTotal = $fines['s'] ?? 0;
$hour = (int) date('H');
$greeting = $hour < 12 ? 'Selamat Pagi' : ($hour < 17 ? 'Selamat Siang' : 'Selamat Malam');
?>

<style>
  .stat-glass {
    background: rgba(255,255,255,0.8);
    backdrop-filter: blur(16px) saturate(160%);
    -webkit-backdrop-filter: blur(16px) saturate(160%);
    border: 1px solid rgba(0,0,0,0.06);
    border-radius: 18px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.07);
    padding: 20px 22px;
    transition: all 0.2s ease;
  }

  .stat-glass:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
  }

  .stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    flex-shrink: 0;
  }

  .stat-num {
    font-size: 2rem;
    font-weight: 800;
    line-height: 1;
    letter-spacing: -0.03em;
  }

  .stat-label {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--text-secondary);
    margin-bottom: 4px;
  }

  .stat-sub {
    font-size: 0.76rem;
    color: var(--text-muted);
    margin-top: 3px;
  }

  .welcome-banner {
    background: linear-gradient(135deg, rgba(99,102,241,0.08), rgba(14,165,233,0.06));
    border: 1px solid rgba(99,102,241,0.12);
    border-radius: 20px;
    padding: 22px 26px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
  }

  .welcome-text h4 {
    font-weight: 800;
    font-size: 1.2rem;
    letter-spacing: -0.02em;
    margin-bottom: 4px;
    color: var(--text-primary);
  }

  .welcome-text p {
    font-size: 0.85rem;
    color: var(--text-secondary);
    margin: 0;
  }

  .welcome-emoji {
    font-size: 3rem;
    line-height: 1;
    flex-shrink: 0;
  }

  .section-title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--text-primary);
    letter-spacing: -0.01em;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .section-title i {
    color: var(--accent);
    font-size: 1.1rem;
  }

  .quick-action {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    background: rgba(255,255,255,0.75);
    border: 1px solid rgba(0,0,0,0.07);
    border-radius: 14px;
    text-decoration: none;
    color: var(--text-primary);
    font-weight: 500;
    font-size: 0.88rem;
    transition: all 0.2s ease;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
  }

  .quick-action:hover {
    background: rgba(99,102,241,0.08);
    border-color: rgba(99,102,241,0.2);
    color: var(--accent);
    transform: translateX(3px);
  }

  .quick-action .qa-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
  }

  .card-table {
    background: rgba(255,255,255,0.8);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(0,0,0,0.06);
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(0,0,0,0.07);
  }

  .card-table-header {
    padding: 16px 20px;
    border-bottom: 1px solid rgba(0,0,0,0.06);
    background: rgba(255,255,255,0.5);
  }

  .card-table-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
  }

  .empty-state {
    text-align: center;
    padding: 36px 20px;
    color: var(--text-muted);
  }

  .empty-state i {
    font-size: 2.5rem;
    opacity: 0.4;
    display: block;
    margin-bottom: 10px;
  }

  .empty-state p {
    font-size: 0.88rem;
    margin: 0;
  }

  .overdue-row td {
    background: rgba(239,68,68,0.03) !important;
  }
</style>

<!-- Welcome Banner -->
<div class="welcome-banner">
  <div class="welcome-text">
    <h4><?= $greeting ?>, <?= htmlspecialchars(explode(' ', $name)[0]) ?>! 👋</h4>
    <p>Semangat belajar hari ini. Berikut ringkasan aktivitas perpustakaanmu.</p>
  </div>
  <div class="welcome-emoji d-none d-sm-block">📚</div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
  <div class="col-sm-4">
    <div class="stat-glass">
      <div class="d-flex align-items-center gap-3 mb-2">
        <div class="stat-icon" style="background: rgba(99,102,241,0.1); color: #6366f1;">
          <i class="bi bi-book-half"></i>
        </div>
        <div>
          <div class="stat-label">Dipinjam</div>
          <div class="stat-num" style="color: #6366f1;"><?= $activeCount ?></div>
        </div>
      </div>
      <div class="stat-sub">Buku aktif dipinjam saat ini</div>
    </div>
  </div>

  <div class="col-sm-4">
    <div class="stat-glass">
      <div class="d-flex align-items-center gap-3 mb-2">
        <div class="stat-icon" style="background: rgba(239,68,68,0.1); color: #ef4444;">
          <i class="bi bi-exclamation-triangle"></i>
        </div>
        <div>
          <div class="stat-label">Terlambat</div>
          <div class="stat-num" style="color: #ef4444;"><?= $overdueCount ?></div>
        </div>
      </div>
      <div class="stat-sub">Buku melewati jatuh tempo</div>
    </div>
  </div>

  <div class="col-sm-4">
    <div class="stat-glass">
      <div class="d-flex align-items-center gap-3 mb-2">
        <div class="stat-icon" style="background: rgba(245,158,11,0.1); color: #f59e0b;">
          <i class="bi bi-cash-coin"></i>
        </div>
        <div>
          <div class="stat-label">Total Denda</div>
          <div class="stat-num" style="color: #f59e0b; font-size: 1.3rem;">Rp <?= number_format($fineTotal) ?></div>
        </div>
      </div>
      <div class="stat-sub">Denda belum diselesaikan</div>
    </div>
  </div>
</div>

<div class="row g-3">
  <!-- Main Table: Active Loans -->
  <div class="col-12 col-lg-8">
    <div class="card-table">
      <div class="card-table-header d-flex align-items-center justify-content-between">
        <h6 class="card-table-title">
          <i class="bi bi-journal-check me-2" style="color: var(--accent);"></i>
          Buku Sedang Dipinjam
        </h6>
        <?php if (!empty($activeLoansDetail)): ?>
          <span class="badge" style="background: rgba(99,102,241,0.1); color: var(--accent); font-size: 0.75rem; font-weight: 600; padding: 5px 10px; border-radius: 999px;">
            <?= count($activeLoansDetail) ?> buku
          </span>
        <?php endif; ?>
      </div>

      <?php if (!empty($activeLoansDetail)): ?>
        <div class="table-responsive">
          <table class="table mb-0">
            <thead>
              <tr>
                <th>Judul Buku</th>
                <th>Kode</th>
                <th>Jatuh Tempo</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($activeLoansDetail as $loan): ?>
              <?php $isOvd = $loan['due_date'] < date('Y-m-d'); ?>
              <tr class="<?= $isOvd ? 'overdue-row' : '' ?>">
                <td>
                  <div style="font-weight: 500; color: var(--text-primary); font-size: 0.87rem;">
                    <?= htmlspecialchars($loan['title'] ?? '') ?>
                  </div>
                </td>
                <td>
                  <span style="font-family: monospace; font-size: 0.82rem; color: var(--text-secondary);">
                    <?= htmlspecialchars($loan['item_code'] ?? '') ?>
                  </span>
                </td>
                <td>
                  <span style="color: <?= $isOvd ? '#ef4444' : 'var(--text-secondary)' ?>; font-weight: <?= $isOvd ? '600' : '400' ?>; font-size: 0.85rem;">
                    <?php
                      $due = new DateTime($loan['due_date']);
                      echo $due->format('d M Y');
                    ?>
                  </span>
                  <?php if ($isOvd): ?>
                    <br><span style="font-size: 0.73rem; color: #ef4444;">
                      <?= (new DateTime())->diff($due)->days ?> hari terlambat
                    </span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if (in_array($loan['loan_id'], $pendingReturnLoanIds ?? [])): ?>
                    <span class="badge" style="background: rgba(14,165,233,0.1); color: #0ea5e9; font-size: 0.75rem; padding: 4px 9px; border-radius: 6px;">
                      <i class="bi bi-hourglass-split me-1"></i>Menunggu
                    </span>
                  <?php elseif ($isOvd): ?>
                    <span class="badge" style="background: rgba(239,68,68,0.1); color: #ef4444; font-size: 0.75rem; padding: 4px 9px; border-radius: 6px;">
                      <i class="bi bi-exclamation-circle me-1"></i>Terlambat
                    </span>
                  <?php else: ?>
                    <span class="badge" style="background: rgba(16,185,129,0.1); color: #10b981; font-size: 0.75rem; padding: 4px 9px; border-radius: 6px;">
                      <i class="bi bi-check-circle me-1"></i>Aktif
                    </span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if (in_array($loan['loan_id'], $pendingReturnLoanIds ?? [])): ?>
                    <button class="btn btn-sm" style="padding: 5px 12px; font-size: 0.78rem; background: rgba(100,116,139,0.08); border: 1px solid rgba(100,116,139,0.2); color: var(--text-muted); border-radius: 8px;" disabled>
                      <i class="bi bi-hourglass-split me-1"></i>Menunggu
                    </button>
                  <?php else: ?>
                    <form method="POST" action="index.php?r=student/requestReturn" class="d-inline"
                          onsubmit="return confirm('Ajukan pengembalian buku ini? Silakan kembalikan fisik buku ke perpustakaan.')">
                      <input type="hidden" name="loan_id" value="<?= $loan['loan_id'] ?>">
                      <button type="submit" class="btn btn-sm" style="padding: 5px 12px; font-size: 0.78rem; background: rgba(245,158,11,0.08); border: 1px solid rgba(245,158,11,0.25); color: #d97706; border-radius: 8px; transition: all 0.2s ease;">
                        <i class="bi bi-arrow-return-left me-1"></i>Kembalikan
                      </button>
                    </form>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div class="empty-state">
          <i class="bi bi-journal-x"></i>
          <p>Tidak ada buku yang sedang dipinjam.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Quick Actions -->
  <div class="col-12 col-lg-4">
    <div class="section-title">
      <i class="bi bi-lightning-charge-fill"></i>
      Aksi Cepat
    </div>

    <div class="d-flex flex-column gap-2">
      <a href="index.php?r=student/requests/index" class="quick-action">
        <div class="qa-icon" style="background: rgba(99,102,241,0.1); color: #6366f1;">
          <i class="bi bi-journal-plus"></i>
        </div>
        <div>
          <div style="font-weight: 600; font-size: 0.87rem;">Request Peminjaman</div>
          <div style="font-size: 0.77rem; color: var(--text-muted);">Ajukan request buku baru</div>
        </div>
        <i class="bi bi-chevron-right ms-auto" style="color: var(--text-muted); font-size: 0.8rem;"></i>
      </a>

      <a href="index.php?r=student/books/index" class="quick-action">
        <div class="qa-icon" style="background: rgba(14,165,233,0.1); color: #0ea5e9;">
          <i class="bi bi-search"></i>
        </div>
        <div>
          <div style="font-weight: 600; font-size: 0.87rem;">Cari Buku</div>
          <div style="font-size: 0.77rem; color: var(--text-muted);">Jelajahi katalog perpustakaan</div>
        </div>
        <i class="bi bi-chevron-right ms-auto" style="color: var(--text-muted); font-size: 0.8rem;"></i>
      </a>

      <a href="index.php?r=student/profile" class="quick-action">
        <div class="qa-icon" style="background: rgba(16,185,129,0.1); color: #10b981;">
          <i class="bi bi-person-circle"></i>
        </div>
        <div>
          <div style="font-weight: 600; font-size: 0.87rem;">Profil Saya</div>
          <div style="font-size: 0.77rem; color: var(--text-muted);">Lihat & ubah profil akun</div>
        </div>
        <i class="bi bi-chevron-right ms-auto" style="color: var(--text-muted); font-size: 0.8rem;"></i>
      </a>
    </div>

    <?php if ($overdueCount > 0): ?>
    <div style="margin-top: 16px; padding: 14px 16px; background: rgba(239,68,68,0.06); border: 1px solid rgba(239,68,68,0.2); border-radius: 14px;">
      <div style="font-size: 0.82rem; font-weight: 700; color: #ef4444; margin-bottom: 5px;">
        <i class="bi bi-exclamation-triangle-fill me-1"></i> Perhatian
      </div>
      <p style="font-size: 0.8rem; color: #64748b; margin: 0; line-height: 1.5;">
        Kamu memiliki <strong><?= $overdueCount ?> buku terlambat</strong>. Segera kembalikan untuk menghindari denda lebih besar.
      </p>
    </div>
    <?php endif; ?>

    <?php if ($fineTotal > 0): ?>
    <div style="margin-top: 10px; padding: 14px 16px; background: rgba(245,158,11,0.06); border: 1px solid rgba(245,158,11,0.2); border-radius: 14px;">
      <div style="font-size: 0.82rem; font-weight: 700; color: #d97706; margin-bottom: 5px;">
        <i class="bi bi-cash-coin me-1"></i> Info Denda
      </div>
      <p style="font-size: 0.8rem; color: #64748b; margin: 0; line-height: 1.5;">
        Total denda yang harus dibayar: <strong>Rp <?= number_format($fineTotal) ?></strong>. Hubungi petugas perpustakaan.
      </p>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>