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
    <h4 class="mb-0">Karya Guru dan Siswa</h4>
    <div class="text-muted small">Baca karya digital dari guru dan siswa</div>
  </div>
  <form method="get" class="d-flex gap-2" style="max-width:350px;">
    <input type="hidden" name="r" value="student/digital/index">
    <input type="search" name="q" class="form-control form-control-sm"
           placeholder="Cari judul atau penulis..." value="<?= htmlspecialchars($q ?? '') ?>">
    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i></button>
  </form>
</div>

<?php if (empty($rows)): ?>
  <div class="text-center py-5">
    <i class="bi bi-journal-richtext display-1 text-muted"></i>
    <p class="text-muted mt-3">Belum ada karya digital<?= ($q ?? '') ? ' untuk pencarian "'.htmlspecialchars($q).'"' : '' ?></p>
  </div>
<?php else: ?>
  <div class="row g-3">
    <?php foreach ($rows as $w): ?>
      <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <div class="card h-100" style="transition:transform .2s,box-shadow .2s;">
          <div class="card-body d-flex flex-column">
            <?php if (!empty($w['cover_image'])): ?>
              <img src="<?= BASE_URL ?>/public/serve_cover.php?id=<?= (int)$w['work_id'] ?>"
                   alt="cover" style="width:100%;height:180px;object-fit:cover;border-radius:8px;margin-bottom:12px;">
            <?php else: ?>
              <div style="width:100%;height:180px;background:var(--accent-soft);border-radius:8px;margin-bottom:12px;display:flex;align-items:center;justify-content:center;">
                <i class="bi bi-file-earmark-text" style="font-size:2.5rem;color:var(--accent);"></i>
              </div>
            <?php endif; ?>

            <h6 class="card-title fw-bold" style="margin:0;"><?= htmlspecialchars($w['title']) ?></h6>
            <div class="text-muted small mt-1">
              <i class="bi bi-person me-1"></i><?= htmlspecialchars($w['author_name']) ?>
            </div>
            <div class="text-muted small mt-1" style="flex:1;">
              <?= htmlspecialchars(mb_strimwidth($w['description'] ?? '', 0, 80, '...')) ?>
            </div>

            <div class="mt-3">
              <a href="index.php?r=student/digital/read&id=<?= (int)$w['work_id'] ?>"
                 class="btn btn-primary btn-sm w-100">
                <i class="bi bi-book me-1"></i>Baca
              </a>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
