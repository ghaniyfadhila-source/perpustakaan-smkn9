<?php
class PrintController {

  // Print barcode untuk semua item dari 1 biblio_id
  public function bookBarcodes() {
    $biblioId = (int)($_GET['biblio_id'] ?? 0);
    if ($biblioId <= 0) die('biblio_id invalid');

    $book = DB::selectOne(
      "SELECT biblio_id, title, call_number, classification FROM biblio WHERE biblio_id=? LIMIT 1",
      "i",
      [$biblioId]
    );
    if (!$book) die('Buku tidak ditemukan');

    $items = DB::select(
      "SELECT item_code FROM item WHERE biblio_id=? ORDER BY item_id ASC",
      "i",
      [$biblioId]
    );

    require __DIR__ . '/../views/print/book_barcodes.php';
  }

  // Print label (call number + barcode) untuk item tertentu
  public function itemLabel() {
    $itemCode = trim($_GET['item_code'] ?? '');
    if ($itemCode === '') die('item_code kosong');

    $row = DB::selectOne("
      SELECT i.item_code, b.title, b.call_number, b.classification
      FROM item i
      JOIN biblio b ON b.biblio_id=i.biblio_id
      WHERE i.item_code=? LIMIT 1
    ", "s", [$itemCode]);

    if (!$row) die('Item tidak ditemukan');

    require __DIR__ . '/../views/print/item_label.php';
  }

  // Print label untuk item terpilih (checkbox dari halaman buku)
  public function itemLabels() {
    $codes = $_GET['item_code'] ?? [];
    if (!is_array($codes)) $codes = [$codes];

    $codes = array_values(array_filter(array_map('trim', $codes)));
    $codes = array_values(array_unique($codes));

    if (empty($codes)) die('Tidak ada item_code yang dipilih');

    // Ambil detail semua item terpilih
    $placeholders = implode(',', array_fill(0, count($codes), '?'));
    $types = str_repeat('s', count($codes));

    $items = DB::select("
      SELECT i.item_code, b.title, b.call_number, b.classification
      FROM item i
      JOIN biblio b ON b.biblio_id=i.biblio_id
      WHERE i.item_code IN ($placeholders)
      ORDER BY b.title ASC, i.item_code ASC
    ", $types, $codes);

    if (empty($items)) die('Item tidak ditemukan');

    // pakai view yang sama untuk print massal
    $book = ['title' => 'Label Terpilih', 'call_number' => '', 'classification' => ''];
    require __DIR__ . '/../views/print/book_barcodes.php';
  }
}