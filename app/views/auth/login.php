<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Login - <?= APP_NAME ?></title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

<style>
  :root{
    --brand:#6366f1;
    --brand2:#06b6d4;
    --ink:#0f172a;
    --muted:#64748b;
    --card:rgba(255,255,255,.78);
    --stroke:rgba(15,23,42,.08);
  }

  /* ===== PAGE / BACKGROUND ===== */
  body{
  min-height:100vh;
  background:#f1f5f9;
  overflow-x:hidden;
  color:var(--ink);
  font-family:'Poppins', sans-serif;
  letter-spacing:0.2px;
}

  .page{
    position:relative;
    min-height:100vh;
    display:flex;
    align-items:stretch;
  }

  /* soft grid texture */
  .page::before{
    content:"";
    position:absolute;
    inset:0;
    background:
      radial-gradient(900px 420px at 10% 0%, rgba(99,102,241,.10), transparent 60%),
      radial-gradient(900px 420px at 95% 10%, rgba(6,182,212,.10), transparent 55%),
      linear-gradient(to bottom, rgba(255,255,255,.7), rgba(255,255,255,.7));
    pointer-events:none;
  }

  /* Animated blobs */
  .bg-shape{
    position:absolute;
    border-radius:50%;
    filter:blur(110px);
    opacity:.55;
    animation:float 12s infinite alternate ease-in-out;
    pointer-events:none;
  }
  .bg1{width:520px;height:520px;background:var(--brand);top:-170px;right:-140px;}
  .bg2{width:480px;height:480px;background:var(--brand2);bottom:-200px;right:40px;}

  @keyframes float{
    from{transform:translateY(-26px)}
    to{transform:translateY(26px)}
  }

  /* ===== LEFT PANEL ===== */
  .left-panel{
    position:relative;
    padding:72px 72px 72px 88px;
  }

  .brand-badge{
    display:inline-flex;
    align-items:center;
    gap:10px;
    padding:10px 14px;
    border:1px solid var(--stroke);
    border-radius:999px;
    background:rgba(255,255,255,.65);
    backdrop-filter: blur(10px);
  }
  .logo{
    width:44px;
    height:44px;
    object-fit:contain;
    animation:logoFloat 4s infinite ease-in-out;
  }
  @keyframes logoFloat{
    0%,100%{transform:translateY(0)}
    50%{transform:translateY(-10px)}
  }

  .left-title{
    font-weight:600;
    letter-spacing:-.02em;
    line-height:1.05;
  }

  .left-sub{
    color:var(--muted);
    font-size:1.05rem;
    max-width:520px;
  }

  .feature-grid{
    display:flex;
    flex-wrap:wrap;
    gap:10px;
    margin-top:18px;
    max-width:560px;
  }
  .chip{
    display:inline-flex;
    align-items:center;
    gap:10px;
    padding:10px 12px;
    border-radius:14px;
    border:1px solid var(--stroke);
    background:rgba(255,255,255,.6);
    backdrop-filter: blur(10px);
    color:var(--muted);
    font-weight:600;
    font-size:.95rem;
  }
  .chip i{
    color:var(--brand);
    font-size:1.05rem;
  }

  .school-note{
    margin-top:28px;
    padding:14px 16px;
    border:1px dashed rgba(99,102,241,.35);
    border-radius:16px;
    background:rgba(99,102,241,.06);
    color:#334155;
    max-width:560px;
  }

  /* ===== RIGHT PANEL / CARD ===== */
  .right-panel{
    position:relative;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:36px 18px;
  }

  .login-card{
    width:440px;
    border:1px solid var(--stroke);
    border-radius:28px;
    backdrop-filter:blur(25px);
    background:var(--card);
    box-shadow:0 26px 70px rgba(2,6,23,.15);
    animation:fadeUp .8s ease;
  }
  @keyframes fadeUp{
    from{opacity:0;transform:translateY(40px)}
    to{opacity:1;transform:translateY(0)}
  }

  .card-head{
    padding:26px 26px 10px 26px;
  }
  .card-body{
    padding:10px 26px 26px 26px;
  }

  .mini-title{
    color:var(--muted);
    font-weight:700;
    font-size:.9rem;
  }

  /* input modern */
  .form-label{
    font-weight:700;
    color:#334155;
    font-size:.9rem;
    margin-bottom:6px;
  }

  .form-control{
    border-radius:14px;
    border:2px solid rgba(148,163,184,.35);
    padding:12px 14px;
    transition:.25s;
    background:rgba(255,255,255,.85);
  }

  .form-control:focus{
    border-color:rgba(99,102,241,.75);
    box-shadow:0 0 0 6px rgba(99,102,241,.14);
    background:#fff;
  }

  .input-group-text{
    border-radius:14px;
    border:2px solid rgba(148,163,184,.35);
    background:rgba(255,255,255,.85);
  }

  /* button */
  .btn-login{
    border:none;
    border-radius:999px;
    padding:12px 14px;
    background:linear-gradient(135deg,var(--brand),var(--brand2));
    font-weight:800;
    letter-spacing:.2px;
    transition:.25s;
  }

  .btn-login:hover{
    transform:translateY(-2px);
    box-shadow:0 16px 38px rgba(2,6,23,.18);
  }

  .helper-row{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    flex-wrap:wrap;
  }

  .link-soft{
    color:var(--brand);
    font-weight:700;
    text-decoration:none;
  }
  .link-soft:hover{ text-decoration:underline; }

  /* ===== RESPONSIVE ===== */
  @media (max-width: 991.98px){
    .left-panel{ display:none; }
    .right-panel{ padding:22px 14px; }
    .login-card{ width:min(460px, 100%); }
    body{ overflow-x:hidden; }
  }

  /* Mobile: show small header above card */
  .mobile-brand{
    display:none;
  }
  @media (max-width: 991.98px){
    .mobile-brand{
      display:flex;
      align-items:center;
      justify-content:center;
      gap:10px;
      margin-bottom:14px;
      color:#334155;
      font-weight:800;
    }
    .mobile-brand img{
      width:36px;height:36px;object-fit:contain;
    }
  }
</style>
</head>

<body>
<div class="page">
  <div class="bg-shape bg1"></div>
  <div class="bg-shape bg2"></div>

  <div class="container-fluid position-relative">
    <div class="row min-vh-100">

      <!-- LEFT SIDE -->
      <div class="col-lg-7 d-none d-lg-flex flex-column justify-content-center left-panel">
        <div class="brand-badge mb-4">
          <img src="<?= BASE_URL ?>/app/image/SMKN9.png" class="logo" alt="Logo Sekolah">
          <div class="d-flex flex-column lh-sm">
            <span class="fw-bold" style="color:#0f172a;">Portal <?= APP_NAME ?></span>
            <small class="text-muted">Sistem Informasi Prakerin</small>
          </div>
        </div>

        <h1 class="left-title display-4 mb-3">
          Selamat Datang Kembali
        </h1>

        <p class="left-sub mb-2">
          Masuk untuk mengakses jurnal harian, presensi, penilaian, dan informasi prakerin.
        </p>

        <div class="feature-grid">
          <span class="chip"><i class="bi bi-shield-check"></i> Akses Aman</span>
          <span class="chip"><i class="bi bi-geo-alt"></i> Presensi Lokasi</span>
          <span class="chip"><i class="bi bi-journal-text"></i> Jurnal Harian</span>
          <span class="chip"><i class="bi bi-building"></i> Data Mitra DU/DI</span>
          <span class="chip"><i class="bi bi-award"></i> Sertifikat & Laporan</span>
        </div>

        <div class="school-note">
          <div class="fw-bold mb-1"><i class="bi bi-info-circle me-1"></i> Catatan</div>
          <div class="small" style="color:#475569;">
            Gunakan akun yang diberikan sekolah. Jika lupa password, hubungi admin/pembimbing prakerin.
          </div>
        </div>
      </div>

      <!-- RIGHT SIDE LOGIN -->
      <div class="col-lg-5 right-panel">
        <div class="w-100 d-flex flex-column align-items-center">

          <div class="mobile-brand">
            <img src="<?= BASE_URL ?>/app/image/SMKN9.png" alt="Logo Sekolah">
            <div><?= APP_NAME ?></div>
          </div>

          <div class="card login-card">
            <div class="card-head">
              <div class="mini-title mb-1">Login Akun</div>
              <h2 class="fw-bold mb-1">Masuk</h2>
              <div class="text-muted">Silakan isi username & password.</div>
            </div>

             <div class="card-body">
               <form method="post" action="index.php?r=login/process" autocomplete="on">
                <div class="mb-3">
                  <label class="form-label">Username</label>
                  <input class="form-control" name="username" placeholder="" required>
                </div>

                <div class="mb-3">
                  <label class="form-label">Password</label>
                  <div class="input-group">
                    <input id="password" type="password" class="form-control" name="password" placeholder="" required>
                    <button class="btn input-group-text" type="button" id="togglePass" aria-label="Tampilkan password">
                      <i class="bi bi-eye"></i>
                    </button>
                  </div>
                </div>

                <div class="helper-row mb-4">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember">
                    <label class="form-check-label text-muted" for="remember">Ingat saya</label>
                  </div>
                  <a href="#" class="link-soft">Lupa password?</a>
                </div>

                <button class="btn btn-login w-100">
                  <i class="bi bi-box-arrow-in-right me-1"></i> Login
                </button>

                <div class="text-center mt-3 small text-muted">
                  © <?= date('Y') ?> <?= APP_NAME ?> • SMK
                </div>
              </form>
            </div>
          </div>

        </div>
      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const toggle = document.getElementById('togglePass');
  const pass = document.getElementById('password');

  toggle?.addEventListener('click', () => {
    const isPass = pass.type === 'password';
    pass.type = isPass ? 'text' : 'password';
    toggle.innerHTML = isPass ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
  });
</script>
</body>
</html>