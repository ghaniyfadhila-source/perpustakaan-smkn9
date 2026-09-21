<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="d-flex justify-content-between align-items-end mb-3">
  <div><h4 class="mb-0">Laporan Terlambat</h4><div class="text-muted small">Pinjaman aktif yang melewati due date</div></div>
  <a class="btn btn-outline-secondary"
   href="?r=reports/overdue_pdf"
   target="_blank">
  PDF
</a>
</div>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-striped table-hover align-middle mb-0">
      <thead class="table-light">
        <tr><th>ID</th><th>Member</th><th>Barcode</th><th>Judul</th><th>Due Date</th><th>Terlambat</th><th>Petugas</th></tr>
      </thead>
      <tbody>
        <?php if (empty($rows)): ?>
          <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada yang terlambat</td></tr>
        <?php else: foreach ($rows as $r): ?>
          <tr>
            <td><?= (int)$r['loan_id'] ?></td>
            <td><?= htmlspecialchars($r['member_name'] ?? '-') ?></td>
            <td><?= htmlspecialchars($r['item_code']) ?></td>
            <td><?= htmlspecialchars($r['title'] ?? '-') ?></td>
            <td><?= htmlspecialchars($r['due_date']) ?></td>
            <td><span class="badge text-bg-danger"><?= (int)$r['late_days'] ?> hari</span></td>
            <td><?= htmlspecialchars($r['staff_name'] ?? '-') ?></td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
