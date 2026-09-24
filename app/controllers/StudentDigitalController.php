<?php
class StudentDigitalController {

  public function index() {
    $q   = trim($_GET['q'] ?? '');
    $dm  = new DigitalWorkModel();
    $rows = $q !== '' ? $dm->search($q) : $dm->published();

    require __DIR__ . '/../views/student/digital/index.php';
  }

  public function read() {
    $id   = (int)($_GET['id'] ?? 0);
    $dm   = new DigitalWorkModel();
    $work = $dm->find($id);

    if (!$work || $work['status'] !== 'published') {
      $_SESSION['flash'] = ['type'=>'danger','msg'=>'Karya tidak ditemukan.'];
      header("Location: index.php?r=student/digital/index");
      exit;
    }

    $dm->incrementView($id);

    require __DIR__ . '/../views/student/digital/read.php';
  }
}
