<?php
// app/views/dashboard/request_detail.php

$status = $request['status'];

// Badge & icon info per status
$statusInfo = match($status) {
  'PENDING'   => ['class' => 'bg-warning text-dark', 'icon' => 'bi-hourglass-split',    'label' => 'Menunggu'],
  'APPROVED'  => ['class' => 'bg-success',           'icon' => 'bi-check-circle-fill',  'label' => 'Disetujui'],
  'REJECTED'  => ['class' => 'bg-danger',            'icon' => 'bi-x-circle-fill',      'label' => 'Ditolak'],
  'CANCELLED' => ['class' => 'bg-secondary',         'icon' => 'bi-slash-circle-fill',  'label' => 'Dibatalkan'],
  default     => ['class' => 'bg-light text-dark',   'icon' => 'bi-question-circle',    'label' => $status],
};

// Cek expire date member
$memberData   = DB::selectOne("SELECT expire_date, member_phone FROM member WHERE member_id=? LIMIT 1", "s", [$request['member_id']]);
$expireDate   = $memberData['expire_date'] ?? null;
$isExpired    = $expireDate && strtotime($expireDate) < strtotime(date('Y-m-d'));
$expireSoon   = $expireDate && !$isExpired && strtotime($expireDate) < strtotime('+30 days');
?>

<!-- Header -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
  <div class="d-flex align-items-center gap-3">
    <a href="index.php?r=dashboard/index" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
    <div>
      <h5 class="mb-0 fw-bold">
        <i class="bi bi-journal-check me-2 text-primary"></i>Detail Request
        <span class="badge <?= $statusInfo['class'] ?> ms-2">
          <i class="bi <?= $statusInfo['icon'] ?> me-1"></i><?= $statusInfo['label'] ?>
        </span>
      </h5>
      <div class="text-muted small">Request #<?= $request['request_id'] ?> &middot; <?= date('d M Y H:i', strtotime($request['request_date'])) ?> WIB</div>
    </div>
  </div>
</div>

<!-- Flash message -->
<?php if (!empty($_SESSION['flash'])): ?>
  <div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show mb-4" role="alert">
    <i class="bi bi-<?= $_SESSION['flash']['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?> me-2"></i>
    <?= $_SESSION['flash']['msg'] ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<!-- Warning: Member Expired -->
<?php if ($isExpired): ?>
  <div class="alert alert-danger d-flex align-items-start gap-3 mb-4">
    <i class="bi bi-exclamation-octagon-fill fs-4 mt-1 flex-shrink-0"></i>
    <div>
      <div class="fw-bold">Masa Aktif Anggota Sudah Habis</div>
      <div class="small">
        Masa aktif anggota ini berakhir pada <strong><?= date('d M Y', strtotime($expireDate)) ?></strong>.
        Request tidak dapat disetujui sampai masa aktif diperpanjang.
      </div>
    </div>
  </div>
<?php elseif ($expireSoon): ?>
  <div class="alert alert-warning d-flex align-items-start gap-3 mb-4">
    <i class="bi bi-exclamation-triangle-fill fs-4 mt-1 flex-shrink-0"></i>
    <div>
      <div class="fw-bold">Masa Aktif Hampir Habis</div>
      <div class="small">
        Masa aktif anggota akan berakhir pada <strong><?= date('d M Y', strtotime($expireDate)) ?></strong>.
      </div>
    </div>
  </div>
<?php endif; ?>

<div class="row g-4">
  <!-- Kolom Kiri: Info Siswa + Info Buku -->
  <div class="col-lg-7">

    <!-- Card: Info Siswa -->
    <div class="card shadow-sm mb-4">
      <div class="card-header bg-light d-flex align-items-center gap-2">
        <i class="bi bi-person-circle text-primary"></i>
        <span class="fw-semibold">Informasi Siswa</span>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-sm-6">
            <div class="text-muted small mb-1">Nama Lengkap</div>
            <div class="fw-semibold"><?= htmlspecialchars($request['member_name'] ?? '-') ?></div>
          </div>
          <div class="col-sm-6">
            <div class="text-muted small mb-1">Username</div>
            <div class="fw-semibold">@<?= htmlspecialchars($request['username'] ?? '-') ?></div>
          </div>
          <div class="col-sm-6">
            <div class="text-muted small mb-1">ID Anggota</div>
            <div class="fw-semibold"><?= htmlspecialchars($request['member_id'] ?? '-') ?></div>
          </div>
          <div class="col-sm-6">
            <div class="text-muted small mb-1">No. Telepon</div>
            <div class="fw-semibold"><?= htmlspecialchars($memberData['member_phone'] ?? '-') ?></div>
          </div>
          <div class="col-sm-6">
            <div class="text-muted small mb-1">Masa Aktif Anggota</div>
            <?php if ($expireDate): ?>
              <span class="badge <?= $isExpired ? 'bg-danger' : ($expireSoon ? 'bg-warning text-dark' : 'bg-success') ?> px-2 py-1">
                <i class="bi bi-<?= $isExpired ? 'x-circle' : 'calendar-check' ?> me-1"></i>
                <?= date('d M Y', strtotime($expireDate)) ?>
              </span>
            <?php else: ?>
              <span class="text-muted">-</span>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Card: Info Buku -->
    <div class="card shadow-sm mb-4">
      <div class="card-header bg-light d-flex align-items-center gap-2">
        <i class="bi bi-book text-primary"></i>
        <span class="fw-semibold">Informasi Buku</span>
      </div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-12">
            <div class="text-muted small mb-1">Judul</div>
            <div class="fw-bold fs-5"><?= htmlspecialchars($request['title'] ?? '-') ?></div>
          </div>
          <div class="col-sm-6">
            <div class="text-muted small mb-1">ISBN / ISSN</div>
            <div><?= htmlspecialchars($request['isbn_issn'] ?? '-') ?></div>
          </div>
          <div class="col-sm-6">
            <div class="text-muted small mb-1">Tahun Terbit</div>
            <div><?= htmlspecialchars($request['publish_year'] ?? '-') ?></div>
          </div>
          <div class="col-sm-6">
            <div class="text-muted small mb-1">Klasifikasi</div>
            <div><?= htmlspecialchars($request['classification'] ?? '-') ?></div>
          </div>
          <div class="col-sm-6">
            <div class="text-muted small mb-1">Nomor Panggil</div>
            <div><?= htmlspecialchars($request['call_number'] ?? '-') ?></div>
          </div>
          <?php if (!empty($request['actual_item_code'])): ?>
          <div class="col-sm-6">
            <div class="text-muted small mb-1">Kode Eksemplar (Dipinjam)</div>
            <div><span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2"><?= htmlspecialchars($request['actual_item_code']) ?></span></div>
          </div>
          <?php endif; ?>
        </div>

        <?php if ($status === 'REJECTED' && !empty($request['rejection_reason'])): ?>
          <hr>
          <div class="alert alert-danger mb-0">
            <div class="fw-semibold mb-1"><i class="bi bi-chat-left-text me-2"></i>Alasan Penolakan</div>
            <?= nl2br(htmlspecialchars($request['rejection_reason'])) ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

  </div>

  <!-- Kolom Kanan: Aksi -->
  <div class="col-lg-5">
    <div class="card shadow-sm">
      <div class="card-header bg-light d-flex align-items-center gap-2">
        <i class="bi bi-box-seam text-primary"></i>
        <span class="fw-semibold">Eksemplar Tersedia</span>
        <?php if (!empty($copies)): ?>
          <span class="badge bg-success-subtle text-success border border-success-subtle ms-auto"><?= count($copies) ?> tersedia</span>
        <?php else: ?>
          <span class="badge bg-danger-subtle text-danger border border-danger-subtle ms-auto">Habis</span>
        <?php endif; ?>
      </div>
      <div class="card-body">
        <?php if (empty($copies)): ?>
          <div class="text-center py-4 text-muted">
            <i class="bi bi-box2 display-4 d-block mb-2"></i>
            <div class="fw-semibold">Tidak ada eksemplar tersedia</div>
            <small>Semua eksemplar sedang dipinjam atau tidak ada</small>
          </div>
        <?php elseif ($status === 'PENDING'): ?>

          <!-- Form Approve -->
          <?php if ($isExpired): ?>
            <div class="alert alert-danger mb-3">
              <i class="bi bi-lock me-2"></i>
              Tidak dapat disetujui karena masa aktif anggota sudah habis.
            </div>
          <?php else: ?>
            <form method="POST" action="index.php?r=dashboard/approveRequest" id="approveForm">
              <input type="hidden" name="request_id" value="<?= $request['request_id'] ?>">
              <div class="mb-3">
                <label class="form-label fw-semibold">
                  Pilih Eksemplar <span class="text-danger">*</span>
                </label>
                <select name="item_code" class="form-select" required>
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
              <button type="submit" class="btn btn-success w-100 mb-3"
                      onclick="return confirm('Setujui request ini dan buat peminjaman untuk eksemplar yang dipilih?')">
                <i class="bi bi-check-circle-fill me-2"></i>Setujui & Buat Peminjaman
              </button>
            </form>
          <?php endif; ?>

          <hr class="my-3">

          <!-- Form Reject -->
          <form method="POST" action="index.php?r=dashboard/rejectRequest" id="rejectForm">
            <input type="hidden" name="request_id" value="<?= $request['request_id'] ?>">
            <div class="mb-3">
              <label class="form-label fw-semibold">
                Alasan Penolakan <span class="text-danger">*</span>
              </label>
              <textarea name="rejection_reason" class="form-control" rows="3" required
                        placeholder="Masukkan alasan penolakan..."></textarea>
            </div>
            <button type="submit" class="btn btn-danger w-100"
                    onclick="return confirm('Tolak request ini? Siswa akan mendapat notifikasi.')">
              <i class="bi bi-x-circle-fill me-2"></i>Tolak Request
            </button>
          </form>

        <?php else: ?>
          <!-- Status bukan PENDING: tampilkan list eksemplar saja -->
          <ul class="list-group list-group-flush">
            <?php foreach ($copies as $copy): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <span class="badge bg-primary-subtle text-primary border border-primary-subtle me-2">
                    <?= htmlspecialchars($copy['item_code']) ?>
                  </span>
                  <small class="text-muted"><?= htmlspecialchars($copy['item_status_name'] ?? '-') ?></small>
                </div>
                <small class="text-muted"><?= htmlspecialchars($copy['location_name'] ?? '-') ?></small>
              </li>
            <?php endforeach; ?>
          </ul>
          <div class="mt-3 text-center">
            <span class="badge <?= $statusInfo['class'] ?> px-3 py-2 fs-6">
              <i class="bi <?= $statusInfo['icon'] ?> me-2"></i>Request ini sudah <?= $statusInfo['label'] ?>
            </span>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Timeline Card -->
    <div class="card shadow-sm mt-4">
      <div class="card-header bg-light d-flex align-items-center gap-2">
        <i class="bi bi-clock-history text-primary"></i>
        <span class="fw-semibold">Timeline</span>
      </div>
      <div class="card-body py-3">
        <div class="d-flex flex-column gap-3">
          <div class="d-flex gap-3">
            <div class="text-primary"><i class="bi bi-circle-fill" style="font-size:.6rem;margin-top:.45rem;"></i></div>
            <div>
              <div class="fw-semibold small">Request Dibuat</div>
              <div class="text-muted small"><?= date('d M Y H:i', strtotime($request['request_date'])) ?> WIB</div>
            </div>
          </div>
          <?php if ($status === 'APPROVED' && !empty($request['approved_at'])): ?>
            <div class="d-flex gap-3">
              <div class="text-success"><i class="bi bi-circle-fill" style="font-size:.6rem;margin-top:.45rem;"></i></div>
              <div>
                <div class="fw-semibold small">Disetujui Admin</div>
                <div class="text-muted small"><?= date('d M Y H:i', strtotime($request['approved_at'])) ?> WIB</div>
              </div>
            </div>
          <?php elseif ($status === 'REJECTED' && !empty($request['rejected_at'])): ?>
            <div class="d-flex gap-3">
              <div class="text-danger"><i class="bi bi-circle-fill" style="font-size:.6rem;margin-top:.45rem;"></i></div>
              <div>
                <div class="fw-semibold small">Ditolak Admin</div>
                <div class="text-muted small"><?= date('d M Y H:i', strtotime($request['rejected_at'])) ?> WIB</div>
              </div>
            </div>
          <?php elseif ($status === 'PENDING'): ?>
            <div class="d-flex gap-3">
              <div class="text-muted"><i class="bi bi-circle" style="font-size:.6rem;margin-top:.45rem;"></i></div>
              <div>
                <div class="text-muted small fw-semibold">Menunggu Keputusan Admin</div>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

  </div>
</div>