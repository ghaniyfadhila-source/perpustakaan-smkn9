<?php
class StockController {
  public function scan() {
    $code = trim($_GET['code'] ?? '');
    $data = null;

    if ($code !== '') {
      $svc = new StockService();
      $data = $svc->scan($code);
    }

    require __DIR__ . '/../views/stock/scan.php';
  }
}
