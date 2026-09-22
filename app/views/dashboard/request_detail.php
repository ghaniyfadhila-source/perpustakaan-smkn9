<?php
$status = $request['status'];

$statusInfo = match($status) {
  'PENDING'   => ['class' => 'bg-warning text-dark', 'icon' => 'bi-hourglass-split',    'label' => 'Menunggu',    'color' => '#ffc107'],
  'APPROVED'  => ['class' => 'bg-success',           'icon' => 'bi-check-circle-fill',  'label' => 'Disetujui',   'color' => '#198754'],
  'REJECTED'  => ['class' => 'bg-danger',            'icon' => 'bi-x-circle-fill',      'label' => 'Ditolak',     'color' => '#dc3545'],
  'CANCELLED' => ['class' => 'bg-secondary',         'icon' => 'bi-slash-circle-fill',  'label' => 'Dibatalkan',  'color' => '#6c757d'],
  default     => ['class' => 'bg-light text-dark',   'icon' => 'bi-question-circle',    'label' => $status,       'color' => '#6c757d'],
};

$memberData = DB::selectOne("SELECT expire_date, member_phone FROM member WHERE member_id=? LIMIT 1", "s", [$request['member_id']]);
$expireDate = $memberData['expire_date'] ?? null;
$isExpired  = $expireDate && strtotime($expireDate) < strtotime(date('Y-m-d'));
$expireSoon = $expireDate && !$isExpired && strtotime($expireDate) < strtotime('+30 days');
?>

<style>
  .req-detail-header{background:linear-gradient(135deg,#6366f1 0%,#06b6d4 100%);border-radius:16px;padding:24px 28px;color:#fff;margin-bottom:1.5rem;}
  .req-detail-header .badge{font-size:.75rem;padding:.45em .75em;backdrop-filter:blur(4px);background:rgba(255,255,255,.2)!important;color:#fff!important;border:1px solid rgba(255,255,255,.3)!important;}
  .req-info-card{border:1px solid rgba(0,0,0,.06);border-radius:16px;overflow:hidden;transition:box-shadow .2s;}
  .req-info-card:hover{box-shadow:0 8px 30px rgba(0,0,0,.08);}
  .req-info-card .card-header{background:linear-gradient(135deg,#f8fafc,#f1f5f9);border-bottom:1px solid rgba(0,0,0,.05);padding:14px 20px;}
  .req-info-card .card-body{padding:20px;}
  .req-label{font-size:.75rem;text-transform:uppercase;letter-spacing:.5px;color:#94a3b8;font-weight:600;margin-bottom:4px;}
  .req-value{font-size:.95rem;color:#1e293b;font-weight:600;}
  .req-divider{border:none;border-top:1px dashed rgba(0,0,0,.1);margin:16px 0;}
  .action-card{border:2px solid rgba(99,102,241,.15);border-radius:16px;overflow:hidden;}
  .action-card .card-header{background:linear-gradient(135deg,#eef2ff,#e0e7ff);border-bottom:1px solid rgba(99,102,241,.1);padding:14px 20px;}
  .shortcut-btn{border-radius:10px;font-weight:600;font-size:.82rem;padding:6px 14px;border:2px solid #6366f1;color:#6366f1;background:transparent;transition:all .2s;}
  .shortcut-btn:hover,.shortcut-btn.active{background:#6366f1;color:#fff;transform:translateY(-1px);box-shadow:0 4px 12px rgba(99,102,241,.3);}
  .btn-approve{border-radius:12px;padding:12px;font-weight:700;background:linear-gradient(135deg,#10b981,#059669);border:none;transition:all .25s;font-size:.95rem;}
  .btn-approve:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(16,185,129,.35);}
  .btn-reject{border-radius:12px;padding:12px;font-weight:700;background:linear-gradient(135deg,#ef4444,#dc2626);border:none;transition:all .25s;font-size:.95rem;}
  .btn-reject:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(239,68,68,.35);}
  .timeline-line{position:relative;padding-left:24px;}
  .timeline-line::before{content:'';position:absolute;left:6px;top:20px;bottom:0;width:2px;background:linear-gradient(to bottom,#6366f1,#e2e8f0);}
  .timeline-dot{width:14px;height:14px;border-radius:50%;border:3px solid;position:absolute;left:0;top:2px;}
  .fine-warning{background:linear-gradient(135deg,#fef3c7,#fde68a);border:1px solid #f59e0b;border-radius:12px;padding:12px 16px;font-size:.85rem;color:#92400e;}
  .status-badge-lg{font-size:.9rem;padding:.55em 1em;border-radius:12px;}
  .form-select{border-radius:12px;border:2px solid rgba(148,163,184,.3);padding:10px 14px;}
  .form-select:focus{border-color:#6366f1;box-shadow:0 0 0 4px rgba(99,102,241,.1);}
  .form-control{border-radius:12px;border:2px solid rgba(148,163,184,.3);padding:10px 14px;}
  .form-control:focus{border-color:#6366f1;box-shadow:0 0 0 4px rgba(99,102,241,.1);}
</style>

<!-- Header -->
<div class="req-detail-header d-flex flex-wrap align-items-center justify-content-between gap-3">
  <div class="d-flex align-items-center gap-3">
    <a href="index.php?r=dashboard/requests" class="btn btn-outline-light btn-sm" style="border-radius:10px;">
      <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
    <div>
      <h4 class="mb-1 fw-bold">
        <i class="bi bi-journal-check me-2"></i>Detail Request
        <span class="badge status-badge-lg ms-2" style="background:rgba(255,255,255,.2);border:1px solid rgba(255,255,255,.3);">
          <i class="bi <?= $statusInfo['icon'] ?> me-1"></i><?= $statusInfo['label'] ?>
        </span>
      </h4>
      <div style="color:rgba(255,255,255,.8);font-size:.85rem;">
        Request #<?= $request['request_id'] ?> &middot; <?= date('d M Y H:i', strtotime($request['request_date'])) ?> WIB
      </div>
    </div>
  </div>
  <div class="text-end d-none d-md-block">
    <div style="color:rgba(255,255,255,.7);font-size:.8rem;">Tahap</div>
    <div class="fw-bold" style="font-size:1.4rem;">3</div>
  </div>
</div>

<!-- Flash message -->
<?php if (!empty($_SESSION['flash'])): ?>
  <div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show" role="alert" style="border-radius:12px;border:none;box-shadow:0 4px 15px rgba(0,0,0,.08);">
    <i class="bi bi-<?= $_SESSION['flash']['type'] === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill' ?> me-2"></i>
    <?= $_SESSION['flash']['msg'] ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<!-- Warning: Member Expired -->
<?php if ($isExpired): ?>
  <div class="alert alert-danger d-flex align-items-start gap-3" style="border-radius:12px;border:none;box-shadow:0 4px 15px rgba(220,53,69,.12);">
    <i class="bi bi-exclamation-octagon-fill fs-3 mt-1 flex-shrink-0"></i>
    <div>
      <div class="fw-bold">Masa Aktif Anggota Sudah Habis</div>
      <div class="small">Berakhir pada <strong><?= date('d M Y', strtotime($expireDate)) ?></strong>. Request tidak dapat disetujui.</div>
    </div>
  </div>
<?php elseif ($expireSoon): ?>
  <div class="alert alert-warning d-flex align-items-start gap-3" style="border-radius:12px;border:none;box-shadow:0 4px 15px rgba(255,193,7,.12);">
    <i class="bi bi-exclamation-triangle-fill fs-3 mt-1 flex-shrink-0"></i>
    <div>
      <div class="fw-bold">Masa Aktif Hampir Habis</div>
      <div class="small">Berakhir pada <strong><?= date('d M Y', strtotime($expireDate)) ?></strong>.</div>
    </div>
  </div>
<?php endif; ?>

<div class="row g-4">
  <!-- Kolom Kiri -->
  <div class="col-lg-7">

    <!-- Card: Info Siswa -->
    <div class="card req-info-card mb-4 shadow-sm">
      <div class="card-header d-flex align-items-center gap-2">
        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;background:linear-gradient(135deg,#6366f1,#818cf8);">
          <i class="bi bi-person-fill text-white" style="font-size:.85rem;"></i>
        </div>
        <span class="fw-bold" style="color:#334155;">Informasi Siswa</span>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-sm-6">
            <div class="req-label">Nama Lengkap</div>
            <div class="req-value"><?= htmlspecialchars($request['member_name'] ?? '-') ?></div>
          </div>
          <div class="col-sm-6">
            <div class="req-label">Username</div>
            <div class="req-value">@<?= htmlspecialchars($request['username'] ?? '-') ?></div>
          </div>
          <div class="col-sm-6">
            <div class="req-label">ID Anggota</div>
            <div class="req-value"><code style="background:#f1f5f9;padding:3px 8px;border-radius:6px;font-size:.85rem;"><?= htmlspecialchars($request['member_id'] ?? '-') ?></code></div>
          </div>
          <div class="col-sm-6">
            <div class="req-label">No. Telepon</div>
            <div class="req-value"><?= htmlspecialchars($memberData['member_phone'] ?? '-') ?></div>
          </div>
          <div class="col-12">
            <div class="req-label">Masa Aktif Anggota</div>
            <?php if ($expireDate): ?>
              <span class="badge <?= $isExpired ? 'bg-danger' : ($expireSoon ? 'bg-warning text-dark' : 'bg-success') ?> status-badge-lg">
                <i class="bi bi-<?= $isExpired ? 'x-circle' : 'calendar-check' ?> me-1"></i>
                <?= date('d M Y', strtotime($expireDate)) ?>
                <?php if ($isExpired): ?>
                  <small>(Habis)</small>
                <?php elseif ($expireSoon): ?>
                  <small>(Segera habis)</small>
                <?php endif; ?>
              </span>
            <?php else: ?>
              <span class="text-muted">-</span>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Card: Info Buku -->
    <div class="card req-info-card mb-4 shadow-sm">
      <div class="card-header d-flex align-items-center gap-2">
        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;background:linear-gradient(135deg,#06b6d4,#22d3ee);">
          <i class="bi bi-book-fill text-white" style="font-size:.85rem;"></i>
        </div>
        <span class="fw-bold" style="color:#334155;">Informasi Buku</span>
      </div>
      <div class="card-body">
        <div class="mb-3">
          <div class="req-label">Judul Buku</div>
          <div class="fw-bold" style="font-size:1.15rem;color:#0f172a;"><?= htmlspecialchars($request['title'] ?? '-') ?></div>
        </div>
        <hr class="req-divider">
        <div class="row g-3">
          <div class="col-sm-6">
            <div class="req-label">ISBN / ISSN</div>
            <div class="req-value" style="font-weight:400;"><?= htmlspecialchars($request['isbn_issn'] ?? '-') ?></div>
          </div>
          <div class="col-sm-6">
            <div class="req-label">Tahun Terbit</div>
            <div class="req-value" style="font-weight:400;"><?= htmlspecialchars($request['publish_year'] ?? '-') ?></div>
          </div>
          <div class="col-sm-6">
            <div class="req-label">Klasifikasi</div>
            <div class="req-value" style="font-weight:400;"><?= htmlspecialchars($request['classification'] ?? '-') ?></div>
          </div>
          <div class="col-sm-6">
            <div class="req-label">Nomor Panggil</div>
            <div class="req-value" style="font-weight:400;"><?= htmlspecialchars($request['call_number'] ?? '-') ?></div>
          </div>
          <?php if (!empty($request['actual_item_code'])): ?>
          <div class="col-sm-6">
            <div class="req-label">Kode Eksemplar</div>
            <span class="badge" style="background:linear-gradient(135deg,#ede9fe,#c4b5fd);color:#5b21b6;padding:6px 12px;border-radius:8px;font-size:.85rem;">
              <i class="bi bi-upc-scan me-1"></i><?= htmlspecialchars($request['actual_item_code']) ?>
            </span>
          </div>
          <?php endif; ?>
        </div>

        <?php if ($status === 'REJECTED' && !empty($request['rejection_reason'])): ?>
          <hr class="req-divider">
          <div class="alert mb-0" style="background:#fef2f2;border:1px solid #fecaca;border-radius:12px;color:#991b1b;">
            <div class="fw-bold mb-1"><i class="bi bi-chat-left-text me-2"></i>Alasan Penolakan</div>
            <?= nl2br(htmlspecialchars($request['rejection_reason'])) ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

  </div>

  <!-- Kolom Kanan: Aksi -->
  <div class="col-lg-5">
    <div class="card action-card shadow-sm">
      <div class="card-header d-flex align-items-center gap-2">
        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;background:linear-gradient(135deg,#10b981,#34d399);">
          <i class="bi bi-box-seam-fill text-white" style="font-size:.85rem;"></i>
        </div>
        <span class="fw-bold" style="color:#334155;">Eksemplar & Aksi</span>
        <?php if (!empty($copies)): ?>
          <span class="badge ms-auto" style="background:#dcfce7;color:#166534;padding:5px 10px;border-radius:8px;font-size:.78rem;">
            <i class="bi bi-check2-circle me-1"></i><?= count($copies) ?> tersedia
          </span>
        <?php else: ?>
          <span class="badge ms-auto" style="background:#fef2f2;color:#991b1b;padding:5px 10px;border-radius:8px;font-size:.78rem;">
            <i class="bi bi-x-circle me-1"></i>Habis
          </span>
        <?php endif; ?>
      </div>
      <div class="card-body">
        <?php if (empty($copies)): ?>
          <div class="text-center py-4">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;background:#fef2f2;">
              <i class="bi bi-box2" style="font-size:1.5rem;color:#dc3545;"></i>
            </div>
            <div class="fw-bold" style="color:#334155;">Tidak ada eksemplar tersedia</div>
            <small class="text-muted">Semua eksemplar sedang dipinjam</small>
          </div>

        <?php elseif ($status === 'PENDING'): ?>

          <?php if ($isExpired): ?>
            <div class="alert mb-3" style="background:#fef2f2;border:1px solid #fecaca;border-radius:12px;color:#991b1b;">
              <i class="bi bi-lock-fill me-2"></i>
              Tidak dapat disetujui — masa aktif anggota habis.
            </div>
          <?php else: ?>

            <!-- Pilih Eksemplar -->
            <div class="mb-3">
              <label class="fw-bold mb-2" style="color:#334155;font-size:.9rem;">
                <i class="bi bi-upc-scan me-1 text-primary"></i>Pilih Eksemplar <span class="text-danger">*</span>
              </label>
              <select name="item_code" class="form-select" id="itemCode" form="approveForm" required>
                <option value="">-- Pilih Eksemplar --</option>
                <?php foreach ($copies as $copy): ?>
                  <option value="<?= htmlspecialchars($copy['item_code']) ?>">
                    <?= htmlspecialchars($copy['item_code']) ?>
                    — <?= htmlspecialchars($copy['location_name'] ?? $copy['location_id'] ?? '-') ?>
                    (<?= htmlspecialchars($copy['item_status_name'] ?? '-') ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <hr class="req-divider">

            <!-- Jatuh Tempo -->
            <div class="mb-3">
              <label class="fw-bold mb-2" style="color:#334155;font-size:.9rem;">
                <i class="bi bi-calendar-event me-1 text-primary"></i>Jatuh Tempo <span class="text-danger">*</span>
              </label>
              <div class="d-flex gap-2 flex-wrap mb-2">
                <button type="button" class="shortcut-btn" data-days="3" onclick="setDueDate(3, this)">3 hari</button>
                <button type="button" class="shortcut-btn" data-days="5" onclick="setDueDate(5, this)">5 hari</button>
                <button type="button" class="shortcut-btn" data-days="7" onclick="setDueDate(7, this)">7 hari</button>
                <button type="button" class="shortcut-btn" data-days="14" onclick="setDueDate(14, this)">14 hari</button>
              </div>
              <input type="date" name="due_date" id="dueDate" class="form-control" required
                     value="<?= date('Y-m-d', strtotime('+3 days')) ?>" form="approveForm">
              <div class="d-flex align-items-center gap-2 mt-2">
                <div class="fine-warning flex-grow-1">
                  <i class="bi bi-exclamation-triangle me-1"></i>Denda <strong>Rp 20.000/hari</strong> jika terlambat
                </div>
              </div>
            </div>

            <form method="POST" action="index.php?r=dashboard/approveRequest" id="approveForm">
              <input type="hidden" name="request_id" value="<?= $request['request_id'] ?>">
              <input type="hidden" name="item_code" id="itemCodeHidden">
              <input type="hidden" name="due_date" id="dueDateHidden">
            </form>

            <button type="button" class="btn btn-approve w-100 mb-3 text-white" onclick="submitApprove()">
              <i class="bi bi-check-circle-fill me-2"></i>Setujui & Buat Peminjaman
            </button>

          <?php endif; ?>

          <hr class="req-divider">

          <!-- Form Reject -->
          <div class="mb-2">
            <label class="fw-bold mb-2" style="color:#334155;font-size:.9rem;">
              <i class="bi bi-x-circle me-1 text-danger"></i>Alasan Penolakan
            </label>
            <textarea name="rejection_reason" id="rejectReason" class="form-control" rows="3" required
                      placeholder="Masukkan alasan penolakan..." style="font-size:.9rem;"></textarea>
          </div>

          <form method="POST" action="index.php?r=dashboard/rejectRequest" id="rejectForm">
            <input type="hidden" name="request_id" value="<?= $request['request_id'] ?>">
            <input type="hidden" name="rejection_reason" id="rejectReasonHidden">
          </form>

          <button type="button" class="btn btn-reject w-100 text-white" onclick="submitReject()">
            <i class="bi bi-x-circle-fill me-2"></i>Tolak Request
          </button>

        <?php else: ?>
          <!-- Status bukan PENDING -->
          <div class="text-center py-4">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;background:<?= $statusInfo['color'] ?>22;">
              <i class="bi <?= $statusInfo['icon'] ?>" style="font-size:1.5rem;color:<?= $statusInfo['color'] ?>;"></i>
            </div>
            <div class="fw-bold" style="color:#334155;">Request ini sudah <?= $statusInfo['label'] ?></div>
            <small class="text-muted">
              <?php if ($status === 'APPROVED' && !empty($request['approved_at'])): ?>
                Disetujui pada <?= date('d M Y H:i', strtotime($request['approved_at'])) ?> WIB
              <?php elseif ($status === 'REJECTED' && !empty($request['rejected_at'])): ?>
                Ditolak pada <?= date('d M Y H:i', strtotime($request['rejected_at'])) ?> WIB
              <?php endif; ?>
            </small>
          </div>
          <ul class="list-group list-group-flush mt-3">
            <?php foreach ($copies as $copy): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center" style="border-radius:8px;margin-bottom:4px;">
                <div>
                  <span class="badge" style="background:#ede9fe;color:#5b21b6;padding:5px 10px;border-radius:8px;">
                    <?= htmlspecialchars($copy['item_code']) ?>
                  </span>
                  <small class="text-muted ms-2"><?= htmlspecialchars($copy['item_status_name'] ?? '-') ?></small>
                </div>
                <small class="text-muted"><?= htmlspecialchars($copy['location_name'] ?? '-') ?></small>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </div>

    <!-- Timeline -->
    <div class="card req-info-card mt-4 shadow-sm">
      <div class="card-header d-flex align-items-center gap-2">
        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:32px;height:32px;background:linear-gradient(135deg,#f59e0b,#fbbf24);">
          <i class="bi bi-clock-history text-white" style="font-size:.85rem;"></i>
        </div>
        <span class="fw-bold" style="color:#334155;">Riwayat</span>
      </div>
      <div class="card-body py-3">
        <div class="timeline-line">
          <div class="position-relative mb-4">
            <div class="timeline-dot" style="border-color:#6366f1;background:#6366f1;"></div>
            <div class="fw-semibold small" style="color:#334155;">Request Dibuat</div>
            <div class="text-muted" style="font-size:.8rem;"><?= date('d M Y H:i', strtotime($request['request_date'])) ?> WIB</div>
          </div>
          <?php if ($status === 'APPROVED' && !empty($request['approved_at'])): ?>
            <div class="position-relative mb-4">
              <div class="timeline-dot" style="border-color:#10b981;background:#10b981;"></div>
              <div class="fw-semibold small" style="color:#334155;">Disetujui Admin</div>
              <div class="text-muted" style="font-size:.8rem;"><?= date('d M Y H:i', strtotime($request['approved_at'])) ?> WIB</div>
            </div>
          <?php elseif ($status === 'REJECTED' && !empty($request['rejected_at'])): ?>
            <div class="position-relative mb-4">
              <div class="timeline-dot" style="border-color:#ef4444;background:#ef4444;"></div>
              <div class="fw-semibold small" style="color:#334155;">Ditolak Admin</div>
              <div class="text-muted" style="font-size:.8rem;"><?= date('d M Y H:i', strtotime($request['rejected_at'])) ?> WIB</div>
            </div>
          <?php elseif ($status === 'PENDING'): ?>
            <div class="position-relative">
              <div class="timeline-dot" style="border-color:#94a3b8;background:#fff;"></div>
              <div class="fw-semibold small text-muted">Menunggu Keputusan Admin</div>
            </div>
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
  document.querySelectorAll('.shortcut-btn').forEach(function(b) { b.classList.remove('active'); });
  if (el) el.classList.add('active');
}

function submitApprove() {
  var itemCode = document.getElementById('itemCode').value;
  var dueDate = document.getElementById('dueDate').value;
  if (!itemCode) { alert('Pilih eksemplar terlebih dahulu'); return; }
  if (!dueDate) { alert('Isi tanggal jatuh tempo'); return; }
  document.getElementById('itemCodeHidden').value = itemCode;
  document.getElementById('dueDateHidden').value = dueDate;
  if (confirm('Setujui request ini dan buat peminjaman?')) {
    document.getElementById('approveForm').submit();
  }
}

function submitReject() {
  var reason = document.getElementById('rejectReason').value.trim();
  if (!reason) { alert('Alasan penolakan wajib diisi'); return; }
  document.getElementById('rejectReasonHidden').value = reason;
  if (confirm('Tolak request ini? Siswa akan mendapat notifikasi.')) {
    document.getElementById('rejectForm').submit();
  }
}

// Set default active shortcut (3 hari)
document.querySelector('.shortcut-btn[data-days="3"]').classList.add('active');
</script>
