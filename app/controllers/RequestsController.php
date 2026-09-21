<?php
class RequestsController {

  public function index() {
    if (!Auth::check()) { redirect('login/index'); return; }
    // Redirect to unified dashboard with notice
    $_SESSION['flash'] = ['type'=>'info', 'msg'=>'Menu Request Buku telah dipindah ke Dashboard > Tab "Request Verifikasi"'];
    header("Location: index.php?r=dashboard/index#requests");
    exit;
  }

  public function approve() {
    if (!Auth::check()) { redirect('login/index'); return; }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    }

    header("Location: index.php?r=dashboard/requests");
    exit;
  }

  public function reject() {
    if (!Auth::check()) { redirect('login/index'); return; }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    }

    header("Location: index.php?r=dashboard/requests");
    exit;
  }

  public function detail() {
    if (!Auth::check()) { redirect('login/index'); return; }

    $requestId = (int)($_GET['id'] ?? 0);
    // Redirect to new dashboard detail
    header("Location: index.php?r=dashboard/requestDetail&id=$requestId");
    exit;
  }
}