TAHAP 1: Foundation - Autentikasi Siswa & Dashboard Dasar
Tujuan: Membangun sistem login siswa dan dashboard dasar tanpa fitur lanjutan.
Fitur yang Akan Dibuild:
- Login siswa dengan username/password (ditambahkan ke tabel member)
- Dashboard siswa menampilkan: nama, status pinjaman aktif, notifikasi sederhana
- TIDAK ADA: tanggal expired, foto profil, fitur permintaan buku
Database Changes:
ALTER TABLE member ADD COLUMN username VARCHAR(50) UNIQUE;
ALTER TABLE member ADD COLUMN password_hash VARCHAR(255);
ALTER TABLE member ADD COLUMN last_login DATETIME;
-- COLUMNS YANG DIHAPUS dari rencana sebelumnya:
-- ALTER TABLE member ADD COLUMN expire_date_extension... (tidak ada)
-- ALTER TABLE member ADD COLUMN member_image... (tidak ada)
Kontroller Baru:
- StudentAuthController.php - login/logout siswa
- StudentDashboardController.php - dashboard utama siswa
Estimasi: 2-3 hari kerja
TAHAP 2: Sistem Permintaan Buku Fisik
Tujuan: Memungkinkan siswa mengajukan request peminjaman yang harus disetujui admin.
Fitur yang Akan Dibuild:
- Siswa bisa mengajukan request peminjaman buku di katalog
- Admin melihat daftar request yang menunggu persetujuan
- Admin bisa Setujui atau Tolak request dengan alasan
- Siswa bisa melihat status request-nya (pending/approved/rejected)
- Sistem otomatis membuat entri peminjaman (loan table) setelah admin konfirmasi "buku telah diberikan"
Database Changes:
CREATE TABLE book_requests (
    request_id INT PRIMARY KEY AUTO_INCREMENT,
    member_id VARCHAR(20) NOT NULL,
    item_code VARCHAR(20) NOT NULL,
    status ENUM('pending','approved','rejected','fulfilled','cancelled') DEFAULT 'pending',
    admin_response TEXT,
    processed_by INT,
    processed_at DATETIME,
    FOREIGN KEY (member_id) REFERENCES member(member_id),
    FOREIGN KEY (item_code) REFERENCES item(item_code)
);
Kontroller & Model Baru:
- StudentRequestController.php - mengajukan dan melacak request
- Admin/RequestController.php - menyetujui/menolak request
- BookRequestModel.php - operasi tabel book_requests
Estimasi: 3-4 hari kerja
TAHAP 3: Akses Buku Digital & Profil Siswa
Tujuan: Akses buku digital dan profil sederhana tanpa foto/tanggal expired.
Fitur yang Akan Dibuild:
- Akses Buku Digital: Buku dengan file digital (PDF/ebook) dapat dibaca online atau diunduh
- Profil Siswa: Lihat data dasar (nama, NIS, telepon, email) - tanpa foto profil dan tanpa tanggal expired
- ID WhatsApp Baru: Ditampilkan di profil siswa dan digunakan untuk notifikasi
Database Changes:
- Tidak perlu perubahan struktur tabel besar (sudah ada files, biblio_attachment)
- Pastikan konfigurasi config/whatsapp.php sudah benar dengan ID baru
Kontroller & Model Baru:
- StudentBookController.php - baca unduh buku digital
- StudentProfileController.php - lihat/edit profil sederhana (tanpa foto/expire)
- DigitalAccessModel.php - cek hak akses file digital
Estimasi: 2-3 hari kerja
TAHAP 4: Notifikasi WhatsApp Menggunakan ID Baru
Tujuan: Sistem notifikasi WhatsApp otomatis menggunakan ID WhatsApp baru.
Fitur yang Akan Dibuild:
- Notifikasi kirim otomatis ke WhatsApp siswa via ID WhatsApp baru
- Jenis notifikasi:
- Request permintaan buku disetujui/ditolak
- Pengingat jatuh tempo buku
- Notifikasi buku terlambat
- Konfirmasi buku telah dikembalikan
Konfigurasi:
// config/whatsapp.php
return [
  'base_url'  => 'https://app.whacenter.com/api/send',
  'device_id' => 'ID_WHATSAP_BARU_YANG_DI_GUNAKAN',  // Ganti dengan ID baru
  'timeout'   => 15,
];
Kontroller & Metode Baru:
- Metode di StudentNotificationController atau ekspansi di WaController
- Panggilan WhatsApp::send($phone, $message) dengan nomor member_phone siswa
Estimasi: 2 hari kerja
TAHAP 5: Integrasi & Pengujian Sistem Utuh
Tujuan: Menggabungkan semua fitur dan melakukan pengujian comprehensif.
Fitur yang Akan Dibuild:
- Alur kerja lengkap dari login hingga peminjaman buku
- Integrasi antara tahap 1-4 bekerja menyatu
- Validasi keamanan dan data
Aktivitas:
- Pengujian alur: siswa login → cari buku → ajukan request → admin setujui → ambil buku → diterima notifikasi WA
- Pengujian fitur digital: siswa login → cari buku digital → baca/unduh
- Pengujian profil: lihat data siswa tanpa foto/expired, tampilkan ID WA
Estimasi: 2-3 hari kerja
KESIMPULAN TAHAPAN:
Tahap	Fitur Utama
1	Autentikasi + Dashboard Dasar
2	Sistem Permintaan Buku Fisik
3	Digital Book & Profil Sederhana
4	Notifikasi WhatsApp Baru
5	Integrasi & Pengujian
TOTAL	Semua Fitur
Cara Review:
Setiap tahap bisa diuji dan direview terpisah sebelum tahap berikutnya dimulai. Anda bisa:
- Menerima tahap 1 ✅
- Menunggu hingga tahap 1 selesai baru lanjut ke tahap 2
- Begitu seterusnya
Setiap tahap akan menghasilkan fitur yang berfungsi sendiri dan dapat diuji independen sebelum integrasi ke sistem utuh.
