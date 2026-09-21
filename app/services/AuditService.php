<?php
class AuditService {
  public static function log(string $type, string $id, string $location, string $action, string $msg): void {
    DB::exec("INSERT INTO system_log (log_type, id, log_location, action, log_msg, log_date)
              VALUES (?, ?, ?, ?, ?, NOW())",
              "sssss", [$type, $id, $location, $action, $msg]);
  }
}
