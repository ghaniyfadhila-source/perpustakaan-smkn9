<?php
class MembersController {

  public function index() {
    $q = trim($_GET['q'] ?? '');
    if ($q !== '') {
      $like = "%$q%";
      $rows = DB::select("SELECT member_id, member_name, expire_date, member_type_id
                          FROM member
                          WHERE member_id LIKE ? OR member_name LIKE ?
                          ORDER BY member_name ASC LIMIT 100", "ss", [$like, $like]);
    } else {
      $rows = DB::select("SELECT member_id, member_name, expire_date, member_type_id
                          FROM member ORDER BY member_name ASC LIMIT 100");
    }
    require __DIR__ . '/../views/members/index.php';
  }

public function create() {
  $types = DB::select("SELECT member_type_id, member_type_name FROM mst_member_type ORDER BY member_type_name ASC");

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $this->collect();

    // Validate unique member_id
    $existingId = DB::selectOne("SELECT member_id FROM member WHERE member_id=? LIMIT 1", "s", [$data['member_id']]);
    if ($existingId) {
      $_SESSION['flash'] = ['type'=>'danger','msg'=>'Member ID sudah digunakan.'];
      require __DIR__ . '/../views/members/create.php';
      return;
    }

    // Validate unique username
    $existing = DB::selectOne("SELECT member_id FROM member WHERE username=? LIMIT 1", "s", [$data['username']]);
    if ($existing) {
      $_SESSION['flash'] = ['type'=>'danger','msg'=>'Username sudah digunakan.'];
      require __DIR__ . '/../views/members/create.php';
      return;
    }

    $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

    $birthDate = $data['birth_date'] !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['birth_date']) ? $data['birth_date'] : null;

    try {
      DB::exec(
  "INSERT INTO member
    (member_id, member_name, username, password_hash, gender, birth_date, member_type_id, member_address, member_phone,
     register_date, expire_date, input_date)
  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), ?, CURDATE())",
  "ssssisisss",
  [
    $data['member_id'],
    $data['member_name'],
    $data['username'],
    $passwordHash,
    $data['gender'],
    $birthDate,
    $data['member_type_id'],
    $data['member_address'],
    $data['member_phone'],
    $data['expire_date'],
  ]
);
    } catch (\Exception $e) {
      $_SESSION['flash'] = ['type'=>'danger','msg'=>'Gagal menyimpan: ' . $e->getMessage()];
      require __DIR__ . '/../views/members/create.php';
      return;
    }


    AuditService::log('staff', (string)Auth::user()['user_id'], 'members', 'create', "CREATE_MEMBER member_id=".$data['member_id']);
    $_SESSION['flash'] = ['type'=>'success','msg'=>'Anggota berhasil ditambahkan.'];
    header("Location: index.php?r=members/index");
    exit;
  }

  require __DIR__ . '/../views/members/create.php';
}


  public function edit() {
  $id = trim($_GET['id'] ?? '');
  $member = DB::selectOne("SELECT * FROM member WHERE member_id=? LIMIT 1", "s", [$id]);
  if (!$member) die('Anggota tidak ditemukan');

  $types = DB::select("SELECT member_type_id, member_type_name FROM mst_member_type ORDER BY member_type_name ASC");

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $this->collect(false);

    // Password optional - only update if provided
    $password = trim($_POST['password'] ?? '');
    $birthDate = $data['birth_date'] !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['birth_date']) ? $data['birth_date'] : null;
    try {
      if ($password !== '') {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        DB::exec(
          "UPDATE member SET member_name=?, gender=?, birth_date=?, member_type_id=?, member_address=?, member_phone=?, expire_date=?, password_hash=?, last_update=CURDATE() WHERE member_id=?",
          "sisisssss",
          [
            $data['member_name'],
            $data['gender'],
            $birthDate,
            $data['member_type_id'],
            $data['member_address'],
            $data['member_phone'],
            $data['expire_date'],
            $passwordHash,
            $id
          ]
        );
      } else {
        DB::exec(
          "UPDATE member SET member_name=?, gender=?, birth_date=?, member_type_id=?, member_address=?, member_phone=?, expire_date=?, last_update=CURDATE() WHERE member_id=?",
          "sisissss",
          [
            $data['member_name'],
            $data['gender'],
            $birthDate,
            $data['member_type_id'],
            $data['member_address'],
            $data['member_phone'],
            $data['expire_date'],
            $id
          ]
        );
      }
    } catch (\Exception $e) {
      $_SESSION['flash'] = ['type'=>'danger','msg'=>'Gagal mengupdate: ' . $e->getMessage()];
      require __DIR__ . '/../views/members/edit.php';
      return;
    }

    AuditService::log('staff', (string)Auth::user()['user_id'], 'members', 'update', "UPDATE_MEMBER member_id=".$id);
    $_SESSION['flash'] = ['type'=>'success','msg'=>'Anggota berhasil diupdate.'];
    header("Location: index.php?r=members/index");
    exit;
  }

  require __DIR__ . '/../views/members/edit.php';
}


  public function delete() {
    $id = trim($_GET['id'] ?? '');

    // blok kalau masih ada pinjaman aktif
    $active = DB::selectOne("SELECT loan_id FROM loan WHERE member_id=? AND is_return=0 LIMIT 1", "s", [$id]);
    if ($active) {
      $_SESSION['flash'] = ['type'=>'danger','msg'=>'Tidak bisa hapus: anggota masih punya pinjaman aktif.'];
      header("Location: index.php?r=members/index");
      exit;
    }

    DB::exec("DELETE FROM member WHERE member_id=?", "s", [$id]);
    AuditService::log('staff', (string)Auth::user()['user_id'], 'members', 'delete', "DELETE_MEMBER member_id=".$id);

    $_SESSION['flash'] = ['type'=>'success','msg'=>'Anggota dihapus.'];
    header("Location: index.php?r=members/index");
    exit;
  }

  public function printCard() {
    $id = trim($_GET['id'] ?? '');
    $member = DB::selectOne("SELECT m.member_id, m.member_name, m.expire_date, t.member_type_name
                             FROM member m
                             LEFT JOIN mst_member_type t ON t.member_type_id=m.member_type_id
                             WHERE m.member_id=? LIMIT 1", "s", [$id]);
    if (!$member) die('Anggota tidak ditemukan');
    require __DIR__ . '/../views/members/print_card.php';
  }

  private function collect(bool $includeId = true): array {
    $data = [
      'member_id' => trim($_POST['member_id'] ?? ''),
      'member_name' => trim($_POST['member_name'] ?? ''),
      'username' => trim($_POST['username'] ?? ''),
      'password' => $_POST['password'] ?? '',
      'gender' => (int)($_POST['gender'] ?? 1),
      'birth_date' => trim($_POST['birth_date'] ?? null),
      'member_type_id' => (int)($_POST['member_type_id'] ?? 0),
      'member_address' => trim($_POST['member_address'] ?? ''),
      'member_phone' => trim($_POST['member_phone'] ?? ''),
      'expire_date' => trim($_POST['expire_date'] ?? date('Y-m-d', strtotime('+1 year'))),
    ];
    if (!$includeId) unset($data['member_id']);
    return $data;
  }
}
