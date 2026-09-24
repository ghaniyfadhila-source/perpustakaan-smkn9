<?php $student = $_SESSION['student'] ?? null; ?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= APP_NAME ?? 'Perpustakaan SMKN 9 Semarang' ?> — Siswa</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <style>
    :root {
      --white: #ffffff;
      --bg-page: #f0f4f8;
      --bg-sidebar: rgba(255, 255, 255, 0.75);
      --bg-navbar: rgba(255, 255, 255, 0.85);
      --glass-blur: blur(24px) saturate(180%);

      --accent: #6366f1;
      --accent-soft: rgba(99, 102, 241, 0.08);
      --accent-border: rgba(99, 102, 241, 0.22);
      --accent-hover: #4f46e5;

      --teal: #0ea5e9;
      --teal-soft: rgba(14, 165, 233, 0.08);

      --text-primary: #1e293b;
      --text-secondary: #64748b;
      --text-muted: #94a3b8;

      --border: rgba(0, 0, 0, 0.06);
      --border-strong: rgba(0, 0, 0, 0.1);
      --shadow-sm: 0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
      --shadow-md: 0 4px 16px rgba(0,0,0,.07), 0 1px 4px rgba(0,0,0,.04);
      --shadow-lg: 0 20px 40px rgba(0,0,0,.08), 0 4px 16px rgba(0,0,0,.05);

      --radius-sm: 10px;
      --radius-md: 16px;
      --radius-lg: 22px;
      --radius-xl: 28px;

      --sidebar-width: 240px;
      --navbar-height: 68px;
      --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--bg-page);
      color: var(--text-primary);
      min-height: 100vh;
      overflow-x: hidden;
      -webkit-font-smoothing: antialiased;
    }

    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background:
        radial-gradient(ellipse 700px 500px at 0% 0%, rgba(99,102,241,.07), transparent 55%),
        radial-gradient(ellipse 600px 400px at 100% 100%, rgba(14,165,233,.06), transparent 55%),
        radial-gradient(ellipse 500px 350px at 55% 40%, rgba(168,85,247,.04), transparent 60%);
      pointer-events: none;
      z-index: 0;
    }

    /* NAVBAR */
    .app-navbar {
      position: fixed;
      top: 0; left: 0; right: 0;
      height: var(--navbar-height);
      background: var(--bg-navbar);
      backdrop-filter: var(--glass-blur);
      -webkit-backdrop-filter: var(--glass-blur);
      border-bottom: 1px solid var(--border);
      box-shadow: var(--shadow-sm);
      display: flex;
      align-items: center;
      padding: 0 1.5rem;
      gap: 1rem;
      z-index: 1000;
    }

    .navbar-brand-wrap {
      display: flex;
      align-items: center;
      gap: 10px;
      text-decoration: none;
    }

    .navbar-logo {
      height: 38px;
      width: 38px;
      object-fit: contain;
      border-radius: 10px;
    }

    .navbar-brand-text strong {
      display: block;
      font-size: 0.88rem;
      font-weight: 700;
      color: var(--text-primary);
      line-height: 1.2;
    }

    .navbar-brand-text small {
      font-size: 0.72rem;
      color: var(--text-muted);
      line-height: 1;
    }

    .navbar-divider {
      width: 1px;
      height: 28px;
      background: var(--border-strong);
      flex-shrink: 0;
    }

    .navbar-mobile-toggle {
      display: none;
      align-items: center;
      justify-content: center;
      width: 38px;
      height: 38px;
      border: 1px solid var(--border-strong);
      border-radius: var(--radius-sm);
      background: rgba(255,255,255,0.7);
      color: var(--text-secondary);
      cursor: pointer;
      transition: var(--transition);
    }

    .navbar-mobile-toggle:hover {
      background: var(--accent-soft);
      border-color: var(--accent-border);
      color: var(--accent);
    }

    @media (max-width: 991.98px) {
      .navbar-mobile-toggle { display: flex; }
    }

    .navbar-spacer { flex: 1; }

    .navbar-user-area {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .navbar-user-chip {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 6px 14px;
      background: rgba(255,255,255,0.7);
      border: 1px solid var(--border-strong);
      border-radius: 999px;
      font-size: 0.82rem;
      font-weight: 500;
      color: var(--text-secondary);
    }

    .navbar-user-chip .avatar-dot {
      width: 28px;
      height: 28px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--teal), var(--accent));
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 0.7rem;
      font-weight: 700;
    }

    .btn-logout {
      display: flex;
      align-items: center;
      gap: 6px;
      padding: 7px 16px;
      border-radius: 999px;
      font-size: 0.82rem;
      font-weight: 600;
      color: #ef4444;
      background: rgba(239, 68, 68, 0.07);
      border: 1px solid rgba(239, 68, 68, 0.2);
      text-decoration: none;
      transition: var(--transition);
      white-space: nowrap;
    }

    .btn-logout:hover {
      background: #ef4444;
      color: white;
      box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);
    }

    /* LAYOUT */
    .app-layout {
      display: flex;
      padding-top: var(--navbar-height);
      min-height: 100vh;
      position: relative;
      z-index: 1;
    }

    /* SIDEBAR */
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

    /* Cards */
    .card {
      border: 1px solid var(--border) !important;
      border-radius: var(--radius-md) !important;
      background: rgba(255,255,255,0.8) !important;
      backdrop-filter: blur(16px) saturate(160%);
      -webkit-backdrop-filter: blur(16px) saturate(160%);
      box-shadow: var(--shadow-sm) !important;
      transition: var(--transition);
    }

    .card:hover {
      box-shadow: var(--shadow-md) !important;
    }

    .card-header {
      background: rgba(255,255,255,0.5) !important;
      border-bottom: 1px solid var(--border) !important;
      font-weight: 600;
      color: var(--text-primary);
      border-radius: var(--radius-md) var(--radius-md) 0 0 !important;
      padding: 1rem 1.25rem !important;
    }

    .card-body { padding: 1.25rem !important; }

    .table { font-size: 0.88rem; color: var(--text-primary); }

    .table th {
      font-weight: 600;
      font-size: 0.78rem;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      color: var(--text-secondary);
      border-bottom: 1px solid var(--border-strong) !important;
      padding: 0.75rem 1rem !important;
      background: transparent !important;
    }

    .table td {
      padding: 0.75rem 1rem !important;
      border-bottom: 1px solid var(--border) !important;
      vertical-align: middle;
    }

    .btn {
      font-weight: 500;
      font-size: 0.85rem;
      border-radius: var(--radius-sm) !important;
      transition: var(--transition);
    }

    .btn-primary {
      background: var(--accent) !important;
      border-color: var(--accent) !important;
      box-shadow: 0 2px 8px rgba(99,102,241,0.25);
    }

    .btn-primary:hover {
      background: var(--accent-hover) !important;
      border-color: var(--accent-hover) !important;
      transform: translateY(-1px);
    }

    .form-control, .form-select {
      border: 1px solid var(--border-strong) !important;
      border-radius: var(--radius-sm) !important;
      background: rgba(255,255,255,0.8) !important;
      color: var(--text-primary) !important;
      font-size: 0.9rem;
      padding: 0.55rem 0.85rem !important;
      transition: var(--transition);
    }

    .form-control:focus, .form-select:focus {
      border-color: var(--accent) !important;
      box-shadow: 0 0 0 3px rgba(99,102,241,0.12) !important;
      background: white !important;
      outline: none !important;
    }

    .form-label {
      font-size: 0.83rem;
      font-weight: 600;
      color: var(--text-secondary);
      margin-bottom: 5px;
    }

    .badge {
      font-weight: 500;
      font-size: 0.73rem;
      padding: 4px 9px;
      border-radius: 6px;
    }

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

    ::-webkit-scrollbar { width: 5px; height: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.12); border-radius: 9px; }
    ::-webkit-scrollbar-thumb:hover { background: rgba(0,0,0,0.22); }

    .content-inner {
      flex: 1;
      min-width: 0;
      padding: 1.5rem;
    }

    @media (max-width: 991.98px) {
      .content-inner { padding: 1rem; }
    }
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="app-navbar">
  <a class="navbar-brand-wrap" href="index.php?r=student/dashboard">
    <img src="<?= BASE_URL ?>/app/image/SMKN9.png" class="navbar-logo" alt="Logo SMKN9">
    <div class="navbar-brand-text">
      <strong><?= APP_NAME ?? 'Perpustakaan SMKN 9' ?></strong>
      <small>Portal Siswa</small>
    </div>
  </a>

  <div class="navbar-divider d-none d-lg-block"></div>

  <button class="navbar-mobile-toggle d-lg-none"
          type="button"
          data-bs-toggle="offcanvas"
          data-bs-target="#offcanvasSidebar"
          aria-label="Buka menu">
    <i class="bi bi-list fs-5"></i>
  </button>

  <div class="navbar-spacer"></div>

  <div class="navbar-user-area">
    <div class="navbar-user-chip d-none d-sm-flex">
      <div class="avatar-dot"><?= strtoupper(substr($student['student_name'] ?? 'S', 0, 1)) ?></div>
      <span><?= htmlspecialchars($student['student_name'] ?? '') ?></span>
    </div>
    <a class="btn-logout" href="index.php?r=student/logout">
      <i class="bi bi-box-arrow-right"></i>
      <span class="d-none d-sm-inline">Logout</span>
    </a>
  </div>
</nav>

<!-- LAYOUT -->
<div class="app-layout">