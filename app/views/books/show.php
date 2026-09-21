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
    <h4 class="mb-1"><?= htmlspecialchars($book['title']) ?></h4>
    <div class="text-muted small">
      GMD: <?= htmlspecialchars($book['gmd_name'] ?? '-') ?> |
      Publisher: <?= htmlspecialchars($book['publisher_name'] ?? '-') ?> |
      Tahun: <?= htmlspecialchars($book['publish_year'] ?? '-') ?> |
      Seri: <?= htmlspecialchars($book['series_title'] ?? '-') ?>
    </div>
  </div>

  <div class="d-flex gap-2">
    <a class="btn btn-outline-secondary" href="index.php?r=books/index">
      <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>

    <a class="btn btn-outline-primary" href="index.php?r=books/edit&id=<?= (int)$book['biblio_id'] ?>">
      <i class="bi bi-pencil me-1"></i>Edit
    </a>

    <a class="btn btn-danger" href="index.php?r=books/delete&id=<?= (int)$book['biblio_id'] ?>"
       onclick="return confirm('Yakin hapus buku ini? (akan dicek dulu apakah aman)')">
      <i class="bi bi-trash me-1"></i>Hapus Buku
    </a>
  </div>
</div>

<div class="row g-3">
  <div class="col-12 col-lg-5">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="fw-semibold mb-2">Detail</div>
        <div class="small text-muted">ISBN: <?= htmlspecialchars($book['isbn_issn'] ?? '-') ?></div>
        <div class="small text-muted">Edisi: <?= htmlspecialchars($book['edition'] ?? '-') ?></div>
        <div class="small text-muted">Call Number: <?= htmlspecialchars($book['call_number'] ?? '-') ?></div>
        <div class="small text-muted">Klasifikasi: <?= htmlspecialchars($book['classification'] ?? '-') ?></div>
        <hr>
        <div class="fw-semibold mb-1">Catatan</div>
        <div><?= nl2br(htmlspecialchars($book['notes'] ?? '-')) ?></div>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-7">
    <div class="card shadow-sm">
      <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div>
          <div class="fw-semibold">Eksemplar (Item)</div>
          <div class="text-muted small">Barcode per buku (tabel item)</div>
        </div>

        <div class="d-flex flex-wrap gap-2">
          <a class="btn btn-primary btn-sm" href="index.php?r=books/addCopy&id=<?= (int)$book['biblio_id'] ?>">
            <i class="bi bi-plus-lg me-1"></i>Tambah Eksemplar
          </a>

          <a class="btn btn-outline-dark btn-sm" target="_blank"
             href="index.php?r=print/bookBarcodes&biblio_id=<?= (int)$book['biblio_id'] ?>">
            <i class="bi bi-printer me-1"></i>Print Semua
          </a>
        </div>
      </div>

      <!-- FORM BULK DELETE -->
      <form method="post" action="index.php?r=books/deleteCopies"
            onsubmit="return confirm('Yakin hapus eksemplar yang dipilih?')">
        <input type="hidden" name="biblio_id" value="<?= (int)$book['biblio_id'] ?>">

        <div class="px-3 pb-3">
          <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="text-muted small">
              Centang eksemplar yang ingin dihapus, lalu klik <b>Hapus Terpilih</b>.
            </div>
            <button type="submit" class="btn btn-danger btn-sm" id="btnDeleteSelected" disabled>
              <i class="bi bi-trash me-1"></i>Hapus Terpilih
            </button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-striped table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th style="width:50px;" class="text-center">
                  <input type="checkbox" id="checkAll">
                </th>
                <th>Barcode</th>
                <th>Lokasi</th>
                <th>Status</th>
                <th class="text-end">Kondisi</th>
                <th class="text-end" style="width: 140px;">Aksi</th>
              </tr>
            </thead>

            <tbody>
              <?php if (empty($copies)): ?>
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">Belum ada eksemplar</td>
                </tr>
              <?php else: ?>
                <?php foreach ($copies as $c): ?>
                  <?php $isBorrowed = !empty($c['active_loan_id']); ?>
                  <tr>
                    <td class="text-center">
                      <!-- disable kalau sedang dipinjam -->
                      <input type="checkbox"
                             class="copy-check"
                             name="item_ids[]"
                             value="<?= (int)$c['item_id'] ?>"
                             <?= $isBorrowed ? 'disabled' : '' ?>>
                    </td>

                    <td class="fw-semibold"><?= htmlspecialchars($c['item_code']) ?></td>
                    <td><?= htmlspecialchars($c['location_id'] ?? '-') ?></td>

                    <td>
                      <?php if ($isBorrowed): ?>
                        <span class="badge text-bg-warning">Dipinjam</span>
                        <span class="text-muted small ms-1">(tidak bisa dihapus)</span>
                      <?php else: ?>
                        <span class="badge text-bg-success">Tersedia</span>
                      <?php endif; ?>
                    </td>

                    <td class="text-end text-muted small">
                      Status ID: <?= htmlspecialchars($c['item_status_id'] ?? '-') ?>
                    </td>

                    <td class="text-end">
                      <a class="btn btn-sm btn-outline-secondary"
                         target="_blank"
                         href="index.php?r=print/itemLabel&item_code=<?= urlencode($c['item_code']) ?>">
                        <i class="bi bi-printer me-1"></i>Print
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </form>
      <!-- /FORM BULK DELETE -->

    </div>
  </div>
</div>

<script>
  const checkAll = document.getElementById('checkAll');
  const btn = document.getElementById('btnDeleteSelected');

  function refreshBtn(){
    const checks = document.querySelectorAll('.copy-check:checked');
    btn.disabled = checks.length === 0;
  }

  if (checkAll) {
    checkAll.addEventListener('change', function(){
      const all = document.querySelectorAll('.copy-check');
      all.forEach(cb => {
        if (!cb.disabled) cb.checked = checkAll.checked;
      });
      refreshBtn();
    });
  }

  document.querySelectorAll('.copy-check').forEach(cb => {
    cb.addEventListener('change', refreshBtn);
  });

  refreshBtn();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>