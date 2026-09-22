<?php
$returnRequests = $returnRequests ?? [];
$returnCount = $returnCount ?? count($returnRequests);
?>

<style>
  .return-header{background:linear-gradient(135deg,#0ea5e9,#06b6d4);border-radius:16px;padding:20px 24px;color:#fff;margin-bottom:1rem;}
  .return-row{transition:background .15s;}
  .return-row:hover{background:#f0fdfa!important;}
  .btn-confirm{border-radius:10px;font-weight:600;font-size:.82rem;padding:6px 16px;background:linear-gradient(135deg,#10b981,#059669);border:none;color:#fff;transition:all .2s;}
  .btn-confirm:hover{transform:translateY(-1px);box-shadow:0 4px 15px rgba(16,185,129,.35);color:#fff;}
  .overdue-badge{background:#fef2f2;color:#991b1b;border:1px solid #fecaca;padding:4px 10px;border-radius:8px;font-size:.78rem;font-weight:600;}
  .ok-badge{background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;padding:4px 10px;border-radius:8px;font-size:.78rem;}
  .code-badge{background:linear-gradient(135deg,#ede9fe,#c4b5fd);color:#5b21b6;padding:4px 10px;border-radius:8px;font-size:.82rem;font-weight:600;}
</style>

<?php if (!empty($_SESSION['flash'])): ?>
  <div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show mb-3" role="alert" style="border-radius:12px;border:none;box-shadow:0 4px 15px rgba(0,0,0,.08);">
    <i class="bi bi-<?= $_SESSION['flash']['type'] === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill' ?> me-2"></i>
    <?= $_SESSION['flash']['msg'] ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<?php if (empty($returnRequests)): ?>
  <div class="text-center py-5">
    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:80px;height:80px;background:linear-gradient(135deg,#dcfce7,#bbf7d0);">
      <i class="bi bi-check-circle" style="font-size:2.2rem;color:#16a34a;"></i>
    </div>
    <h5 class="fw-bold" style="color:#334155;">Semua Selesai</h5>
    <p class="text-muted">Tidak ada pengembalian yang menunggu konfirmasi</p>
  </div>
<?php else: ?>
  <div class="table-responsive">
    <table class="table table-hover mb-0" style="border-collapse:separate;border-spacing:0 6px;">
      <thead>
        <tr>
          <th style="border:none;color:#64748b;font-size:.78rem;text-transform:uppercase;letter-spacing:.5px;padding:0 12px 8px;">Siswa</th>
          <th style="border:none;color:#64748b;font-size:.78rem;text-transform:uppercase;letter-spacing:.5px;padding:0 12px 8px;">Buku</th>
          <th style="border:none;color:#64748b;font-size:.78rem;text-transform:uppercase;letter-spacing:.5px;padding:0 12px 8px;">Kode</th>
          <th style="border:none;color:#64748b;font-size:.78rem;text-transform:uppercase;letter-spacing:.5px;padding:0 12px 8px;">Pinjam</th>
          <th style="border:none;color:#64748b;font-size:.78rem;text-transform:uppercase;letter-spacing:.5px;padding:0 12px 8px;">Jatuh Tempo</th>
          <th style="border:none;color:#64748b;font-size:.78rem;text-transform:uppercase;letter-spacing:.5px;padding:0 12px 8px;">Request</th>
          <th style="border:none;color:#64748b;font-size:.78rem;text-transform:uppercase;letter-spacing:.5px;padding:0 12px 8px;width:140px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($returnRequests as $rr):
          $isOverdue = $rr['due_date'] < date('Y-m-d');
          $daysLate = $isOverdue ? (int)floor((strtotime(date('Y-m-d')) - strtotime($rr['due_date'])) / 86400) : 0;
          $fine = $daysLate * 20000;
        ?>
          <tr class="return-row" style="background:#fff;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.04);">
            <td style="border:none;border-radius:12px 0 0 12px;padding:12px;">
              <div class="fw-semibold" style="color:#1e293b;"><?= htmlspecialchars($rr['member_name'] ?? '-') ?></div>
              <small class="text-muted">@<?= htmlspecialchars($rr['username'] ?? '-') ?></small>
            </td>
            <td style="border:none;padding:12px;">
              <div style="color:#1e293b;"><?= htmlspecialchars($rr['title'] ?? '-') ?></div>
            </td>
            <td style="border:none;padding:12px;">
              <span class="code-badge"><i class="bi bi-upc-scan me-1"></i><?= htmlspecialchars($rr['item_code']) ?></span>
            </td>
            <td style="border:none;padding:12px;color:#64748b;font-size:.88rem;">
              <?= date('d/m/Y', strtotime($rr['loan_date'])) ?>
            </td>
            <td style="border:none;padding:12px;">
              <span class="<?= $isOverdue ? '' : '' ?> fw-semibold" style="color:<?= $isOverdue ? '#dc2626' : '#1e293b' ?>;font-size:.88rem;">
                <?= date('d/m/Y', strtotime($rr['due_date'])) ?>
              </span>
              <?php if ($isOverdue): ?>
                <br><span class="overdue-badge mt-1 d-inline-block"><i class="bi bi-exclamation-triangle me-1"></i>Terlambat <?= $daysLate ?> hari</span>
              <?php else: ?>
                <br><span class="ok-badge mt-1 d-inline-block"><i class="bi bi-check me-1"></i>On time</span>
              <?php endif; ?>
            </td>
            <td style="border:none;padding:12px;color:#64748b;font-size:.85rem;">
              <?= date('d/m/Y H:i', strtotime($rr['request_date'])) ?>
            </td>
            <td style="border:none;border-radius:0 12px 12px 0;padding:12px;">
              <form method="POST" action="index.php?r=dashboard/confirmReturn" class="d-inline">
                <input type="hidden" name="return_request_id" value="<?= $rr['return_request_id'] ?>">
                <button type="submit" class="btn-confirm" onclick="return confirm('Konfirmasi pengembalian buku ini<?= $isOverdue ? ' (denda Rp ' . number_format($fine) . ')' : '' ?>?')">
                  <i class="bi bi-check-circle me-1"></i>Konfirmasi
                </button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="mt-3 text-end">
    <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Total <?= $returnCount ?> pengembalian menunggu konfirmasi</small>
  </div>
<?php endif; ?>
