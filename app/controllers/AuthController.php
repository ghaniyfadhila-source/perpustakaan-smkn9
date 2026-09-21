<?php
class AuthController {
  public function login() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $username = trim($_POST['username'] ?? '');
      $password = $_POST['password'] ?? '';

      $u = DB::selectOne("SELECT * FROM user WHERE username=? LIMIT 1", "s", [$username]);
      if (!$u) { $error="User tidak ditemukan"; require __DIR__ . '/../views/auth/login.php'; return; }

      // Untuk pelajar: password disimpan hash password_hash()
      if (!password_verify($password, $u['passwd'])) {
        $error="Password salah"; require __DIR__ . '/../views/auth/login.php'; return;
      }

      Auth::login($u);
      redirect('dashboard/index');
    }

    require __DIR__ . '/../views/auth/login.php';
  }

  public function logout() {
    Auth::logout();
    redirect('auth/login');
  }
}
