=== Donasai - Platform Donasi & Penggalangan Dana ===
Contributors: hadie danker
Tags: donation, fundraising, zakat, qurban, donasi, sedekah, crowdfunding, payment gateway, midtrans
Requires at least: 6.4
Tested up to: 6.6
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Platform donasi dan penggalangan dana WordPress lengkap untuk Yayasan, Masjid, dan Komunitas. Integrasi Midtrans & Transfer Manual.

== Description ==

**Donasai** adalah plugin WordPress donasi karya anak bangsa yang dirancang khusus untuk kebutuhan penggalangan dana di Indonesia. Cocok untuk Yayasan, Masjid, Lembaga Zakat, atau komunitas yang ingin menerima donasi secara profesional dan transparan.

Plugin ini mendukung berbagai jenis akad donasi seperti **Zakat Maal**, **Zakat Penghasilan**, **Qurban**, **Infaq**, **Sodaqoh**, dan **Wakaf**.

### Fitur Utama

*   **Manajemen Kampanye**: Buat kampanye donasi unlimited dengan target, batas waktu, dan kategori.
*   **Kalkulator Zakat**: Hitung otomatis Zakat Maal dan Penghasilan di dalam form donasi.
*   **Jenis Donasi Lengkap**: Mendukung nominal tetap, nominal bebas, dan pilihan paket (misal: Paket Sembako).
*   **Sistem Pembayaran**:
    *   **Transfer Bank Manual**: Verifikasi manual dengan unggah bukti transfer.
    *   **Midtrans Payment Gateway**: Integrasi otomatis Virtual Account, QRIS, E-Wallet (GoPay, OVO, ShopeePay), dan Kartu Kredit.
*   **Fundraising**: Fitur untuk relawan/fundraiser membagikan link kampanye dan melacak perolehan donasi mereka.
*   **Kuitansi & Notifikasi**: Email notifikasi otomatis dan kuitansi donasi yang profesional.
*   **Manajemen Donatur**: Dashboard untuk donatur melihat riwayat donasi mereka.
*   **Laporan**: Rekapitulasi donasi harian, bulanan, dan per kampanye.

### Kenapa Memilih Donasai?

1.  **UI/UX Modern**: Form donasi yang mobile-friendly dan mudah digunakan.
2.  **Ringan & Cepat**: Dibangun dengan standar coding WordPress modern (React + PHP).
3.  **Khas Indonesia**: Istilah dan alur yang disesuaikan dengan kebiasaan berdonasi di Indonesia.

== Installation ==

1.  Upload folder `donasai` ke direktori `/wp-content/plugins/` atau install via menu Plugins > Add New di WordPress.
2.  Aktifkan plugin melalui menu 'Plugins'.
3.  Akan muncul menu **Donasai** di sidebar admin.
4.  Masuk ke **Donasai > Settings** untuk mengatur Payment Gateway (Midtrans) dan Rekening Bank.
5.  Buat kampanye pertama Anda di **Donasai > Campaigns**.
6.  Gunakan shortcode `[wpd_donation_form]` (jika manual) atau biarkan plugin membuat halaman otomatis.

== Screenshots ==

1. **Dashboard Kampanye** - Tampilan manajemen kampanye yang informatif.
2. **Form Donasi** - Tampilan form donasi yang bersih dan mudah digunakan.
3. **Pilihan Pembayaran** - Mendukung Transfer Bank dan E-Wallet via Midtrans.
4. **Kalkulator Zakat** - Fitur hitung zakat otomatis.

== Frequently Asked Questions ==

= Apakah plugin ini gratis? =
Ya, versi dasar (Donasai Free) gratis selamanya dan sudah cukup untuk menerima donasi. Tersedia versi PRO untuk fitur lanjutan seperti Donasi Berulang (Recurring) dan Notifikasi WhatsApp.

= Bagaimana cara setting Midtrans? =
Anda perlu mendaftar akun di [Midtrans](https://midtrans.com), lalu ambil **Server Key** dan **Client Key**. Masukkan key tersebut di menu Donasai > Settings > Payment.

= Apakah mendukung mata uang selain Rupiah? =
Saat ini Donasai difokuskan untuk pengguna di Indonesia dengan mata uang Rupiah (IDR).

== Changelog ==

= 1.0.0 =
*   Initial release.
*   Fitur dasar: Kampanye, Donasi, Zakat, Midtrans, Transfer Manual.

