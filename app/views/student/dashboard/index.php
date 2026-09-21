<?php $student = $_SESSION['student'] ?? null; ?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard Siswa - <?= APP_NAME ?? 'Perpustakaan SMKN 9 Semarang' ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

<style>
  body { background: #f6f7fb; }
  .stat-card {
    border: 1px solid rgba(0,0,0,.06);
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 4px 15px rgba(0,0,0,.03);
  }
  .stat-card .num { font-size: 2rem; font-weight: 800; }
  .stat-card .label { font-size: .85rem; color: #6c757d; }
</style>
</head>
<body>

<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="container-fluid">
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="stat-card p-3">
        <div class="label">Buku Sedang Dipinjam</div>
        <div class="num text-primary"><?= $activeLoans['c'] ?? 0 ?></div>
        <small class="text-muted">Total buku yang sedang dipinjam</small>
      </div>
    </div>
    <div class="col-md-4">
      <div class="stat-card p-3">
        <div class="label">Buku Terlambat</div>
        <div class="num text-danger"><?= $overdue['c'] ?? 0 ?></div>
        <small class="text-muted">Buku yang sudah melewati jatuh tempo</small>
      </div>
    </div>
    <div class="col-md-4">
      <div class="stat-card p-3">
        <div class="label">Total Denda</div>
        <div class="num text-warning">Rp <?= number_format($fines['s'] ?? 0) ?></div>
        <small class="text-muted">Denda yang harus dibayar</small>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="stat-card p-3">
        <h5 class="mb-3">Buku yang Sedang Dipinjam</h5>
        <?php if (!empty($activeLoansDetail)): ?>
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>Judul Buku</th>
                <th>Kode</th>
                <th>Tgl Pinjam</th>
                <th>Jatuh Tempo</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($activeLoansDetail as $loan): ?>
              <tr>
                <td><?= htmlspecialchars($loan['title'] ?? '') ?></td>
                <td><?= htmlspecialchars($loan['item_code'] ?? '') ?></td>
                <td><?= htmlspecialchars($loan['loan_date'] ?? '') ?></td>
                <td>
                  <?php 
                  $isOverdue = $loan['due_date'] < date('Y-m-d');
                  $dateClass = $isOverdue ? 'text-danger fw-bold' : '';
                  ?>
                  <span class="<?= $dateClass ?>">
                  <?= htmlspecialchars($loan['due_date'] ?? '') ?>
                  </span>
                </td>
                <td>
                  <?php if ($loan['due_date'] < date('Y-m-d')): ?>
                    <span class="badge bg-danger">Terlambat</span>
                  <?php else: ?>
                    <span class="badge bg-success">Aktif</span>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php else: ?>
          <p class="text-muted mb-0">Tidak ada buku yang sedang dipinjam.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>