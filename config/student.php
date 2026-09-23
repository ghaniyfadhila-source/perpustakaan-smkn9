<?php
date_default_timezone_set('Asia/Jakarta');

define('BASE_URL', 'http://localhost/perpus-main');

function redirect($route) {
    header("Location: index.php?r=$route");
    exit;
}