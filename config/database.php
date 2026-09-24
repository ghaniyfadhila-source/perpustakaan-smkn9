<?php
return [
  'host' => getenv('MYSQLHOST') ?: (getenv('DB_HOST') ?: 'localhost'),
  'user' => getenv('MYSQLUSER') ?: (getenv('DB_USER') ?: 'root'),
  'pass' => getenv('MYSQLPASSWORD') ?: (getenv('DB_PASS') ?: ''),
  'name' => getenv('MYSQLDATABASE') ?: (getenv('DB_NAME') ?: 'perpussmkn9'),
  'port' => getenv('MYSQLPORT') ?: (getenv('DB_PORT') ?: 3306),
];