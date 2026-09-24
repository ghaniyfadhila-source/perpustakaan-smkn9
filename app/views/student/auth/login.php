<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login Siswa — <?= APP_NAME ?? 'Perpustakaan SMKN 9' ?></title>
<meta name="description" content="Login Siswa Perpustakaan SMKN 9 Semarang">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

<style>
  :root {
    --accent: #0ea5e9;
    --accent2: #6366f1;
    --accent3: #34d399;
    --ink: #0f172a;
    --secondary: #475569;
    --muted: #94a3b8;
    --card-bg: rgba(255,255,255,0.82);
    --card-border: rgba(255,255,255,0.9);
    --stroke-strong: rgba(0,0,0,0.1);
    --radius: 24px;
    --transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
  }

  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    font-family: 'Inter', sans-serif;
    min-height: 100vh;
    background: #f0f7ff;
    color: var(--ink);
    overflow-x: hidden;
    -webkit-font-smoothing: antialiased;
  }

  .page-bg {
    position: fixed;
    inset: 0;
    z-index: 0;
    overflow: hidden;
    background: linear-gradient(135deg, #f0f7ff 0%, #e0f2fe 35%, #f0fdf4 65%, #eef2ff 100%);
  }

  .blob {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.5;
    animation: blobFloat 14s ease-in-out infinite alternate;
  }

  .blob-1 {
    width: 550px; height: 550px;
    background: radial-gradient(circle, rgba(14,165,233,0.45), rgba(14,165,233,0.05));
    top: -180px; right: -120px;
    animation-delay: 0s;
  }

  .blob-2 {
    width: 480px; height: 480px;
    background: radial-gradient(circle, rgba(52,211,153,0.4), rgba(52,211,153,0.05));
    bottom: -140px; left: -80px;
    animation-delay: -6s;
  }

  .blob-3 {
    width: 380px; height: 380px;
    background: radial-gradient(circle, rgba(99,102,241,0.28), rgba(99,102,241,0.03));
    top: 45%; left: 30%;
    animation-delay: -10s;
  }

  @keyframes blobFloat {
    from { transform: translate(0, 0) scale(1); }
    to   { transform: translate(25px, -35px) scale(1.07); }
  }

  .page-bg::after {
    content: '';
    position: absolute;
    inset: 0;
    background-image: radial-gradient(rgba(14,165,233,0.07) 1px, transparent 1px);
    background-size: 28px 28px;
    pointer-events: none;
  }

  .login-page {
    position: relative;
    z-index: 1;
    min-height: 100vh;
    display: flex;
    align-items: stretch;
  }

  /* LEFT PANEL */
  .left-panel {
    flex: 1;
    display: none;
    flex-direction: column;
    justify-content: center;
    padding: 80px 72px 80px 88px;
  }

  @media (min-width: 1024px) {
    .left-panel { display: flex; }
  }

  .brand-pill {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    padding: 10px 18px 10px 10px;
    background: rgba(255,255,255,0.7);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border: 1px solid rgba(255,255,255,0.9);
    border-radius: 999px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    margin-bottom: 36px;
    width: fit-content;
  }

  .brand-pill img {
    width: 44px;
    height: 44px;
    object-fit: contain;
    border-radius: 12px;
    animation: logoFloat 5s ease-in-out infinite;
  }

  @keyframes logoFloat {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
  }

  .brand-pill-text strong {
    display: block;
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--ink);
    line-height: 1.2;
  }

  .brand-pill-text small {
    font-size: 0.75rem;
    color: var(--muted);
  }

  .left-title {
    font-size: clamp(2rem, 3.5vw, 3rem);
    font-weight: 800;
    letter-spacing: -0.035em;
    line-height: 1.1;
    color: var(--ink);
    margin-bottom: 16px;
  }

  .left-title span {
    background: linear-gradient(135deg, var(--accent), var(--accent3));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .left-sub {
    font-size: 1rem;
    color: var(--secondary);
    line-height: 1.65;
    max-width: 480px;
    margin-bottom: 32px;
  }

  .features-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    max-width: 520px;
    margin-bottom: 32px;
  }

  .feature-chip {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 16px;
    background: rgba(255,255,255,0.65);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.85);
    border-radius: 12px;
    font-size: 0.85rem;
    font-weight: 500;
    color: var(--secondary);
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    transition: var(--transition);
  }

  .feature-chip:hover {
    background: rgba(255,255,255,0.85);
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(0,0,0,0.07);
  }

  .feature-chip i {
    font-size: 1rem;
    color: var(--accent);
  }

  .info-box {
    padding: 16px 20px;
    background: rgba(14,165,233,0.06);
    border: 1px solid rgba(14,165,233,0.18);
    border-radius: 16px;
    max-width: 480px;
  }

  .info-box-title {
    font-size: 0.82rem;
    font-weight: 700;
    color: var(--accent);
    margin-bottom: 6px;
  }

  .info-box p {
    font-size: 0.83rem;
    color: var(--secondary);
    line-height: 1.6;
    margin: 0;
  }

  /* RIGHT PANEL */
  .right-panel {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 32px 20px;
  }

  @media (min-width: 1024px) {
    .right-panel {
      width: 440px;
      min-width: 440px;
      padding: 48px 32px;
    }
  }

  .login-card {
    width: 100%;
    max-width: 400px;
    background: var(--card-bg);
    backdrop-filter: blur(32px) saturate(200%);
    -webkit-backdrop-filter: blur(32px) saturate(200%);
    border: 1px solid var(--card-border);
    border-radius: var(--radius);
    box-shadow:
      0 32px 64px rgba(0,0,0,0.1),
      0 8px 24px rgba(0,0,0,0.06),
      inset 0 1px 0 rgba(255,255,255,0.9);
    animation: cardIn 0.7s cubic-bezier(0.4, 0, 0.2, 1) both;
    overflow: hidden;
  }

  @keyframes cardIn {
    from { opacity: 0; transform: translateY(28px) scale(0.98); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
  }

  .card-strip {
    height: 4px;
    background: linear-gradient(90deg, var(--accent), var(--accent3), var(--accent2));
  }

  .card-inner {
    padding: 32px 32px 36px;
  }

  .card-icon {
    width: 52px;
    height: 52px;
    border-radius: 16px;
    background: linear-gradient(135deg, var(--accent), var(--accent3));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.4rem;
    margin-bottom: 20px;
    box-shadow: 0 6px 20px rgba(14,165,233,0.3);
  }

  .card-title {
    font-size: 1.5rem;
    font-weight: 800;
    letter-spacing: -0.03em;
    color: var(--ink);
    margin-bottom: 4px;
  }

  .card-sub {
    font-size: 0.85rem;
    color: var(--muted);
    margin-bottom: 28px;
  }

  .alert-glass {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    background: rgba(239, 68, 68, 0.07);
    border: 1px solid rgba(239, 68, 68, 0.2);
    border-radius: 12px;
    font-size: 0.85rem;
    color: #dc2626;
    margin-bottom: 20px;
    animation: shake 0.4s ease;
  }

  @keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-6px); }
    75% { transform: translateX(6px); }
  }

  .field-group { margin-bottom: 18px; }

  .field-label {
    display: block;
    font-size: 0.82rem;
    font-weight: 600;
    color: var(--secondary);
    margin-bottom: 7px;
  }

  .field-input {
    width: 100%;
    padding: 12px 16px;
    font-size: 0.92rem;
    font-family: 'Inter', sans-serif;
    color: var(--ink);
    background: rgba(255,255,255,0.75);
    border: 1.5px solid var(--stroke-strong);
    border-radius: 14px;
    outline: none;
    transition: var(--transition);
  }

  .field-input:focus {
    border-color: var(--accent);
    background: white;
    box-shadow: 0 0 0 4px rgba(14,165,233,0.12);
  }

  .field-input::placeholder { color: var(--muted); }

  .input-wrap {
    position: relative;
    display: flex;
  }

  .input-wrap .field-input { padding-right: 48px; }

  .toggle-pass {
    position: absolute;
    right: 0;
    top: 0;
    height: 100%;
    width: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: none;
    border: none;
    color: var(--muted);
    cursor: pointer;
    transition: var(--transition);
    font-size: 1rem;
  }

  .toggle-pass:hover { color: var(--accent); }

  .field-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
    gap: 10px;
  }

  .check-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.83rem;
    color: var(--secondary);
    cursor: pointer;
  }

  .check-label input[type="checkbox"] {
    width: 16px;
    height: 16px;
    accent-color: var(--accent);
  }

  .link-forgot {
    font-size: 0.83rem;
    font-weight: 600;
    color: var(--accent);
    text-decoration: none;
    transition: var(--transition);
  }

  .link-forgot:hover { opacity: 0.75; }

  .btn-submit {
    width: 100%;
    padding: 13px;
    font-size: 0.95rem;
    font-weight: 700;
    font-family: 'Inter', sans-serif;
    color: white;
    background: linear-gradient(135deg, var(--accent), var(--accent3));
    border: none;
    border-radius: 14px;
    cursor: pointer;
    transition: var(--transition);
    box-shadow: 0 6px 20px rgba(14,165,233,0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    letter-spacing: 0.01em;
  }

  .btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 28px rgba(14,165,233,0.4);
  }

  .btn-submit:active { transform: translateY(0); }

  .admin-link-box {
    margin-top: 24px;
    padding: 14px 16px;
    background: rgba(99,102,241,0.04);
    border: 1px solid rgba(99,102,241,0.12);
    border-radius: 14px;
    text-align: center;
    font-size: 0.83rem;
    color: var(--muted);
  }

  .admin-link-box a {
    color: var(--accent2);
    font-weight: 600;
    text-decoration: none;
  }

  .admin-link-box a:hover { text-decoration: underline; }

  .card-footer-text {
    text-align: center;
    margin-top: 20px;
    font-size: 0.75rem;
    color: var(--muted);
  }

  .mobile-brand {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-bottom: 24px;
  }

  .mobile-brand img { width: 36px; height: 36px; object-fit: contain; }

  .mobile-brand span {
    font-weight: 700;
    font-size: 0.9rem;
    color: var(--ink);
  }

  @media (min-width: 1024px) { .mobile-brand { display: none; } }
</style>
</head>

<body>
<div class="page-bg">
  <div class="blob blob-1"></div>
  <div class="blob blob-2"></div>
  <div class="blob blob-3"></div>
</div>

<div class="login-page">

  <!-- LEFT -->
  <div class="left-panel">
    <div class="brand-pill">
      <img src="<?= BASE_URL ?>/app/image/SMKN9.png" alt="Logo SMKN9">
      <div class="brand-pill-text">
        <strong><?= APP_NAME ?? 'Perpustakaan SMKN 9' ?></strong>
        <small>Portal Siswa</small>
      </div>
    </div>

    <h1 class="left-title">
      Halo, Siswa!<br><span>Selamat Datang</span> Kembali
    </h1>

    <p class="left-sub">
      Masuk untuk melihat status peminjaman, mengajukan request buku, dan jelajahi koleksi perpustakaan sekolah.
    </p>

    <div class="features-grid">
      <span class="feature-chip"><i class="bi bi-shield-check"></i> Akses Aman</span>
      <span class="feature-chip"><i class="bi bi-journal-plus"></i> Request Buku</span>
      <span class="feature-chip"><i class="bi bi-book"></i> Katalog Online</span>
      <span class="feature-chip"><i class="bi bi-person-circle"></i> Profil Pribadi</span>
    </div>

    <div class="info-box">
      <div class="info-box-title"><i class="bi bi-info-circle me-1"></i> Informasi Siswa</div>
      <p>Gunakan username dan password yang diberikan oleh pustakawan. Jika ada masalah, hubungi petugas perpustakaan.</p>
    </div>
  </div>

  <!-- RIGHT -->
  <div class="right-panel">
    <div class="w-100" style="max-width: 400px;">

      <div class="mobile-brand">
        <img src="<?= BASE_URL ?>/app/image/SMKN9.png" alt="Logo">
        <span><?= APP_NAME ?? 'Perpustakaan SMKN 9' ?></span>
      </div>

      <div class="login-card">
        <div class="card-strip"></div>
        <div class="card-inner">

          <div class="card-icon">
            <i class="bi bi-mortarboard"></i>
          </div>

          <div class="card-title">Login Siswa</div>
          <div class="card-sub">Masukkan username dan password kamu</div>

          <?php if (!empty($error)): ?>
            <div class="alert-glass">
              <i class="bi bi-exclamation-circle-fill"></i>
              <?= htmlspecialchars($error) ?>
            </div>
          <?php endif; ?>

          <form method="post" autocomplete="on">

            <div class="field-group">
              <label class="field-label" for="username">Username</label>
              <input
                id="username"
                class="field-input"
                type="text"
                name="username"
                placeholder="Username kamu"
                autocomplete="username"
                required
                value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
              >
            </div>

            <div class="field-group">
              <label class="field-label" for="password">Password</label>
              <div class="input-wrap">
                <input
                  id="password"
                  class="field-input"
                  type="password"
                  name="password"
                  placeholder="Password kamu"
                  autocomplete="current-password"
                  required
                >
                <button class="toggle-pass" type="button" id="togglePass" aria-label="Tampilkan password">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
            </div>

            <div class="field-row">
              <label class="check-label">
                <input type="checkbox" id="remember" name="remember">
                Ingat saya
              </label>
              <a href="#" class="link-forgot">Lupa password?</a>
            </div>

            <button class="btn-submit" type="submit">
              <i class="bi bi-door-open"></i>
              Masuk Sekarang
            </button>

          </form>

          <div class="admin-link-box">
            Petugas perpustakaan? <a href="index.php?r=login/index">Login Admin →</a>
          </div>

          <div class="card-footer-text">
            © <?= date('Y') ?> <?= APP_NAME ?? 'Perpustakaan SMKN 9' ?> &bull; Portal Siswa
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
    toggle.innerHTML = isPass
      ? '<i class="bi bi-eye-slash"></i>'
      : '<i class="bi bi-eye"></i>';
  });
</script>
</body>
</html>