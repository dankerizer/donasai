# Backlog Submit Direktori WordPress

Dokumen ini merinci perbaikan dan peningkatan yang diperlukan berdasarkan review awal dari WordPress.org (Review Otomatis).

**Sumber:** `docs/01-email-review.html`
**Status:** Review Ditunda (Menunggu perbaikan)

## 🚨 Kritis / Penghambat (Wajib Diperbaiki)

### 1. Verifikasi Identitas & Kepemilikan
*   **Masalah:** Email yang disubmit (`gmail.com`) dan username (`hadie-danker`) tidak cocok dengan domain URI plugin (`donasai.com`) atau kontributor.
*   **Tindakan yang Diperlukan:**
    *   [ ] Ubah email profil WordPress.org ke alamat yang berakhiran `@donasai.com`.
    *   [ ] Balas email review untuk mengonfirmasi perubahan atau menjelaskan kepemilikan.

### 2. Keamanan: Sanitasi Input
*   **Masalah:** Sanitasi input (`$_GET`, `$_POST`, `$_REQUEST`) hilang atau terlambat dilakukan.
*   **Contoh Spesifik:**
    *   [x] `includes/api/donations-controller.php:250`: Validasi penggunaan `wp_verify_nonce`. Pastikan input nonce disanitasi *sebelum* diverifikasi.
        *   *Perbaikan:* `wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['...'] ) ), ... )`
    *   [x] **Audit Global:** Pindai kode untuk semua `$_GET`, `$_POST`, `$_REQUEST` dan pastikan menggunakan:
        *   `sanitize_text_field()` untuk teks.
        *   `sanitize_email()` untuk email.
        *   `absint()` untuk integer (angka bulat).
        *   `esc_url_raw()` untuk URL.

### 3. Keamanan: Nonce & Izin (Permissions)
*   **Masalah:** Endpoint REST API menggunakan `__return_true` tanpa alasan yang jelas atau pemeriksaan izin spesifik untuk tindakan sensitif.
*   **Contoh Spesifik:**
    *   [x] `includes/api/campaigns-controller.php:12`: `POST /donate`.
        *   *Tindakan:* Verifikasi apakah ini harus publik. Jika ya, dokumentasikan alasannya. Jika tidak, tambahkan pemeriksaan izin.
    *   [x] `includes/api/fundraisers-controller.php:23`: `GET /fundraisers`.
        *   *Tindakan:* Tentukan apakah ini menampilkan data pengguna sensitif. Jika leaderboard publik, `__return_true` mungkin aman tapi perlu diverifikasi.
    *   [x] `includes/api/webhook-controller.php:12`: `POST /midtrans/webhook`.
        *   *Tindakan:* Webhook biasanya butuh akses publik tapi harus memverifikasi signature payload/keamanan di dalam callback. Pastikan ini aman.
*   **Umum:**
    *   [x] Pastikan semua tindakan AJAX memiliki `check_ajax_referer` atau `wp_verify_nonce`.
    *   [x] Pastikan semua tindakan sensitif memeriksa `current_user_can()`.

### 4. Kualitas Kode: Prefixing (Awalan Nama)
*   **Masalah:** Nama fungsi, class, option, atau global variabel yang terlalu umum dan bisa bentrok dengan plugin lain.
*   **Tindakan yang Diperlukan:**
    *   [x] Pastikan SEMUA fungsi, class, dan variabel global di namespace global dimulai dengan `donasai_` atau `wpd_`.
    *   [x] Cek `includes/gateways/stripe.php` dan file include lainnya.
    *   [x] Cek key `update_option`, `get_option` (harus `donasai_settings`, bukan nama umum).

### 5. Arsitektur: Enqueuing Assets (CSS/JS)
*   **Masalah:** Tag `<link>`, `<style>`, dan `<script>` ditemukan langsung di dalam file template (inline).
*   **Tindakan yang Diperlukan:** Pindahkan ke `wp_enqueue_scripts` / `admin_enqueue_scripts` atau gunakan `wp_add_inline_script` / `wp_add_inline_style`.
*   **Status:** Selesai
    *   [x] `frontend/templates/donation-form.php`
    *   [x] `frontend/templates/donation-summary.php`
    *   [x] `frontend/templates/donor-dashboard.php`
    *   [x] `frontend/templates/confirmation-form.php`
    *   [x] `includes/admin/campaign-columns.php`

## 📋 Kepatuhan & Dokumentasi

### 6. Layanan Eksternal
*   **Masalah:** Penggunaan layanan pihak ke-3 (Facebook Pixel, Midtrans, dll) tidak didokumentasikan secara eksplisit di `readme.txt`.
*   **Tindakan yang Diperlukan:**
    *   [ ] Update `readme.txt` dengan bagian `== External Services ==`.
    *   [ ] Daftar layanan:
        *   Facebook Pixel (jika digunakan)
        *   Midtrans / Payment Gateway (Stripe, dll)
        *   CDN atau font eksternal (Google Fonts)
    *   [ ] Untuk setiap layanan, sertakan: Tujuan, Data yang dikirim, Link ke Syarat Ketentuan/Kebijakan Privasi mereka.

### 7. Lisensi & Pembatasan (Trialware)
*   **Masalah:** Potensi fitur terkunci atau kode "Upsell" yang menonaktifkan fungsionalitas di versi gratis.
*   **Tindakan yang Diperlukan:**
    *   [ ] Audit kode untuk pengecekan "Hanya Pro" yang *menonaktifkan* kode fungsional yang ada di repositori.
    *   [ ] Pastikan plugin berfungsi penuh apa adanya. Fitur Pro harus menjadi plugin tambahan yang terpisah atau kode yang sama sekali tidak ada di repo gratis.

### 8. Manajemen Library
*   **Masalah:** `includes/gateways/stripe.php` ditandai sebagai potensi konflik library.
*   **Tindakan yang Diperlukan:**
    *   [ ] Cek bagaimana Stripe SDK disertakan.
    *   [ ] Jika menggunakan library bawaan, pertimbangkan untuk memberi namespace (menggunakan Strauss/Mozart) atau cek `class_exists` untuk menghindari fatal error jika plugin lain memuat versi berbeda.

## ✅ Langkah Selanjutnya
1.  **Perbaiki Item Kritis** (Identitas, Sanitasi, Nonce).
2.  **Refactor Kode** untuk Prefix dan Enqueuing.
3.  **Update Dokumentasi** (Readme).
4.  **Tes secara menyeluruh**.
5.  **Balas Email Review WordPress** dengan update tersebut.
