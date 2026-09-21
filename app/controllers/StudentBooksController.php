<?php
class StudentBooksController {

  public function index() {
    $u = $_SESSION['student'] ?? null;
    if (!$u) { redirect('student/login'); return; }

    $q = trim($_GET['q'] ?? '');
    $bm = new BookModel();
    $rows = $q !== '' ? $bm->search($q) : $bm->latest(50);

    // Get available copies count for each book
    foreach ($rows as &$book) {
      $copies = DB::selectOne(
        "SELECT COUNT(*) c FROM item i
         LEFT JOIN loan l ON l.item_code=i.item_code AND l.is_lent=1 AND l.is_return=0
         WHERE i.biblio_id=? AND l.loan_id IS NULL",
        "i", [$book['biblio_id']]
      );
      $book['available_copies'] = (int)$copies['c'];
    }

    require __DIR__ . '/../views/student/books/index.php';
  }

  public function show() {
    $u = $_SESSION['student'] ?? null;
    if (!$u) { redirect('student/login'); return; }

    $id = (int)($_GET['id'] ?? 0);
    $bm = new BookModel();
    $book = $bm->findWithRefs($id);
    if (!$book) die('Buku tidak ditemukan');

    // Get available copies
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
      "i", [$id]
    );

    $cm = new CopyModel();
    $masters = $cm->masters();

    require __DIR__ . '/../views/student/books/show.php';
  }

  public function request() {
    $u = $_SESSION['student'] ?? null;
    if (!$u) { redirect('student/login'); return; }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $biblioId = (int)($_POST['biblio_id'] ?? 0);
      $itemCode = trim($_POST['item_code'] ?? '');

      if ($biblioId <= 0) {
        $error = 'Buku tidak valid';
      } else {
        // Check if already has pending request for this book
        $existing = DB::selectOne(
          "SELECT request_id FROM book_requests WHERE member_id=? AND biblio_id=? AND status='PENDING' LIMIT 1",
          "si", [$u['student_id'], $biblioId]
        );
        if ($existing) {
          $error = 'Anda sudah mengajukan request untuk buku ini dan masih menunggu persetujuan';
        } else {
          // Validate item availability if specified
          if ($itemCode !== '') {
            $item = DB::selectOne(
              "SELECT i.item_code FROM item i WHERE i.item_code=? AND i.biblio_id=? LIMIT 1",
              "si", [$itemCode, $biblioId]
            );
            if (!$item) {
              $error = 'Kode eksemplar tidak valid';
            } else {
              $onLoan = DB::selectOne(
                "SELECT loan_id FROM loan WHERE item_code=? AND is_lent=1 AND is_return=0 LIMIT 1",
                "s", [$itemCode]
              );
              if ($onLoan) {
                $error = 'Eksemplar tersebut sedang dipinjam';
              }
            }
          }

          if (!isset($error)) {
            $model = new BookRequestModel();
            $model->create([
              'member_id' => $u['student_id'],
              'biblio_id' => $biblioId,
              'item_code' => $itemCode ?: null
            ]);
            $_SESSION['flash'] = ['type'=>'success', 'msg'=>'Request peminjaman berhasil dikirim. Menunggu persetujuan admin.'];
            header("Location: index.php?r=student/requests/index");
            exit;
          }
        }
      }
    }

    // If error or GET, show the book detail with request form
    $id = (int)($_GET['id'] ?? 0);
    $bm = new BookModel();
    $book = $bm->findWithRefs($id);
    if (!$book) die('Buku tidak ditemukan');

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
      "i", [$id]
    );

    require __DIR__ . '/../views/student/books/request.php';
  }
}