<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-3">
  <div>
    <h4 class="mb-0">Log WhatsApp</h4>
    <div class="text-muted small">Detail request/response untuk Outbox ID #<?= (int)$out['id'] ?></div>
  </div>
  <div class="d-flex gap-2">
    <a class="btn btn-outline-secondary" href="?r=wa">Kembali</a>
  </div>
</div>

<div class="card shadow-sm mb-3">
  <div class="card-body">
    <div class="row g-2">
      <div class="col-md-3"><div class="text-muted small">Member</div><div class="fw-semibold"><?= htmlspecialchars($out['member_id']) ?></div></div>
      <div class="col-md-3"><div class="text-muted small">Phone</div><div class="fw-semibold"><?= htmlspecialchars($out['phone']) ?></div></div>
      <div class="col-md-3"><div class="text-muted small">Kategori</div><div class="fw-semibold"><?= htmlspecialchars($out['category']) ?></div></div>
      <div class="col-md-3"><div class="text-muted small">Status</div><div class="fw-semibold"><?= htmlspecialchars($out['status']) ?></div></div>
      <div class="col-12 mt-2">
        <div class="text-muted small">Pesan</div>
        <pre class="mb-0" style="white-space:pre-wrap"><?= htmlspecialchars($out['message']) ?></pre>
      </div>
    </div>
  </div>
</div>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-striped align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th style="width:70px">ID</th>
          <th style="width:170px">Waktu</th>
          <th style="width:90px">HTTP</th>
          <th>Response / Error</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($logs)): ?>
          <tr><td colspan="4" class="text-center text-muted py-4">Belum ada log</td></tr>
        <?php else: foreach ($logs as $l): ?>
          <tr>
            <td><?= (int)$l['id'] ?></td>
            <td><?= htmlspecialchars($l['created_at']) ?></td>
            <td><?= (int)($l['http_code'] ?? 0) ?></td>
            <td>
              <?php if (!empty($l['error'])): ?>
                <div class="text-danger small"><?= htmlspecialchars($l['error']) ?></div>
              <?php endif; ?>
              <pre class="mb-0 small" style="white-space:pre-wrap"><?= htmlspecialchars(mb_strimwidth((string)$l['response'],0,2500,'…')) ?></pre>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>