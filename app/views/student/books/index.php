<?php $student = $_SESSION['student'] ?? null; ?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Katalog Buku - <?= APP_NAME ?? 'Perpustakaan SMKN 9 Semarang' ?></title>
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
  .book-card { transition: transform .2s, box-shadow .2s; border: 1px solid rgba(0,0,0,.06); }
  .book-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,.1); }
  .badge-available { background: #198754; }
  .badge-unavailable { background: #dc3545; }
  .search-highlight { background: #fff3cd; padding: 2px 4px; border-radius: 3px; }
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
      <a class="nav-link active" href="index.php?r=student/books/index"><i class="bi bi-book me-2"></i>Katalog Buku</a>
      <a class="nav-link" href="index.php?r=student/requests/index"><i class="bi bi-journal-plus me-2"></i>Request Peminjaman</a>
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
        <a class="nav-link active" href="index.php?r=student/books/index">Katalog Buku</a>
        <a class="nav-link" href="index.php?r=student/requests/index">Request Peminjaman</a>
        <a class="nav-link" href="index.php?r=student/profile">Profil Saya</a>
      </div>
    </div>
  </div>

  <main class="content flex-grow-1">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="mb-0"><i class="bi bi-book me-2"></i>Katalog Buku</h4>
      <form method="GET" class="d-flex gap-2" style="max-width: 400px;">
        <input type="hidden" name="r" value="student/books">
        <input type="search" name="q" class="form-control form-control-sm" placeholder="Cari judul, ISBN, penulis..." value="<?= htmlspecialchars($q ?? '') ?>">
        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i></button>
      </form>
    </div>

    <?php if (!empty($_SESSION['flash'])): ?>
      <div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show" role="alert">
        <?= $_SESSION['flash']['msg'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <?php if (empty($rows)): ?>
      <div class="text-center py-5">
        <i class="bi bi-book display-1 text-muted"></i>
        <p class="text-muted mt-3">Tidak ada buku ditemukan<?= $q ? ' untuk pencarian "'.htmlspecialchars($q).'"' : '' ?></p>
      </div>
    <?php else: ?>
      <div class="row g-3">
        <?php foreach ($rows as $book): ?>
          <div class="col-12 col-sm-6 col-md-4 col-lg-3">
            <div class="card book-card h-100">
              <div class="card-body d-flex flex-column">
                <h6 class="card-title fw-bold"><?= htmlspecialchars($book['title'] ?? '-') ?></h6>
                <div class="small text-muted mb-2">
                  <?php if (!empty($book['isbn_issn'])): ?>
                    <div>ISBN: <?= htmlspecialchars($book['isbn_issn']) ?></div>
                  <?php endif; ?>
                  <?php if (!empty($book['publish_year'])): ?>
                    <div>Tahun: <?= htmlspecialchars($book['publish_year']) ?></div>
                  <?php endif; ?>
                  <?php if (!empty($book['classification'])): ?>
                    <div>Klasifikasi: <?= htmlspecialchars($book['classification']) ?></div>
                  <?php endif; ?>
                </div>
                <div class="mt-auto pt-2">
                  <?php $avail = $book['available_copies'] ?? 0; ?>
                  <span class="badge <?= $avail > 0 ? 'badge-available' : 'badge-unavailable' ?> me-1">
                    <?= $avail > 0 ? 'Tersedia ('.$avail.')' : 'Tidak Tersedia' ?>
                  </span>
                  <div class="d-flex flex-column gap-2 mt-2">
                    <a href="index.php?r=student/books/show&id=<?= $book['biblio_id'] ?>" class="btn btn-outline-primary btn-sm w-100">
                      <i class="bi bi-eye me-1"></i> Detail & Request
                    </a>
                    <button type="button" class="btn btn-outline-success btn-sm w-100" onclick="return false;" style="cursor: default;" title="Fitur dalam pengembangan">
                      <i class="bi bi-book me-1"></i> Baca Online
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>