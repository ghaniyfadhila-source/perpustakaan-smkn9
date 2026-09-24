<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<?php if (!empty($_SESSION['flash'])): $f=$_SESSION['flash']; unset($_SESSION['flash']); ?>
  <div class="alert alert-<?= htmlspecialchars($f['type']) ?> alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($f['msg']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<div class="d-flex flex-wrap align-items-start justify-content-between mb-3">
  <div>
    <h4 class="mb-1"><?= htmlspecialchars($work['title']) ?></h4>
    <div class="text-muted small">Oleh: <?= htmlspecialchars($work['author_name']) ?></div>
  </div>
  <div class="d-flex gap-2">
    <a class="btn btn-outline-secondary" href="index.php?r=digital_works/index">
      <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
    <a class="btn btn-outline-primary" href="index.php?r=digital_works/edit&id=<?= (int)$work['work_id'] ?>">
      <i class="bi bi-pencil me-1"></i>Edit
    </a>
    <a class="btn btn-danger" href="index.php?r=digital_works/delete&id=<?= (int)$work['work_id'] ?>"
       onclick="return confirm('Yakin hapus karya ini?')">
      <i class="bi bi-trash me-1"></i>Hapus
    </a>
  </div>
</div>

<div class="row g-3">
  <div class="col-12 col-lg-5">
    <div class="card shadow-sm">
      <div class="card-body">
        <?php if (!empty($work['cover_image'])): ?>
          <img src="<?= BASE_URL ?>/public/serve_cover.php?id=<?= (int)$work['work_id'] ?>"
               alt="cover" class="w-100 mb-3" style="max-height:300px;object-fit:contain;border-radius:8px;">
        <?php else: ?>
          <div class="text-center py-4 mb-3" style="background:var(--accent-soft);border-radius:8px;">
            <i class="bi bi-file-earmark-text display-3" style="color:var(--accent);"></i>
          </div>
        <?php endif; ?>

        <div class="small text-muted">Deskripsi:</div>
        <div><?= nl2br(htmlspecialchars($work['description'] ?? '-')) ?></div>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-7">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="fw-semibold mb-3">Detail Karya</div>

        <div class="row g-3">
          <div class="col-6">
            <div class="small text-muted">Status</div>
            <?php if ($work['status'] === 'published'): ?>
              <span class="badge text-bg-success">Published</span>
            <?php else: ?>
              <span class="badge text-bg-secondary">Draft</span>
            <?php endif; ?>
          </div>
          <div class="col-6">
            <div class="small text-muted">Views</div>
            <div class="fw-semibold"><?= (int)($work['view_count'] ?? 0) ?></div>
          </div>
          <div class="col-6">
            <div class="small text-muted">Ukuran File</div>
            <div class="fw-semibold"><?= round(($work['file_size'] ?? 0) / 1024 / 1024, 2) ?> MB</div>
          </div>
          <div class="col-6">
            <div class="small text-muted">Tanggal Upload</div>
            <div class="fw-semibold"><?= date('d M Y H:i', strtotime($work['input_date'])) ?></div>
          </div>
        </div>

        <hr>

        <div class="small text-muted mb-1">File PDF</div>
        <a href="storage/digital_works/pdfs/<?= htmlspecialchars($work['pdf_file']) ?>"
           target="_blank" class="btn btn-outline-dark btn-sm">
          <i class="bi bi-file-earmark-pdf me-1"></i>Lihat PDF
        </a>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
