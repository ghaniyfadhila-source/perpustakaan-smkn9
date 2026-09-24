<?php
class Digital_worksController {

  private string $coverDir = __DIR__ . '/../../storage/digital_works/covers/';
  private string $pdfDir   = __DIR__ . '/../../storage/digital_works/pdfs/';

  public function index() {
    $dm = new DigitalWorkModel();
    $rows = $dm->all();
    require __DIR__ . '/../views/digital_works/index.php';
  }

  public function create() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $dm = new DigitalWorkModel();
      $userId = Auth::user()['user_id'];

      $title       = trim($_POST['title'] ?? '');
      $authorName  = trim($_POST['author_name'] ?? '');
      $description = trim($_POST['description'] ?? '');

      if ($title === '' || $authorName === '') {
        $error = 'Judul dan Nama Penulis wajib diisi.';
        require __DIR__ . '/../views/digital_works/create.php';
        return;
      }

      if (empty($_FILES['pdf_file']['tmp_name']) || $_FILES['pdf_file']['error'] !== UPLOAD_ERR_OK) {
        $error = 'File PDF wajib diupload.';
        require __DIR__ . '/../views/digital_works/create.php';
        return;
      }

      if ($_FILES['pdf_file']['size'] > 50 * 1024 * 1024) {
        $error = 'Ukuran PDF maksimal 50MB.';
        require __DIR__ . '/../views/digital_works/create.php';
        return;
      }

      $ext = strtolower(pathinfo($_FILES['pdf_file']['name'], PATHINFO_EXTENSION));
      if ($ext !== 'pdf') {
        $error = 'File harus berformat PDF.';
        require __DIR__ . '/../views/digital_works/create.php';
        return;
      }

      $pdfName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($_FILES['pdf_file']['name']));
      move_uploaded_file($_FILES['pdf_file']['tmp_name'], $this->pdfDir . $pdfName);

      $coverName = '';
      if (!empty($_FILES['cover_image']['tmp_name']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $cExt = strtolower(pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION));
        if (in_array($cExt, ['jpg','jpeg','png']) && $_FILES['cover_image']['size'] <= 2 * 1024 * 1024) {
          $coverName = time() . '_cover.' . $cExt;
          move_uploaded_file($_FILES['cover_image']['tmp_name'], $this->coverDir . $coverName);
        }
      }

      $workId = $dm->create([
        'title'       => $title,
        'author_name' => $authorName,
        'description' => $description,
        'cover_image' => $coverName,
        'pdf_file'    => $pdfName,
        'file_size'   => $_FILES['pdf_file']['size'],
        'status'      => 'published',
      ], $userId);

      AuditService::log('staff', (string)$userId, 'digital_works', 'create', "CREATE_WORK work_id=$workId title=$title");

      $_SESSION['flash'] = ['type'=>'success','msg'=>'Karya berhasil diupload.'];
      header("Location: index.php?r=digital_works/index");
      exit;
    }

    require __DIR__ . '/../views/digital_works/create.php';
  }

  public function show() {
    $id   = (int)($_GET['id'] ?? 0);
    $dm   = new DigitalWorkModel();
    $work = $dm->find($id);
    if (!$work) die('Karya tidak ditemukan');

    require __DIR__ . '/../views/digital_works/show.php';
  }

  public function edit() {
    $id   = (int)($_GET['id'] ?? 0);
    $dm   = new DigitalWorkModel();
    $work = $dm->find($id);
    if (!$work) die('Karya tidak ditemukan');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $userId = Auth::user()['user_id'];
      $data = [
        'title'       => trim($_POST['title'] ?? ''),
        'author_name' => trim($_POST['author_name'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'status'      => $_POST['status'] ?? 'published',
      ];

      if ($data['title'] === '' || $data['author_name'] === '') {
        $error = 'Judul dan Nama Penulis wajib diisi.';
        require __DIR__ . '/../views/digital_works/edit.php';
        return;
      }

      if (!empty($_FILES['pdf_file']['tmp_name']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
        if ($_FILES['pdf_file']['size'] > 50 * 1024 * 1024) {
          $error = 'Ukuran PDF maksimal 50MB.';
          require __DIR__ . '/../views/digital_works/edit.php';
          return;
        }
        $ext = strtolower(pathinfo($_FILES['pdf_file']['name'], PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
          $error = 'File harus berformat PDF.';
          require __DIR__ . '/../views/digital_works/edit.php';
          return;
        }
        $pdfName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($_FILES['pdf_file']['name']));
        move_uploaded_file($_FILES['pdf_file']['tmp_name'], $this->pdfDir . $pdfName);
        $data['pdf_file']  = $pdfName;
        $data['file_size'] = $_FILES['pdf_file']['size'];

        if (!empty($work['pdf_file']) && file_exists($this->pdfDir . $work['pdf_file'])) {
          unlink($this->pdfDir . $work['pdf_file']);
        }
      }

      if (!empty($_FILES['cover_image']['tmp_name']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $cExt = strtolower(pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION));
        if (in_array($cExt, ['jpg','jpeg','png']) && $_FILES['cover_image']['size'] <= 2 * 1024 * 1024) {
          $coverName = time() . '_cover.' . $cExt;
          move_uploaded_file($_FILES['cover_image']['tmp_name'], $this->coverDir . $coverName);
          $data['cover_image'] = $coverName;

          if (!empty($work['cover_image']) && file_exists($this->coverDir . $work['cover_image'])) {
            unlink($this->coverDir . $work['cover_image']);
          }
        }
      }

      $dm->update($id, $data);

      AuditService::log('staff', (string)$userId, 'digital_works', 'update', "UPDATE_WORK work_id=$id title=".$data['title']);

      $_SESSION['flash'] = ['type'=>'success','msg'=>'Karya berhasil diperbarui.'];
      header("Location: index.php?r=digital_works/show&id=".$id);
      exit;
    }

    require __DIR__ . '/../views/digital_works/edit.php';
  }

  public function delete() {
    $id   = (int)($_GET['id'] ?? 0);
    $dm   = new DigitalWorkModel();
    $work = $dm->find($id);
    if (!$work) die('Karya tidak ditemukan');

    $userId = Auth::user()['user_id'];

    if (!empty($work['pdf_file']) && file_exists($this->pdfDir . $work['pdf_file'])) {
      unlink($this->pdfDir . $work['pdf_file']);
    }
    if (!empty($work['cover_image']) && file_exists($this->coverDir . $work['cover_image'])) {
      unlink($this->coverDir . $work['cover_image']);
    }

    $dm->delete($id);

    AuditService::log('staff', (string)$userId, 'digital_works', 'delete', "DELETE_WORK work_id=$id title=".$work['title']);

    $_SESSION['flash'] = ['type'=>'success','msg'=>'Karya berhasil dihapus.'];
    header("Location: index.php?r=digital_works/index");
    exit;
  }
}
