<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="d-flex justify-content-between align-items-end mb-3">
  <div><h4 class="mb-0">Laporan Peminjaman</h4><div class="text-muted small">Periode pinjam</div></div>
  <a class="btn btn-outline-secondary"
   href="?r=reports/loans_pdf&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>"
   target="_blank">
  PDF
</a>
</div>


<div class="card shadow-sm mb-3">
  <div class="card-body">
    <form class="row g-2" method="get">
      <input type="hidden" name="r" value="reports/loans">
      <div class="col-12 col-md-4"><label class="form-label">Dari</label><input class="form-control" type="date" name="from" value="<?= htmlspecialchars($from) ?>"></div>
      <div class="col-12 col-md-4"><label class="form-label">Sampai</label><input class="form-control" type="date" name="to" value="<?= htmlspecialchars($to) ?>"></div>
      <div class="col-12 col-md-4 d-grid"><label class="form-label d-none d-md-block">&nbsp;</label><button class="btn btn-primary">Filter</button></div>
    </form>
  </div>
</div>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-striped table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>ID</th><th>Member</th><th>Barcode</th><th>Judul</th><th>Pinjam</th><th>Jatuh Tempo</th><th>Petugas</th></tr>
      </thead>
      <tbody>
        <?php if (empty($rows)): ?>
          <tr><td colspan="7" class="text-center text-muted py-4">Data kosong</td></tr>
        <?php else: foreach ($rows as $r): ?>
          <tr>
            <td><?= (int)$r['loan_id'] ?></td>
            <td><?= htmlspecialchars($r['member_id']) ?></td>
            <td><?= htmlspecialchars($r['item_code']) ?></td>
            <td><?= htmlspecialchars($r['title'] ?? '-') ?></td>
            <td><?= htmlspecialchars($r['loan_date']) ?></td>
            <td><?= htmlspecialchars($r['due_date']) ?></td>
            <td><?= htmlspecialchars($r['staff_name'] ?? '-') ?></td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
