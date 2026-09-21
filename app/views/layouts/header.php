<?php $user = Auth::user(); ?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= APP_NAME ?></title>

  <!-- Bootstrap 5 CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  <style>
    body { background: #f6f7fb; }

    .sidebar {
      width: 260px;
      min-height: 100vh;
      background: #fff;
      border-right: 1px solid rgba(0,0,0,.08);
    }

    .sidebar .nav-link { color: #333; border-radius: .6rem; }
    .sidebar .nav-link:hover { background: #f1f3f5; }
    .sidebar .nav-link.active { background: #0d6efd; color: #fff; }

    .content { padding: 1.25rem; width: 100%; }

    /* ===== NAVBAR MODERN ===== */
    .navbar-modern{
      height:70px;
      background:white;
      border-bottom:1px solid #e5e7eb;
      box-shadow:0 4px 15px rgba(0,0,0,.05);
    }

    .brand-box{
      width:42px;
      height:42px;
      border-radius:12px;
      background:linear-gradient(135deg,#6366f1,#06b6d4);
      color:white;
      display:flex;
      align-items:center;
      justify-content:center;
      font-size:20px;
    }

  </style>
</head>
<body>

<!-- ===== NAVBAR UPDATED ===== -->
<nav class="navbar navbar-modern px-2 px-md-4">

  <!-- LEFT -->
  <div class="d-flex align-items-center gap-3">

    <!-- Mobile toggle -->
    <button class="btn btn-light d-lg-none"
            type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#offcanvasSidebar">
      <i class="bi bi-list fs-5"></i>
    </button>

    <!-- Brand icon -->
    <div class="text-center py-2 border-bottom">
    <img src="<?= BASE_URL ?>/app/image/SMKN9.png" class="logo" alt="Logo Sekolah"
       alt="Logo"
       class="img-fluid"
       style="max-height: 50px;">
</div>

    <!-- Brand text -->
    <div>
      <div class="fw-bold text-dark" style="line-height:1;">
        <?= APP_NAME ?>
      </div>
      <small class="text-muted">Library Management System</small>
    </div>

  </div>

  <!-- RIGHT -->
  <div class="ms-auto d-flex align-items-center gap-3">

    <div class="d-flex align-items-center gap-2 text-muted small">
      <i class="bi bi-person-circle fs-5"></i>
      <span class="fw-semibold d-none d-sm-inline"><?= htmlspecialchars($user['realname'] ?? '') ?></span>
    </div>

    <a class="btn btn-outline-danger btn-sm rounded-pill px-3"
       href="index.php?r=auth/logout">
      <i class="bi bi-box-arrow-right me-1"></i> Logout
    </a>

  </div>

</nav>