<?php
class LoansController {

  public function borrow() {
    $error = null;

    // ===== GET: member + pinjaman aktif =====
    $memberId = trim($_GET['member_id'] ?? '');
    $member = null;
    $activeLoans = [];

    if ($memberId !== '') {
      $member = DB::selectOne(
        "SELECT member_id, member_name, expire_date, member_type_id
         FROM member
         WHERE member_id=? LIMIT 1",
        "s",
        [$memberId]
      );

      if ($member) {
        $activeLoans = DB::select(
          "SELECT l.loan_id, l.item_code, l.loan_date, l.due_date, b.title
           FROM loan l
           LEFT JOIN item i ON i.item_code=l.item_code
           LEFT JOIN biblio b ON b.biblio_id=i.biblio_id
           WHERE l.member_id=? AND l.is_return=0
           ORDER BY l.loan_id DESC",
          "s",
          [$memberId]
        );
      }
    }

    // ===== POST: proses peminjaman =====
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $memberId = trim($_POST['member_id'] ?? '');
      $codesRaw = (string)($_POST['item_codes'] ?? '');

      $codes = preg_split('/\r\n|\r|\n|,/', $codesRaw);
      $codes = array_values(array_filter(array_map('trim', $codes)));

      $u = Auth::user();
      $staffId = (int)($u['user_id'] ?? 0);

      if ($staffId <= 0) {
        $error = 'Session login tidak valid. Silakan login ulang.';
      } else {
        $svc = new LoanService();
        $res = $svc->borrow($memberId, $codes, $staffId);

        if (!empty($res['ok'])) {
          $_SESSION['flash'] = ['type'=>'success', 'msg'=>'Peminjaman berhasil disimpan.'];
          header("Location: index.php?r=loans/borrow&member_id=" . urlencode($memberId));
          exit;
        } else {
          $error = $res['error'] ?? 'Gagal menyimpan peminjaman.';
        }
      }
    }

    require __DIR__ . '/../views/loans/borrow.php';
  }

  public function returnBook() {
    $error  = null;
    $result = null;

    // ===== GET: ambil hasil dari session (PRG) =====
    if (!empty($_SESSION['return_result']) && is_array($_SESSION['return_result'])) {
      $result = $_SESSION['return_result'];
      unset($_SESSION['return_result']);
    }

    // ===== POST: proses pengembalian =====
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $itemCode = trim($_POST['item_code'] ?? '');
      $u = Auth::user();
      $staffId = (int)($u['user_id'] ?? 0);

      if ($staffId <= 0) {
        $error = 'Session login tidak valid. Silakan login ulang.';
      } else {
        $svc = new LoanService();
        $res = $svc->returnBook($itemCode, $staffId);

        if (!empty($res['ok'])) {

          // ===== FALLBACK: kalau detail kosong, ambil paksa dari DB pakai loan_id =====
          $loanId = (int)($res['loan_id'] ?? 0);

          if (
            empty($res['item_code']) ||
            empty($res['title']) ||
            empty($res['member_id']) ||
            empty($res['member_name'])
          ) {
            $row = DB::selectOne("
              SELECT
                l.loan_id,
                l.item_code,
                l.member_id,
                m.member_name,
                b.title
              FROM loan l
              JOIN member m ON m.member_id = l.member_id
              JOIN item i ON i.item_code = l.item_code
              JOIN biblio b ON b.biblio_id = i.biblio_id
              WHERE l.loan_id = ?
              LIMIT 1
            ", "i", [$loanId]);

            if ($row) {
              $res['item_code']   = $res['item_code']   ?: $row['item_code'];
              $res['member_id']   = $res['member_id']   ?: $row['member_id'];
              $res['member_name'] = $res['member_name'] ?: $row['member_name'];
              $res['title']       = $res['title']       ?: $row['title'];
            }
          }

          $_SESSION['return_result'] = $res;

          $_SESSION['flash'] = [
            'type' => 'success',
            'msg'  => "Pengembalian OK. Denda: Rp " . number_format((int)($res['fine'] ?? 0))
          ];

          header("Location: index.php?r=loans/returnBook");
          exit;

        } else {
          $error = $res['error'] ?? 'Gagal memproses pengembalian.';
        }
      }
    }

    require __DIR__ . '/../views/loans/return.php';
  }

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
