<?php
require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/sidebar.php';

$status = $request['status'];

$statusInfo = match($status) {
  'PENDING'   => ['icon' => 'bi-hourglass-split',   'label' => 'Menunggu',    'bg' => 'rgba(245,158,11,0.12)', 'color' => '#d97706', 'border' => 'rgba(245,158,11,0.25)'],
  'APPROVED'  => ['icon' => 'bi-check-circle-fill', 'label' => 'Disetujui',   'bg' => 'rgba(16,185,129,0.12)', 'color' => '#059669', 'border' => 'rgba(16,185,129,0.25)'],
  'REJECTED'  => ['icon' => 'bi-x-circle-fill',     'label' => 'Ditolak',     'bg' => 'rgba(239,68,68,0.12)',  'color' => '#dc2626', 'border' => 'rgba(239,68,68,0.25)'],
  'CANCELLED' => ['icon' => 'bi-slash-circle-fill', 'label' => 'Dibatalkan',  'bg' => 'rgba(100,116,139,0.12)','color' => '#475569', 'border' => 'rgba(100,116,139,0.25)'],
  default     => ['icon' => 'bi-question-circle',   'label' => $status,      'bg' => 'rgba(100,116,139,0.12)','color' => '#475569', 'border' => 'rgba(100,116,139,0.25)'],
};

$memberData = DB::selectOne("SELECT expire_date, member_phone FROM member WHERE member_id=? LIMIT 1", "s", [$request['member_id']]);
$expireDate = $memberData['expire_date'] ?? null;
$isExpired  = $expireDate && strtotime($expireDate) < strtotime(date('Y-m-d'));
$expireSoon = $expireDate && !$isExpired && strtotime($expireDate) < strtotime('+30 days');
?>

<style>
  :root {
    --glass-bg: rgba(255, 255, 255, 0.65);
    --glass-bg-subtle: rgba(255, 255, 255, 0.4);
    --glass-border: rgba(255, 255, 255, 0.8);
    --glass-border-dark: rgba(0, 0, 0, 0.06);
    --glass-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.05), 0 0 1px 1px rgba(255, 255, 255, 0.9) inset;
    --text-primary: #1d1d1f;
    --text-secondary: #86868b;
    --apple-blue: #0071e3;
    --apple-blue-hover: #0077ed;
  }

  .glass-wrapper {
    position: relative;
    padding-bottom: 2rem;
  }

  /* Liquid background ambient blur */
  .glass-ambient-1 {
    position: absolute;
    width: 450px;
    height: 450px;
    top: -60px;
    right: -40px;
    background: radial-gradient(circle, rgba(147, 197, 253, 0.45) 0%, rgba(196, 181, 253, 0.25) 50%, transparent 70%);
    filter: blur(60px);
    z-index: 0;
    pointer-events: none;
  }
  .glass-ambient-2 {
    position: absolute;
    width: 400px;
    height: 400px;
    bottom: 0;
    left: -60px;
    background: radial-gradient(circle, rgba(167, 243, 208, 0.35) 0%, rgba(186, 230, 253, 0.2) 60%, transparent 80%);
    filter: blur(60px);
    z-index: 0;
    pointer-events: none;
  }

  .glass-content {
    position: relative;
    z-index: 1;
  }

  .glass-card {
    background: var(--glass-bg);
    backdrop-filter: blur(24px) saturate(180%);
    -webkit-backdrop-filter: blur(24px) saturate(180%);
    border: 1px solid var(--glass-border);
    border-radius: 22px;
    box-shadow: var(--glass-shadow);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
  }

  .glass-header {
    background: rgba(255, 255, 255, 0.5);
    backdrop-filter: blur(30px) saturate(190%);
    -webkit-backdrop-filter: blur(30px) saturate(190%);
    border: 1px solid rgba(255, 255, 255, 0.85);
    border-radius: 24px;
    padding: 22px 28px;
    box-shadow: 0 10px 30px -10px rgba(0,0,0,0.04);
  }

  .btn-back-apple {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 980px;
    background: rgba(0, 0, 0, 0.04);
    color: var(--text-primary);
    font-size: 0.85rem;
    font-weight: 500;
    border: 1px solid rgba(0, 0, 0, 0.04);
    text-decoration: none;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  }
  .btn-back-apple:hover {
    background: rgba(0, 0, 0, 0.08);
    color: #000;
    transform: scale(0.98);
  }

  .apple-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 980px;
    font-size: 0.8rem;
    font-weight: 600;
    letter-spacing: -0.01em;
  }

  .apple-label {
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--text-secondary);
    font-weight: 600;
    margin-bottom: 3px;
  }
  .apple-value {
    font-size: 0.95rem;
    color: var(--text-primary);
    font-weight: 500;
    letter-spacing: -0.015em;
  }

  .glass-input, .glass-select {
    background: rgba(255, 255, 255, 0.75) !important;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(0, 0, 0, 0.1) !important;
    border-radius: 14px !important;
    padding: 10px 14px !important;
    font-size: 0.9rem !important;
    color: var(--text-primary) !important;
    transition: all 0.2s ease !important;
  }
  .glass-input:focus, .glass-select:focus {
    background: rgba(255, 255, 255, 0.95) !important;
    border-color: var(--apple-blue) !important;
    box-shadow: 0 0 0 4px rgba(0, 113, 227, 0.15) !important;
    outline: none;
  }

  .apple-pill-btn {
    border: 1px solid rgba(0, 0, 0, 0.08);
    background: rgba(255, 255, 255, 0.6);
    backdrop-filter: blur(10px);
    color: var(--text-primary);
    font-size: 0.8rem;
    font-weight: 500;
    padding: 6px 14px;
    border-radius: 980px;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  }
  .apple-pill-btn:hover {
    background: rgba(0, 0, 0, 0.05);
    color: #000;
  }
  .apple-pill-btn.active {
    background: #1d1d1f;
    color: #fff;
    border-color: #1d1d1f;
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
  }

  .btn-apple-primary {
    background: var(--apple-blue);
    color: #fff;
    font-weight: 500;
    font-size: 0.92rem;
    padding: 12px 20px;
    border-radius: 980px;
    border: none;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: 0 4px 14px rgba(0, 113, 227, 0.25);
  }
  .btn-apple-primary:hover {
    background: var(--apple-blue-hover);
    color: #fff;
    transform: scale(0.99);
    box-shadow: 0 6px 20px rgba(0, 113, 227, 0.35);
  }

  .btn-apple-danger {
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
    font-weight: 500;
    font-size: 0.92rem;
    padding: 12px 20px;
    border-radius: 980px;
    border: 1px solid rgba(239, 68, 68, 0.2);
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  }
  .btn-apple-danger:hover {
    background: #dc2626;
    color: #fff;
    transform: scale(0.99);
    box-shadow: 0 6px 20px rgba(220, 38, 38, 0.25);
  }

  .apple-timeline {
    position: relative;
    padding-left: 20px;
  }
  .apple-timeline::before {
    content: '';
    position: absolute;
    left: 5px;
    top: 6px;
    bottom: 6px;
    width: 1.5px;
    background: rgba(0, 0, 0, 0.08);
  }
  .apple-timeline-dot {
    position: absolute;
    left: 0;
    top: 4px;
    width: 11px;
    height: 11px;
    border-radius: 50%;
    background: #fff;
    border: 2.5px solid var(--apple-blue);
    box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.8);
  }

  .copy-item {
    background: rgba(255, 255, 255, 0.5);
    border: 1px solid rgba(0, 0, 0, 0.04);
    border-radius: 14px;
    padding: 10px 14px;
    margin-bottom: 8px;
    transition: background 0.2s;
  }
  .copy-item:hover {
    background: rgba(255, 255, 255, 0.85);
  }
</style>

<div class="glass-wrapper">
  <div class="glass-ambient-1"></div>
  <div class="glass-ambient-2"></div>

  <div class="glass-content">
    <!-- Header Bar -->
    <div class="glass-header d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
      <div class="d-flex align-items-center gap-3">
        <a href="index.php?r=dashboard/requests" class="btn-back-apple">
          <i class="bi bi-chevron-left"></i> Kembali
        </a>
        <div>
          <div class="d-flex align-items-center gap-2">
            <h4 class="mb-0 fw-semibold" style="letter-spacing: -0.02em; color: var(--text-primary);">Request Peminjaman</h4>
            <span class="apple-badge" style="background: <?= $statusInfo['bg'] ?>; color: <?= $statusInfo['color'] ?>; border: 1px solid <?= $statusInfo['border'] ?>;">
              <i class="bi <?= $statusInfo['icon'] ?>"></i> <?= $statusInfo['label'] ?>
            </span>
          </div>
          <div class="small text-muted mt-1">ID #<?= $request['request_id'] ?> &middot; <?= date('d M Y, H:i', strtotime($request['request_date'])) ?> WIB</div>
        </div>
      </div>
    </div>

    <!-- Flash Message -->
    <?php if (!empty($_SESSION['flash'])): ?>
      <div class="glass-card p-3 mb-4 d-flex align-items-center justify-content-between" style="border-left: 4px solid <?= $_SESSION['flash']['type'] === 'success' ? '#10b981' : '#ef4444' ?>;">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-<?= $_SESSION['flash']['type'] === 'success' ? 'check-circle-fill text-success' : 'exclamation-circle-fill text-danger' ?> fs-5"></i>
          <span class="fw-medium" style="color: var(--text-primary);"><?= $_SESSION['flash']['msg'] ?></span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" style="font-size: 0.8rem;"></button>
      </div>
      <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <!-- Alert Expiry -->
    <?php if ($isExpired): ?>
      <div class="glass-card p-3 mb-4" style="background: rgba(254, 242, 242, 0.7); border-color: rgba(239, 68, 68, 0.3);">
        <div class="d-flex align-items-center gap-2 text-danger fw-semibold mb-1">
          <i class="bi bi-exclamation-octagon-fill"></i> Masa Aktif Anggota Habis
        </div>
        <div class="small text-muted">Keanggotaan siswa berakhir pada <strong><?= date('d M Y', strtotime($expireDate)) ?></strong>. Perpanjang akun sebelum menyetujui request.</div>
      </div>
    <?php endif; ?>

    <div class="row g-4">
      <!-- Kolom Kiri: Detail Informasi -->
      <div class="col-lg-7">

        <!-- Card Siswa -->
        <div class="glass-card p-4 mb-4">
          <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom" style="border-color: rgba(0,0,0,0.05) !important;">
            <i class="bi bi-person text-secondary fs-5"></i>
            <span class="fw-semibold" style="color: var(--text-primary); letter-spacing: -0.01em;">Data Siswa</span>
          </div>
          <div class="row g-3">
            <div class="col-sm-6">
              <div class="apple-label">Nama Lengkap</div>
              <div class="apple-value"><?= htmlspecialchars($request['member_name'] ?? '-') ?></div>
            </div>
            <div class="col-sm-6">
              <div class="apple-label">Username</div>
              <div class="apple-value text-muted">@<?= htmlspecialchars($request['username'] ?? '-') ?></div>
            </div>
            <div class="col-sm-6">
              <div class="apple-label">Nomor Induk / ID</div>
              <div class="apple-value"><code><?= htmlspecialchars($request['member_id'] ?? '-') ?></code></div>
            </div>
            <div class="col-sm-6">
              <div class="apple-label">Kontak WhatsApp</div>
              <div class="apple-value"><?= htmlspecialchars($memberData['member_phone'] ?? '-') ?></div>
            </div>
          </div>
        </div>

        <!-- Card Buku -->
        <div class="glass-card p-4 mb-4">
          <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom" style="border-color: rgba(0,0,0,0.05) !important;">
            <i class="bi bi-book text-secondary fs-5"></i>
            <span class="fw-semibold" style="color: var(--text-primary); letter-spacing: -0.01em;">Pustaka Buku</span>
          </div>
          <div class="mb-3">
            <div class="apple-label">Judul Buku</div>
            <div class="fs-5 fw-semibold" style="color: var(--text-primary); letter-spacing: -0.02em;"><?= htmlspecialchars($request['title'] ?? '-') ?></div>
          </div>
          <div class="row g-3 pt-2">
            <div class="col-sm-6">
              <div class="apple-label">ISBN / ISSN</div>
              <div class="apple-value"><?= htmlspecialchars($request['isbn_issn'] ?? '-') ?></div>
            </div>
            <div class="col-sm-6">
              <div class="apple-label">Tahun Publikasi</div>
              <div class="apple-value"><?= htmlspecialchars($request['publish_year'] ?? '-') ?></div>
            </div>
            <div class="col-sm-6">
              <div class="apple-label">Klasifikasi DDC</div>
              <div class="apple-value"><?= htmlspecialchars($request['classification'] ?? '-') ?></div>
            </div>
            <div class="col-sm-6">
              <div class="apple-label">Nomor Panggil</div>
              <div class="apple-value"><?= htmlspecialchars($request['call_number'] ?? '-') ?></div>
            </div>
          </div>

          <?php if ($status === 'REJECTED' && !empty($request['rejection_reason'])): ?>
            <div class="mt-3 p-3 rounded-4" style="background: rgba(239, 68, 68, 0.06); border: 1px solid rgba(239, 68, 68, 0.15);">
              <div class="apple-label text-danger mb-1">Alasan Penolakan</div>
              <div class="small text-danger" style="line-height: 1.5;"><?= nl2br(htmlspecialchars($request['rejection_reason'])) ?></div>
            </div>
          <?php endif; ?>
        </div>

        <!-- Riwayat / Timeline -->
        <div class="glass-card p-4">
          <div class="d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-clock-history text-secondary"></i>
            <span class="fw-semibold" style="color: var(--text-primary); letter-spacing: -0.01em;">Aktivitas</span>
          </div>
          <div class="apple-timeline">
            <div class="position-relative mb-3">
              <div class="apple-timeline-dot"></div>
              <div class="fw-medium small" style="color: var(--text-primary);">Pengajuan dibuat</div>
              <div class="text-muted" style="font-size: 0.75rem;"><?= date('d M Y, H:i', strtotime($request['request_date'])) ?> WIB</div>
            </div>
            <?php if ($status === 'APPROVED' && !empty($request['approved_at'])): ?>
              <div class="position-relative">
                <div class="apple-timeline-dot" style="border-color: #10b981;"></div>
                <div class="fw-medium small text-success">Disetujui Petugas</div>
                <div class="text-muted" style="font-size: 0.75rem;"><?= date('d M Y, H:i', strtotime($request['approved_at'])) ?> WIB</div>
              </div>
            <?php elseif ($status === 'REJECTED' && !empty($request['rejected_at'])): ?>
              <div class="position-relative">
                <div class="apple-timeline-dot" style="border-color: #ef4444;"></div>
                <div class="fw-medium small text-danger">Pengajuan Ditolak</div>
                <div class="text-muted" style="font-size: 0.75rem;"><?= date('d M Y, H:i', strtotime($request['rejected_at'])) ?> WIB</div>
              </div>
            <?php endif; ?>
          </div>
        </div>

      </div>

      <!-- Kolom Kanan: Actions -->
      <div class="col-lg-5">
        <div class="glass-card p-4 sticky-top" style="top: 24px;">
          <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom" style="border-color: rgba(0,0,0,0.05) !important;">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-sliders text-secondary"></i>
              <span class="fw-semibold" style="color: var(--text-primary);">Aksi Petugas</span>
            </div>
            <span class="small text-muted"><?= count($copies) ?> eksemplar</span>
          </div>

          <?php if ($status === 'PENDING'): ?>
            <?php if ($isExpired): ?>
              <p class="text-muted small">Peminjaman terkunci karena masa aktif siswa berakhir.</p>
            <?php else: ?>
              
              <!-- Eksemplar Selector -->
              <div class="mb-3">
                <label class="apple-label mb-2">Pilih Eksemplar Fisik <span class="text-danger">*</span></label>
                <select id="itemCode" class="glass-select form-select w-100" required>
                  <option value="">-- Pilih Eksemplar Tersedia --</option>
                  <?php foreach ($copies as $copy): ?>
                    <option value="<?= htmlspecialchars($copy['item_code']) ?>">
                      <?= htmlspecialchars($copy['item_code']) ?> &middot; <?= htmlspecialchars($copy['location_name'] ?? 'Rak Utama') ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- Durasi & Jatuh Tempo -->
              <div class="mb-4">
                <label class="apple-label mb-2">Durasi Peminjaman</label>
                <div class="d-flex gap-2 flex-wrap mb-2">
                  <button type="button" class="apple-pill-btn active" data-days="3" onclick="setDueDate(3, this)">3 Hari</button>
                  <button type="button" class="apple-pill-btn" data-days="5" onclick="setDueDate(5, this)">5 Hari</button>
                  <button type="button" class="apple-pill-btn" data-days="7" onclick="setDueDate(7, this)">7 Hari</button>
                  <button type="button" class="apple-pill-btn" data-days="14" onclick="setDueDate(14, this)">14 Hari</button>
                </div>
                <input type="date" id="dueDate" class="glass-input form-control w-100" value="<?= date('Y-m-d', strtotime('+3 days')) ?>" required>
                <div class="d-flex align-items-center gap-1 mt-2 text-muted" style="font-size: 0.75rem;">
                  <i class="bi bi-info-circle"></i> Denda Rp 20.000 / hari jika melewati tenggat
                </div>
              </div>

              <!-- Action Buttons -->
              <form method="POST" action="index.php?r=dashboard/approveRequest" id="approveForm">
                <input type="hidden" name="request_id" value="<?= $request['request_id'] ?>">
                <input type="hidden" name="item_code" id="itemCodeHidden">
                <input type="hidden" name="due_date" id="dueDateHidden">
                <button type="button" class="btn-apple-primary w-100 mb-3" onclick="submitApprove()">
                  <i class="bi bi-check2 me-1"></i> Setujui Peminjaman
                </button>
              </form>

              <div class="text-center my-2 text-muted" style="font-size: 0.75rem;">atau</div>

              <div class="mb-3">
                <textarea id="rejectReason" class="glass-input form-control w-100" rows="2" placeholder="Tulis alasan penolakan jika ditolak..."></textarea>
              </div>

              <form method="POST" action="index.php?r=dashboard/rejectRequest" id="rejectForm">
                <input type="hidden" name="request_id" value="<?= $request['request_id'] ?>">
                <input type="hidden" name="rejection_reason" id="rejectReasonHidden">
                <button type="button" class="btn-apple-danger w-100" onclick="submitReject()">
                  Tolak Permintaan
                </button>
              </form>

            <?php endif; ?>
          <?php else: ?>
            <div class="text-center py-4">
              <i class="bi <?= $statusInfo['icon'] ?> display-4 d-block mb-2" style="color: <?= $statusInfo['color'] ?>;"></i>
              <div class="fw-medium" style="color: var(--text-primary);">Status: <?= $statusInfo['label'] ?></div>
              <div class="small text-muted mt-1">Permintaan ini sudah selesai diproses.</div>
            </div>

            <?php if (!empty($copies)): ?>
              <div class="mt-3">
                <div class="apple-label mb-2">Daftar Eksemplar</div>
                <?php foreach ($copies as $copy): ?>
                  <div class="copy-item d-flex justify-content-between align-items-center">
                    <span class="fw-medium small"><?= htmlspecialchars($copy['item_code']) ?></span>
                    <span class="text-muted" style="font-size: 0.75rem;"><?= htmlspecialchars($copy['location_name'] ?? 'Rak Utama') ?></span>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </div>
</div>

<script>
function setDueDate(days, el) {
  var d = new Date();
  d.setDate(d.getDate() + days);
  var yyyy = d.getFullYear();
  var mm = String(d.getMonth() + 1).padStart(2, '0');
  var dd = String(d.getDate()).padStart(2, '0');
  document.getElementById('dueDate').value = yyyy + '-' + mm + '-' + dd;
  document.querySelectorAll('.apple-pill-btn').forEach(function(b) { b.classList.remove('active'); });
  if (el) el.classList.add('active');
}

function submitApprove() {
  var itemCode = document.getElementById('itemCode').value;
  var dueDate = document.getElementById('dueDate').value;
  if (!itemCode) { alert('Pilih eksemplar buku terlebih dahulu'); return; }
  if (!dueDate) { alert('Tentukan tanggal jatuh tempo'); return; }
  document.getElementById('itemCodeHidden').value = itemCode;
  document.getElementById('dueDateHidden').value = dueDate;
  if (confirm('Konfirmasi persetujuan peminjaman buku?')) {
    document.getElementById('approveForm').submit();
  }
}

function submitReject() {
  var reason = document.getElementById('rejectReason').value.trim();
  if (!reason) { alert('Masukkan alasan penolakan terlebih dahulu'); return; }
  document.getElementById('rejectReasonHidden').value = reason;
  if (confirm('Konfirmasi tolak permintaan peminjaman?')) {
    document.getElementById('rejectForm').submit();
  }
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
