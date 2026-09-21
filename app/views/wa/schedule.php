<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="d-flex flex-wrap justify-content-between align-items-end mb-3 gap-2">
  <div>
    <h4 class="mb-0">WA Jadwal Besok</h4>
    <div class="text-muted small">
      Membuat antrian otomatis untuk <b>besok jam 07:00</b> (Overdue + Reminder), dan mengirim otomatis saat waktunya tiba.
    </div>
  </div>

  <div class="d-flex gap-2">
    <button class="btn btn-outline-secondary" id="btnSendDue">
      <i class="bi bi-lightning-charge me-1"></i>Kirim yang Jatuh Tempo
    </button>
    <button class="btn btn-primary" id="btnSchedule">
      <i class="bi bi-calendar2-plus me-1"></i>Buat Jadwal Besok
    </button>
  </div>
</div>

<!-- NOTIF AREA -->
<div id="alertArea"></div>

<?php
// ringkasan status
$sum = ['PENDING'=>0,'SENT'=>0,'FAILED'=>0];
foreach ($rows as $r) {
  $st = $r['status'] ?? '';
  if (isset($sum[$st])) $sum[$st]++;
}
?>

<div class="row g-3 mb-3">
  <div class="col-12 col-lg-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="text-muted small">Pending</div>
            <div class="fs-4 fw-semibold"><?= (int)$sum['PENDING'] ?></div>
          </div>
          <span class="badge text-bg-warning">PENDING</span>
        </div>
        <div class="small text-muted mt-2">Menunggu jadwal atau proses kirim.</div>
      </div>
    </div>
  </div>
  <div class="col-12 col-lg-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="text-muted small">Terkirim</div>
            <div class="fs-4 fw-semibold"><?= (int)$sum['SENT'] ?></div>
          </div>
          <span class="badge text-bg-success">SENT</span>
        </div>
        <div class="small text-muted mt-2">Berhasil dikirim ke WhatsApp.</div>
      </div>
    </div>
  </div>
  <div class="col-12 col-lg-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="text-muted small">Gagal</div>
            <div class="fs-4 fw-semibold"><?= (int)$sum['FAILED'] ?></div>
          </div>
          <span class="badge text-bg-danger">FAILED</span>
        </div>
        <div class="small text-muted mt-2">Gagal kirim (cek error di tabel).</div>
      </div>
    </div>
  </div>
</div>

<div class="card shadow-sm mb-3">
  <div class="card-body d-flex justify-content-between align-items-center">
    <div>
      <div class="fw-semibold">Auto Sender</div>
      <div class="small text-muted">
        Halaman ini akan mengecek antrian setiap <b>25 detik</b> dan mengirim yang sudah waktunya.
        (Kalau halaman ditutup, auto sender tidak berjalan.)
      </div>
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
            <th style="width:70px">ID</th>
            <th>Member</th>
            <th>No HP</th>
            <th style="width:120px">Kategori</th>
            <th style="width:170px">Jadwal</th>
            <th style="width:140px">Status</th>
            <th style="width:70px">Try</th>
            <th>Error</th>
            <th style="width:110px">Aksi</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($rows)): ?>
            <tr><td colspan="9" class="text-center text-muted py-4">Belum ada antrian</td></tr>
        <?php else: foreach ($rows as $r): ?>
            <?php
            $st = $r['status'] ?? 'PENDING';
            $cls = 'secondary';
            if ($st === 'PENDING')   $cls = 'warning';
            if ($st === 'SENT')      $cls = 'success';
            if ($st === 'FAILED')    $cls = 'danger';
            if ($st === 'CANCELLED') $cls = 'dark';
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
            <td>
                <?php if ($st === 'PENDING'): ?>
                <button type="button"
  class="btn btn-sm btn-outline-danger"
  onclick="cancelOutbox(<?= (int)$r['id'] ?>)">
  <i class="bi bi-x-circle me-1"></i>Cancel
</button>
                <?php else: ?>
                <span class="text-muted small">-</span>
                <?php endif; ?>
            </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
  </div>
</div>

<script>
(function(){
  const alertArea  = document.getElementById('alertArea');
  const hbStatus   = document.getElementById('hbStatus');
  const hbInfo     = document.getElementById('hbInfo');
  const btnSchedule= document.getElementById('btnSchedule');
  const btnSendDue = document.getElementById('btnSendDue');

  function showAlert(kind, title, html){
    alertArea.innerHTML = `
      <div class="alert alert-${kind} alert-dismissible fade show shadow-sm" role="alert">
        <div class="fw-semibold mb-1">${title}</div>
        <div class="small">${html}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    `;
    window.scrollTo({top:0, behavior:'smooth'});
  }

  function setHB(type, text){
    hbInfo.textContent = text || '-';
    if(type === 'running'){
      hbStatus.className = 'badge text-bg-primary';
      hbStatus.textContent = 'running';
    } else if(type === 'ok'){
      hbStatus.className = 'badge text-bg-success';
      hbStatus.textContent = 'ok';
    } else if(type === 'error'){
      hbStatus.className = 'badge text-bg-danger';
      hbStatus.textContent = 'error';
    } else {
      hbStatus.className = 'badge text-bg-secondary';
      hbStatus.textContent = 'idle';
    }
  }

  async function requestJson(url){
    const res = await fetch(url, {cache:'no-store'});
    const text = await res.text();
    try { return JSON.parse(text); }
    catch(e){ throw new Error('Response bukan JSON (kemungkinan error PHP/BIND).'); }
  }

  function lockBtn(btn, lock, loadingText){
    btn.disabled = lock;
    if(lock){
      btn.dataset.old = btn.innerHTML;
      btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>${loadingText || 'Memproses...'}`;
    } else {
      if(btn.dataset.old) btn.innerHTML = btn.dataset.old;
    }
  }

  async function doSchedule(){
    if(!confirm('Buat jadwal besok jam 07:00 untuk Overdue + Reminder?')) return;

    lockBtn(btnSchedule, true, 'Membuat jadwal...');
    showAlert('info', 'Memproses...', 'Sedang membuat jadwal besok. Mohon tunggu.');

    try{
      const j = await requestJson('index.php?r=wa/scheduleTomorrow');
      showAlert('success', 'Berhasil', `${j.msg}<br><b>Jadwal:</b> ${j.send_at}<br><b>Antrian dibuat:</b> ${j.inserted}`);
      setTimeout(()=> location.reload(), 600);
    }catch(e){
      showAlert('danger', 'Gagal', e.message);
    }finally{
      lockBtn(btnSchedule, false);
    }
  }
  

  async function doSendDue(manual){
    try{
      if(manual){
        lockBtn(btnSendDue, true, 'Mengirim...');
        showAlert('info', 'Mengirim...', 'Mengirim semua pesan yang jadwalnya sudah tiba.');
      }

      setHB('running', 'Mengirim pesan yang jatuh tempo...');
      const j = await requestJson('index.php?r=wa/sendDue');

      if((j.sent ?? 0) > 0 && (j.failed ?? 0) === 0){
        setHB('ok', `picked=${j.picked} sent=${j.sent}`);
        if(manual) showAlert('success', 'Pengiriman Berhasil', `Pesan terkirim: <b>${j.sent}</b>`);
        if(j.sent > 0) setTimeout(()=> location.reload(), 500);
      } else if((j.sent ?? 0) > 0 && (j.failed ?? 0) > 0){
        setHB('ok', `picked=${j.picked} sent=${j.sent} failed=${j.failed}`);
        if(manual) showAlert('warning', 'Pengiriman Sebagian', `Terkirim: <b>${j.sent}</b> | Gagal: <b>${j.failed}</b>. Cek error di tabel.`);
        setTimeout(()=> location.reload(), 700);
      } else if((j.failed ?? 0) > 0){
        setHB('error', `picked=${j.picked} failed=${j.failed}`);
        if(manual) showAlert('danger', 'Pengiriman Gagal', `Gagal: <b>${j.failed}</b>. Cek error di tabel.`);
        setTimeout(()=> location.reload(), 700);
      } else {
        setHB('ok', 'Tidak ada antrian yang jatuh tempo.');
        if(manual) showAlert('success', 'Tidak Ada Antrian', 'Saat ini tidak ada pesan yang jadwalnya sudah tiba.');
      }

    } catch(e){
      setHB('error', e.message);
      if(manual) showAlert('danger', 'Terjadi Kesalahan', e.message);
    } finally {
      if(manual) lockBtn(btnSendDue, false);
    }
  }

  // actions
  btnSchedule.addEventListener('click', doSchedule);
  btnSendDue.addEventListener('click', ()=> doSendDue(true));

  // heartbeat sender (otomatis)
  doSendDue(false);
  setInterval(()=> doSendDue(false), 25000);
})();
</script>

<script>
window.cancelOutbox = async function(id){
  if(!confirm('Batalkan antrian ini?')) return;

  const alertArea = document.getElementById('alertArea');

  try{
    // kirim sebagai x-www-form-urlencoded (paling kompatibel)
    const body = new URLSearchParams();
    body.append('id', String(id));

    const res = await fetch('index.php?r=wa/cancel', {
      method: 'POST',
      headers: {'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'},
      body: body.toString(),
      cache: 'no-store'
    });

    const text = await res.text();
    let j;
    try { j = JSON.parse(text); }
    catch(e){ throw new Error('Response bukan JSON (cek error PHP).'); }

    if(!j.ok) throw new Error(j.msg || 'Gagal cancel');

    alertArea.innerHTML = `
      <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <div class="fw-semibold mb-1">Berhasil</div>
        <div class="small">${j.msg}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    `;
    setTimeout(()=> location.reload(), 500);

  } catch(err){
    alertArea.innerHTML = `
      <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <div class="fw-semibold mb-1">Gagal</div>
        <div class="small">${err.message}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    `;
    window.scrollTo({top:0, behavior:'smooth'});
  }
};
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>