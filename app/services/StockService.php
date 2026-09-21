<?php
class StockService {
  public function scan(string $barcode): array {
    $item = DB::selectOne("SELECT i.item_code, i.biblio_id, b.title
                           FROM item i JOIN biblio b ON b.biblio_id=i.biblio_id
                           WHERE i.item_code=? LIMIT 1", "s", [$barcode]);
    if (!$item) return ['ok'=>false, 'error'=>'Barcode tidak ditemukan'];

    $stock = DB::selectOne("
      SELECT
        COUNT(*) AS total_copies,
        SUM(CASE WHEN l.loan_id IS NOT NULL THEN 1 ELSE 0 END) AS on_loan,
        COUNT(*) - SUM(CASE WHEN l.loan_id IS NOT NULL THEN 1 ELSE 0 END) AS available
      FROM item i
      LEFT JOIN loan l
        ON l.item_code=i.item_code AND l.is_lent=1 AND l.is_return=0
      WHERE i.biblio_id=?
    ", "i", [(int)$item['biblio_id']]);

    return ['ok'=>true, 'item'=>$item, 'stock'=>$stock];
  }
}
