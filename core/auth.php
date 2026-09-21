<?php
class Auth {
  public static function check(): bool {
    return isset($_SESSION['user']);
  }

  public static function user(): ?array {
    return $_SESSION['user'] ?? null;
  }

  public static function login(array $u): void {
    $_SESSION['user'] = [
      'user_id' => (int)$u['user_id'],
      'username' => $u['username'],
      'realname' => $u['realname'],
      'groups' => $u['groups'] ?? '',
    ];
  }

  public static function logout(): void {
    unset($_SESSION['user']);
  }
}
