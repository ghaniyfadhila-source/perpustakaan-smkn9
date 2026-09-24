<?php
session_start();
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../core/db.php';

$token = $_GET['token'] ?? '';

if (!$token) {
  http_response_code(403);
  exit('Akses ditolak. Token tidak valid.');
}

$row = DB::selectOne(
  "SELECT work_id FROM pdf_access_tokens WHERE token=? AND expires_at > NOW() LIMIT 1",
  "s", [$token]
);

if (!$row) {
  http_response_code(403);
  exit('Token tidak valid atau kedaluwarsa.');
}

$workId = (int)$row['work_id'];

$work = DB::selectOne(
  "SELECT pdf_file FROM digital_works WHERE work_id=? AND status='published' LIMIT 1",
  "i", [$workId]
);

if (!$work || empty($work['pdf_file'])) {
  http_response_code(404);
  exit('Karya tidak ditemukan.');
}

$pdfPath = __DIR__ . '/../storage/digital_works/pdfs/' . $work['pdf_file'];

if (!file_exists($pdfPath)) {
  http_response_code(404);
  exit('File PDF tidak ditemukan.');
}

header('Content-Type: application/pdf');
header('Content-Length: ' . filesize($pdfPath));
header('Content-Disposition: inline; filename="' . basename($pdfPath) . '"');
header('Cache-Control: private, max-age=3600');
header('Accept-Ranges: bytes');

readfile($pdfPath);
exit;
