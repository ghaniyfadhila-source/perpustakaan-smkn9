<?php
class LoginController {

  public function index() {
    require __DIR__ . '/../views/auth/login.php';
  }

  public function process() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      redirect('login/index');
      return;
    }

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
      $error = 'Username dan password wajib diisi';
      require __DIR__ . '/../views/auth/login.php';
      return;
    }

    // 1. Cek di tabel `user` (admin/staff)
    $admin = DB::selectOne(
      "SELECT * FROM `user` WHERE username=? LIMIT 1",
      "s",
      [$username]
    );

    if ($admin && password_verify($password, $admin['passwd'])) {
      Auth::login($admin);
      redirect('dashboard/index');
      return;
    }

    // 2. Cek di tabel `member` (siswa)
    $student = DB::selectOne(
      "SELECT * FROM member WHERE username=? LIMIT 1",
      "s",
      [$username]
    );

    if ($student && password_verify($password, $student['password_hash'])) {
      $_SESSION['student'] = [
        'student_id' => $student['member_id'],
        'student_name' => $student['member_name'],
        'username'     => $student['username'],
      ];
      DB::exec("UPDATE member SET last_login=NOW() WHERE member_id=?", "s", [$student['member_id']]);
      redirect('student/dashboard');
      return;
    }

    // 3. Tidak ditemukan
    $error = 'Username atau password salah';
    require __DIR__ . '/../views/auth/login.php';
  }

  public function logout() {
    $isStudent = isset($_SESSION['student']);
    Auth::logout();
    unset($_SESSION['student']);
    redirect('login/index');
  }
}