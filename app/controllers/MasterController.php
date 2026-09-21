<?php
class MasterController {

  private function map(string $type): array {
    // mapping type -> table + pk + name column
    $maps = [
      'gmd' => ['table'=>'mst_gmd', 'pk'=>'gmd_id', 'name'=>'gmd_name'],
      'content_type' => ['table'=>'mst_content_type', 'pk'=>'id', 'name'=>'content_type'],
      'media_type' => ['table'=>'mst_media_type', 'pk'=>'id', 'name'=>'media_type'],
      'carrier_type' => ['table'=>'mst_carrier_type', 'pk'=>'id', 'name'=>'carrier_type'],
      'publisher' => ['table'=>'mst_publisher', 'pk'=>'publisher_id', 'name'=>'publisher_name'],
      'supplier' => ['table'=>'mst_supplier', 'pk'=>'supplier_id', 'name'=>'supplier_name'],
      'author' => ['table'=>'mst_author', 'pk'=>'author_id', 'name'=>'author_name'],
      'subject' => ['table'=>'mst_topic', 'pk'=>'topic_id', 'name'=>'topic'],
      'location' => ['table'=>'mst_location', 'pk'=>'location_id', 'name'=>'location_name'],
    ];
    return $maps[$type] ?? $maps['gmd'];
  }

  public function index() {
    $type = $_GET['type'] ?? 'gmd';
    $m = $this->map($type);

    $rows = DB::select("SELECT * FROM {$m['table']} ORDER BY {$m['name']} ASC LIMIT 500");

    require __DIR__ . '/../views/master/index.php';
  }

  public function create() {
    if (!ACL::isAdmin()) die('Admin only');
    $type = $_GET['type'] ?? 'gmd';
    $m = $this->map($type);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $name = trim($_POST['name'] ?? '');
      if ($name === '') $error = "Nama wajib diisi";
      else {
        // insert sederhana (kolom lain dibiarkan default)
        DB::exec("INSERT INTO {$m['table']} ({$m['name']}) VALUES (?)", "s", [$name]);
        AuditService::log('staff', (string)Auth::user()['user_id'], 'master', 'create', "CREATE_MASTER $type name=$name");
        $_SESSION['flash'] = ['type'=>'success','msg'=>'Master data ditambahkan.'];
        header("Location: index.php?r=master/index&type=".$type);
        exit;
      }
    }

    require __DIR__ . '/../views/master/create.php';
  }

  public function delete() {
    if (!ACL::isAdmin()) die('Admin only');
    $type = $_GET['type'] ?? 'gmd';
    $id = $_GET['id'] ?? '';
    $m = $this->map($type);

    DB::exec("DELETE FROM {$m['table']} WHERE {$m['pk']}=?", "s", [$id]);
    AuditService::log('staff', (string)Auth::user()['user_id'], 'master', 'delete', "DELETE_MASTER $type id=$id");
    $_SESSION['flash'] = ['type'=>'success','msg'=>'Master data dihapus.'];
    header("Location: index.php?r=master/index&type=".$type);
    exit;
  }
}
