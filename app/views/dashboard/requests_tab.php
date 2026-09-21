<?php
// app/views/dashboard/requests_tab.php
// Bisa di-render sebagai partial (dalam tab) ATAU standalone via dashboard/requests
$status    = $_GET['status'] ?? 'PENDING';
$isAjax    = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
          && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$isPartial = $isAjax || (defined('PARTIAL_RENDER') && PARTIAL_RENDER);

if (!isset($requests)) {
  $model    = new BookRequestModel();
  $requests = $model->getAll($status, 200);
}
if (!isset($requestSummary)) {
  $pending   = DB::selectOne("SELECT COUNT(*) c FROM book_requests WHERE status='PENDING'");
  $approved  = DB::selectOne("SELECT COUNT(*) c FROM book_requests WHERE status='APPROVED'");
  $rejected  = DB::selectOne("SELECT COUNT(*) c FROM book_requests WHERE status='REJECTED'");
  $cancelled = DB::selectOne("SELECT COUNT(*) c FROM book_requests WHERE status='CANCELLED'");
  $requestSummary = [
    'PENDING'   => (int)($pending['c']   ?? 0),
    'APPROVED'  => (int)($approved['c']  ?? 0),
    'REJECTED'  => (int)($rejected['c']  ?? 0),
    'CANCELLED' => (int)($cancelled['c'] ?? 0),
  ];
}
?>

<?php if (!empty($_SESSION['flash'])): ?>
  <div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show mb-3" role="alert">
    <?= $_SESSION['flash']['msg'] ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<!-- ===== Filter Bar ===== -->
<div class="d-flex gap-2 flex-wrap mb-3 align-items-center">
  <span class="text-muted small me-1">Filter:</span>
  <button type="button"
          class="btn btn-sm filter-btn <?= ($status === '')            ? 'btn-primary'          : 'btn-outline-secondary' ?>"
          data-status="">
    <i class="bi bi-list-ul me-1"></i>Semua
    <span class="badge bg-white text-dark ms-1"><?= array_sum($requestSummary) ?></span>
  </button>
  <button type="button"
          class="btn btn-sm filter-btn <?= ($status === 'PENDING')    ? 'btn-warning text-dark' : 'btn-outline-warning' ?>"
          data-status="PENDING">
    <i class="bi bi-hourglass-split me-1"></i>Menunggu
    <span class="badge bg-white text-dark ms-1"><?= $requestSummary['PENDING'] ?></span>
  </button>
  <button type="button"
          class="btn btn-sm filter-btn <?= ($status === 'APPROVED')   ? 'btn-success'           : 'btn-outline-success' ?>"
          data-status="APPROVED">
    <i class="bi bi-check-circle me-1"></i>Disetujui
    <span class="badge bg-white text-dark ms-1"><?= $requestSummary['APPROVED'] ?></span>
  </button>
  <button type="button"
          class="btn btn-sm filter-btn <?= ($status === 'REJECTED')   ? 'btn-danger'            : 'btn-outline-danger' ?>"
          data-status="REJECTED">
    <i class="bi bi-x-circle me-1"></i>Ditolak
    <span class="badge bg-white text-dark ms-1"><?= $requestSummary['REJECTED'] ?></span>
  </button>
  <button type="button"
          class="btn btn-sm filter-btn <?= ($status === 'CANCELLED')  ? 'btn-secondary'         : 'btn-outline-secondary' ?>"
          data-status="CANCELLED">
    <i class="bi bi-slash-circle me-1"></i>Dibatalkan
    <span class="badge bg-white text-dark ms-1"><?= $requestSummary['CANCELLED'] ?></span>
  </button>
</div>

<!-- ===== Table ===== -->
<?php if (empty($requests)): ?>
  <div class="text-center py-5 text-muted">
    <i class="bi bi-inbox display-4 d-block mb-3"></i>
    <p class="mb-0">Tidak ada request
      <?php if ($status): ?>
        dengan status <strong><?= htmlspecialchars($status) ?></strong>
      <?php endif; ?>
    </p>
  </div>
<?php else: ?>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th class="ps-3">#</th>
          <th>Siswa</th>
          <th>Judul Buku</th>
          <th>Tgl Request</th>
          <th>Status</th>
          <th class="text-center" style="width:100px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($requests as $i => $req):
          $st = $req['status'];
          $badgeInfo = match($st) {
            'PENDING'   => ['class' => 'bg-warning text-dark', 'icon' => 'bi-hourglass-split', 'label' => 'Menunggu'],
            'APPROVED'  => ['class' => 'bg-success',           'icon' => 'bi-check-circle',    'label' => 'Disetujui'],
            'REJECTED'  => ['class' => 'bg-danger',            'icon' => 'bi-x-circle',        'label' => 'Ditolak'],
            'CANCELLED' => ['class' => 'bg-secondary',         'icon' => 'bi-slash-circle',    'label' => 'Dibatalkan'],
            default     => ['class' => 'bg-light text-dark',   'icon' => 'bi-question-circle', 'label' => $st],
          };
        ?>
          <tr>
            <td class="ps-3 text-muted small"><?= $i + 1 ?></td>
            <td>
              <div class="fw-semibold"><?= htmlspecialchars($req['member_name'] ?? '-') ?></div>
              <div class="text-muted small">
                <i class="bi bi-person me-1"></i><?= htmlspecialchars($req['username'] ?? '-') ?>
                &nbsp;·&nbsp; ID: <?= htmlspecialchars($req['member_id'] ?? '-') ?>
              </div>
            </td>
            <td>
              <div class="fw-semibold"><?= htmlspecialchars($req['title'] ?? '-') ?></div>
              <div class="text-muted small">
                <?php if (!empty($req['isbn_issn'])): ?>
                  <span class="me-2"><i class="bi bi-upc me-1"></i><?= htmlspecialchars($req['isbn_issn']) ?></span>
                <?php endif; ?>
                <?php if (!empty($req['actual_item_code'])): ?>
                  <span><i class="bi bi-tag me-1"></i><?= htmlspecialchars($req['actual_item_code']) ?></span>
                <?php endif; ?>
              </div>
            </td>
            <td class="text-muted small">
              <i class="bi bi-calendar3 me-1"></i>
              <?= date('d M Y', strtotime($req['request_date'])) ?>
              <br><?= date('H:i', strtotime($req['request_date'])) ?> WIB
            </td>
            <td>
              <span class="badge <?= $badgeInfo['class'] ?> px-2 py-1">
                <i class="bi <?= $badgeInfo['icon'] ?> me-1"></i><?= $badgeInfo['label'] ?>
              </span>
              <?php if ($st === 'APPROVED' && !empty($req['approved_at'])): ?>
                <div class="text-muted small mt-1">
                  <i class="bi bi-check2-all me-1"></i><?= date('d M Y H:i', strtotime($req['approved_at'])) ?>
                </div>
              <?php elseif ($st === 'REJECTED' && !empty($req['rejected_at'])): ?>
                <div class="text-muted small mt-1">
                  <i class="bi bi-calendar-x me-1"></i><?= date('d M Y H:i', strtotime($req['rejected_at'])) ?>
                </div>
                <?php if (!empty($req['rejection_reason'])): ?>
                  <div class="text-danger small">
                    <i class="bi bi-chat-left-text me-1"></i><?= htmlspecialchars($req['rejection_reason']) ?>
                  </div>
                <?php endif; ?>
              <?php endif; ?>
            </td>
            <td class="text-center">
              <?php if ($st === 'PENDING'): ?>
                <a href="index.php?r=dashboard/requestDetail&id=<?= $req['request_id'] ?>"
                   class="btn btn-primary btn-sm"
                   title="Proses Request">
                  <i class="bi bi-pencil-square me-1"></i>Proses
                </a>
              <?php else: ?>
                <a href="index.php?r=dashboard/requestDetail&id=<?= $req['request_id'] ?>"
                   class="btn btn-outline-secondary btn-sm"
                   title="Lihat Detail">
                  <i class="bi bi-eye"></i>
                </a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="text-muted small mt-2 px-1">
    Menampilkan <?= count($requests) ?> request
    <?= $status ? 'dengan status ' . htmlspecialchars($status) : '' ?>
  </div>
<?php endif; ?>