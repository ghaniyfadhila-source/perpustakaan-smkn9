<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

    <h4 class="mb-4"><i class="bi bi-person me-2"></i>Profil Saya</h4>

    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-md-4 text-center mb-3">
            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px; font-size: 2.5rem;">
              <i class="bi bi-person"></i>
            </div>
          </div>
          <div class="col-md-8">
            <div class="table-responsive">
              <table class="table table-borderless">
                <tr><th style="width: 150px;">Nama Lengkap</th><td><?= htmlspecialchars($student['student_name'] ?? '-') ?></td></tr>
                <tr><th>Username</th><td>@<?= htmlspecialchars($student['username'] ?? '-') ?></td></tr>
                <tr><th>ID Siswa</th><td><?= htmlspecialchars($student['student_id'] ?? '-') ?></td></tr>
              </table>
            </div>
          </div>
        </div>
        <hr>
        <p class="text-muted small">Fitur profil akan dikembangkan lebih lanjut (ubah password, data kontak, riwayat peminjaman, dll).</p>
      </div>
    </div>
<?php require __DIR__ . '/../layouts/footer.php'; ?>