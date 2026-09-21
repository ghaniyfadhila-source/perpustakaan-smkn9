<?php
class BookModel {

  public function latest(int $limit = 50): array {
    $sql = "SELECT biblio_id, title, isbn_issn, publish_year, series_title, classification
            FROM biblio
            ORDER BY biblio_id DESC
            LIMIT ?";
    return DB::select($sql, "i", [$limit]);
  }

  public function search(string $q): array {
    $q = trim($q);
    $like = "%$q%";

    $sql = "SELECT b.biblio_id, b.title, b.isbn_issn, b.publish_year, b.series_title, b.classification
            FROM biblio b
            WHERE
              (
                b.title LIKE ?
                OR b.isbn_issn LIKE ?
                OR b.series_title LIKE ?
                OR b.call_number LIKE ?
                OR b.classification LIKE ?

                OR EXISTS (
                  SELECT 1
                  FROM item i
                  WHERE i.biblio_id = b.biblio_id
                    AND i.item_code = ?
                )
                OR EXISTS (
                  SELECT 1
                  FROM item i2
                  WHERE i2.biblio_id = b.biblio_id
                    AND i2.item_code LIKE ?
                )
              )
            ORDER BY b.biblio_id DESC
            LIMIT 50";

    return DB::select($sql, "sssssss", [
      $like, $like, $like, $like, $like,
      $q,
      $like
    ]);
  }

  public function find(int $id): ?array {
    return DB::selectOne(
      "SELECT * FROM biblio WHERE biblio_id=? LIMIT 1",
      "i",
      [$id]
    );
  }

  public function findWithRefs(int $id): ?array {
    $sql = "SELECT b.*,
              g.gmd_name,
              p.publisher_name
            FROM biblio b
            LEFT JOIN mst_gmd g ON g.gmd_id=b.gmd_id
            LEFT JOIN mst_publisher p ON p.publisher_id=b.publisher_id
            WHERE b.biblio_id=? LIMIT 1";

    return DB::selectOne($sql, "i", [$id]);
  }

  public function masters(): array {
    // penting: kalau DB::select kamu wajib types+params
    $gmds = DB::select(
      "SELECT gmd_id, gmd_name FROM mst_gmd ORDER BY gmd_name ASC",
      "",
      []
    );

    $publishers = DB::select(
      "SELECT publisher_id, publisher_name FROM mst_publisher ORDER BY publisher_name ASC",
      "",
      []
    );

    return [
      'gmds'=>$gmds,
      'publishers'=>$publishers
    ];
  }

  public function create(array $data, int $userId): int {
    $sql = "INSERT INTO biblio
              (gmd_id, title, edition, isbn_issn, publisher_id, publish_year,
               series_title, call_number, classification, notes, input_date, uid)
            VALUES
              (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)";

    $params = [
      (int)($data['gmd_id'] ?? 0),
      (string)($data['title'] ?? ''),
      (string)($data['edition'] ?? ''),
      (string)($data['isbn_issn'] ?? ($data['isbn'] ?? '')),
      (int)($data['publisher_id'] ?? 0),
      (int)($data['publish_year'] ?? 0),
      (string)($data['series_title'] ?? ''),
      (string)($data['call_number'] ?? ''),
      (string)($data['classification'] ?? ''),
      (string)($data['notes'] ?? ''),
      (int)$userId
    ];

    DB::exec($sql, "", $params); // autoTypes
    return DB::lastId();
  }

  public function update(int $id, array $data): int {
    $sql = "UPDATE biblio
            SET gmd_id=?,
                title=?,
                edition=?,
                isbn_issn=?,
                publisher_id=?,
                publish_year=?,
                series_title=?,
                call_number=?,
                classification=?,
                notes=?,
                last_update=NOW()
            WHERE biblio_id=?";

    $params = [
      (int)($data['gmd_id'] ?? 0),
      (string)($data['title'] ?? ''),
      (string)($data['edition'] ?? ''),
      (string)($data['isbn_issn'] ?? ($data['isbn'] ?? '')),
      (int)($data['publisher_id'] ?? 0),
      (int)($data['publish_year'] ?? 0),
      (string)($data['series_title'] ?? ''),
      (string)($data['call_number'] ?? ''),
      (string)($data['classification'] ?? ''),
      (string)($data['notes'] ?? ''),
      (int)$id
    ];

    return DB::exec($sql, "", $params);
  }

  public function canDelete(int $biblioId): bool {

    // ❌ tidak boleh kalau masih punya eksemplar
    $hasCopy = DB::selectOne(
      "SELECT item_id FROM item WHERE biblio_id=? LIMIT 1",
      "i",
      [$biblioId]
    );
    if ($hasCopy) return false;

    // ❌ tidak boleh kalau punya riwayat peminjaman
    $hasHistory = DB::selectOne(
      "SELECT loan_id FROM loan_history WHERE biblio_id=? LIMIT 1",
      "i",
      [$biblioId]
    );
    if ($hasHistory) return false;

    return true;
  }

  public function delete(int $id): int {
    return DB::exec(
      "DELETE FROM biblio WHERE biblio_id=?",
      "i",
      [$id]
    );
  }
}