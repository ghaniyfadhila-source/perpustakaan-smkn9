<?php
$current = $_GET['r'] ?? 'dashboard/index';
function isActive($r, $current) { return str_starts_with($current, $r) ? 'active' : ''; }

$navItems = [
  ['r' => 'dashboard', 'icon' => 'bi-speedometer2', 'label' => 'Dashboard', 'href' => 'dashboard/index'],
  ['r' => 'books',     'icon' => 'bi-journal-bookmark', 'label' => 'Katalog Buku', 'href' => 'books/index'],
  ['r' => 'members',   'icon' => 'bi-people', 'label' => 'Anggota', 'href' => 'members/index'],
  ['r' => 'stock',     'icon' => 'bi-upc-scan', 'label' => 'Stock Scan', 'href' => 'stock/scan'],
  ['r' => 'master',    'icon' => 'bi-database', 'label' => 'Master Data', 'href' => 'master/index'],
  ['r' => 'digital_works', 'icon' => 'bi-journal-richtext', 'label' => 'Karya Guru dan Murid', 'href' => 'digital_works/index'],
  ['r' => 'baca_online', 'icon' => 'bi-book-half', 'label' => 'Baca Online', 'href' => 'https://script.google.com/macros/s/AKfycbwrMuLlP_CrhJo0VzsbvWozntpYWbS6lISNQD1WcvZl055pcR5C-QjT_23xld_FRDVZxQ/exec', 'external' => true],
  ['r' => 'reports',   'icon' => 'bi-graph-up-arrow', 'label' => 'Laporan', 'href' => 'reports/index'],
  ['r' => 'wa/schedule', 'icon' => 'bi-calendar2-week', 'label' => 'WA Jadwal', 'href' => 'wa/schedule'],
  ['r' => 'wa/blast',  'icon' => 'bi-megaphone', 'label' => 'WA Blast', 'href' => 'wa/blast'],
];

$adminItems = [
  ['r' => 'users',  'icon' => 'bi-person-gear', 'label' => 'Manajemen User', 'href' => 'users/index'],
  ['r' => 'system', 'icon' => 'bi-cash-coin',   'label' => 'Atur Denda',     'href' => 'system/fineRules'],
  ['r' => 'logs',   'icon' => 'bi-journal-code', 'label' => 'System Log',    'href' => 'logs/index'],
];
?>

<style>
  /* =============================================
     SIDEBAR
  ============================================= */
  .app-sidebar {
    width: var(--sidebar-width);
    min-height: calc(100vh - var(--navbar-height));
    background: var(--bg-sidebar);
    backdrop-filter: var(--glass-blur);
    -webkit-backdrop-filter: var(--glass-blur);
    border-right: 1px solid var(--border);
    padding: 1.25rem 0.875rem;
    flex-shrink: 0;
    position: sticky;
    top: var(--navbar-height);
    height: calc(100vh - var(--navbar-height));
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 2px;
  }

  .sidebar-section-label {
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: var(--text-muted);
    padding: 0 0.75rem;
    margin: 1rem 0 0.4rem;
  }

  .sidebar-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 12px;
    border-radius: var(--radius-sm);
    font-size: 0.86rem;
    font-weight: 500;
    color: var(--text-secondary);
    text-decoration: none;
    transition: var(--transition);
    position: relative;
  }

  .sidebar-link:hover {
    background: rgba(99,102,241,0.06);
    color: var(--text-primary);
  }

  .sidebar-link.active {
    background: var(--accent-soft);
    color: var(--accent);
    font-weight: 600;
  }

  .sidebar-link.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 20%;
    height: 60%;
    width: 3px;
    background: var(--accent);
    border-radius: 0 3px 3px 0;
  }

  .sidebar-link i {
    font-size: 1rem;
    width: 20px;
    text-align: center;
    flex-shrink: 0;
  }

  .sidebar-divider {
    height: 1px;
    background: var(--border);
    margin: 0.75rem 0;
  }

  /* Offcanvas for mobile */
  .offcanvas {
    width: 280px !important;
    background: rgba(255,255,255,0.96) !important;
    backdrop-filter: var(--glass-blur);
    -webkit-backdrop-filter: var(--glass-blur);
    border-right: 1px solid var(--border) !important;
  }

  .offcanvas-header {
    border-bottom: 1px solid var(--border) !important;
    padding: 1rem 1.25rem !important;
  }

  .offcanvas-body {
    padding: 1rem 0.875rem !important;
  }
</style>

<!-- ===== SIDEBAR DESKTOP ===== -->
<aside class="app-sidebar d-none d-lg-flex flex-column">
  <div class="sidebar-section-label">Menu Utama</div>

  <?php foreach ($navItems as $item): ?>
    <a class="sidebar-link <?= isActive($item['r'], $current) ?>"
       href="<?= isset($item['external']) && $item['external'] ? htmlspecialchars($item['href']) : 'index.php?r=' . $item['href'] ?>"
       <?= isset($item['external']) && $item['external'] ? 'target="_blank" rel="noopener noreferrer"' : '' ?>>
      <i class="bi <?= $item['icon'] ?>"></i>
      <?= $item['label'] ?>
    </a>
  <?php endforeach; ?>

  <?php if (ACL::isAdmin()): ?>
    <div class="sidebar-divider"></div>
    <div class="sidebar-section-label">Admin</div>
    <?php foreach ($adminItems as $item): ?>
      <a class="sidebar-link <?= isActive($item['r'], $current) ?>"
         href="<?= isset($item['external']) && $item['external'] ? htmlspecialchars($item['href']) : 'index.php?r=' . $item['href'] ?>"
         <?= isset($item['external']) && $item['external'] ? 'target="_blank" rel="noopener noreferrer"' : '' ?>>
        <i class="bi <?= $item['icon'] ?>"></i>
        <?= $item['label'] ?>
      </a>
    <?php endforeach; ?>
  <?php endif; ?>
</aside>

<!-- ===== OFFCANVAS MOBILE ===== -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title fw-bold" style="font-size: 0.95rem;">
      <i class="bi bi-book-half me-2" style="color: var(--accent);"></i><?= APP_NAME ?>
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
  </div>
  <div class="offcanvas-body">
    <div class="sidebar-section-label" style="padding: 0 0.25rem; margin-top: 0;">Menu Utama</div>
    <?php foreach ($navItems as $item): ?>
      <a class="sidebar-link <?= isActive($item['r'], $current) ?>"
         href="<?= isset($item['external']) && $item['external'] ? htmlspecialchars($item['href']) : 'index.php?r=' . $item['href'] ?>"
         <?= isset($item['external']) && $item['external'] ? 'target="_blank" rel="noopener noreferrer"' : '' ?>>
        <i class="bi <?= $item['icon'] ?>"></i>
        <?= $item['label'] ?>
      </a>
    <?php endforeach; ?>

    <?php if (ACL::isAdmin()): ?>
      <div class="sidebar-divider"></div>
      <div class="sidebar-section-label" style="padding: 0 0.25rem;">Admin</div>
      <?php foreach ($adminItems as $item): ?>
        <a class="sidebar-link <?= isActive($item['r'], $current) ?>"
           href="<?= isset($item['external']) && $item['external'] ? htmlspecialchars($item['href']) : 'index.php?r=' . $item['href'] ?>"
           <?= isset($item['external']) && $item['external'] ? 'target="_blank" rel="noopener noreferrer"' : '' ?>>
          <i class="bi <?= $item['icon'] ?>"></i>
          <?= $item['label'] ?>
        </a>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<!-- ===== MAIN CONTENT AREA ===== -->
<main class="flex-grow-1" style="min-width: 0; padding: 1.5rem;">
