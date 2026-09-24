<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<?php
// Generate temporary token for PDF access (valid 10 menit)
$token = bin2hex(random_bytes(16));
$workId = (int)$work['work_id'];
$expires = date('Y-m-d H:i:s', time() + 600);

DB::exec(
  "INSERT INTO pdf_access_tokens (token, work_id, expires_at) VALUES (?, ?, ?)",
  "sis", [$token, $workId, $expires]
);

// Cleanup expired tokens
DB::exec("DELETE FROM pdf_access_tokens WHERE expires_at < NOW()", "", []);
$pdfUrl = BASE_URL . '/public/serve_pdf.php?token=' . $token;
?>

<div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
  <div>
    <a href="index.php?r=student/digital/index" class="text-decoration-none text-muted small">
      <i class="bi bi-arrow-left me-1"></i>Kembali ke Galeri
    </a>
    <h4 class="mb-0 mt-1"><?= htmlspecialchars($work['title']) ?></h4>
    <div class="text-muted small">
      <i class="bi bi-person me-1"></i><?= htmlspecialchars($work['author_name']) ?>
    </div>
  </div>
  <a href="<?= $pdfUrl ?>" target="_blank" class="btn btn-sm btn-outline-light">
    <i class="bi bi-box-arrow-up-right me-1"></i>Buka di Tab Baru
  </a>
</div>

<div class="card shadow-sm" style="overflow:hidden;">
  <div class="card-body p-0" style="background:#525659;">
    <iframe
      src="<?= $pdfUrl ?>"
      style="width:100%;height:calc(100vh - 200px);min-height:500px;border:none;"
    ></iframe>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>