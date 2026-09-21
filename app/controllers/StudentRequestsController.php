<?php
class StudentRequestsController {

  public function index() {
    $u = $_SESSION['student'] ?? null;
    if (!$u) { redirect('student/login'); return; }

    $status = $_GET['status'] ?? '';
    $model = new BookRequestModel();
    $requests = $model->getByMember($u['student_id'], $status);

    require __DIR__ . '/../views/student/requests/index.php';
  }

  public function cancel() {
    $u = $_SESSION['student'] ?? null;
    if (!$u) { redirect('student/login'); return; }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $requestId = (int)($_POST['request_id'] ?? 0);
      if ($requestId <= 0) {
        $_SESSION['flash'] = ['type'=>'danger', 'msg'=>'Request tidak valid'];
      } else {
        $model = new BookRequestModel();
        $res = $model->cancel($requestId, $u['student_id']);
        if ($res['ok']) {
          $_SESSION['flash'] = ['type'=>'success', 'msg'=>'Request berhasil dibatalkan'];
        } else {
          $_SESSION['flash'] = ['type'=>'danger', 'msg'=>$res['error']];
        }
      }
    }

    header("Location: index.php?r=student/requests/index");
    exit;
  }
}