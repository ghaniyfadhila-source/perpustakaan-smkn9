<?php
class ACL {
  public static function groupId(): int {
    $u = Auth::user(); if (!$u) return 0;
    preg_match('/\d+/', (string)$u['groups'], $m);
    return isset($m[0]) ? (int)$m[0] : 0;
  }

  public static function isAdmin(): bool {
    return self::groupId() === 1;
  }
}
