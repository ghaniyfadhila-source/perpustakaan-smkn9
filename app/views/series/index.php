<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="d-flex justify-content-between align-items-end mb-3">
  <div>
    <h4 class="mb-0">Cari Seri Buku</h4>
    <div class="text-muted small">Mencari berdasarkan series_title</div>
  </div>
</div>

<div class="card shadow-sm mb-3">
  <div class="card-body">
    <form class="row g-2" method="get">
      <input type="hidden" name="r" value="series/index">
      <div class="col-12 col-md-10">
        <input class="form-control" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Ketik nama seri...">
      </div>
      <div class="col-12 col-md-2 d-grid">
        <button class="btn btn-primary"><i class="bi bi-search me-1"></i>Cari</button>
      </div>
    </form>
  </div>
</div>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-striped table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>ID</th><th>Judul</th><th>Seri</th><th>Tahun</th><th class="text-end">Aksi</th></tr>
      </thead>
      <tbody>
        <?php if ($q==='' ): ?>
          <tr><td colspan="5" class="text-center text-muted py-4">Masukkan kata kunci seri.</td></tr>
        <?php elseif (empty($rows)): ?>
          <tr><td colspan="5" class="text-center text-muted py-4">Tidak ada hasil.</td></tr>
        <?php else: foreach ($rows as $r): ?>
          <tr>
            <td><?= (int)$r['biblio_id'] ?></td>
            <td><?= htmlspecialchars($r['title']) ?></td>
            <td><?= htmlspecialchars($r['series_title']) ?></td>
            <td><?= htmlspecialchars($r['publish_year'] ?? '-') ?></td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-dark" href="index.php?r=books/show&id=<?= (int)$r['biblio_id'] ?>">
                <i class="bi bi-eye"></i>
              </a>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
