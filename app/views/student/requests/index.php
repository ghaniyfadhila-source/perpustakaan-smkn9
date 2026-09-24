<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<?php if (!empty($_SESSION['flash'])): ?>
  <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type']) ?> alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($_SESSION['flash']['msg']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
  <div>
    <h4 class="mb-0">Request Peminjaman Saya</h4>
    <div class="text-muted small">Status pengajuan peminjaman buku</div>
  </div>
  <a href="index.php?r=student/books/index" class="btn btn-primary btn-sm">
    <i class="bi bi-plus-circle me-1"></i>Ajukan Baru
  </a>
</div>

<div class="card mb-3">
  <div class="card-body py-2">
    <div class="d-flex gap-2 flex-wrap">
      <a href="index.php?r=student/requests/index" class="btn btn-sm <?= empty($_GET['status']) ? 'btn-primary' : 'btn-outline-primary' ?>">Semua</a>
      <a href="index.php?r=student/requests/index&status=PENDING" class="btn btn-sm <?= ($_GET['status']??'')==='PENDING' ? 'btn-warning text-dark' : 'btn-outline-warning' ?>">Menunggu</a>
      <a href="index.php?r=student/requests/index&status=APPROVED" class="btn btn-sm <?= ($_GET['status']??'')==='APPROVED' ? 'btn-success' : 'btn-outline-success' ?>">Disetujui</a>
      <a href="index.php?r=student/requests/index&status=REJECTED" class="btn btn-sm <?= ($_GET['status']??'')==='REJECTED' ? 'btn-danger' : 'btn-outline-danger' ?>">Ditolak</a>
      <a href="index.php?r=student/requests/index&status=CANCELLED" class="btn btn-sm <?= ($_GET['status']??'')==='CANCELLED' ? 'btn-secondary' : 'btn-outline-secondary' ?>">Dibatalkan</a>
    </div>
  </div>
</div>

<?php if (empty($requests)): ?>
  <div class="card shadow-sm">
    <div class="card-body text-center py-5">
      <i class="bi bi-journal-x display-1 text-muted"></i>
      <p class="text-muted mt-3">Belum ada request peminjaman</p>
      <a href="index.php?r=student/books/index" class="btn btn-primary"><i class="bi bi-book me-1"></i>Cari Buku & Ajukan Request</a>
    </div>
  </div>
<?php else: ?>
  <div class="card shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Judul Buku</th>
              <th>Tgl Request</th>
              <th>Status</th>
              <th style="width:150px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($requests as $req): ?>
              <tr>
                <td>
                  <strong><?= htmlspecialchars($req['title'] ?? '-') ?></strong>
                  <?php if (!empty($req['actual_item_code'])): ?>
                    <br><small class="text-muted">Kode: <?= htmlspecialchars($req['actual_item_code']) ?></small>
                  <?php endif; ?>
                </td>
                <td class="text-muted small"><?= date('d/m/Y H:i', strtotime($req['request_date'])) ?></td>
                <td>
                  <?php
                    $badgeClass = match($req['status']) {
                      'PENDING' => 'bg-warning text-dark',
                      'APPROVED' => 'bg-success',
                      'REJECTED' => 'bg-danger',
                      'CANCELLED' => 'bg-secondary',
                      default => 'bg-light text-dark'
                    };
                  ?>
                  <span class="badge <?= $badgeClass ?>"><?= $req['status'] ?></span>
                  <?php if ($req['status'] === 'APPROVED' && !empty($req['approved_at'])): ?>
                    <br><small class="text-muted">Disetujui: <?= date('d/m/Y H:i', strtotime($req['approved_at'])) ?></small>
                  <?php elseif ($req['status'] === 'REJECTED' && !empty($req['rejected_at'])): ?>
                    <br><small class="text-muted">Ditolak: <?= date('d/m/Y H:i', strtotime($req['rejected_at'])) ?></small>
                    <?php if (!empty($req['rejection_reason'])): ?>
                      <br><small class="text-danger"><?= htmlspecialchars($req['rejection_reason']) ?></small>
                    <?php endif; ?>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($req['status'] === 'PENDING'): ?>
                    <form method="POST" action="index.php?r=student/requests/cancel" class="d-inline" onsubmit="return confirm('Batalkan request ini?')">
                      <input type="hidden" name="request_id" value="<?= $req['request_id'] ?>">
                      <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-x-circle me-1"></i>Batalkan
                      </button>
                    </form>
                  <?php elseif ($req['status'] === 'APPROVED'): ?>
                    <span class="badge bg-success">Selesai</span>
                  <?php else: ?>
                    <span class="text-muted small">-</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
