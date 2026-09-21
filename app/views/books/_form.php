<?php
// $masters['gmds'], $masters['publishers']
// $book (optional)
?>
<div class="row g-3">
  <div class="col-12">
    <label class="form-label">Judul Buku</label>
    <input class="form-control" name="title" required
           value="<?= htmlspecialchars($book['title'] ?? '') ?>">
  </div>

  <div class="col-12 col-md-4">
    <label class="form-label">GMD</label>
    <select class="form-select" name="gmd_id">
      <option value="0">- pilih -</option>
      <?php foreach ($masters['gmds'] as $g): ?>
        <option value="<?= (int)$g['gmd_id'] ?>" <?= ((int)($book['gmd_id'] ?? 0) === (int)$g['gmd_id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($g['gmd_name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="col-12 col-md-4">
    <label class="form-label">Publisher</label>
    <select class="form-select" name="publisher_id">
      <option value="0">- pilih -</option>
      <?php foreach ($masters['publishers'] as $p): ?>
        <option value="<?= (int)$p['publisher_id'] ?>" <?= ((int)($book['publisher_id'] ?? 0) === (int)$p['publisher_id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($p['publisher_name']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="col-12 col-md-4">
    <label class="form-label">Tahun Terbit</label>
    <input class="form-control" name="publish_year" placeholder="contoh: 2024"
           value="<?= htmlspecialchars($book['publish_year'] ?? '') ?>">
  </div>

  <div class="col-12 col-md-4">
    <label class="form-label">ISBN/ISSN</label>
    <input class="form-control" name="isbn" value="<?= htmlspecialchars($book['isbn_issn'] ?? ($book['isbn'] ?? '')) ?>">
  </div>

  <div class="col-12 col-md-4">
    <label class="form-label">Edisi</label>
    <input class="form-control" name="edition" value="<?= htmlspecialchars($book['edition'] ?? '') ?>">
  </div>

  <div class="col-12 col-md-4">
    <label class="form-label">Seri Buku</label>
    <input class="form-control" name="series_title" value="<?= htmlspecialchars($book['series_title'] ?? '') ?>">
  </div>

  <div class="col-12 col-md-6">
    <label class="form-label">Call Number</label>
    <input class="form-control" name="call_number" value="<?= htmlspecialchars($book['call_number'] ?? '') ?>">
  </div>

  <div class="col-12 col-md-6">
    <label class="form-label">Klasifikasi</label>
    <input class="form-control" name="classification" value="<?= htmlspecialchars($book['classification'] ?? '') ?>">
  </div>

  <div class="col-12">
    <label class="form-label">Catatan</label>
    <textarea class="form-control" rows="3" name="notes"><?= htmlspecialchars($book['notes'] ?? '') ?></textarea>
  </div>
</div>
