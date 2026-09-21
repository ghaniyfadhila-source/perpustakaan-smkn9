<?php
class StudentController {

  /* =========================
     AUTHENTICATION (Student)
     ========================= */

  public function login() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $username = trim($_POST['username'] ?? '');
      $password = $_POST['password'] ?? '';

      // Cari member berdasarkan username (baru di tabel member)
      $m = DB::selectOne(
        "SELECT * FROM member WHERE username=? LIMIT 1",
        "s",
        [$username]
      );
      if (!$m) {
        $error = 'Username tidak ditemukan';
        require __DIR__ . '/../views/student/auth/login.php';
        return;
      }

      // Verifikasi password
      if (!password_verify($password, $m['password_hash'])) {
        $error = 'Password salah';
        require __DIR__ . '/../views/student/auth/login.php';
        return;
      }

      // Set session untuk siswa
      $_SESSION['student'] = [
        'student_id' => $m['member_id'],
        'student_name' => $m['member_name'],
        'username'     => $m['username'],
      ];

      // Update last_login
      DB::exec("UPDATE member SET last_login=NOW() WHERE member_id=?", "s", [$m['member_id']]);

      redirect('student/dashboard');
    }

    require __DIR__ . '/../views/student/auth/login.php';
  }

  public function logout() {
    unset($_SESSION['student']);
    redirect('student/login');
  }

  /* =========================
     DASHBOARD
     ========================= */

  public function dashboard() {
    $u = $_SESSION['student'] ?? null;
    if (!$u) {
      redirect('student/login');
      return;
    }

    $student_id = $u['student_id'];

    // Statistik pribadi siswa
    $activeLoans = DB::selectOne(
      "SELECT COUNT(*) c FROM loan WHERE member_id=? AND is_return=0",
      "s",
      [$student_id]
    );

    $overdue = DB::selectOne(
      "SELECT COUNT(*) c FROM loan WHERE member_id=? AND is_return=0 AND due_date < CURDATE()",
      "s",
      [$student_id]
    );

    $fines = DB::selectOne(
      "SELECT COALESCE(SUM(debet), 0) s FROM fines WHERE member_id=?",
      "s",
      [$student_id]
    );

    $activeLoansDetail = DB::select(
      "SELECT l.loan_id, l.item_code, l.loan_date, l.due_date, l.return_date, b.title
       FROM loan l
       JOIN item i ON i.item_code = l.item_code
       JOIN biblio b ON b.biblio_id = i.biblio_id
       WHERE l.member_id=? AND l.is_return=0
       ORDER BY l.loan_date DESC",
      "s",
      [$student_id]
    );

    require __DIR__ . '/../views/student/dashboard/index.php';
  }

  /* =========================
     PROFIL
     ========================= */

  public function profile() {
    $u = $_SESSION['student'] ?? null;
    if (!$u) {
      redirect('student/login');
      return;
    }

    $student_id = $u['student_id'];
    $member = DB::selectOne("SELECT * FROM member WHERE member_id=? LIMIT 1", "s", [$student_id]);

    require __DIR__ . '/../views/student/profile/index.php';
  }
}