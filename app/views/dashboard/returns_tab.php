<?php
$returnRequests = $returnRequests ?? [];
$returnCount = $returnCount ?? count($returnRequests);
?>

<div class="card shadow-sm mb-3">
  <div class="card-header d-flex align-items-center justify-content-between">
    <h5 class="mb-0"><i class="bi bi-arrow-return-left me-2"></i>Pengembalian Buku</h5>
    <span class="badge bg-info text-dark"><?= $returnCount ?> menunggu konfirmasi</span>
  </div>
  <div class="card-body">
    <?php if (!empty($_SESSION['flash'])): ?>
      <div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show" role="alert">
        <?= $_SESSION['flash']['msg'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <?php if (empty($returnRequests)): ?>
      <div class="text-center py-5">
        <i class="bi bi-check-circle display-1 text-success"></i>
        <p class="text-muted mt-3">Tidak ada pengembalian yang menunggu konfirmasi</p>
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>Siswa</th>
              <th>Buku</th>
              <th>Kode</th>
              <th>Tgl Pinjam</th>
              <th>Jatuh Tempo</th>
              <th>Tgl Request</th>
              <th style="width: 130px;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($returnRequests as $rr):
              $isOverdue = $rr['due_date'] < date('Y-m-d');
              $daysLate = $isOverdue ? (int)floor((strtotime(date('Y-m-d')) - strtotime($rr['due_date'])) / 86400) : 0;
              $fine = $daysLate * 20000;
            ?>
              <tr>
                <td>
                  <strong><?= htmlspecialchars($rr['member_name'] ?? '-') ?></strong>
                  <br><small class="text-muted">@<?= htmlspecialchars($rr['username'] ?? '-') ?></small>
                </td>
                <td><?= htmlspecialchars($rr['title'] ?? '-') ?></td>
                <td><span class="badge bg-primary-subtle text-primary border border-primary-subtle"><?= htmlspecialchars($rr['item_code']) ?></span></td>
                <td><?= date('d/m/Y', strtotime($rr['loan_date'])) ?></td>
                <td>
                  <span class="<?= $isOverdue ? 'text-danger fw-bold' : '' ?>">
                    <?= date('d/m/Y', strtotime($rr['due_date'])) ?>
                  </span>
                  <?php if ($isOverdue): ?>
                    <br><small class="text-danger">Terlambat <?= $daysLate ?> hari</small>
                  <?php endif; ?>
                </td>
                <td><?= date('d/m/Y H:i', strtotime($rr['request_date'])) ?></td>
                <td>
                  <form method="POST" action="index.php?r=dashboard/confirmReturn" class="d-inline">
                    <input type="hidden" name="return_request_id" value="<?= $rr['return_request_id'] ?>">
                    <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Konfirmasi pengembalian buku ini<?= $isOverdue ? ' (denda Rp ' . number_format($fine) . ')' : '' ?>?')">
                      <i class="bi bi-check-circle me-1"></i>Konfirmasi
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>
