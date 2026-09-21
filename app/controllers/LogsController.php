<?php
class LogsController {
  public function index() {
    if (!ACL::isAdmin()) die('Admin only');

    $q = trim($_GET['q'] ?? '');
    $sql = "SELECT log_id, log_type, id, log_location, action, log_msg, log_date
            FROM system_log WHERE 1=1";
    $types = "";
    $params = [];

    if ($q !== '') {
      $like = "%$q%";
      $sql .= " AND (log_location LIKE ? OR action LIKE ? OR log_msg LIKE ? OR id LIKE ?)";
      $types = "ssss";
      $params = [$like, $like, $like, $like];
    }

    $sql .= " ORDER BY log_id DESC LIMIT 500";
    $rows = $types ? DB::select($sql, $types, $params) : DB::select($sql);

    require __DIR__ . '/../views/logs/index.php';
  }
}
