<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
      <div>
        <a href="index.php?r=student/books/index" class="btn btn-outline-secondary btn-sm mb-2"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
        <h4 class="mb-0"><i class="bi bi-book me-2"></i><?= htmlspecialchars($book['title'] ?? '-') ?></h4>
      </div>
    </div>

    <?php if (!empty($_SESSION['flash'])): ?>
      <div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show" role="alert">
        <?= $_SESSION['flash']['msg'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <?php if (isset($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="row g-4">
      <div class="col-md-8">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($book['title'] ?? '-') ?></h5>
            <hr>
            <div class="row">
              <div class="col-md-6">
                <p class="mb-1"><strong>ISBN/ISSN:</strong> <?= htmlspecialchars($book['isbn_issn'] ?? '-') ?></p>
                <p class="mb-1"><strong>Tahun Terbit:</strong> <?= htmlspecialchars($book['publish_year'] ?? '-') ?></p>
                <p class="mb-1"><strong>Edisi:</strong> <?= htmlspecialchars($book['edition'] ?? '-') ?></p>
                <p class="mb-1"><strong>Penerbit:</strong> <?= htmlspecialchars($book['publisher_name'] ?? '-') ?></p>
              </div>
              <div class="col-md-6">
                <p class="mb-1"><strong>Klasifikasi:</strong> <?= htmlspecialchars($book['classification'] ?? '-') ?></p>
                <p class="mb-1"><strong>Nomor Panggil:</strong> <?= htmlspecialchars($book['call_number'] ?? '-') ?></p>
                <p class="mb-1"><strong>Jenis Buku:</strong> <?= htmlspecialchars($book['gmd_name'] ?? '-') ?></p>
                <p class="mb-1"><strong>Bahasa:</strong> <?= htmlspecialchars($book['language_id'] ?? '-') ?></p>
              </div>
            </div>
            <?php if (!empty($book['notes'])): ?>
              <hr>
              <p class="mb-0"><strong>Catatan:</strong></p>
              <p><?= nl2br(htmlspecialchars($book['notes'])) ?></p>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0"><i class="bi bi-box-seam me-2"></i>Eksemplar Tersedia</h6>
          </div>
          <div class="card-body">
            <?php if (empty($copies)): ?>
              <div class="text-center py-4">
                <i class="bi bi-x-circle display-1 text-danger"></i>
                <p class="text-muted mt-2">Tidak ada eksemplar tersedia</p>
                <p class="small text-muted">Semua eksemplar sedang dipinjam atau tidak tersedia</p>
              </div>
            <?php else: ?>
              <form method="POST" action="index.php?r=student/books/request&id=<?= $book['biblio_id'] ?>">
                <input type="hidden" name="biblio_id" value="<?= $book['biblio_id'] ?>">
                <div class="mb-3">
                  <label class="form-label">Pilih Eksemplar (Opsional)</label>
                  <select name="item_code" class="form-select form-select-sm">
                    <option value="">Otomatis (eksemplar tersedia pertama)</option>
                    <?php foreach ($copies as $copy): ?>
                      <option value="<?= htmlspecialchars($copy['item_code']) ?>">
                        <?= htmlspecialchars($copy['item_code']) ?>
                        (<?= htmlspecialchars($copy['location_name'] ?? $copy['location_id'] ?? '-') ?>
                        - <?= htmlspecialchars($copy['item_status_name'] ?? '-') ?>)
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <div class="form-text">Kosongkan untuk memilih eksemplar tersedia secara otomatis</div>
                </div>
                <button type="submit" class="btn btn-primary w-100">
                  <i class="bi bi-send me-1"></i> Ajukan Request Peminjaman
                </button>
              </form>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>