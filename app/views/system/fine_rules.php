<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4 class="mb-0">Aturan Denda (Loan Rules)</h4>
    <small class="text-muted">Tambah / edit / hapus aturan peminjaman</small>
  </div>

  <a href="index.php?r=system/addRule" class="btn btn-primary">
    + Tambah Data
  </a>
</div>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-striped table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>No</th>
          <th>ID</th>
          <th>MemberType</th>
          <th>CollType</th>
          <th>GMD</th>
          <th>Limit</th>
          <th>Periode</th>
          <th>Denda/Hari</th>
          <th>Grace</th>
          <th width="140">Aksi</th>
        </tr>
      </thead>

      <tbody>
        <?php $no = 1; ?>
        <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= $r['loan_rules_id'] ?></td>
          <td><?= $r['member_type_id'] ?></td>
          <td><?= $r['coll_type_id'] ?></td>
          <td><?= $r['gmd_id'] ?></td>
          <td><?= $r['loan_limit'] ?></td>
          <td><?= $r['loan_periode'] ?> hari</td>
          <td>Rp <?= number_format($r['fine_each_day']) ?></td>
          <td><?= $r['grace_periode'] ?> hari</td>

          <td class="text-end">

            <a href="index.php?r=system/editRule&id=<?= $r['loan_rules_id'] ?>"
               class="btn btn-sm btn-outline-primary me-1">
              <i class="bi bi-pencil"></i>
            </a>

            <a href="index.php?r=system/fineRules&hapus=<?= $r['loan_rules_id'] ?>"
               onclick="return confirm('Hapus data?')"
               class="btn btn-sm btn-outline-danger">
              <i class="bi bi-trash"></i>
            </a>

          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>

    </table>
  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>