<?php
if (!Auth::check()) { redirect('login/index'); return; }
$user = Auth::user();
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Kelola Request - <?= APP_NAME ?? 'Perpustakaan SMKN 9 Semarang' ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
  body { background: #f6f7fb; }
  .sidebar { width: 260px; min-height: 100vh; background: #fff; border-right: 1px solid rgba(0,0,0,.08); flex-shrink: 0; }
  .sidebar .nav-link { color: #333; border-radius: .6rem; }
  .sidebar .nav-link:hover { background: #f1f3f5; }
  .sidebar .nav-link.active { background: #0d6efd; color: #fff; }
  .content { padding: 1.25rem; flex: 1 1 auto; min-width: 0; }
  .navbar-modern{ height:70px; background:white; border-bottom:1px solid #e5e7eb; box-shadow:0 4px 15px rgba(0,0,0,.05); flex-shrink: 0; }
  .status-badge { font-size: .8rem; padding: .4em .6em; }
  .table th { font-weight: 600; font-size: .85rem; }
  .table td { font-size: .9rem; vertical-align: middle; }
</style>
</head>
<body>
<div class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-modern px-4">
  <div class="d-flex align-items-center gap-3">
    <button class="btn btn-light d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar">
      <i class="bi bi-list fs-5"></i>
    </button>
    <div class="text-center py-2 border-bottom">
      <img src="<?= BASE_URL ?>/app/image/SMKN9.png" class="logo" alt="Logo Sekolah" class="img-fluid" style="max-height: 50px;">
    </div>
    <div>
      <div class="fw-bold text-dark" style="line-height:1;"><?= APP_NAME ?? 'Perpustakaan SMKN 9 Semarang' ?></div>
      <small class="text-muted">Admin Panel</small>
    </div>
  </div>
  <div class="ms-auto d-flex align-items-center gap-3">
    <div class="d-flex align-items-center gap-2 text-muted small">
      <i class="bi bi-person-circle fs-5"></i>
      <span class="fw-semibold"><?= htmlspecialchars($user['realname'] ?? $user['username'] ?? '') ?></span>
    </div>
    <a class="btn btn-outline-danger btn-sm rounded-pill px-3" href="index.php?r=auth/logout">
      <i class="bi bi-box-arrow-right me-1"></i> Logout
    </a>
  </div>
</nav>

<div class="d-flex flex-1" style="min-height: 0; align-items: stretch;">
  <aside class="sidebar d-none d-lg-block p-3">
    <div class="mb-3 small text-muted">MENU ADMIN</div>
    <div class="nav flex-column gap-1">
      <a class="nav-link" href="index.php?r=dashboard/index"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
      <a class="nav-link" href="index.php?r=loans/borrow"><i class="bi bi-journal-plus me-2"></i>Peminjaman</a>
      <a class="nav-link" href="index.php?r=loans/returnBook"><i class="bi bi-journal-arrow-down me-2"></i>Pengembalian</a>
      <a class="nav-link active" href="index.php?r=requests/index"><i class="bi bi-journal-check me-2"></i>Request Buku</a>
      <a class="nav-link" href="index.php?r=books/index"><i class="bi bi-book me-2"></i>Katalog Buku</a>
      <a class="nav-link" href="index.php?r=members/index"><i class="bi bi-people me-2"></i>Anggota</a>
    </div>
  </aside>

  <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title"><i class="bi bi-book-half me-2"></i><?= APP_NAME ?? 'Perpustakaan SMKN 9 Semarang' ?></h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
      <div class="nav flex-column gap-1">
        <a class="nav-link" href="index.php?r=dashboard/index">Dashboard</a>
        <a class="nav-link" href="index.php?r=loans/borrow">Peminjaman</a>
        <a class="nav-link" href="index.php?r=loans/returnBook">Pengembalian</a>
        <a class="nav-link active" href="index.php?r=requests/index">Request Buku</a>
        <a class="nav-link" href="index.php?r=books/index">Katalog Buku</a>
        <a class="nav-link" href="index.php?r=members/index">Anggota</a>
      </div>
    </div>
  </div>

  <main class="content flex-grow-1">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="mb-0"><i class="bi bi-journal-check me-2"></i>Kelola Request Peminjaman</h4>
    </div>

    <?php if (!empty($_SESSION['flash'])): ?>
      <div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show" role="alert">
        <?= $_SESSION['flash']['msg'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <div class="card mb-3">
      <div class="card-body">
        <div class="d-flex gap-2 flex-wrap">
          <a href="index.php?r=requests/index&status=PENDING" class="btn btn-sm <?= ($_GET['status']??'')==='PENDING' ? 'btn-warning' : 'btn-outline-warning' ?>">Menunggu (<?= count(array_filter($requests, fn($r)=>$r['status']==='PENDING')) ?>)</a>
          <a href="index.php?r=requests/index&status=APPROVED" class="btn btn-sm <?= ($_GET['status']??'')==='APPROVED' ? 'btn-success' : 'btn-outline-success' ?>">Disetujui</a>
          <a href="index.php?r=requests/index&status=REJECTED" class="btn btn-sm <?= ($_GET['status']??'')==='REJECTED' ? 'btn-danger' : 'btn-outline-danger' ?>">Ditolak</a>
          <a href="index.php?r=requests/index&status=CANCELLED" class="btn btn-sm <?= ($_GET['status']??'')==='CANCELLED' ? 'btn-secondary' : 'btn-outline-secondary' ?>">Dibatalkan</a>
          <a href="index.php?r=requests/index" class="btn btn-sm <?= empty($_GET['status']) ? 'btn-primary' : 'btn-outline-primary' ?>">Semua</a>
        </div>
      </div>
    </div>

    <?php if (empty($requests)): ?>
      <div class="card">
        <div class="card-body text-center py-5">
          <i class="bi bi-journal-x display-1 text-muted"></i>
          <p class="text-muted mt-3">Tidak ada request<?= $_GET['status'] ? ' dengan status '.htmlspecialchars($_GET['status']) : '' ?></p>
        </div>
      </div>
    <?php else: ?>
      <div class="card">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>Siswa</th>
                  <th>Judul Buku</th>
                  <th>Tgl Request</th>
                  <th>Status</th>
                  <th style="width: 180px;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($requests as $req): ?>
                  <tr>
                    <td>
                      <strong><?= htmlspecialchars($req['member_name'] ?? '-') ?></strong>
                      <br><small class="text-muted">@<?= htmlspecialchars($req['username'] ?? '-') ?></small>
                    </td>
                    <td>
                      <strong><?= htmlspecialchars($req['title'] ?? '-') ?></strong>
                      <?php if (!empty($req['actual_item_code'])): ?>
                        <br><small class="text-muted">Kode: <?= htmlspecialchars($req['actual_item_code']) ?></small>
                      <?php endif; ?>
                    </td>
                    <td><?= date('d/m/Y H:i', strtotime($req['request_date'])) ?></td>
                    <td>
                      <?php
                        $status = $req['status'];
                        $badgeClass = match($status) {
                          'PENDING' => 'bg-warning text-dark',
                          'APPROVED' => 'bg-success',
                          'REJECTED' => 'bg-danger',
                          'CANCELLED' => 'bg-secondary',
                          default => 'bg-light text-dark'
                        };
                      ?>
                      <span class="badge status-badge <?= $badgeClass ?>"><?= $status ?></span>
                    </td>
                    <td>
                      <?php if ($status === 'PENDING'): ?>
                        <a href="index.php?r=requests/detail&id=<?= $req['request_id'] ?>" class="btn btn-primary btn-sm me-1" title="Detail & Proses">
                          <i class="bi bi-eye"></i>
                        </a>
                      <?php else: ?>
                        <a href="index.php?r=requests/detail&id=<?= $req['request_id'] ?>" class="btn btn-outline-secondary btn-sm" title="Detail">
                          <i class="bi bi-eye"></i>
                        </a>
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
  </main>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>