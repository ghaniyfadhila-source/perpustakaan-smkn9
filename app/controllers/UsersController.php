<?php
class UsersController {

  public function index() {
    if (!ACL::isAdmin()) die('Admin only');
    $rows = DB::select("SELECT user_id, username, realname, email, `groups`, last_login FROM `user` ORDER BY user_id DESC");
    require __DIR__ . '/../views/users/index.php';
  }

  public function create() {
    if (!ACL::isAdmin()) die('Admin only');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $username = trim($_POST['username'] ?? '');
      $realname = trim($_POST['realname'] ?? '');
      $email = trim($_POST['email'] ?? '');
      $group = trim($_POST['groups'] ?? '2'); // 1 admin, 2 operator
      $pass = $_POST['password'] ?? '';

      if ($username==='' || $realname==='' || $pass==='') $error="Username, nama, password wajib";
      else {
        $exists = DB::selectOne("SELECT user_id FROM `user` WHERE username=? LIMIT 1", "s", [$username]);
        if ($exists) $error="Username sudah dipakai";
        else {
          $hash = password_hash($pass, PASSWORD_DEFAULT);
          DB::exec("INSERT INTO `user` (username, realname, passwd, email, `groups`, input_date)
                    VALUES (?, ?, ?, ?, ?, CURDATE())",
                    "sssss", [$username, $realname, $hash, $email, $group]);

          AuditService::log('staff', (string)Auth::user()['user_id'], 'system', 'create_user', "CREATE_USER username=$username groups=$group");
          $_SESSION['flash']=['type'=>'success','msg'=>'User berhasil dibuat.'];
          header("Location: index.php?r=users/index");
          exit;
        }
      }
    }

    require __DIR__ . '/../views/users/create.php';
  }

  public function edit() {
    if (!ACL::isAdmin()) die('Admin only');
    $id = (int)($_GET['id'] ?? 0);
    $user = DB::selectOne("SELECT user_id, username, realname, email, `groups` FROM `user` WHERE user_id=? LIMIT 1", "i", [$id]);
    if (!$user) die('User tidak ditemukan');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $realname = trim($_POST['realname'] ?? '');
      $email = trim($_POST['email'] ?? '');
      $group = trim($_POST['groups'] ?? $user['groups']);

      DB::exec("UPDATE `user` SET realname=?, email=?, `groups`=?, last_update=CURDATE() WHERE user_id=?",
               "sssi", [$realname, $email, $group, $id]);

      AuditService::log('staff', (string)Auth::user()['user_id'], 'system', 'update_user', "UPDATE_USER id=$id groups=$group");
      $_SESSION['flash']=['type'=>'success','msg'=>'User berhasil diupdate.'];
      header("Location: index.php?r=users/index");
      exit;
    }

    require __DIR__ . '/../views/users/edit.php';
  }

  public function resetPassword() {
    if (!ACL::isAdmin()) die('Admin only');
    $id = (int)($_GET['id'] ?? 0);

    $user = DB::selectOne("SELECT user_id, username FROM `user` WHERE user_id=? LIMIT 1", "i", [$id]);
    if (!$user) die('User tidak ditemukan');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $pass = $_POST['password'] ?? '';
      if ($pass === '') $error="Password tidak boleh kosong";
      else {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        DB::exec("UPDATE `user` SET passwd=?, last_update=CURDATE() WHERE user_id=?", "si", [$hash, $id]);

        AuditService::log('staff', (string)Auth::user()['user_id'], 'system', 'reset_password', "RESET_PASSWORD user_id=$id");
        $_SESSION['flash']=['type'=>'success','msg'=>'Password berhasil direset.'];
        header("Location: index.php?r=users/index");
        exit;
      }
    }

    require __DIR__ . '/../views/users/reset_password.php';
  }

  public function delete() {
    if (!ACL::isAdmin()) die('Admin only');
    $id = (int)($_GET['id'] ?? 0);

    if ($id === (int)Auth::user()['user_id']) {
      $_SESSION['flash']=['type'=>'danger','msg'=>'Tidak bisa hapus akun sendiri.'];
      header("Location: index.php?r=users/index");
      exit;
    }

    DB::exec("DELETE FROM `user` WHERE user_id=?", "i", [$id]);
    AuditService::log('staff', (string)Auth::user()['user_id'], 'system', 'delete_user', "DELETE_USER user_id=$id");

    $_SESSION['flash']=['type'=>'success','msg'=>'User dihapus.'];
    header("Location: index.php?r=users/index");
    exit;
  }
}
