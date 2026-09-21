<?php
class DashboardController {

  public function index() {
    $this->checkAuth();

    // Statistik utama
    $activeLoans = DB::selectOne("SELECT COUNT(*) c FROM loan WHERE is_return=0");
    $overdue = DB::selectOne("SELECT COUNT(*) c FROM loan WHERE is_return=0 AND CURDATE() > due_date");
    $finesToday = DB::selectOne("SELECT SUM(debet) s FROM fines WHERE fines_date=CURDATE()");

    $monthlyLoans = DB::select("
      SELECT
        DATE_FORMAT(d.m, '%b') AS label,
        COALESCE(b.borrowed, 0) AS borrowed,
        COALESCE(r.returned, 0) AS returned
      FROM (
        SELECT DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL n MONTH), '%Y-%m-01') AS m
        FROM (
          SELECT 0 n UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5
          UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10 UNION ALL SELECT 11
        ) x
      ) d
      LEFT JOIN (
        SELECT DATE_FORMAT(loan_date, '%Y-%m-01') AS m, COUNT(*) AS borrowed
        FROM loan
        WHERE loan_date >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 11 MONTH), '%Y-%m-01')
        GROUP BY DATE_FORMAT(loan_date, '%Y-%m-01')
      ) b ON b.m = d.m
      LEFT JOIN (
        SELECT DATE_FORMAT(return_date, '%Y-%m-01') AS m, COUNT(*) AS returned
        FROM loan
        WHERE is_return = 1
          AND return_date IS NOT NULL
          AND return_date >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 11 MONTH), '%Y-%m-01')
        GROUP BY DATE_FORMAT(return_date, '%Y-%m-01')
      ) r ON r.m = d.m
      ORDER BY d.m ASC
    ");

    $topBooks = DB::select("
      SELECT b.title AS title, COUNT(*) AS cnt
      FROM loan l
      JOIN item i   ON i.item_code  = l.item_code
      JOIN biblio b ON b.biblio_id  = i.biblio_id
      GROUP BY b.biblio_id, b.title
      ORDER BY cnt DESC
      LIMIT 10
    ");

    $memberTypes = DB::select("
      SELECT mt.member_type_name AS label, COUNT(*) AS cnt
      FROM member m
      JOIN mst_member_type mt ON mt.member_type_id = m.member_type_id
      GROUP BY mt.member_type_id, mt.member_type_name
      ORDER BY cnt DESC
    ");

    // Request summary untuk tab
    $requestSummary = $this->getRequestSummary();

    // Cek apakah sudah verifikasi password untuk request
    $requestVerified = $this->isRequestVerified();

    require __DIR__ . '/../views/dashboard/index.php';
  }

  public function verifyRequestPassword() {
    $this->checkAuth();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      header('Content-Type: application/json');
      echo json_encode(['ok' => false, 'msg' => 'Method tidak diizinkan']);
      exit;
    }

    $input = trim($_POST['password'] ?? '');
    $config = require __DIR__ . '/../../config/admin_security.php';
    $hash = $config['request_verification_password'] ?? '';

    if ($input === '' || $hash === '') {
      header('Content-Type: application/json');
      echo json_encode(['ok' => false, 'msg' => 'Konfigurasi password tidak ditemukan']);
      exit;
    }

    $verified = password_verify($input, $hash);
    if ($verified) {
      $_SESSION['admin_request_verified'] = true;
      $_SESSION['admin_request_verified_at'] = time();
      header('Content-Type: application/json');
      echo json_encode(['ok' => true, 'msg' => 'Verifikasi berhasil']);
    } else {
      header('Content-Type: application/json');
      echo json_encode(['ok' => false, 'msg' => 'Password salah']);
    }
    exit;
  }

  public function requests() {
    $this->checkAuth();
    $this->requireRequestVerified();

    $status = $_GET['status'] ?? 'PENDING';
    $model  = new BookRequestModel();
    $requests = $model->getAll($status, 200);

    $requestSummary = $this->getRequestSummary();

    // Jika AJAX request, render partial saja (tanpa layout)
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
           && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    if ($isAjax) {
      require __DIR__ . '/../views/dashboard/requests_tab.php';
      exit;
    }

    // Akses non-AJAX → tampilkan dashboard dengan tab requests aktif
    // Load full dashboard dengan flag agar tab requests terbuka
    $this->index();
  }

  public function requestDetail() {
    $this->checkAuth();
    $this->requireRequestVerified();

    $requestId = (int)($_GET['id'] ?? 0);
    $model = new BookRequestModel();
    $request = $model->findWithDetails($requestId);
    if (!$request) die('Request tidak ditemukan');

    $copies = DB::select(
      "SELECT i.item_code, i.item_status_id, s.item_status_name, i.location_id, l.location_name
       FROM item i
       LEFT JOIN mst_item_status s ON s.item_status_id=i.item_status_id
       LEFT JOIN mst_location l ON l.location_id=i.location_id
       WHERE i.biblio_id=?
         AND NOT EXISTS (
         SELECT 1 FROM loan l2 WHERE l2.item_code=i.item_code AND l2.is_lent=1 AND l2.is_return=0
       )
       ORDER BY i.item_code",
      "i", [$request['biblio_id']]
    );

    require __DIR__ . '/../views/dashboard/request_detail.php';
  }

  public function approveRequest() {
    $this->checkAuth();
    $this->requireRequestVerified();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      redirect('dashboard/index');
      return;
    }

    $requestId = (int)($_POST['request_id'] ?? 0);
    $itemCode = trim($_POST['item_code'] ?? '');
    $u = Auth::user();
    $staffId = (int)$u['user_id'];

    if ($requestId <= 0 || $itemCode === '') {
      $_SESSION['flash'] = ['type'=>'danger', 'msg'=>'Data tidak lengkap'];
    } else {
      $model = new BookRequestModel();
      $res = $model->approve($requestId, $staffId, $itemCode);
      if ($res['ok']) {
        $_SESSION['flash'] = ['type'=>'success', 'msg'=>'Request disetujui dan peminjaman dibuat'];
      } else {
        $_SESSION['flash'] = ['type'=>'danger', 'msg'=>$res['error']];
      }
    }

    header("Location: index.php?r=dashboard/requests");
    exit;
  }

  public function rejectRequest() {
    $this->checkAuth();
    $this->requireRequestVerified();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      redirect('dashboard/index');
      return;
    }

    $requestId = (int)($_POST['request_id'] ?? 0);
    $reason = trim($_POST['rejection_reason'] ?? '');
    $u = Auth::user();
    $staffId = (int)$u['user_id'];

    if ($requestId <= 0 || $reason === '') {
      $_SESSION['flash'] = ['type'=>'danger', 'msg'=>'Alasan penolakan wajib diisi'];
    } else {
      $model = new BookRequestModel();
      $res = $model->reject($requestId, $staffId, $reason);
      if ($res['ok']) {
        $_SESSION['flash'] = ['type'=>'success', 'msg'=>'Request ditolak'];
      } else {
        $_SESSION['flash'] = ['type'=>'danger', 'msg'=>$res['error']];
      }
    }

    header("Location: index.php?r=dashboard/requests");
    exit;
  }

  // ===== Helper Methods =====

  private function checkAuth(): void {
    if (!Auth::check()) {
      redirect('login/index');
    }
  }

  private function getRequestSummary(): array {
    $pending = DB::selectOne("SELECT COUNT(*) c FROM book_requests WHERE status='PENDING'");
    $approved = DB::selectOne("SELECT COUNT(*) c FROM book_requests WHERE status='APPROVED'");
    $rejected = DB::selectOne("SELECT COUNT(*) c FROM book_requests WHERE status='REJECTED'");
    $cancelled = DB::selectOne("SELECT COUNT(*) c FROM book_requests WHERE status='CANCELLED'");

    return [
      'PENDING' => (int)($pending['c'] ?? 0),
      'APPROVED' => (int)($approved['c'] ?? 0),
      'REJECTED' => (int)($rejected['c'] ?? 0),
      'CANCELLED' => (int)($cancelled['c'] ?? 0),
      'TOTAL' => (int)(($pending['c'] ?? 0) + ($approved['c'] ?? 0) + ($rejected['c'] ?? 0) + ($cancelled['c'] ?? 0)),
    ];
  }

  private function isRequestVerified(): bool {
    if (!isset($_SESSION['admin_request_verified'])) return false;
    
    $config = require __DIR__ . '/../../config/admin_security.php';
    $timeout = (int)($config['request_verification_timeout'] ?? 0);
    
    if ($timeout > 0 && isset($_SESSION['admin_request_verified_at'])) {
      if (time() - $_SESSION['admin_request_verified_at'] > $timeout) {
        unset($_SESSION['admin_request_verified']);
        unset($_SESSION['admin_request_verified_at']);
        return false;
      }
    }
    
    return true;
  }

  private function requireRequestVerified(): void {
    if (!$this->isRequestVerified()) {
      // Jika AJAX request, return JSON
      if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'msg' => 'Belum verifikasi password', 'redirect' => 'dashboard/index']);
        exit;
      }
      // Redirect ke dashboard dengan flag untuk tampilkan modal
      redirect('dashboard/index?request_verify=1');
    }
  }
}
