<?php $m = $member ?? []; ?>
<div class="row g-3">
  <?php if (empty($m)): ?>
    <div class="col-12 col-md-4">
      <label class="form-label">Member ID</label>
      <input class="form-control" name="member_id" required>
    </div>
  <?php else: ?>
    <div class="col-12 col-md-4">
      <label class="form-label">Member ID</label>
      <input class="form-control" value="<?= htmlspecialchars($m['member_id']) ?>" disabled>
    </div>
  <?php endif; ?>

  <div class="col-12 col-md-8">
    <label class="form-label">Nama</label>
    <input class="form-control" name="member_name" required value="<?= htmlspecialchars($m['member_name'] ?? '') ?>">
  </div>

  <div class="col-12 col-md-4">
    <label class="form-label">Gender</label>
    <select class="form-select" name="gender">
      <option value="1" <?= ((int)($m['gender'] ?? 1)===1)?'selected':'' ?>>Laki-laki</option>
      <option value="2" <?= ((int)($m['gender'] ?? 1)===2)?'selected':'' ?>>Perempuan</option>
    </select>
  </div>

  <div class="col-12 col-md-4">
    <label class="form-label">Tanggal Lahir</label>
    <input class="form-control" type="date" name="birth_date" value="<?= htmlspecialchars($m['birth_date'] ?? '') ?>">
  </div>

  <div class="col-12 col-md-4">
    <label class="form-label">Tipe Anggota</label>
    <select class="form-select" name="member_type_id">
      <?php foreach ($types as $t): ?>
        <option value="<?= (int)$t['member_type_id'] ?>" <?= ((int)($m['member_type_id'] ?? 0)===(int)$t['member_type_id'])?'selected':'' ?>>
          <?= htmlspecialchars($t['member_type_name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="col-12 col-md-8">
    <label class="form-label">Alamat</label>
    <input class="form-control" name="member_address" value="<?= htmlspecialchars($m['member_address'] ?? '') ?>">
  </div>

  <div class="col-12 col-md-4">
    <label class="form-label">No HP</label>
    <input class="form-control" name="member_phone" value="<?= htmlspecialchars($m['member_phone'] ?? '') ?>">
  </div>

  <div class="col-12 col-md-4">
    <label class="form-label">Expire Date</label>
    <input class="form-control" type="date" name="expire_date" value="<?= htmlspecialchars($m['expire_date'] ?? date('Y-m-d', strtotime('+1 year'))) ?>">
  </div>
</div>
