<?php $student = $_SESSION['student'] ?? null; ?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Request Peminjaman - <?= APP_NAME ?? 'Perpustakaan SMKN 9 Semarang' ?></title>
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
      <small class="text-muted">Dashboard Siswa</small>
    </div>
  </div>
  <div class="ms-auto d-flex align-items-center gap-3">
    <div class="d-flex align-items-center gap-2 text-muted small">
      <i class="bi bi-person-circle fs-5"></i>
      <span class="fw-semibold"><?= htmlspecialchars($student['student_name'] ?? '') ?></span>
    </div>
    <a class="btn btn-outline-danger btn-sm rounded-pill px-3" href="index.php?r=student/logout">
      <i class="bi bi-box-arrow-right me-1"></i> Logout
    </a>
  </div>
</nav>

<div class="d-flex flex-1" style="min-height: 0; align-items: stretch;">
  <aside class="sidebar d-none d-lg-block p-3">
    <div class="mb-3 small text-muted">MENU SISWA</div>
    <div class="nav flex-column gap-1">
      <a class="nav-link" href="index.php?r=student/dashboard"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
      <a class="nav-link" href="index.php?r=student/books/index"><i class="bi bi-book me-2"></i>Katalog Buku</a>
      <a class="nav-link active" href="index.php?r=student/requests/index"><i class="bi bi-journal-plus me-2"></i>Request Peminjaman</a>
      <a class="nav-link" href="index.php?r=student/profile"><i class="bi bi-person me-2"></i>Profil Saya</a>
    </div>
  </aside>

  <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title"><i class="bi bi-book-half me-2"></i><?= APP_NAME ?? 'Perpustakaan SMKN 9 Semarang' ?></h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
      <div class="nav flex-column gap-1">
        <a class="nav-link" href="index.php?r=student/dashboard">Dashboard</a>
        <a class="nav-link" href="index.php?r=student/books/index">Katalog Buku</a>
        <a class="nav-link active" href="index.php?r=student/requests/index">Request Peminjaman</a>
        <a class="nav-link" href="index.php?r=student/profile">Profil Saya</a>
      </div>
    </div>
  </div>

  <main class="content flex-grow-1">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="mb-0"><i class="bi bi-journal-plus me-2"></i>Request Peminjaman Saya</h4>
      <a href="index.php?r=student/books/index" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-1"></i> Ajukan Baru</a>
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
          <a href="index.php?r=student/requests/index" class="btn btn-sm <?= empty($_GET['status']) ? 'btn-primary' : 'btn-outline-primary' ?>">Semua</a>
          <a href="index.php?r=student/requests/index&status=PENDING" class="btn btn-sm <?= ($_GET['status']??'')==='PENDING' ? 'btn-warning' : 'btn-outline-warning' ?>">Menunggu</a>
          <a href="index.php?r=student/requests/index&status=APPROVED" class="btn btn-sm <?= ($_GET['status']??'')==='APPROVED' ? 'btn-success' : 'btn-outline-success' ?>">Disetujui</a>
          <a href="index.php?r=student/requests/index&status=REJECTED" class="btn btn-sm <?= ($_GET['status']??'')==='REJECTED' ? 'btn-danger' : 'btn-outline-danger' ?>">Ditolak</a>
          <a href="index.php?r=student/requests/index&status=CANCELLED" class="btn btn-sm <?= ($_GET['status']??'')==='CANCELLED' ? 'btn-secondary' : 'btn-outline-secondary' ?>">Dibatalkan</a>
        </div>
      </div>
    </div>

    <?php if (empty($requests)): ?>
      <div class="card">
        <div class="card-body text-center py-5">
          <i class="bi bi-journal-x display-1 text-muted"></i>
          <p class="text-muted mt-3">Belum ada request peminjaman</p>
          <a href="index.php?r=student/books/index" class="btn btn-primary"><i class="bi bi-book me-1"></i> Cari Buku & Ajukan Request</a>
        </div>
      </div>
    <?php else: ?>
      <div class="card">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>Judul Buku</th>
                  <th>Tgl Request</th>
                  <th>Status</th>
                  <th style="width: 150px;">Aksi</th>
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
                      <?php if ($status === 'APPROVED' && !empty($req['approved_at'])): ?>
                        <br><small class="text-muted">Disetujui: <?= date('d/m/Y H:i', strtotime($req['approved_at'])) ?></small>
                      <?php elseif ($status === 'REJECTED' && !empty($req['rejected_at'])): ?>
                        <br><small class="text-muted">Ditolak: <?= date('d/m/Y H:i', strtotime($req['rejected_at'])) ?></small>
                        <?php if (!empty($req['rejection_reason'])): ?>
                          <br><small class="text-danger"><?= htmlspecialchars($req['rejection_reason']) ?></small>
                        <?php endif; ?>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if ($status === 'PENDING'): ?>
                        <form method="POST" action="index.php?r=student/requests/cancel" class="d-inline" onsubmit="return confirm('Batalkan request ini?')">
                          <input type="hidden" name="request_id" value="<?= $req['request_id'] ?>">
                          <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-x-circle me-1"></i> Batalkan
                          </button>
                        </form>
                      <?php elseif ($status === 'APPROVED'): ?>
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
  </main>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>