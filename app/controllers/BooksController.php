<?php
class BooksController {

  public function index() {
    $q = trim($_GET['q'] ?? '');
    $rows = [];

    $bm = new BookModel();
    if ($q !== '') $rows = $bm->search($q);
    else $rows = $bm->latest(50);

    require __DIR__ . '/../views/books/index.php';
  }

  public function create() {
    $bm = new BookModel();
    $masters = $bm->masters();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $data = $this->collectBookForm();
      $userId = Auth::user()['user_id'];

      $bookId = $bm->create($data, $userId);

      // log sederhana ke system_log
      AuditService::log('staff', (string)$userId, 'books', 'create', "CREATE_BOOK biblio_id=$bookId title=".$data['title']);

      header("Location: index.php?r=books/show&id=".$bookId);
      exit;
    }

    require __DIR__ . '/../views/books/create.php';
  }

  public function edit() {
    $id = (int)($_GET['id'] ?? 0);
    $bm = new BookModel();
    $book = $bm->find($id);
    if (!$book) die('Buku tidak ditemukan');

    $masters = $bm->masters();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $data = $this->collectBookForm();
      $bm->update($id, $data);

      $userId = Auth::user()['user_id'];
      AuditService::log('staff', (string)$userId, 'books', 'update', "UPDATE_BOOK biblio_id=$id title=".$data['title']);

      header("Location: index.php?r=books/show&id=".$id);
      exit;
    }

    require __DIR__ . '/../views/books/edit.php';
  }

  public function delete() {
    $id = (int)($_GET['id'] ?? 0);
    $bm = new BookModel();
    $book = $bm->find($id);
    if (!$book) die('Buku tidak ditemukan');

    $userId = Auth::user()['user_id'];

    // aman: tidak boleh delete kalau pernah dipinjam atau punya item
    if (!$bm->canDelete($id)) {
      AuditService::log('staff', (string)$userId, 'books', 'delete_blocked', "DELETE_BLOCKED biblio_id=$id");
      $_SESSION['flash'] = ['type'=>'danger','msg'=>'Buku tidak bisa dihapus karena sudah memiliki riwayat peminjaman atau memiliki eksemplar.'];
      header("Location: index.php?r=books/show&id=".$id);
      exit;
    }

    $bm->delete($id);
    AuditService::log('staff', (string)$userId, 'books', 'delete', "DELETE_BOOK biblio_id=$id");

    $_SESSION['flash'] = ['type'=>'success','msg'=>'Buku berhasil dihapus.'];
    header("Location: index.php?r=books/index");
    exit;
  }

  public function show() {
    $id = (int)($_GET['id'] ?? 0);
    $bm = new BookModel();
    $book = $bm->findWithRefs($id);
    if (!$book) die('Buku tidak ditemukan');

    $cm = new CopyModel();
    $copies = $cm->listByBook($id);

    require __DIR__ . '/../views/books/show.php';
  }

  public function deleteCopy() {
  $biblioId = (int)($_GET['id'] ?? 0);       // id buku
  $itemId   = (int)($_GET['item_id'] ?? 0); // id eksemplar

  if (!$biblioId || !$itemId) die('Parameter tidak lengkap');

  $bm = new BookModel();
  $book = $bm->find($biblioId);
  if (!$book) die('Buku tidak ditemukan');

  $cm = new CopyModel();

  $userId = Auth::user()['user_id'];

  if (!$cm->canDelete($itemId)) {
    AuditService::log('staff', (string)$userId, 'copies', 'delete_blocked', "DELETE_COPY_BLOCKED biblio_id=$biblioId item_id=$itemId");
    $_SESSION['flash'] = ['type'=>'danger','msg'=>'Eksemplar tidak bisa dihapus karena sedang dipinjam.'];
    header("Location: index.php?r=books/show&id=".$biblioId);
    exit;
  }

  $cm->delete($itemId);
  AuditService::log('staff', (string)$userId, 'copies', 'delete', "DELETE_COPY biblio_id=$biblioId item_id=$itemId");
  $_SESSION['flash'] = ['type'=>'success','msg'=>'Eksemplar berhasil dihapus.'];

  header("Location: index.php?r=books/show&id=".$biblioId);
  exit;
}

public function deleteCopies()
{
  $biblioId = (int)($_POST['biblio_id'] ?? 0);
  $itemIds = $_POST['item_ids'] ?? [];

  if (!$biblioId || empty($itemIds)) {
    $_SESSION['flash'] = ['type'=>'danger','msg'=>'Tidak ada eksemplar dipilih.'];
    header("Location: index.php?r=books/show&id=".$biblioId);
    exit;
  }

  $cm = new CopyModel();

  $result = $cm->bulkDelete($itemIds);

  $_SESSION['flash'] = [
    'type' => 'success',
    'msg'  => "Berhasil hapus {$result['deleted']} eksemplar. ".
              ($result['blocked'] ? "{$result['blocked']} gagal (sedang dipinjam)." : "")
  ];

  header("Location: index.php?r=books/show&id=".$biblioId);
  exit;
}

  public function addCopy() {
    $biblioId = (int)($_GET['id'] ?? 0);
    $bm = new BookModel();
    $book = $bm->find($biblioId);
    if (!$book) die('Buku tidak ditemukan');

    $cm = new CopyModel();
    $masters = $cm->masters();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $data = [
        'biblio_id' => $biblioId,
        'item_code' => trim($_POST['item_code'] ?? ''),
        'coll_type_id' => (int)($_POST['coll_type_id'] ?? 0),
        'location_id' => trim($_POST['location_id'] ?? ''),
        'item_status_id' => trim($_POST['item_status_id'] ?? ''),
      ];

      if ($data['item_code'] === '') {
        $error = "Barcode wajib diisi";
        require __DIR__ . '/../views/books/add_copy.php';
        return;
      }

      $userId = Auth::user()['user_id'];
      try {
        $cm->create($data, $userId);
        AuditService::log('staff', (string)$userId, 'copies', 'create', "CREATE_COPY biblio_id=$biblioId item_code=".$data['item_code']);
        $_SESSION['flash'] = ['type'=>'success','msg'=>'Eksemplar berhasil ditambahkan.'];
        header("Location: index.php?r=books/show&id=".$biblioId);
        exit;
      } catch (Throwable $e) {
        $error = "Gagal tambah eksemplar. Barcode mungkin sudah dipakai.";
      }
    }

    require __DIR__ . '/../views/books/add_copy.php';
  }

  private function collectBookForm(): array {
    return [
      'gmd_id' => (int)($_POST['gmd_id'] ?? 0),
      'title' => trim($_POST['title'] ?? ''),
      'edition' => trim($_POST['edition'] ?? ''),
      'isbn' => trim($_POST['isbn'] ?? ''),
      'publisher_id' => (int)($_POST['publisher_id'] ?? 0),
      'publish_year' => trim($_POST['publish_year'] ?? ''),
      'series_title' => trim($_POST['series_title'] ?? ''),
      'call_number' => trim($_POST['call_number'] ?? ''),
      'classification' => trim($_POST['classification'] ?? ''),
      'notes' => trim($_POST['notes'] ?? ''),
    ];
  }
}
