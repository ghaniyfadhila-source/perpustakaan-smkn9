<?php
class StudentDashboardController {

  public function index() {
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
}