<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<?php if (!empty($_SESSION['flash'])): $f=$_SESSION['flash']; unset($_SESSION['flash']); ?>
  <div class="alert alert-<?= htmlspecialchars($f['type']) ?> alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($f['msg']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
  <div>
    <h4 class="mb-0">Katalog Buku</h4>
    <div class="text-muted small">Cari, tambah, edit, hapus buku dan eksemplar</div>
  </div>
  <a class="btn btn-primary" href="index.php?r=books/create">
    <i class="bi bi-plus-lg me-1"></i>Tambah Buku
  </a>
</div>

<div class="card shadow-sm mb-3">
  <div class="card-body">
    <form class="row g-2" method="get">
      <input type="hidden" name="r" value="books/index">
      <div class="col-12 col-md-10">
        <input class="form-control" name="q" value="<?= htmlspecialchars($q) ?>"
               placeholder="Cari judul / ISBN / seri / call number / klasifikasi...">
      </div>
      <div class="col-12 col-md-2 d-grid">
        <button class="btn btn-outline-primary">
          <i class="bi bi-search me-1"></i>Cari
        </button>
      </div>
      <div class="col-12">
        <div class="text-muted small">
          Tips: isi nama seri untuk fitur <b>cari seri buku</b> (kolom <code>series_title</code>).
        </div>
      </div>
    </form>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th style="width:70px">ID</th>
            <th>Judul</th>
            <th style="width:160px">ISBN</th>
            <th style="width:110px">Tahun</th>
            <th>Seri</th>
            <th style="width:140px">Klasifikasi</th>
            <th class="text-end" style="width:220px">Aksi</th>
          </tr>
        </thead>
        <tbody>
        <?php if (empty($rows)): ?>
          <tr><td colspan="7" class="text-center text-muted py-4">Data tidak ditemukan</td></tr>
        <?php else: ?>
          <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= (int)$r['biblio_id'] ?></td>
            <td>
              <div class="fw-semibold"><?= htmlspecialchars($r['title']) ?></div>
              <div class="text-muted small">Seri: <?= htmlspecialchars($r['series_title'] ?? '-') ?></div>
            </td>
            <td><?= htmlspecialchars($r['isbn_issn'] ?? '-') ?></td>
            <td><?= htmlspecialchars($r['publish_year'] ?? '-') ?></td>
            <td><?= htmlspecialchars($r['series_title'] ?? '-') ?></td>
            <td><?= htmlspecialchars($r['classification'] ?? '-') ?></td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-dark" href="index.php?r=books/show&id=<?= (int)$r['biblio_id'] ?>">
                <i class="bi bi-eye me-1"></i>Detail
              </a>
              <a class="btn btn-sm btn-outline-primary" href="index.php?r=books/edit&id=<?= (int)$r['biblio_id'] ?>">
                <i class="bi bi-pencil me-1"></i>Edit
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
