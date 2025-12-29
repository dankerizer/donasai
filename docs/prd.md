Berikut draft PRD untuk **wp-donasi** (versi freemium: free di wp.org + Pro berbayar), sudah menggabungkan konsep campaign + form + payment + dashboard + arsitektur React/Tailwind.

***

## 1. Overview

**Nama Produk:** wp-donasi  
**Tipe:** Plugin WordPress donasi/fundraising freemium (Free di WordPress.org, Pro sebagai add-on).[1][2]

**Tujuan utama:**

- Memudahkan lembaga (masjid, NGO, sekolah, komunitas) membuat halaman donasi profesional tanpa coding.[3][4]
- Menyediakan platform donasi modern dengan campaign, form fleksibel, payment gateway, dan dashboard yang enak dipakai.[2][1]

**Target user:**

- Admin WordPress dari lembaga non-profit yang butuh donasi online.[4][3]
- Developer/agency yang bikin website untuk lembaga zakat, masjid, dsb dan perlu plugin donasi yang customizable.[5][3]

***

## 2. Scope & Non-Goals

### 2.1. In-Scope (MVP/MLP)

- Campaign management (CPT campaign + landing per campaign).[1][3]
- Donation form per campaign (single-step, mobile-friendly, bisa preset + custom amount).[6][1]
- Minimal payment: offline bank transfer + 1–2 gateway populer (Stripe/PayPal/global) di free; gateway lokal & advanced di Pro.[7][8][1]
- Dashboard admin: ringkasan donasi, list donasi, grafik dasar.[9][1]
- Donor listing + basic donor profile.[7][1]
- Donor “My Donations” dashboard sederhana (optional free).[8][1]
- Admin UI pakai React + TypeScript + shadcn di halaman plugin.[10][11]
- Frontend view HTML + Tailwind, bisa di-custom (template override/filter).[12][8]

### 2.2. Out-of-Scope (di luar versi pertama)

- Recurring donation/ subscription (bisa masuk Pro tahap berikutnya).[3][2]
- PDF receipt advanced, multi-currency complex, multi-tenant SaaS, dsb.[13][8]
- Full CRM/marketing automation (email campaign builder dsb).[2][7]

***

## 3. Product Features

### 3.1. Campaign Management

**Deskripsi:**

- Custom Post Type `wpd_campaign`.  
- Setiap campaign punya landing page dengan informasi lengkap + progress bar + form donasi.[6][1]

**Data utama:**

- Title, slug, excerpt, content.[1]
- Target amount, collected amount, deadline, status (draft, published, closed).[3]
- Category (zakat, infak, wakaf, qurban, umum).[3]
- Thumbnail/image, optional video embed.[3]

**User stories:**

- Sebagai admin, saya ingin membuat campaign donasi dengan target dan deadline agar bisa mengatur fokus penggalangan dana.[1][3]
- Sebagai pengunjung, saya ingin melihat info lengkap campaign dan progress agar yakin untuk berdonasi.[1][3]

**Free vs Pro (campaign):**

- Free: unlimited campaign, basic fields, 1 template landing.[1][3]
- Pro: lebih banyak template/skin, custom fields (fund category, lokasi, dsb.), layout builder.[8][2]

***

### 3.2. Donation Forms

**Deskripsi:**

- 1 campaign = 1 form donasi (auto-created) dengan opsi styling via Tailwind dan setting.[6][1]
- Form bisa ditampilkan via shortcode/block di halaman mana pun.[6][1]

**Fitur form (Free):**

- Amount selection: preset levels (misal 50k, 100k, 250k) + custom amount.[6][1]
- Donor info: name, email, phone, notes, anonymous toggle.[7][1]
- Terms & conditions checkbox.[7]
- Success/fail redirect page.[7]

**Validasi:**

- Amount > 0 dan >= minimal yang ditentukan.[7]
- Email format, wajib name + email.[7]

**Pro enhancements (future):**

- Recurring donation (monthly, yearly).[2][3]
- Custom fields builder.[8][7]
- Fee coverage checkbox (“donatur menanggung biaya admin”).[8][7]

***

### 3.3. Payment Processing

**Deskripsi:**

- Abstraksi payment gateway dengan interface tunggal (`GatewayInterface`) dan implementasi per provider.[8][7]
- Minimal support free:  
  - Offline transfer (manual confirmation).[7]
  - Satu gateway global (misal Stripe/PayPal) agar plugin usable global.[6][1]

**Entity:**

- `wpd_donations` (custom DB table):  
  - `id`, `campaign_id`, `user_id`, `name`, `email`, `phone`.  
  - `amount`, `currency`, `payment_method`, `status` (pending, complete, failed, refunded).[1][7]
  - `gateway`, `transaction_id`, `metadata`, `created_at`, `updated_at`.[1][7]

**Flow (global):**

1. Donor submit form → record donation as `pending`.[7]
2. Jika payment gateway: buat charge / payment session → redirect/tampilkan instruksi.[7]
3. Gateway callback/webhook → update status ke `complete`, update progress campaign.[8][7]
4. Kirim email konfirmasi ke donor (minimal di Free).[7]

**Pro:**

- Payment gateway tambahan (Midtrans, Xendit, iPaymu, dsb.).[5][3]
- Express checkout (Apple Pay/Google Pay dsb kalau pakai gateway yang support).[8]
- Recurring payment.[3][8]

***

### 3.4. Donor & Reporting Dashboard

#### 3.4.1. Admin Dashboard (React)

**Stack:**

- React + TypeScript + shadcn UI untuk halaman admin plugin `wp-donasi`.[11][10]
- Tailwind untuk styling; asset dibundle dan hanya di-enqueue di halaman plugin.[14][12]

**Fitur tampilan (Free):**

- Overview cards:  
  - Total donations (all time, this month).[9][1]
  - Number of donors.[9][1]
  - Active vs closed campaigns.[1]
- Chart sederhana: donasi per hari/ minggu (line/bar chart).[9][1]
- Recent donations table: 10 terakhir (nama, nominal, campaign, status).[9][1]

**Fitur data & filtering (Free):**

- Filter donation by date range, campaign, status.[3][1]
- Export CSV sederhana.[1][7]

**Pro reporting:**

- Filter lebih advanced (payment method, currency, donor segment).[15][1]
- Export detail (per donor, per campaign, recurring overview).[15][1]

#### 3.4.2. Donor Dashboard (Frontend)

**Free (minimal):**

- Halaman “My Donations” (shortcode) untuk user login: list donation miliknya (tanggal, campaign, amount, status).[8][1]

**Pro:**

- Donor bisa download receipt/PDF.[13][8]
- Kelola recurring donations (pause/cancel).[8]

***

## 4. UX / UI Requirements

### 4.1. Admin UX

- Admin menu: `Donasi` dengan sub-menu: Dashboard, Campaigns, Donations, Settings.[9][1]
- Dashboard, Donations, Settings ditangani oleh React SPA.[10][11]
- Navigasi internal SPA tanpa reload full page.[16][10]
- Responsif, minimal enak digunakan di tablet.[8]

### 4.2. Frontend UX

- Campaign landing:  
  - Hero (judul, target, progress bar, tombol donasi).[3][1]
  - Detail campaign (deskripsi, foto/video).[1]
  - List donatur terbaru (opsional).[3]
- Form donasi:  
  - Satu kolom, minim distraksi, mobile-first.[8][1]
  - Setelah submit: jelas status (sedang proses, menunggu pembayaran, sukses).[7]

### 4.3. Customization & Tailwind

- Tailwind class bisa diubah melalui:  
  - Template override di theme (misal copy `templates/campaign-single.php`).[12][8]
  - Filter PHP untuk HTML output (misal `wpd_campaign_form_html`).[12]

***

## 5. Technical Requirements

### 5.1. Platform

- Minimum: WordPress 6.4+, PHP 7.4+, MySQL 5.7+.[17][18][1]
- Kompatibel dengan theme standard dan builder umum (Elementor, Block Editor).[6][1]

### 5.2. Arsitektur Plugin

- Struktur folder:  
  - `wp-donasi.php` (main plugin file).  
  - `/includes` (CPT, DB, services, REST API).  
  - `/admin-app` (React+TS source).  
  - `/build/admin` (admin bundle JS/CSS).  
  - `/frontend` (PHP templates + compiled CSS).  
- Admin app:  
  - Development: Vite/`wp-scripts` dengan HMR; admin page load dev bundle saat `WP_DEBUG` + env dev.[14][16][10]
  - Production: built assets di-enqueue via `wp_enqueue_script` & `wp_enqueue_style`.[10][14]

### 5.3. Integrations & APIs

- REST API custom namespace `wpd/v1`:  
  - `/campaigns`, `/donations`, `/stats`.[19][10]
- Payment gateway adapter pattern (class per gateway).[8][7]
- Hooks/filters extensible untuk Pro/add-ons (cth: `wpd_register_gateway`, `wpd_after_donation_insert`).[20]

### 5.4. Security & Compliance

- Nonce untuk semua form & AJAX.[21]
- Sanitization/escaping standar WordPress.[18][21]
- Data privacy: dukung export/erase tools WP untuk data donor.[8]

***

## 6. Freemium & Distribution

### 6.1. Free vs Pro Matrix (ringkas)

- Free:  
  - Unlimited campaign & donation.[3][1]
  - 1–2 payment gateway + offline.[1][7]
  - Basic dashboard & reporting.[9][1]
  - Basic donor dashboard.[1][8]

- Pro:  
  - Gateway lokal & recurring, PDF receipts, fee coverage, extra layouts, AI-assisted content (future).[13][15][8]

### 6.2. WordPress.org Guideline

- Free plugin harus fully usable, tanpa time limit / artificial cap.[22][21]
- Upsell Pro hanya lewat halaman/settings plugin dengan cara yang tidak mengganggu.[23][21]

***

## 7. Success Metrics

- 1.000+ active installs dalam 12 bulan pertama.[4][1]
- Rating minimal 4.5 di WordPress.org.[4][1]
- Conversion dari Free → Pro > 2% dari active installs tahun pertama (target internal).[22][23]




## wp-donasi Free vs Pro

| Area                | Fitur                                                       | Free (wp.org)                                                                 | Pro (Berbayar)                                                                                 |
|---------------------|-------------------------------------------------------------|-------------------------------------------------------------------------------|------------------------------------------------------------------------------------------------|
| Campaign            | Jumlah campaign                                             | Unlimited campaign basic [1][3]                                     | Unlimited + advanced options (kategori khusus, lokasi, tags lanjutan) [2][4]        |
|                     | Landing page per campaign                                  | 1 layout default dengan progress bar & detail [1][3]               | Beberapa layout/skin, opsi hero & section builder [2][4]                            |
|                     | Kategori campaign (zakat, infak, wakaf, qurban, umum)      | Ya, kategori dasar [3][5]                                          | Kategori kustom, subkategori, label khusus (program rutin, darurat, dll.) [2][4]    |
|                     | Target & progress                                           | Target amount + progress otomatis [1][3]                            | Multi-target, progress per sub-program [6][4]                                       |
| Donation Form       | Form per campaign (auto generate)                          | Ya, 1 form per campaign [1][7]                                     | Ya, plus multi-form per campaign (segmentasi donor) [6][4]                          |
|                     | Preset amount + custom amount                              | Ya [1][7]                                                           | Ya + logika lanjutan (saran nominal, minimum per metode) [8][6]                     |
|                     | Field donor standar (nama, email, phone, note)             | Ya [1][8]                                                           | Ya + custom fields builder (text, select, checkbox, dsb.) [8][4]                    |
|                     | Donasi anonim                                              | Ya (hide nama di list donatur) [1][8]                               | Ya + kontrol tampilkan inisial/avatar saja [4]                                           |
|                     | Terms & Conditions checkbox                                | Ya, hardcoded link/setting sederhana [8]                                 | Ya + multi-terms per campaign & tracking consent [4]                                     |
| Payment             | Offline transfer / manual payment                          | Ya (instruksi transfer + update manual) [8]                              | Ya + auto reminder & konfirmasi via email/WA (future) [8][4]                        |
|                     | Gateway global (Stripe/PayPal setara)                      | 1 gateway global dasar [1][8]                                       | Banyak gateway global + advanced options (Apple Pay, Google Pay, dsb.) [2][4]       |
|                     | Gateway lokal Indonesia (Midtrans, Xendit, dsb.)           | Tidak                                                                          | Ya, beberapa integrasi sekaligus & per-campaign routing [3][9]                      |
|                     | Recurring / subscription                                   | Tidak [3][4]                                                        | Ya: bulanan/tahunan, kelola dari donor dashboard [2][4]                             |
|                     | Fee coverage (donor menanggung biaya gateway)              | Tidak [8]                                                                 | Ya, configurable per campaign [8][4]                                                |
| Donations & Donors  | Tabel donasi di admin                                      | Ya, list dasar + filter simple [1][8]                               | Filter advanced (campaign, metode, range, status detail) [1][6]                     |
|                     | Export donasi (CSV)                                        | Export sederhana (all filtered) [1][8]                              | Export lanjutan (per donor, per campaign, recurring) [1][6]                         |
|                     | Profil donor basic di admin                                | Ya (agregat per email) [1][8]                                       | Donor CRM mini: lifetime value, campaign favorit, segmentasi [2][6]                 |
| Donor Dashboard     | Halaman “My Donations”                                     | Ya: list donasi milik user login [1][4]                             | Ya + filter, download receipt, kelola recurring [10][4]                              |
|                     | Download receipt / invoice                                 | Tidak                                                                          | Ya: email & PDF receipts [10][4]                                                     |
| Admin Dashboard     | Dashboard overview (React)                                 | Ya: cards total donasi, jumlah donatur, campaign aktif [1][11]       | Ya + breakdown advanced & widget custom [6][2]                                      |
|                     | Grafik donasi per periode                                  | Grafik sederhana (harian/mingguan) [1][11]                          | Beberapa grafik (per kanal, per campaign, recurring vs one-time) [6][2]            |
|                     | Recent donations widget                                    | Ya [1][11]                                                           | Ya + widget custom & quick actions [6]                                                   |
| Frontend Views      | Template HTML + Tailwind default                           | Ya, 1–2 template dasar [12][4]                                       | Lebih banyak template, section builder, preset style [2][4]                         |
|                     | Shortcode / block untuk form & campaign list               | Ya [1][7]                                                           | Ya + param advanced (filter kategori, layout, urutan) [4][6]                        |
|                     | Override template di theme                                 | Ya (copy template ke theme child) [12][4]                            | Ya + support child template khusus Pro (addon sections) [13][4]                      |
| Tech & Dev          | React admin app + TS                                       | Ya (dashboard & settings) [14][15]                                    | Ya + modul admin tambahan (reporting pro, gateway manager) [14][16]                  |
|                     | Tailwind & shadcn di admin                                 | Ya (basic components) [12][17]                                        | Ya + komponen pro khusus (chart advanced, filter builder) [17][16]                    |
|                     | REST API basic (campaign, donations)                       | Ya (read/list + basic create) [14][18]                                | Endpoint tambahan (analytics, recurring mgmt, integrasi eksternal) [4][6]          |
| Automations         | Email konfirmasi donor                                     | Ya (minimal template) [8]                                                | Template kustom, multi bahasa, kondisi per campaign [8][4]                          |
|                     | Reminder pembayaran pending                                | Tidak                                                                          | Ya (email/WA, jika digabung dengan integrasi lain) [8][4]                           |
|                     | Integrasi CRM / webhook custom                             | Tidak                                                                          | Ya (webhook keluar, Zapier/Make-style endpoint) [4][6]                              |
| Licensing & Support | Lisensi & update                                           | Update via wp.org [1][19]                                            | License key, auto update dari server sendiri [20][21]                                 |
|                     | Support                                                    | Forum WordPress.org (best effort) [1][22]                            | Priority support (email/chat), SLA lebih cepat [20][22]                               |

Kalau mau, bisa lanjut dibuatkan:

- Draft copy untuk landing page “wp-donasi Pro” berdasarkan tabel ini, atau  
- Breakdown task teknis per baris fitur (supaya gampang kamu feed ke AI untuk generate kode modul per modul).

[1](https://wordpress.org/plugins/give/)
[2](https://givewp.com)
[3](https://kinsta.com/blog/wordpress-donation-plugins/)
[4](https://www.wc-donation.com/documentation/getting-started/wordpress-donation-plugin/)
[5](https://wp101.com/best-wordpress-donation-plugins/)
[6](https://givewp.com/features/)
[7](https://wordpress.com/plugins/give)
[8](https://www.interserver.net/tips/kb/allow-donate-givewp-donation-plugin/)
[9](https://paymentsplugin.com/blog/best-wordpress-donation-plugins/)
[10](https://givewp.com/documentation/add-ons/pdf-receipts/)
[11](https://easywebdesigntutorials.com/give-donation-wordpress-plugin-walk-through/)
[12](https://underscoretw.com/docs/wordpress-tailwind/)
[13](https://kinsta.com/blog/pro-free-versions-wordpress-plugin/)
[14](https://www.brontobytes.com/blog/building-powerful-wordpress-plugins-with-react-a-developers-guide/)
[15](https://developer.wordpress.org/news/2024/03/how-to-use-wordpress-react-components-for-plugin-pages/)
[16](https://wpcodebox.com/building-a-wordpress-plugin-in-reactjs-my-experience/)
[17](https://tailadmin.com)
[18](https://belovdigital.agency/blog/wordpress-development-with-react-creating-headless-themes/)
[19](https://givewp.com/documentation/core/requirements/)
[20](https://freemius.com/blog/freemium-business-model-wordpress/)
[21](https://freemius.com/blog/submit-plugin-wordpress-repository/)
[22](https://poststatus.com/power-wordpress-org-freemium-products/)
[23](https://donasiaja.id/)


[1](https://wordpress.org/plugins/give/)
[2](https://givewp.com)
[3](https://kinsta.com/blog/wordpress-donation-plugins/)
[4](https://wp101.com/best-wordpress-donation-plugins/)
[5](https://paymentsplugin.com/blog/best-wordpress-donation-plugins/)
[6](https://wordpress.com/plugins/give)
[7](https://www.interserver.net/tips/kb/allow-donate-givewp-donation-plugin/)
[8](https://www.wc-donation.com/documentation/getting-started/wordpress-donation-plugin/)
[9](https://easywebdesigntutorials.com/give-donation-wordpress-plugin-walk-through/)
[10](https://www.brontobytes.com/blog/building-powerful-wordpress-plugins-with-react-a-developers-guide/)
[11](https://developer.wordpress.org/news/2024/03/how-to-use-wordpress-react-components-for-plugin-pages/)
[12](https://underscoretw.com/docs/wordpress-tailwind/)
[13](https://givewp.com/documentation/add-ons/pdf-receipts/)
[14](https://kinsta.com/blog/wp-scripts-development/)
[15](https://givewp.com/features/)
[16](https://wpcodebox.com/building-a-wordpress-plugin-in-reactjs-my-experience/)
[17](https://www.wc-donation.com/documentation/getting-started/system-requirements/)
[18](https://givewp.com/documentation/core/requirements/)
[19](https://belovdigital.agency/blog/wordpress-development-with-react-creating-headless-themes/)
[20](https://kinsta.com/blog/pro-free-versions-wordpress-plugin/)
[21](https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/)
[22](https://poststatus.com/power-wordpress-org-freemium-products/)
[23](https://freemius.com/blog/freemium-business-model-wordpress/)
[24](https://donasiaja.id/)
[25](https://www.youtube.com/watch?v=drhQrx8FqLE)
[26](https://www.wpcharitable.com)
[27](https://www.charitycharge.com/nonprofit-resources/givewp-donation-plugin/)
[28](https://whydonate.com/blog/wordpress-donation-plugins-button/)
[29](https://everestforms.net/blog/wordpress-donation-plugins/)
[30](https://www.isitwp.com/wordpress-plugins/givewp-review/)
[31](https://donorbox.org/nonprofit-blog/wordpress-donation-plugins)