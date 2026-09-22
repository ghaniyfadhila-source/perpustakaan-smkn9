<?php
session_start();

require __DIR__ . '/config/config.php';
require __DIR__ . '/core/db.php';
require __DIR__ . '/core/auth.php';
require __DIR__ . '/core/acl.php';

// autoload sederhana
spl_autoload_register(function($class){
  $paths = [
    __DIR__ . "/app/controllers/$class.php",
    __DIR__ . "/app/models/$class.php",
    __DIR__ . "/app/services/$class.php",
    __DIR__ . "/app/helpers/$class.php",
  ];
  foreach ($paths as $p) {
    if (file_exists($p)) { require $p; return; }
  }
});

$route = $_GET['r'] ?? 'login/index';

// Handle student sub-controllers (student/books, student/requests, etc.)
if (str_starts_with($route, 'student/')) {
  $parts = explode('/', $route);
  // student/books/index -> ['student', 'books', 'index']
  // student/logout -> ['student', 'logout']
  $subController = $parts[1] ?? 'dashboard';
  $method = $parts[2] ?? $subController; // default ke subController jika hanya 2 bagian
  
  // Special cases that use StudentController
  $studentControllerMethods = ['logout', 'login', 'profile', 'dashboard', 'requestReturn'];
  
  if (in_array($subController, $studentControllerMethods)) {
    $controllerClass = 'StudentController';
  } else {
    $controllerClass = 'Student' . ucfirst($subController) . 'Controller';
  }
  $controller = 'student'; // for auth check
} else {
  [$controller, $method] = array_pad(explode('/', $route), 2, 'index');
  $controllerClass = ucfirst($controller) . 'Controller';
}

if (!class_exists($controllerClass)) die("Controller tidak ada: $controllerClass");
$c = new $controllerClass();

if (!method_exists($c, $method)) die("Method tidak ada: $method");

// proteksi login (admin DAN siswa)
$isLoggedIn = Auth::check() || isset($_SESSION['student']);
$controllerLower = strtolower($controller);
if (!in_array($controllerLower, ['login', 'auth']) && !$isLoggedIn) {
  redirect('login/index');
}

// dispatch dengan parameter untuk method tertentu
$result = null;

switch ($controllerClass . '::' . $method) {

  // ===== LOANS: BORROW =====
  case 'LoansController::borrow': {
    $memberId = $_POST['member_id'] ?? '';

    // dukung 2 model input:
    // 1) item_codes[] (multiple input)
    $itemCodes = $_POST['item_codes'] ?? [];

    // 2) barcodes (textarea, 1 barcode per baris)
    if (empty($itemCodes)) {
      $raw = $_POST['barcodes'] ?? '';
      $itemCodes = preg_split("/\r\n|\n|\r/", trim($raw));
      $itemCodes = array_values(array_filter(array_map('trim', $itemCodes)));
    }

    $staffId = (int)($_SESSION['user']['user_id'] ?? 0);

    $result = $c->borrow($memberId, $itemCodes, $staffId);
    break;
  }

  // ===== LOANS: RETURN =====
  case 'LoansController::returnBook': {
    $itemCode = $_POST['item_code'] ?? ($_POST['barcode'] ?? '');
    $staffId  = (int)($_SESSION['user']['user_id'] ?? 0);

    $result = $c->returnBook($itemCode, $staffId);
    break;
  }

  // ===== LOANS: EDIT TYPO =====
  case 'LoansController::editLoanTypo': {
    $loanId  = (int)($_POST['loan_id'] ?? 0);
    $newData = $_POST['data'] ?? $_POST; // fleksibel
    $staffId = (int)($_SESSION['user']['user_id'] ?? 0);

    $result = $c->editLoanTypo($loanId, $newData, $staffId);
    break;
  }

  // ===== DEFAULT: method tanpa argumen =====
  default: {
    $result = $c->$method();
    break;
  }
}
