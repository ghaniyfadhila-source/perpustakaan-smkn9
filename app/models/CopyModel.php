<?php

class CopyModel
{
  public function listByBook(int $biblioId): array
  {
    $sql = "SELECT i.*,
              l.loan_id AS active_loan_id
            FROM item i
            LEFT JOIN loan l ON l.item_code=i.item_code AND l.is_lent=1 AND l.is_return=0
            WHERE i.biblio_id=?
            ORDER BY i.item_id DESC";
    return DB::select($sql, "i", [$biblioId]);
  }

  public function masters(): array
  {
    $colls = DB::select("SELECT coll_type_id, coll_type_name FROM mst_coll_type ORDER BY coll_type_name ASC", "", []);
    $locs  = DB::select("SELECT location_id, location_name FROM mst_location ORDER BY location_name ASC", "", []);
    $stats = DB::select("SELECT item_status_id, item_status_name FROM mst_item_status ORDER BY item_status_name ASC", "", []);
    return ['colls'=>$colls, 'locs'=>$locs, 'stats'=>$stats];
  }

  public function create(array $data, int $userId): int
  {
    $sql = "INSERT INTO item (biblio_id, item_code, coll_type_id, location_id, item_status_id, input_date, uid)
            VALUES (?, ?, ?, ?, ?, NOW(), ?)";

    // Asumsi location_id & item_status_id adalah INT (umum di SLiMS)
    DB::exec($sql, "isiiii", [
      (int)$data['biblio_id'],
      (string)$data['item_code'],
      (int)$data['coll_type_id'],
      (int)$data['location_id'],
      (int)$data['item_status_id'],
      (int)$userId
    ]);

    return DB::lastId();
  }

  public function bulkDelete(array $itemIds): array
{
  $deleted = 0;
  $blocked = 0;

  foreach ($itemIds as $itemId) {

    // cek masih ada?
    $row = DB::selectOne(
      "SELECT item_code FROM item WHERE item_id=? LIMIT 1",
      "i",
      [$itemId]
    );

    if (!$row) continue;

    // cek sedang dipinjam
    $active = DB::selectOne(
      "SELECT loan_id FROM loan
       WHERE item_code=?
         AND is_lent=1
         AND is_return=0
       LIMIT 1",
      "s",
      [$row['item_code']]
    );

    if ($active) {
      $blocked++;
      continue;
    }

    DB::exec("DELETE FROM item WHERE item_id=?", "i", [$itemId]);
    $deleted++;
  }

  return [
    'deleted' => $deleted,
    'blocked' => $blocked
  ];
}

  /**
   * Eksemplar boleh dihapus jika TIDAK sedang dipinjam aktif
   */
  public function canDelete(int $itemId): bool
  {
    // ambil item_code
    $row = DB::select("SELECT item_code FROM item WHERE item_id = ? LIMIT 1", "i", [$itemId]);
    if (empty($row)) return false;

    $itemCode = (string)$row[0]['item_code'];

    // cek pinjaman aktif
    $active = DB::select(
      "SELECT loan_id FROM loan
       WHERE item_code = ?
         AND is_lent = 1
         AND is_return = 0
       LIMIT 1",
      "s",
      [$itemCode]
    );

    return empty($active);
  }

  public function delete(int $itemId): void
  {
    DB::exec("DELETE FROM item WHERE item_id = ?", "i", [$itemId]);
  }
}