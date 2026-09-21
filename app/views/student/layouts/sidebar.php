<?php
$current = $_GET['r'] ?? 'student/dashboard/index';
function isActive($r, $current) { return str_starts_with($current, $r) ? 'active' : ''; }
?>
<div class="d-flex flex-1" style="min-height: 0; align-items: stretch;">

  <aside class="sidebar d-none d-lg-block p-3">
    <div class="mb-3 small text-muted">MENU SISWA</div>

    <div class="nav flex-column gap-1">
      <a class="nav-link <?= isActive('student/dashboard', $current) ?>" href="index.php?r=student/dashboard">
        <i class="bi bi-speedometer2 me-2"></i>Dashboard
      </a>

      <a class="nav-link <?= isActive('student/requests', $current) ?>" href="index.php?r=student/requests/index">
        <i class="bi bi-journal-plus me-2"></i>Request Peminjaman
      </a>

      <a class="nav-link <?= isActive('student/books', $current) ?>" href="index.php?r=student/books/index">
        <i class="bi bi-book me-2"></i>Katalog Buku
      </a>

      <a class="nav-link <?= isActive('student/profile', $current) ?>" href="index.php?r=student/profile">
        <i class="bi bi-person me-2"></i>Profil Saya
      </a>
    </div>
  </aside>

  <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title"><i class="bi bi-book-half me-2"></i><?= APP_NAME ?? 'Perpustakaan SMKN 9 Semarang' ?></h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
      <div class="nav flex-column gap-1">
        <a class="nav-link <?= isActive('student/dashboard', $current) ?>" href="index.php?r=student/dashboard"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
        <a class="nav-link <?= isActive('student/requests', $current) ?>" href="index.php?r=student/requests/index"><i class="bi bi-journal-plus me-2"></i>Request Peminjaman</a>
        <a class="nav-link <?= isActive('student/books', $current) ?>" href="index.php?r=student/books/index"><i class="bi bi-book me-2"></i>Katalog Buku</a>
        <a class="nav-link <?= isActive('student/profile', $current) ?>" href="index.php?r=student/profile"><i class="bi bi-person me-2"></i>Profil Saya</a>
      </div>
    </div>
  </div>

  <main class="content flex-grow-1">
