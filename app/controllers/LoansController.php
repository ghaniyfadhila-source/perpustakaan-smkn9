<?php
class LoansController {

  public function history() {
    $q = trim($_GET['q'] ?? '');
    $from = $_GET['from'] ?? date('Y-m-01');
    $to   = $_GET['to'] ?? date('Y-m-d');

    $sql = "SELECT l.loan_id, l.item_code, l.member_id, l.loan_date, l.due_date, l.return_date, l.is_return,
                   u.realname AS staff_name,
                   b.title
            FROM loan l
            LEFT JOIN user u ON u.user_id=l.uid
            LEFT JOIN item i ON i.item_code=l.item_code
            LEFT JOIN biblio b ON b.biblio_id=i.biblio_id
            WHERE l.loan_date BETWEEN ? AND ?";

    $types = "ss";
    $params = [$from, $to];

    if ($q !== '') {
      $sql .= " AND (l.member_id LIKE ? OR l.item_code LIKE ? OR b.title LIKE ?)";
      $like = "%$q%";
      $types .= "sss";
      array_push($params, $like, $like, $like);
    }

    $sql .= " ORDER BY l.loan_id DESC LIMIT 500";
    $rows = DB::select($sql, $types, $params);

    require __DIR__ . '/../views/loans/history.php';
  }

  public function edit() {
    if (!ACL::isAdmin()) die('Hanya admin yang boleh edit transaksi.');

    $error = null;

    $loanId = (int)($_GET['id'] ?? 0);
    $loan = DB::selectOne("SELECT * FROM loan WHERE loan_id=? LIMIT 1", "i", [$loanId]);
    if (!$loan) die("Loan tidak ditemukan");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $newData = [
        'member_id' => trim($_POST['member_id'] ?? ''),
        'item_code' => trim($_POST['item_code'] ?? ''),
        'due_date'  => trim($_POST['due_date'] ?? ''),
      ];

      $u = Auth::user();
      $staffId = (int)($u['user_id'] ?? 0);

      if ($staffId <= 0) {
        $error = 'Session login tidak valid. Silakan login ulang.';
      } else {
        $svc = new LoanService();
        $res = $svc->editLoanTypo($loanId, $newData, $staffId);

        if (!empty($res['ok'])) {
          $_SESSION['flash'] = ['type'=>'success', 'msg'=>'Transaksi berhasil diupdate (typo).'];
          header("Location: index.php?r=loans/history");
          exit;
        } else {
          $error = $res['error'] ?? 'Gagal update transaksi.';
        }
      }
    }

    require __DIR__ . '/../views/loans/edit.php';
  }
}
