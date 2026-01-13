# Backlog Feature Donasai (Free Version)

Dokumen ini berisi daftar fitur untuk versi **Free** (WordPress.org repo) dari plugin Donasai.
Backlog ini disusun untuk memastikan user mendapatkan fitur dasar yang fungsional namun tetap ada insentif untuk upgrade ke Pro.

**Status:**
- `[x]` Selesai / Fitur Dasar
- `[/]` Sedang Dikerjakan
- `[ ]` Rencana / Belum Dikerjakan

---

## 📦 1. Campaign Management (Core)
Fitur dasar untuk membuat dan menampilkan donasi.

- [x] **Unlimited Campaigns**: Custom Post Type `donasai_campaign`.
- [x] **Basic Landing Page**:
  - [x] Judul, Deskripsi, Featured Image.
  - [x] Target Amount & Progress Bar.
  - [x] Status Campaign (Draft/Publish/Closed).
- [x] **Kategori Campaign**: Taksonomi dasar (Zakat, Infak, Wakaf, Qurban, Umum).
- [x] **Story Builder**: Editor standar WordPress (Gutenberg/Classic) untuk deskripsi campaign.
- [ ] **Basic Shortcode**: `[donasai_campaigns]` untuk menampilkan grid campaign.

## 💰 2. Donation Form & Flow
Formulir donasi yang mudah digunakan.

- [x] **Auto-generated Form**: Form otomatis muncul di halaman campaign.
- [x] **Donation Amount**:
  - [x] Preset Amount (Pilihan nominal default).
  - [x] Custom Amount (Input manual).
- [x] **Donor Fields**:
  - [x] Nama Lengkap, Email, No HP (WA).
  - [x] Pesan / Doa (Optional).
- [x] **Anonymous Donation**: Opsi "Sembunyikan nama saya" (Hamba Allah).
- [x] **Basic T&C**: Checkbox persetujuan syarat & ketentuan (global setting).

## 💳 3. Payment Gateway
Metode pembayaran dasar.

- [x] **Manual Transfer (Bank Transfer)**:
  - [x] Input nomor rekening manual di settings.
  - [x] Instruksi pembayaran statis.
  - [x] Konfirmasi manual oleh Admin.
- [ ] **1 Global Gateway (Optional)**:
  - [ ] Stripe / PayPal Standard (Hanya 1 opsi global untuk versi Free, agar user bisa terima uang online basic).

## 👥 4. Donor Management
Manajemen donatur sederhana.

- [x] **Donor Data Storage**: Simpan data donatur di database custom / meta.
- [x] **My Donations (Frontend)**:
  - [x] Shortcode `[donasai_my_donations]`.
  - [x] Tabel riwayat donasi user yang login.
- [x] **Email Notification**:
  - [x] Email konfirmasi "Pending" (Instruksi bayar).
  - [x] Email konfirmasi "Success" (Terima kasih).

## 📊 5. Reporting & Admin Dashboard
Data untuk pengelola.

- [x] **Admin Dashboard Overview**:
  - [x] Total Donasi masuk.
  - [x] Jumlah Campaign aktif.
- [x] **Donation List**: Tabel list donasi dgn status (Pending/Processing/Completed/Cancelled).
- [ ] **Basic Export**: Export data donasi ke CSV (Simple format: Date, Name, Amount, Campaign).

## 🎨 6. Frontend & Themes
Tampilan di sisi user.

- [x] **Box / Card Design**: Tampilan grid campaign standar.
- [x] **Responsive Design**: Mobile-friendly layout.
- [x] **Theme Compatibility**: Basic support untuk major themes (Astra, Hello, TwentyTwenty*).

---

## 🚫 Excluded from Free (Pro Only)
Fitur ini **TIDAK** boleh ada di versi Free (Insentif Upgrade).

- Recurring (Langganan).
- Payment Gateway Lokal Otomatis (Midtrans/Xendit/Tripay).
- WhatsApp Notification.
- PDF Receipt.
- Multi-step Form / Form Builder.
- Custom Field Campaign.
- Fee Coverage (Biaya Admin ditanggung donatur).
- Report & Grafik Advanced.
