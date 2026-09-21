<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="d-flex justify-content-between align-items-end mb-3">
  <div>
    <h4 class="mb-0">WhatsApp Otomatis</h4>
    <div class="text-muted small">Auto kirim jam 07:00 untuk yang mendekati jatuh tempo & yang terlambat</div>
  </div>

  <div class="d-flex gap-2">
    <button class="btn btn-success" id="btnSendNow">
      <i class="bi bi-send me-1"></i>Kirim Sekarang (Semua Pending)
    </button>
  </div>
</div>

<div class="card shadow-sm mb-3">
  <div class="card-body d-flex justify-content-between align-items-center">
    <div>
      <div class="fw-semibold">Auto Sender Status</div>
      <div class="small text-muted">Halaman ini akan auto-generate (1x per hari) dan auto-send setiap 25 detik.</div>
    </div>
    <div class="text-end">
      <span class="badge text-bg-secondary" id="hbStatus">idle</span>
      <div class="small text-muted mt-1" id="hbInfo">-</div>
    </div>
  </div>
</div>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-striped table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>ID</th><th>Member</th><th>No HP</th><th>Kategori</th>
          <th>Jadwal</th><th>Status</th><th>Try</th><th>Error</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($rows)): ?>
          <tr><td colspan="8" class="text-center text-muted py-4">Belum ada data</td></tr>
        <?php else: foreach ($rows as $r): ?>
          <?php
            $st = $r['status'];
            $cls = 'secondary';
            if ($st === 'PENDING') $cls = 'warning';
            if ($st === 'SENT')    $cls = 'success';
            if ($st === 'FAILED')  $cls = 'danger';
          ?>
          <tr>
            <td><?= (int)$r['id'] ?></td>
            <td><?= htmlspecialchars($r['member_id']) ?></td>
            <td><?= htmlspecialchars($r['phone']) ?></td>
            <td><span class="badge text-bg-info"><?= htmlspecialchars($r['category']) ?></span></td>
            <td><?= htmlspecialchars($r['scheduled_at']) ?></td>
            <td>
              <span class="badge text-bg-<?= $cls ?>"><?= htmlspecialchars($st) ?></span>
              <?php if (!empty($r['sent_at'])): ?>
                <div class="small text-muted">Sent: <?= htmlspecialchars($r['sent_at']) ?></div>
              <?php endif; ?>
            </td>
            <td><?= (int)$r['try_count'] ?></td>
            <td class="text-danger small"><?= htmlspecialchars($r['last_error'] ?? '') ?></td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
(async function(){
  const hbStatus = document.getElementById('hbStatus');
  const hbInfo   = document.getElementById('hbInfo');
  const btnSendNow = document.getElementById('btnSendNow');

  async function tick(){
    try{
      hbStatus.className = 'badge text-bg-primary';
      hbStatus.textContent = 'running';

      const res = await fetch('index.php?r=wa/runAuto', {cache:'no-store'});
      const j = await res.json();

      hbStatus.className = 'badge text-bg-success';
      hbStatus.textContent = 'ok';
      hbInfo.textContent = `now=${j.now} gen=${j.generate.generated} picked=${j.picked} sent=${j.sent} failed=${j.failed}`;

      if ((j.sent + j.failed) > 0 || (j.generate && j.generate.generated > 0)) {
        location.reload();
      }
    } catch (e) {
      hbStatus.className = 'badge text-bg-danger';
      hbStatus.textContent = 'error';
      hbInfo.textContent = String(e);
    }
  }

  tick();
  setInterval(tick, 25000);

  btnSendNow.addEventListener('click', async ()=>{
    if (!confirm('Kirim semua pesan pending sekarang?')) return;
    btnSendNow.disabled = true;
    btnSendNow.textContent = 'Mengirim...';
    try{
      const res = await fetch('index.php?r=wa/sendNowAll', {cache:'no-store'});
      const j = await res.json();
      alert(`Done: gen=${j.generate.generated} picked=${j.picked} sent=${j.sent} failed=${j.failed}`);
      location.reload();
    } catch (e) {
      alert('Gagal: ' + e);
      btnSendNow.disabled = false;
      btnSendNow.textContent = 'Kirim Sekarang (Semua Pending)';
    }
  });
})();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>,