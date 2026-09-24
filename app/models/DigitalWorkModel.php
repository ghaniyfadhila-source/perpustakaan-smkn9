<?php
class DigitalWorkModel {

  public function all(): array {
    return DB::select(
      "SELECT * FROM digital_works ORDER BY input_date DESC",
      "", []
    );
  }

  public function published(): array {
    return DB::select(
      "SELECT * FROM digital_works WHERE status='published' ORDER BY input_date DESC",
      "", []
    );
  }

  public function search(string $q): array {
    $like = "%$q%";
    return DB::select(
      "SELECT * FROM digital_works
       WHERE status='published'
         AND (title LIKE ? OR author_name LIKE ? OR description LIKE ?)
       ORDER BY input_date DESC",
      "sss",
      [$like, $like, $like]
    );
  }

  public function find(int $id): ?array {
    return DB::selectOne(
      "SELECT * FROM digital_works WHERE work_id=? LIMIT 1",
      "i", [$id]
    );
  }

  public function create(array $data, int $userId): int {
    DB::exec(
      "INSERT INTO digital_works
         (title, author_name, description, cover_image, pdf_file, file_size, status, input_date, uid)
       VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?)",
      "",
      [
        $data['title'],
        $data['author_name'],
        $data['description'],
        $data['cover_image'] ?? '',
        $data['pdf_file'],
        (int)($data['file_size'] ?? 0),
        $data['status'] ?? 'published',
        $userId,
      ]
    );
    return DB::lastId();
  }

  public function update(int $id, array $data): int {
    $sets = [];
    $params = [];

    $fields = ['title','author_name','description','cover_image','pdf_file','file_size','status'];
    foreach ($fields as $f) {
      if (array_key_exists($f, $data)) {
        $sets[] = "$f=?";
        $params[] = $f === 'file_size' ? (int)$data[$f] : $data[$f];
      }
    }

    if (empty($sets)) return 0;

    $sets[] = "last_update=NOW()";
    $params[] = $id;

    $sql = "UPDATE digital_works SET " . implode(', ', $sets) . " WHERE work_id=?";
    return DB::exec($sql, "", $params);
  }

  public function incrementView(int $id): void {
    DB::exec(
      "UPDATE digital_works SET view_count = view_count + 1 WHERE work_id=?",
      "i", [$id]
    );
  }

  public function delete(int $id): int {
    return DB::exec(
      "DELETE FROM digital_works WHERE work_id=?",
      "i", [$id]
    );
  }

  public function count(): int {
    $r = DB::selectOne("SELECT COUNT(*) c FROM digital_works", "", []);
    return (int)($r['c'] ?? 0);
  }
}
