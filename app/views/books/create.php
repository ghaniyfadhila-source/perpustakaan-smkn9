<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h4 class="mb-0">Tambah Buku</h4>
    <div class="text-muted small">Input data bibliografi</div>
  </div>
  <a class="btn btn-outline-secondary" href="index.php?r=books/index">
    <i class="bi bi-arrow-left me-1"></i>Kembali
  </a>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <form method="post">
      <?php $book = null; require __DIR__.'/_form.php'; ?>
      <div class="mt-3 d-flex gap-2">
        <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
        <a class="btn btn-outline-secondary" href="index.php?r=books/index">Batal</a>
      </div>
    </form>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
