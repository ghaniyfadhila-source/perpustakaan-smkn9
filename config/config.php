<?php
date_default_timezone_set('Asia/Jakarta');

define('APP_NAME', 'Perpustakaan SMKN 9 Semarang');
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ? 'https://' : 'http://';
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
$default_base = $protocol . $host;

// If we are on localhost, append /perpus-main, otherwise assume root domain on Railway
if ($host === 'localhost' || $host === '127.0.0.1') {
    $default_base .= '/perpus-main';
}

define('BASE_URL', $default_base);

// untuk pesan sederhana
function redirect($route) {
  header("Location: index.php?r=$route");
  exit;
}
