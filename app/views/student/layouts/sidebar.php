<?php
$current = $_GET['r'] ?? 'student/dashboard/index';
if (!function_exists('isActive')) {
  function isActive($r, $current) { return str_starts_with($current, $r) ? 'active' : ''; }
}

$navItems = [
  ['r' => 'student/dashboard', 'icon' => 'bi-speedometer2',  'label' => 'Dashboard',          'href' => 'student/dashboard'],
  ['r' => 'student/requests',  'icon' => 'bi-journal-plus',  'label' => 'Request Peminjaman',  'href' => 'student/requests/index'],
  ['r' => 'student/books',       'icon' => 'bi-book',          'label' => 'Katalog Buku',        'href' => 'student/books/index'],
  ['r' => 'student/digital',     'icon' => 'bi-journal-richtext', 'label' => 'Karya Guru dan Siswa', 'href' => 'student/digital/index'],
  ['r' => 'baca_online',         'icon' => 'bi-book-half', 'label' => 'Baca Online', 'href' => 'https://script.google.com/macros/s/AKfycbwrMuLlP_CrhJo0VzsbvWozntpYWbS6lISNQD1WcvZl055pcR5C-QjT_23xld_FRDVZxQ/exec', 'external' => true],
  ['r' => 'student/profile',     'icon' => 'bi-person-circle', 'label' => 'Profil Saya',         'href' => 'student/profile'],
];
?>

<!-- SIDEBAR DESKTOP -->
<aside class="app-sidebar d-none d-lg-flex flex-column">
  <div class="sidebar-section-label" style="margin-top: 0;">Menu Siswa</div>

  <?php foreach ($navItems as $item): ?>
    <a class="sidebar-link <?= isActive($item['r'], $current) ?>"
       href="<?= isset($item['external']) && $item['external'] ? htmlspecialchars($item['href']) : 'index.php?r=' . $item['href'] ?>"
       <?= isset($item['external']) && $item['external'] ? 'target="_blank" rel="noopener noreferrer"' : '' ?>>
      <i class="bi <?= $item['icon'] ?>"></i>
      <?= $item['label'] ?>
    </a>
  <?php endforeach; ?>
</aside>

<!-- OFFCANVAS MOBILE -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title fw-bold" style="font-size: 0.95rem;">
      <i class="bi bi-book-half me-2" style="color: var(--accent);"></i>Menu Siswa
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
  </div>
  <div class="offcanvas-body">
    <?php foreach ($navItems as $item): ?>
      <a class="sidebar-link <?= isActive($item['r'], $current) ?>"
         href="<?= isset($item['external']) && $item['external'] ? htmlspecialchars($item['href']) : 'index.php?r=' . $item['href'] ?>"
         <?= isset($item['external']) && $item['external'] ? 'target="_blank" rel="noopener noreferrer"' : '' ?>>
        <i class="bi <?= $item['icon'] ?>"></i>
        <?= $item['label'] ?>
      </a>
    <?php endforeach; ?>
  </div>
</div>

<!-- MAIN CONTENT -->
<main class="flex-grow-1" style="min-width: 0; padding: 1.5rem;">
