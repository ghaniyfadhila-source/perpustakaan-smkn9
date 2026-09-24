<?php
require 'config/config.php';
require 'core/db.php';

// Username dari screenshot-mu
$username = 'slimsuser'; 
// Ini password baru yang akan kamu pakai login nanti
$password_baru = 'admin123';

$hash_baru = password_hash($password_baru, PASSWORD_DEFAULT);

DB::exec("UPDATE user SET passwd = ? WHERE username = ?", "ss", [$hash_baru, $username]);

echo "Sukses! Password untuk user '$username' berhasil diubah menjadi: $password_baru";
?>
