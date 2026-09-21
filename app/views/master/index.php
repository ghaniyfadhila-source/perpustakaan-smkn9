<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<?php if (!empty($_SESSION['flash'])): $f=$_SESSION['flash']; unset($_SESSION['flash']); ?>
  <div class="alert alert-<?= htmlspecialchars($f['type']) ?> alert-dismissible fade show">
    <?= htmlspecialchars($f['msg']) ?><button class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-end mb-3">
  <div>
    <h4 class="mb-0">Master Data</h4>
    <div class="text-muted small">Kelola GMD/Publisher/Author/dll (Admin)</div>
  </div>
  <?php if (ACL::isAdmin()): ?>
    <a class="btn btn-primary" href="index.php?r=master/create&type=<?= urlencode($type) ?>"><i class="bi bi-plus-lg me-1"></i>Tambah</a>
  <?php endif; ?>
</div>

<div class="card shadow-sm mb-3">
  <div class="card-body">
    <form method="get" class="row g-2">
      <input type="hidden" name="r" value="master/index">
      <div class="col-12 col-md-6">
        <label class="form-label">Jenis Master</label>
        <select class="form-select" name="type" onchange="this.form.submit()">
          <?php
            $opts = [
              'gmd'=>'GMD','content_type'=>'Content Type','media_type'=>'Media Type','carrier_type'=>'Carrier Type',
              'publisher'=>'Publisher','supplier'=>'Supplier','author'=>'Author','subject'=>'Subject','location'=>'Lokasi'
            ];
            foreach ($opts as $k=>$v):
          ?>
            <option value="<?= $k ?>" <?= ($type===$k)?'selected':'' ?>><?= $v ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </form>
  </div>
</div>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-striped table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>#</th><th>Data</th><?php if (ACL::isAdmin()): ?><th class="text-end">Aksi</th><?php endif; ?></tr>
      </thead>
      <tbody>
        <?php if (empty($rows)): ?>
          <tr><td colspan="<?= ACL::isAdmin()?3:2 ?>" class="text-center text-muted py-4">Kosong</td></tr>
        <?php else: $i=1; foreach ($rows as $r): ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($r[array_keys($r)[1]] ?? '') ?></td>
            <?php if (ACL::isAdmin()): ?>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-danger"
                   href="index.php?r=master/delete&type=<?= urlencode($type) ?>&id=<?= urlencode($r[array_keys($r)[0]]) ?>"
                   onclick="return confirm('Yakin hapus?')"><i class="bi bi-trash"></i></a>
              </td>
            <?php endif; ?>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
