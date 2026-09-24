<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<?php if (!empty($_SESSION['flash'])): ?>
  <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type']) ?> alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($_SESSION['flash']['msg']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
  <div>
    <h4 class="mb-0">Katalog Buku</h4>
    <div class="text-muted small">Cari dan pinjam buku perpustakaan</div>
  </div>
  <form method="get" class="d-flex gap-2" style="max-width:400px;">
    <input type="hidden" name="r" value="student/books/index">
    <input type="search" name="q" class="form-control form-control-sm"
           placeholder="Cari judul, ISBN, penulis..." value="<?= htmlspecialchars($q ?? '') ?>">
    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i></button>
  </form>
</div>

<?php if (empty($rows)): ?>
  <div class="text-center py-5">
    <i class="bi bi-book display-1 text-muted"></i>
    <p class="text-muted mt-3">Tidak ada buku ditemukan<?= ($q ?? '') ? ' untuk pencarian "'.htmlspecialchars($q).'"' : '' ?></p>
  </div>
<?php else: ?>
  <div class="row g-3">
    <?php foreach ($rows as $book): ?>
      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <div class="card h-100">
          <div class="card-body d-flex flex-column">
            <h6 class="card-title fw-bold"><?= htmlspecialchars($book['title'] ?? '-') ?></h6>
            <div class="small text-muted mb-2">
              <?php if (!empty($book['isbn_issn'])): ?>
                <div>ISBN: <?= htmlspecialchars($book['isbn_issn']) ?></div>
              <?php endif; ?>
              <?php if (!empty($book['publish_year'])): ?>
                <div>Tahun: <?= htmlspecialchars($book['publish_year']) ?></div>
              <?php endif; ?>
              <?php if (!empty($book['classification'])): ?>
                <div>Klasifikasi: <?= htmlspecialchars($book['classification']) ?></div>
              <?php endif; ?>
            </div>
            <div class="mt-auto pt-2">
              <?php $avail = $book['available_copies'] ?? 0; ?>
              <span class="badge <?= $avail > 0 ? 'text-bg-success' : 'text-bg-danger' ?> me-1">
                <?= $avail > 0 ? 'Tersedia ('.$avail.')' : 'Tidak Tersedia' ?>
              </span>
              <div class="d-flex flex-column gap-2 mt-2">
                <a href="index.php?r=student/books/show&id=<?= $book['biblio_id'] ?>" class="btn btn-outline-primary btn-sm w-100">
                  <i class="bi bi-eye me-1"></i>Detail & Request
                </a>
                <a href="index.php?r=student/digital/index" class="btn btn-outline-success btn-sm w-100">
                  <i class="bi bi-book me-1"></i>Baca Online
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
