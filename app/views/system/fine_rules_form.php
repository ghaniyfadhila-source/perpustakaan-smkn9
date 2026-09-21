<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h4><?= !empty($data) ? "Edit" : "Tambah" ?> Aturan Denda</h4>
    <small class="text-muted">Pengaturan aturan peminjaman</small>
  </div>

  <a href="index.php?r=system/fineRules" class="btn btn-outline-secondary">
    ← Kembali
  </a>
</div>


<div class="card shadow-sm">
  <div class="card-body">

    <form method="POST" action="index.php?r=system/saveRule">

      <!-- 🔴 FIX UTAMA: pastikan ID selalu jelas -->
      <input type="hidden" name="id" value="<?= !empty($data) ? $data['loan_rules_id'] : '' ?>">

      <div class="row">

        <div class="col-md-6 mb-3">
          <label>Member Type</label>
          <input name="member_type_id" class="form-control"
                 value="<?= $data['member_type_id'] ?? '' ?>" required>
        </div>

        <div class="col-md-6 mb-3">
          <label>Collection Type</label>
          <input name="coll_type_id" class="form-control"
                 value="<?= $data['coll_type_id'] ?? '' ?>" required>
        </div>

        <div class="col-md-6 mb-3">
          <label>GMD</label>
          <input name="gmd_id" class="form-control"
                 value="<?= $data['gmd_id'] ?? '' ?>" required>
        </div>

        <div class="col-md-6 mb-3">
          <label>Loan Limit</label>
          <input name="loan_limit" class="form-control"
                 value="<?= $data['loan_limit'] ?? '' ?>" required>
        </div>

        <div class="col-md-6 mb-3">
          <label>Loan Periode</label>
          <input name="loan_periode" class="form-control"
                 value="<?= $data['loan_periode'] ?? '' ?>" required>
        </div>

        <div class="col-md-6 mb-3">
          <label>Denda per Hari</label>
          <input name="fine_each_day" class="form-control"
                 value="<?= $data['fine_each_day'] ?? '' ?>" required>
        </div>

        <div class="col-md-6 mb-3">
          <label>Grace Periode</label>
          <input name="grace_periode" class="form-control"
                 value="<?= $data['grace_periode'] ?? '' ?>" required>
        </div>

      </div>

      <button class="btn btn-primary">
        <i class="bi bi-check2-square me-1"></i> Simpan
      </button>

    </form>

  </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>