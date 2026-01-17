# Panduan Lengkap Pengguna Plugin Donasai

Selamat datang di dokumentasi resmi **Donasai**. Panduan ini disusun untuk membantu Anda mengelola platform donasi dan penggalangan dana berbasis WordPress dengan mudah, mulai dari instalasi hingga pengelolaan fitur tingkat lanjut.

Dokumen ini mencakup panduan untuk **Donasai (Versi Gratis)** dan **Donasai Pro**.

---

## Daftar Isi

1.  [Perbandingan Fitur: Free vs Pro](#1-perbandingan-fitur-free-vs-pro)
2.  [Instalasi & Aktivasi](#2-instalasi--aktivasi)
3.  [Konfigurasi Awal (Wajib)](#3-konfigurasi-awal-wajib)
4.  [Panduan Penggunaan](#4-panduan-penggunaan)
    *   [Membuat Kampanye Donasi](#41-membuat-kampanye-donasi)
    *   [Mengelola Donasi Masuk](#42-mengelola-donasi-masuk)
    *   [Melihat Laporan](#43-melihat-laporan)
5.  [Fitur & Pengaturan Donasai Pro](#5-fitur--pengaturan-donasai-pro)
    *   [Aktivasi Lisensi](#51-aktivasi-lisensi)
    *   [Payment Gateway Otomatis](#52-payment-gateway-otomatis)
    *   [Donasi Rutin (Langganan)](#53-donasi-rutin-langganan)
    *   [Tampilan & Social Proof](#54-tampilan--social-proof)
    *   [Fee Coverage & Kuitansi PDF](#55-fee-coverage--kuitansi-pdf)
6.  [Shortcode & Halaman](#6-shortcode--halaman)

---

## 1. Perbandingan Fitur: Free vs Pro

Berikut adalah ringkasan fitur berdasarkan versi plugin untuk membantu Anda memahami kemampuan sistem Anda.

| Fitur | Donasai (Free) | Donasai Pro |
| :--- | :--- | :--- |
| **Kampanye** | Unlimited (Tanpa Batas) | Unlimited + Layout Advanced |
| **Jenis Donasi** | Sekali Saja (One-time) | **Sekali & Rutin (Bulanan/Tahunan)** |
| **Pembayaran** | Transfer Bank Manual (Cek Bukti Transfer) | **Otomatis (QRIS, VA, E-Wallet)** via Midtrans/Xendit/Tripay |
| **Rekening Bank** | Input Manual Dasar | **Multi Rekening** & Kode Unik |
| **Notifikasi** | Email Dasar (Pending/Success) | Email + **Reminder Otomatis** |
| **Kuitansi** | Email Sederhana | **PDF Resmi (Downloadable)** |
| **Biaya Admin** | Ditanggung Organisasi | **Fee Coverage** (Opsional ditanggung donatur) |
| **Laporan** | Ringkasan Dasar | **Grafik Detail, Top Donors, Export CSV** |
| **Engagement** | - | **Social Proof (Popup Donasi & Countdown)** |
| **Branding** | Standar | **White Label** (Hapus tulisan "Powered by") |

> **Catatan:** Fitur seperti *WhatsApp Notification* saat ini masih dalam tahap pengembangan (Roadmap) untuk versi Pro.

---

## 2. Instalasi & Aktivasi

### Instalasi Donasai (Gratis)
1.  Masuk ke Dashboard WordPress (`wp-admin`).
2.  Buka menu **Plugins > Add New**.
3.  Cari kata kunci **"Donasai"** atau upload file `.zip` plugin jika Anda mengunduh dari repositori.
4.  Klik **Install Now** kemudian **Activate**.

### Instalasi Donasai Pro
*Syarat: Plugin Donasai (Free) harus sudah terinstal dan aktif.*

1.  Unduh file `donasai-pro.zip` dari member area.
2.  Buka menu **Plugins > Add New > Upload Plugin**.
3.  Pilih file `donasai-pro.zip` dan klik **Install Now**.
4.  Klik **Activate**.

---

## 3. Konfigurasi Awal (Wajib)

Sebelum membuat kampanye, pastikan pengaturan dasar sudah sesuai.

### A. Profil Organisasi
Pergi ke **Donasai > Settings > General**.
*   **Organization Name**: Isi nama yayasan/lembaga Anda.
*   **Contact Info**: Email & No HP untuk footer email/kuitansi.
*   **Currency**: Pastikan terpilih **IDR (Rp)**.

### B. Pengaturan Rekening (Transfer Manual)
Jika Anda menggunakan versi Gratis atau ingin menerima transfer manual di Pro:
1.  Pergi ke **Donasai > Settings > Payment**.
2.  Aktifkan **Manual Bank Transfer**.
3.  Klik **Add Bank Account**.
4.  Masukkan: Nama Bank, Nomor Rekening, dan Atas Nama.
5.  Simpan pengaturan.

---

## 4. Panduan Penggunaan

Bagian ini menjelaskan cara operasional sehari-hari menggunakan Donasai.

### 4.1 Membuat Kampanye Donasi
1.  Masuk ke menu **Donasai > Campaigns > Add New**.
2.  **Judul & Deskripsi**:
    *   Tulis judul yang menarik.
    *   Gunakan "Story Builder" (Editor WordPress) untuk menceritakan kisah lengkap, menambahkan foto, atau video.
3.  **Pengaturan Kampanye (Campaign Data)**:
    *   **Target Amount**: Nominal target (Rp). Kosongkan jika tidak ada target.
    *   **Deadline**: Batas waktu kampanye.
    *   **Category**: Pilih jenis donasi (Zakat, Infak, Wakaf, Qurban, atau Umum). *Fitur Kalkulator Zakat akan aktif otomatis jika kategori Zakat dipilih.*
4.  **Gambar Unggulan (Featured Image)**:
    *   Upload gambar utama (Banner) di sidebar kanan.
5.  Klik **Publish**.

### 4.2 Mengelola Donasi Masuk
Semua donasi tercatat di menu **Donasai > Donations**.

#### Untuk Transfer Manual (Free & Pro):
1.  Donatur mengisi form -> Status donasi menjadi **Pending**.
2.  Donatur melakukan transfer dan mengunggah bukti bayar (Link upload ada di email).
3.  Admin mengecek bukti bayar di kolom donasi.
4.  Jika valid, Admin mengubah status dari **Pending** menjadi **Completed**.
5.  Email "Terima Kasih" terkirim otomatis ke donatur.

#### Untuk Payment Gateway (Pro):
*   Status akan berubah otomatis menjadi **Completed** setelah pembayaran berhasil diverifikasi oleh Midtrans/Xendit/Tripay. Tidak perlu verifikasi manual.

### 4.3 Melihat Laporan
*   **Dashboard**: Lihat ringkasan total donasi dan kampanye aktif di **Donasai > Dashboard**.
*   **Donation List**: Lihat tabel detail siapa saja yang berdonasi.
*   **Export (Pro Only)**: Di versi Pro, Anda bisa mengunduh data donatur ke format CSV untuk diolah di Excel.

---

## 5. Fitur & Pengaturan Donasai Pro

Panduan khusus untuk pengguna lisensi Pro.

### 5.1 Aktivasi Lisensi
Agar fitur Pro berjalan dan mendapat update:
1.  Masuk ke **Donasai > Settings > License**.
2.  Klik **Connect / Activate**.
3.  Login ke akun Donasai.com Anda untuk verifikasi.

### 5.2 Payment Gateway Otomatis
Menerima pembayaran real-time (QRIS, VA, E-Wallet).
1.  Pergi ke **Donasai > Settings > Payment**.
2.  Pilih Gateway (Midtrans / Xendit / Tripay).
3.  Masukkan **Client Key** dan **Server Key** (didapat dari dashboard penyedia payment gateway).
4.  Pastikan mode **Production** aktif untuk transaksi asli.

### 5.3 Donasi Rutin (Langganan)
Fitur ini memungkinkan donatur berkomitmen donasi bulanan/tahunan.
*   Aktif otomatis di formulir donasi Pro.
*   Pastikan Payment Gateway mendukung fitur "Subscription" atau "Recurring" (biasanya kartu kredit atau e-wallet tertentu), atau gunakan metode manual dengan reminder email.
*   Sistem akan mengirim **Email Reminder** saat waktunya perpanjangan donasi.

### 5.4 Tampilan & Social Proof
Tingkatkan kepercayaan calon donatur.
*   **White Label**: Pergi ke **Settings > Appearance**. Centang opsi untuk menghapus branding "Powered by Donasai".
*   **Layout**: Pilih tata letak kampanye (Sidebar Kiri/Kanan atau Full Width).
*   **Social Proof**:
    *   **Recent Sales Popup**: Muncul notifikasi kecil "Hamba Allah baru saja berdonasi Rp 50.000".
    *   **Countdown Timer**: Penanda waktu mundur untuk kampanye dengan deadline.

### 5.5 Fee Coverage & Kuitansi PDF
*   **Fee Coverage**: Pergi ke **Settings > Fee Coverage**. Aktifkan agar donatur bisa menanggung biaya admin/platform (misal: 5% atau Rp 2.000).
*   **Kuitansi PDF**: Donatur dapat mengunduh kuitansi resmi dalam format PDF melalui Dashboard Donatur atau link di email sukses. Template kuitansi dapat diatur di **Settings > Receipt**.

---

## 6. Shortcode & Halaman

Secara default, Donasai membuat halaman otomatis. Jika perlu manual:

*   `[wpd_donation_form]` : Menampilkan formulir donasi di halaman apapun.
*   `[wpd_campaign_list]` : Menampilkan daftar kampanye (Grid).
*   `[wpd_donor_dashboard]` : Halaman area khusus donatur (Riwayat donasi, Download Kuitansi, Edit Profil).
*   `[wpd_my_donations]` : Tabel riwayat donasi simpel.

---

**Butuh Bantuan?**
Jika Anda mengalami kendala teknis, silakan hubungi tim support kami melalui member area atau cek file dokumentasi teknis di folder plugin.
