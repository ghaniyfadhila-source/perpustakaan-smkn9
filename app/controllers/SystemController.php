<?php
class SystemController {

  public function users() {
    if (!ACL::isAdmin()) die('Admin only');

    $rows = DB::select("
      SELECT user_id, username, realname, email, `groups`, last_login
      FROM `user`
      ORDER BY user_id DESC
    ");

    require __DIR__ . '/../views/system/users.php';
  }


  public function changePassword() {
    $userId = Auth::user()['user_id'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $old  = $_POST['old_password'] ?? '';
      $new  = $_POST['new_password'] ?? '';
      $new2 = $_POST['new_password2'] ?? '';

      if ($new === '' || $old === '') {
        $error = "Password tidak boleh kosong";
      } elseif ($new !== $new2) {
        $error = "Konfirmasi password tidak sama";
      } else {
        $u = DB::selectOne("SELECT * FROM `user` WHERE user_id=? LIMIT 1", "i", [$userId]);
        if (!$u || !password_verify($old, $u['passwd'])) {
          $error = "Password lama salah";
        } else {
          $hash = password_hash($new, PASSWORD_DEFAULT);
          DB::exec("UPDATE `user` SET passwd=?, last_update=CURDATE() WHERE user_id=?", "si", [$hash, $userId]);

          $_SESSION['flash'] = ['type'=>'success','msg'=>'Password berhasil diubah.'];
          header("Location: index.php?r=dashboard/index");
          exit;
        }
      }
    }

    require __DIR__ . '/../views/system/change_password.php';
  }


  /* ================= LOAN RULES ================= */

  public function fineRules() {
    if (!ACL::isAdmin()) die('Admin only');

    // DELETE
    if (isset($_GET['hapus'])) {
      DB::exec("DELETE FROM mst_loan_rules WHERE loan_rules_id=?", "i", [$_GET['hapus']]);
      header("Location: index.php?r=system/fineRules");
      exit;
    }

    // 🔥 FIX URUTAN ID DARI KECIL KE BESAR
    $rows = DB::select("
      SELECT * FROM mst_loan_rules
      ORDER BY loan_rules_id ASC
    ");

    require __DIR__ . '/../views/system/fine_rules.php';
  }


  public function addRule() {
    $data = null;
    require __DIR__ . '/../views/system/fine_rules_form.php';
  }


  public function editRule() {
    $id = intval($_GET['id']);

    $data = DB::selectOne("
      SELECT * FROM mst_loan_rules WHERE loan_rules_id=?
    ", "i", [$id]);

    require __DIR__ . '/../views/system/fine_rules_form.php';
  }


  public function saveRule() {

    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    $member_type_id = intval($_POST['member_type_id']);
    $coll_type_id   = intval($_POST['coll_type_id']);
    $gmd_id         = intval($_POST['gmd_id']);
    $loan_limit     = intval($_POST['loan_limit']);
    $loan_periode   = intval($_POST['loan_periode']);
    $fine_each_day  = intval($_POST['fine_each_day']);
    $grace_periode  = intval($_POST['grace_periode']);

    if ($id > 0) {

      DB::exec("
        UPDATE mst_loan_rules SET
        member_type_id=?, coll_type_id=?, gmd_id=?,
        loan_limit=?, loan_periode=?, fine_each_day=?, grace_periode=?
        WHERE loan_rules_id=?
      ", "iiiiiiii", [
        $member_type_id,
        $coll_type_id,
        $gmd_id,
        $loan_limit,
        $loan_periode,
        $fine_each_day,
        $grace_periode,
        $id
      ]);

    } else {

      DB::exec("
        INSERT INTO mst_loan_rules
        (member_type_id,coll_type_id,gmd_id,loan_limit,loan_periode,fine_each_day,grace_periode)
        VALUES (?,?,?,?,?,?,?)
      ", "iiiiiii", [
        $member_type_id,
        $coll_type_id,
        $gmd_id,
        $loan_limit,
        $loan_periode,
        $fine_each_day,
        $grace_periode
      ]);
    }

    header("Location: index.php?r=system/fineRules");
    exit;
  }

}