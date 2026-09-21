<?php
class SeriesController {
  public function index() {
    $q = trim($_GET['q'] ?? '');
    $rows = [];

    if ($q !== '') {
      $like = "%$q%";
      $rows = DB::select("
        SELECT biblio_id, title, series_title, publish_year
        FROM biblio
        WHERE series_title LIKE ?
        ORDER BY series_title ASC, biblio_id DESC
        LIMIT 200
      ", "s", [$like]);
    }

    require __DIR__ . '/../views/series/index.php';
  }
}
