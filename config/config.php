<?php
date_default_timezone_set('Asia/Jakarta');

define('APP_NAME', 'Perpustakaan SMKN 9 Semarang');
define('BASE_URL', 'http://localhost/perpus-main');

// untuk pesan sederhana
function redirect($route) {
  header("Location: index.php?r=$route");
  exit;
}
