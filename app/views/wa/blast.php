<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<div class="d-flex justify-content-between align-items-end mb-3">
  <div>
    <h4 class="mb-0">WA Kirim Sekarang</h4>
    <div class="text-muted small">Kirim massal langsung saat ini (tanpa menunggu jadwal)</div>
  </div>
</div>

<!-- NOTIF AREA -->
<div id="toastArea"></div>
<div id="alertArea"></div>

<div class="row g-3">
  <div class="col-12 col-lg-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="fw-semibold mb-1">Overdue (Terlambat)</div>
        <div class="small text-muted mb-3">Kirim ke semua anggota yang telat.</div>
        <button class="btn btn-danger w-100 wa-btn" data-mode="OVERDUE">
          <i class="bi bi-alarm me-1"></i>Kirim Sekarang Overdue
        </button>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="fw-semibold mb-1">Reminder (H-1/H-2)</div>
        <div class="small text-muted mb-3">Kirim ke yang mendekati jatuh tempo (besok/lusa).</div>
        <button class="btn btn-warning w-100 wa-btn" data-mode="REMINDER">
          <i class="bi bi-bell me-1"></i>Kirim Sekarang Reminder
        </button>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="fw-semibold mb-1">Semua</div>
        <div class="small text-muted mb-3">Kirim Overdue + Reminder sekaligus.</div>
        <button class="btn btn-success w-100 wa-btn" data-mode="ALL">
          <i class="bi bi-send me-1"></i>Kirim Sekarang Semua
        </button>
      </div>
    </div>
  </div>
</div>

<!-- STATUS CARD -->
<div class="card shadow-sm mt-3">
  <div class="card-body d-flex justify-content-between align-items-center">
    <div>
      <div class="fw-semibold">Status Pengiriman</div>
      <div class="small text-muted" id="statusText">Belum ada aksi.</div>
    </div>
    <div class="text-end">
      <span class="badge text-bg-secondary" id="statusBadge">idle</span>
    </div>
  </div>
</div>

<script>
(function(){
  const alertArea  = document.getElementById('alertArea');
  const statusText = document.getElementById('statusText');
  const statusBadge= document.getElementById('statusBadge');

  function setStatus(type, text){
    // type: idle | running | ok | error
    statusText.textContent = text;

    if(type === 'running'){
      statusBadge.className = 'badge text-bg-primary';
      statusBadge.textContent = 'mengirim...';
    } else if(type === 'ok'){
      statusBadge.className = 'badge text-bg-success';
      statusBadge.textContent = 'berhasil';
    } else if(type === 'error'){
      statusBadge.className = 'badge text-bg-danger';
      statusBadge.textContent = 'gagal';
    } else {
      statusBadge.className = 'badge text-bg-secondary';
      statusBadge.textContent = 'idle';
    }
  }

  function showAlert(kind, title, html){
    // kind: success | danger | warning | info
    alertArea.innerHTML = `
      <div class="alert alert-${kind} alert-dismissible fade show shadow-sm" role="alert">
        <div class="fw-semibold mb-1">${title}</div>
        <div class="small">${html}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    `;
    window.scrollTo({top:0, behavior:'smooth'});
  }

  function lockButtons(lock){
    document.querySelectorAll('.wa-btn').forEach(btn=>{
      btn.disabled = lock;
      if(lock){
        btn.dataset.oldHtml = btn.innerHTML;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses...`;
      }else{
        if(btn.dataset.oldHtml) btn.innerHTML = btn.dataset.oldHtml;
      }
    });
  }

  async function blast(mode){
    const label = mode === 'ALL' ? 'Semua' : (mode === 'OVERDUE' ? 'Overdue' : 'Reminder');

    if(!confirm(`Yakin kirim notifikasi ${label} sekarang?`)) return;

    showAlert('info', 'Memproses...', `Sedang mengirim pesan WhatsApp (${label}). Mohon tunggu.`);
    setStatus('running', `Mengirim ${label}...`);
    lockButtons(true);

    try{
      const res = await fetch(`index.php?r=wa/blastSend&mode=${encodeURIComponent(mode)}`, {cache:'no-store'});
      const text = await res.text();

      // Pastikan response JSON bener, kalau ada HTML error, ketahuan di sini
      let j;
      try { j = JSON.parse(text); }
      catch(e){
        throw new Error("Response bukan JSON (kemungkinan ada error PHP / BIND).");
      }

      if(!j.ok){
        setStatus('error', `Gagal mengirim ${label}.`);
        showAlert('danger', 'Pengiriman Gagal', `Terjadi kesalahan saat mengirim. Silakan cek log WA.`);
        lockButtons(false);
        return;
      }

      // sukses UI
      const total = j.total ?? 0;
      const sent  = j.sent ?? 0;
      const fail  = j.failed ?? 0;

      if(sent > 0 && fail === 0){
        setStatus('ok', `Berhasil mengirim ${label}.`);
        showAlert(
          'success',
          'Pengiriman Berhasil',
          `Pesan berhasil dikirim.<br>
           <b>Total target:</b> ${total}<br>
           <b>Terkirim:</b> ${sent}`
        );
      } else if(sent > 0 && fail > 0){
        setStatus('ok', `Sebagian terkirim untuk ${label}.`);
        showAlert(
          'warning',
          'Pengiriman Sebagian Berhasil',
          `Sebagian pesan berhasil dikirim.<br>
           <b>Total target:</b> ${total}<br>
           <b>Terkirim:</b> ${sent}<br>
           <b>Gagal:</b> ${fail}<br>
           <span class="text-muted">Cek halaman Jadwal / log untuk detail nomor yang gagal.</span>`
        );
      } else {
        setStatus('error', `Tidak ada pesan terkirim (${label}).`);
        showAlert(
          'danger',
          'Pengiriman Gagal',
          `Tidak ada pesan yang berhasil dikirim.<br>
           <b>Total target:</b> ${total}<br>
           <b>Gagal:</b> ${fail}<br>
           <span class="text-muted">Cek log WA untuk penyebabnya.</span>`
        );
      }

      lockButtons(false);

    } catch(err){
      setStatus('error', 'Gagal memproses permintaan.');
      showAlert('danger', 'Terjadi Kesalahan', `${err.message}`);
      lockButtons(false);
    }
  }

  document.querySelectorAll('.wa-btn').forEach(btn=>{
    btn.addEventListener('click', ()=> blast(btn.dataset.mode));
  });
})();
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>