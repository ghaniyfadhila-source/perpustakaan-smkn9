<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<?php if (!empty($error)): ?>
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($error) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h4 class="mb-0">Edit Karya</h4>
    <div class="text-muted small"><?= htmlspecialchars($work['title']) ?></div>
  </div>
  <a class="btn btn-outline-secondary" href="index.php?r=digital_works/show&id=<?= (int)$work['work_id'] ?>">
    <i class="bi bi-arrow-left me-1"></i>Kembali
  </a>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <form method="post" enctype="multipart/form-data">
      <div class="row g-3">
        <div class="col-12">
          <label class="form-label">Judul Karya <span class="text-danger">*</span></label>
          <input class="form-control" name="title" required maxlength="255"
                 value="<?= htmlspecialchars($_POST['title'] ?? $work['title']) ?>">
        </div>

        <div class="col-12">
          <label class="form-label">Nama Penulis <span class="text-danger">*</span></label>
          <input class="form-control" name="author_name" required maxlength="150"
                 value="<?= htmlspecialchars($_POST['author_name'] ?? $work['author_name']) ?>">
        </div>

        <div class="col-12">
          <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
          <textarea class="form-control" name="description" rows="4" required
                    ><?= htmlspecialchars($_POST['description'] ?? $work['description']) ?></textarea>
        </div>

        <div class="col-12 col-md-6">
          <label class="form-label">Sampul Saat Ini</label>
          <div class="mb-2">
            <?php if (!empty($work['cover_image'])): ?>
              <img src="storage/digital_works/covers/<?= htmlspecialchars($work['cover_image']) ?>"
                   alt="cover" style="max-height:120px;border-radius:8px;">
            <?php else: ?>
              <div class="text-muted small fst-italic">Belum ada sampul</div>
            <?php endif; ?>
          </div>
          <label class="form-label">Ganti Cover <span class="text-muted">(opsional)</span></label>
          <input class="form-control" type="file" name="cover_image"
                 accept=".jpg,.jpeg,.png" onchange="previewCover(this)">
          <div class="form-text">Format: JPG/PNG, maks 2MB</div>
          <div class="mt-2">
            <img id="coverPreview" src="" alt="Preview" style="display:none;max-height:150px;border-radius:8px;">
          </div>
        </div>

        <div class="col-12 col-md-6">
          <label class="form-label">PDF Saat Ini</label>
          <div class="mb-2">
            <a href="storage/digital_works/pdfs/<?= htmlspecialchars($work['pdf_file']) ?>"
               target="_blank" class="text-decoration-none fw-semibold">
              <i class="bi bi-file-earmark-pdf me-1"></i><?= htmlspecialchars($work['pdf_file']) ?>
            </a>
            <div class="text-muted small"><?= round(($work['file_size'] ?? 0) / 1024 / 1024, 2) ?> MB</div>
          </div>
          <label class="form-label">Ganti PDF <span class="text-muted">(opsional)</span></label>
          <input class="form-control" type="file" name="pdf_file"
                 accept=".pdf" onchange="showFileName(this)">
          <div class="form-text">Format: PDF, maks 50MB. Kosongkan jika tidak ingin ganti.</div>
          <div id="pdfFileName" class="mt-1 text-muted small fw-semibold"></div>
        </div>

        <div class="col-12">
          <label class="form-label">Status</label>
          <select class="form-select" name="status">
            <option value="published" <?= ($work['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
            <option value="draft" <?= ($work['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
          </select>
        </div>
      </div>

      <div class="mt-4 d-flex gap-2">
        <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Perubahan</button>
        <a class="btn btn-outline-secondary" href="index.php?r=digital_works/show&id=<?= (int)$work['work_id'] ?>">Batal</a>
      </div>
    </form>
  </div>
</div>

<script>
function previewCover(input) {
  const img = document.getElementById('coverPreview');
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => { img.src = e.target.result; img.style.display = 'block'; };
    reader.readAsDataURL(input.files[0]);
  } else { img.style.display = 'none'; }
}
function showFileName(input) {
  document.getElementById('pdfFileName').textContent = input.files[0] ? input.files[0].name : '';
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
