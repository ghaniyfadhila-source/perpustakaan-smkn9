<?php
class DB {
  public static function conn(): mysqli {
    static $conn = null;
    if ($conn) return $conn;

    // ✅ FIX: ambil config database yang benar
    $cfg = require __DIR__ . '/../config/database.php';

    $conn = new mysqli($cfg['host'], $cfg['user'], $cfg['pass'], $cfg['name']);
    if ($conn->connect_error) die("DB Error: " . $conn->connect_error);
    $conn->set_charset('utf8mb4');
    return $conn;
  }

  // === AUTO TYPES GENERATOR ===
  private static function autoTypes(array $params): string {
    $types = '';
    foreach ($params as $p) {
      if (is_int($p)) $types .= 'i';
      elseif (is_float($p)) $types .= 'd';
      else $types .= 's'; // string, null, dll dianggap string
    }
    return $types;
  }

  // === SAFE BIND ===
 private static function bind(mysqli_stmt $stmt, string $types, array $params): void {
  if (count($params) === 0) return;

  // kalau types kosong, generate otomatis
  if ($types === '') {
    $types = self::autoTypes($params);
  }

  // ✅ penting: buang spasi/newline tersembunyi
  $types = trim($types);

  // validasi biar errornya jelas
  if (strlen($types) !== count($params)) {
    echo "<pre>";
    echo "BIND MISMATCH\n";
    echo "types_len=" . strlen($types) . "\n";
    echo "types=[" . $types . "]\n";
    echo "types_hex=" . bin2hex($types) . "\n"; // biar kelihatan ada spasi/newline
    echo "params_count=" . count($params) . "\n";
    var_dump($params);
    echo "</pre>";
    exit;
  }

  // bind_param butuh reference
  $refs = [];
  foreach ($params as $k => $v) {
    $refs[$k] = &$params[$k];
  }

  $stmt->bind_param($types, ...$refs);
}

  public static function select(string $sql, string $types = '', array $params = []): array {
    $stmt = self::conn()->prepare($sql);
    if (!$stmt) die("Prepare error: " . self::conn()->error);

    self::bind($stmt, $types, $params);

    $stmt->execute();
    $res = $stmt->get_result();
    $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    $stmt->close();
    return $rows;
  }

  public static function selectOne(string $sql, string $types = '', array $params = []): ?array {
    $rows = self::select($sql, $types, $params);
    return $rows[0] ?? null;
  }

  public static function exec(string $sql, string $types = '', array $params = []): int {
    $stmt = self::conn()->prepare($sql);
    if (!$stmt) die("Prepare error: " . self::conn()->error);

    self::bind($stmt, $types, $params);

    $stmt->execute();
    $affected = $stmt->affected_rows;
    $stmt->close();
    return $affected;
  }

  public static function lastId(): int {
    return self::conn()->insert_id;
  }
}
