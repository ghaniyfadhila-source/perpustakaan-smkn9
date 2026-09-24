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
    <h4 class="mb-0">Karya Guru dan Murid</h4>
    <div class="text-muted small">Koleksi karya digital dari guru dan siswa</div>
  </div>
  <a class="btn btn-primary" href="index.php?r=digital_works/create">
    <i class="bi bi-plus-lg me-1"></i>Upload Karya
  </a>
</div>

<div class="card shadow-sm">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-striped table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th style="width:70px">ID</th>
            <th style="width:60px">Sampul</th>
            <th>Judul</th>
            <th>Penulis</th>
            <th style="width:100px">Status</th>
            <th style="width:100px">Views</th>
            <th style="width:140px">Tanggal</th>
            <th class="text-end" style="width:220px">Aksi</th>
          </tr>
        </thead>
        <tbody>
        <?php if (empty($rows)): ?>
          <tr><td colspan="8" class="text-center text-muted py-4">Belum ada karya</td></tr>
        <?php else: ?>
          <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= (int)$r['work_id'] ?></td>
            <td>
              <?php if (!empty($r['cover_image'])): ?>
                <img src="storage/digital_works/covers/<?= htmlspecialchars($r['cover_image']) ?>"
                     alt="cover" style="width:40px;height:55px;object-fit:cover;border-radius:4px;">
              <?php else: ?>
                <div style="width:40px;height:55px;background:var(--accent-soft);border-radius:4px;display:flex;align-items:center;justify-content:center;">
                  <i class="bi bi-file-earmark-text" style="color:var(--accent);font-size:1rem;"></i>
                </div>
              <?php endif; ?>
            </td>
            <td>
              <div class="fw-semibold"><?= htmlspecialchars($r['title']) ?></div>
              <div class="text-muted small"><?= htmlspecialchars(mb_strimwidth($r['description'] ?? '', 0, 80, '...')) ?></div>
            </td>
            <td><?= htmlspecialchars($r['author_name']) ?></td>
            <td>
              <?php if ($r['status'] === 'published'): ?>
                <span class="badge text-bg-success">Published</span>
              <?php else: ?>
                <span class="badge text-bg-secondary">Draft</span>
              <?php endif; ?>
            </td>
            <td><?= (int)($r['view_count'] ?? 0) ?></td>
            <td class="text-muted small"><?= date('d M Y', strtotime($r['input_date'])) ?></td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-dark" href="index.php?r=digital_works/show&id=<?= (int)$r['work_id'] ?>">
                <i class="bi bi-eye me-1"></i>Detail
              </a>
              <a class="btn btn-sm btn-outline-primary" href="index.php?r=digital_works/edit&id=<?= (int)$r['work_id'] ?>">
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
