<?php
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../core/db.php';

// Optional: allow public access to covers, or require login
// For now, allow public access to covers (they're not sensitive)
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
  http_response_code(400);
  exit('Parameter tidak valid.');
}

$row = DB::selectOne(
  "SELECT cover_image FROM digital_works WHERE work_id=? AND status='published' LIMIT 1",
  "i", [$id]
);

if (!$row || empty($row['cover_image'])) {
  // Return default placeholder SVG
  header('Content-Type: image/svg+xml');
  echo '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="280" viewBox="0 0 200 280"><rect fill="#e0e7ff" width="200" height="280"/><text x="100" y="140" font-family="Arial" font-size="24" fill="#6366f1" text-anchor="middle" dominant-baseline="middle">📄</text></svg>';
  exit;
}

$coverPath = __DIR__ . '/../storage/digital_works/covers/' . $row['cover_image'];

if (!file_exists($coverPath)) {
  header('Content-Type: image/svg+xml');
  echo '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="280" viewBox="0 0 200 280"><rect fill="#e0e7ff" width="200" height="280"/><text x="100" y="140" font-family="Arial" font-size="24" fill="#6366f1" text-anchor="middle" dominant-baseline="middle">📄</text></svg>';
  exit;
}

$ext = strtolower(pathinfo($coverPath, PATHINFO_EXTENSION));
$mime = $ext === 'png' ? 'image/png' : 'image/jpeg';

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($coverPath));
header('Cache-Control: public, max-age=86400');

readfile($coverPath);
exit;