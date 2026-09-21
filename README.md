# 📚 PERPUS — Sistem Informasi Perpustakaan

<p align="center">
  <b>Sistem Informasi Perpustakaan berbasis PHP Native + MySQL + Bootstrap 5</b><br>
  Dilengkapi Notifikasi WhatsApp Otomatis untuk Reminder & Keterlambatan
</p>

---

## 🏷 Tech Stack

![PHP](https://img.shields.io/badge/PHP-Native-blue?style=for-the-badge&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-Database-orange?style=for-the-badge&logo=mysql)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5-purple?style=for-the-badge&logo=bootstrap)
![JavaScript](https://img.shields.io/badge/JavaScript-Fetch_API-yellow?style=for-the-badge&logo=javascript)
![WhatsApp API](https://img.shields.io/badge/WhatsApp-Auto_Reminder-green?style=for-the-badge&logo=whatsapp)

---

# ✨ Fitur Utama

## 📊 Dashboard
- Statistik buku & anggota
- Jumlah peminjaman aktif
- Monitoring status WA (Pending / Sent / Failed)
- Auto sender (heartbeat system)

---

## 📖 Manajemen Buku
- CRUD Buku
- Scan Barcode
- Stock Management
- Katalog Modern

---

## 👥 Manajemen Anggota
- Data lengkap siswa
- Nomor HP terintegrasi untuk WA
- Validasi data anggota

---

## 📦 Sistem Peminjaman
- Proses pinjam & kembali
- Hitung denda otomatis
- Status Overdue
- Atur aturan denda (Admin)

---

# 💬 WhatsApp Notification System

Sistem akan mengirim notifikasi otomatis kepada anggota terkait:

### 🔔 Reminder Mendekati Jatuh Tempo
Notifikasi sebelum tanggal pengembalian buku.

### ⏰ Notifikasi Keterlambatan
Pesan otomatis kepada anggota yang terlambat.

### 💰 Informasi Denda
Menampilkan nominal denda secara otomatis.

---

## ⚙ Mode Pengiriman

### 1️⃣ Manual Send
Admin dapat langsung kirim ke:
- Semua yang Overdue
- Semua yang Mendekati Jatuh Tempo

Pesan langsung terkirim saat tombol ditekan.

---

### 2️⃣ Schedule Besok (Jam 07:00)
- Klik "Buat Jadwal Besok"
- Sistem membuat antrian otomatis
- Bisa Cancel sebelum terkirim
- Monitoring maksimal 25 data terakhir

---

### 3️⃣ Cron Mode (Opsional)

```bash
php cron_wa_generate.php
php cron_wa_send.php
