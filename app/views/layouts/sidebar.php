<?php
$current = $_GET['r'] ?? 'dashboard/index';
function isActive($r, $current) { return str_starts_with($current, $r) ? 'active' : ''; }
?>
<div class="d-flex">

  <!-- Sidebar desktop -->
  <aside class="sidebar d-none d-lg-block p-3">
    <div class="mb-3 small text-muted">MENU</div>

    <div class="nav flex-column gap-1">
      <a class="nav-link <?= isActive('dashboard', $current) ?>" href="index.php?r=dashboard/index">
        <i class="bi bi-speedometer2 me-2"></i>Dashboard
      </a>

      <a class="nav-link <?= isActive('books', $current) ?>" href="index.php?r=books/index">
        <i class="bi bi-journal-text me-2"></i>Katalog Buku
      </a>

      <a class="nav-link <?= isActive('loans/borrow', $current) ?>" href="index.php?r=loans/borrow">
        <i class="bi bi-box-arrow-in-right me-2"></i>Peminjaman
      </a>

      <a class="nav-link <?= isActive('loans/returnBook', $current) ?>" href="index.php?r=loans/returnBook">
        <i class="bi bi-box-arrow-left me-2"></i>Pengembalian
      </a>

      <a class="nav-link <?= isActive('members', $current) ?>" href="index.php?r=members/index">
        <i class="bi bi-people me-2"></i>Anggota
      </a>

      <a class="nav-link <?= isActive('stock', $current) ?>" href="index.php?r=stock/scan">
        <i class="bi bi-upc-scan me-2"></i>Stock Scan
      </a>

      <a class="nav-link <?= isActive('master', $current) ?>" href="index.php?r=master/index">
        <i class="bi bi-database me-2"></i>Master Data
      </a>

      <a class="nav-link <?= isActive('reports', $current) ?>" href="index.php?r=reports/index">
        <i class="bi bi-graph-up-arrow me-2"></i>Laporan
      </a>

      <a class="nav-link <?= isActive('wa/schedule', $current) ?>" href="index.php?r=wa/schedule">
  <i class="bi bi-calendar2-week me-2"></i>WA Jadwal Besok
</a>

<a class="nav-link <?= isActive('wa/blast', $current) ?>" href="index.php?r=wa/blast">
  <i class="bi bi-megaphone me-2"></i>WA Kirim Sekarang
</a>

      <?php if (ACL::isAdmin()): ?>
  <div class="text-uppercase text-muted small mt-4 mb-2 px-3">Admin</div>

  <ul class="nav nav-pills flex-column mb-2">
    <li class="nav-item">
      <a class="nav-link <?= isActive('users', $current) ?>" href="index.php?r=users/index">
        <i class="bi bi-person-gear me-2"></i>Manajemen User
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link <?= isActive('system', $current) ?>" href="index.php?r=system/fineRules">
        <i class="bi bi-cash-coin me-2"></i>Atur Denda
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link <?= isActive('logs', $current) ?>" href="index.php?r=logs/index">
        <i class="bi bi-journal-text me-2"></i>System Log
      </a>
    </li>
  </ul>
<?php endif; ?>

    </div>
  </aside>

  <!-- Sidebar mobile: offcanvas -->
  <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title"><i class="bi bi-book-half me-2"></i><?= APP_NAME ?></h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
      <div class="nav flex-column gap-1">
        <a class="nav-link <?= isActive('dashboard', $current) ?>" href="index.php?r=dashboard/index"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
        <a class="nav-link <?= isActive('books', $current) ?>" href="index.php?r=books/index"><i class="bi bi-journal-text me-2"></i>Katalog Buku</a>
        <a class="nav-link <?= isActive('loans/borrow', $current) ?>" href="index.php?r=loans/borrow"><i class="bi bi-box-arrow-in-right me-2"></i>Peminjaman</a>
        <a class="nav-link <?= isActive('loans/returnBook', $current) ?>" href="index.php?r=loans/returnBook"><i class="bi bi-box-arrow-left me-2"></i>Pengembalian</a>
        <a class="nav-link <?= isActive('members', $current) ?>" href="index.php?r=members/index"><i class="bi bi-people me-2"></i>Anggota</a>
        <a class="nav-link <?= isActive('stock', $current) ?>" href="index.php?r=stock/scan"><i class="bi bi-upc-scan me-2"></i>Stock Scan</a>
        <a class="nav-link <?= isActive('master', $current) ?>" href="index.php?r=master/index"><i class="bi bi-database me-2"></i>Master Data</a>
        <a class="nav-link <?= isActive('reports', $current) ?>" href="index.php?r=reports/index"><i class="bi bi-graph-up-arrow me-2"></i>Laporan</a>
        <a class="nav-link <?= isActive('wa/schedule', $current) ?>" href="index.php?r=wa/schedule"><i class="bi bi-calendar2-week me-2"></i>WA Jadwal Besok</a>
        <a class="nav-link <?= isActive('wa/blast', $current) ?>" href="index.php?r=wa/blast"><i class="bi bi-megaphone me-2"></i>WA Kirim Sekarang</a>
        
        <?php if (ACL::isAdmin()): ?>
          <hr>
          <div class="text-uppercase text-muted small px-3 mb-2">Admin</div>
          <a class="nav-link <?= isActive('users', $current) ?>" href="index.php?r=users/index"><i class="bi bi-person-gear me-2"></i>Manajemen User</a>
          <a class="nav-link <?= isActive('system', $current) ?>" href="index.php?r=system/fineRules"><i class="bi bi-cash-coin me-2"></i>Atur Denda</a>
          <a class="nav-link <?= isActive('logs', $current) ?>" href="index.php?r=logs/index"><i class="bi bi-journal-text me-2"></i>System Log</a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Content -->
  <main class="content">
