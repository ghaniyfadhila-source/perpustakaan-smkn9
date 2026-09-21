<?php
if (!Auth::check()) { redirect('login/index'); return; }
$user = Auth::user();
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Detail Request - <?= APP_NAME ?? 'Perpustakaan SMKN 9 Semarang' ?></title>
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
      <div>
        <a href="index.php?r=requests/index" class="btn btn-outline-secondary btn-sm mb-2"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
        <h4 class="mb-0"><i class="bi bi-journal-check me-2"></i>Detail Request</h4>
      </div>
    </div>

    <?php if (!empty($_SESSION['flash'])): ?>
      <div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show" role="alert">
        <?= $_SESSION['flash']['msg'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <div class="row g-4">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Informasi Request</h5>
            <?php
              $status = $request['status'];
              $badgeClass = match($status) {
                'PENDING' => 'bg-warning text-dark',
                'APPROVED' => 'bg-success',
                'REJECTED' => 'bg-danger',
                'CANCELLED' => 'bg-secondary',
                default => 'bg-light text-dark'
              };
            ?>
            <span class="badge status-badge <?= $badgeClass ?> fs-6"><?= $status ?></span>
          </div>
          <div class="card-body">
            <div class="row mb-3">
              <div class="col-md-6">
                <p class="mb-1"><strong>Siswa:</strong> <?= htmlspecialchars($request['member_name'] ?? '-') ?></p>
                <p class="mb-1"><strong>Username:</strong> @<?= htmlspecialchars($request['username'] ?? '-') ?></p>
                <p class="mb-1"><strong>ID Siswa:</strong> <?= htmlspecialchars($request['member_id'] ?? '-') ?></p>
              </div>
              <div class="col-md-6">
                <p class="mb-1"><strong>Tgl Request:</strong> <?= date('d/m/Y H:i', strtotime($request['request_date'])) ?></p>
                <?php if ($status === 'APPROVED' && !empty($request['approved_at'])): ?>
                  <p class="mb-1"><strong>Disetujui:</strong> <?= date('d/m/Y H:i', strtotime($request['approved_at'])) ?></p>
                <?php elseif ($status === 'REJECTED' && !empty($request['rejected_at'])): ?>
                  <p class="mb-1"><strong>Ditolak:</strong> <?= date('d/m/Y H:i', strtotime($request['rejected_at'])) ?></p>
                <?php endif; ?>
              </div>
            </div>
            <hr>
            <div class="row mb-3">
              <div class="col-md-6">
                <p class="mb-1"><strong>Judul Buku:</strong> <?= htmlspecialchars($request['title'] ?? '-') ?></p>
                <p class="mb-1"><strong>ISBN:</strong> <?= htmlspecialchars($request['isbn_issn'] ?? '-') ?></p>
                <p class="mb-1"><strong>Tahun:</strong> <?= htmlspecialchars($request['publish_year'] ?? '-') ?></p>
              </div>
              <div class="col-md-6">
                <p class="mb-1"><strong>Klasifikasi:</strong> <?= htmlspecialchars($request['classification'] ?? '-') ?></p>
                <p class="mb-1"><strong>Nomor Panggil:</strong> <?= htmlspecialchars($request['call_number'] ?? '-') ?></p>
                <?php if (!empty($request['actual_item_code'])): ?>
                  <p class="mb-1"><strong>Kode Eksemplar:</strong> <?= htmlspecialchars($request['actual_item_code']) ?></p>
                <?php endif; ?>
              </div>
            </div>
            <?php if ($status === 'REJECTED' && !empty($request['rejection_reason'])): ?>
              <div class="alert alert-danger">
                <strong>Alasan Penolakan:</strong><br>
                <?= nl2br(htmlspecialchars($request['rejection_reason'])) ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0"><i class="bi bi-box-seam me-2"></i>Eksemplar Tersedia</h6>
          </div>
          <div class="card-body">
            <?php if (empty($copies)): ?>
              <div class="text-center py-3">
                <i class="bi bi-x-circle display-1 text-danger"></i>
                <p class="text-muted mt-2">Tidak ada eksemplar tersedia</p>
              </div>
            <?php else: ?>
              <?php if ($status === 'PENDING'): ?>
                <form method="POST" action="index.php?r=requests/approve">
                  <input type="hidden" name="request_id" value="<?= $request['request_id'] ?>">
                  <div class="mb-3">
                    <label class="form-label">Pilih Eksemplar</label>
                    <select name="item_code" class="form-select form-select-sm" required>
                      <option value="">-- Pilih Eksemplar --</option>
                      <?php foreach ($copies as $copy): ?>
                        <option value="<?= htmlspecialchars($copy['item_code']) ?>">
                          <?= htmlspecialchars($copy['item_code']) ?>
                          (<?= htmlspecialchars($copy['location_name'] ?? $copy['location_id'] ?? '-') ?>
                          - <?= htmlspecialchars($copy['item_status_name'] ?? '-') ?>)
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <button type="submit" class="btn btn-success w-100 mb-2" onclick="return confirm('Setujui request ini?')">
                    <i class="bi bi-check-circle me-1"></i> Setujui & Buat Peminjaman
                  </button>
                </form>
                <hr>
                <form method="POST" action="index.php?r=requests/reject">
                  <input type="hidden" name="request_id" value="<?= $request['request_id'] ?>">
                  <div class="mb-3">
                    <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                    <textarea name="rejection_reason" class="form-control form-control-sm" rows="3" required placeholder="Masukkan alasan penolakan..."></textarea>
                  </div>
                  <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Tolak request ini?')">
                    <i class="bi bi-x-circle me-1"></i> Tolak Request
                  </button>
                </form>
              <?php else: ?>
                <ul class="list-group list-group-flush">
                  <?php foreach ($copies as $copy): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      <?= htmlspecialchars($copy['item_code']) ?>
                      <small class="text-muted"><?= htmlspecialchars($copy['location_name'] ?? $copy['location_id'] ?? '-') ?></small>
                    </li>
                  <?php endforeach; ?>
                </ul>
              <?php endif; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>