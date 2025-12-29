Berikut **feature list wp-donasi** yang dipisah jelas antara Free dan Pro, sinkron dengan landscape plugin donasi modern seperti GiveWP/Charitable.[1][2][3]

***

## wp-donasi Free (untuk wp.org)

**Campaign & tampilan**

- Unlimited campaign donasi dengan custom post type khusus.[4][1]
- Landing page per campaign dengan: judul, deskripsi, gambar, target, progress bar, dan status (draft/publish/closed).[1][4]
- Kategori campaign dasar (zakat, infak, wakaf, qurban, umum).[5][4]
- Shortcode / block untuk menampilkan list campaign dan single campaign.[6][1]

**Donation form**

- Form donasi otomatis per campaign (tidak perlu create form manual).[6][1]
- Preset amount (beberapa nominal pilihan) + custom amount.[1][6]
- Field donor standar: nama, email, phone, catatan.[7][1]
- Opsi donasi anonim (sembunyikan nama di list donatur).[7][1]
- Checkbox Terms & Conditions dengan link yang bisa diatur.[7]

**Payment**

- Offline transfer / manual payment dengan instruksi transfer.[7]
- 1 payment gateway global dasar (misal Stripe atau PayPal) untuk pembayaran online.[1][7]
- Status donasi: pending, complete, failed; update via callback/webhook gateway & manual.[1][7]

**Donations & donor**

- Tabel donasi di admin: list donasi dengan filter dasar (tanggal, campaign, status).[7][1]
- Data donor tersimpan dan dikelompokkan per email (profil donor sederhana).[1][7]
- Export donasi ke CSV sederhana dari admin.[7][1]

**Dashboard admin (React)**

- Dashboard overview: total donasi, jumlah donor, jumlah campaign aktif/tutup.[8][1]
- Grafik donasi sederhana per periode (harian/mingguan).[8][1]
- Tabel “Recent Donations” (10 donasi terbaru).[8][1]

**Donor dashboard (frontend)**

- Halaman “My Donations” untuk user yang login: list donasi sendiri dengan amount & status.[9][1]

**Frontend & dev**

- Template HTML + Tailwind default untuk form dan campaign (mobile-friendly).[10][9]
- Template override via theme (copy file template ke child theme).[9][10]
- REST API dasar: endpoint list campaign & donation untuk kebutuhan headless/integrasi ringan.[11][12]

**Lain-lain**

- Email konfirmasi donasi sederhana (ke donor dan admin).[7]
- Kompatibel dengan WordPress.org guidelines & update via wp.org.[13][1]

***

## wp-donasi Pro (Add-on Berbayar)

**Campaign & tampilan lanjutan**

- Opsi field campaign tambahan (lokasi, jenis program, tag khusus).[2][9]
- Beberapa layout/skin landing page campaign (hero variasi, section testimoni, dsb.).[2][9]
- Shortcode/block dengan parameter advanced (filter per kategori, urutan, layout grid/list/slider).[14][9]

**Donation form lanjutan**

- Custom fields builder (text, number, select, checkbox, radio) per form.[9][7]
- Multi-form per campaign (untuk segmentasi donor atau paket donasi berbeda).[14][9]
- Fee coverage (donor bisa centang “tambah biaya admin/payment fee”).[9][7]

**Payment Pro**

- Integrasi beberapa payment gateway global (Stripe, PayPal, dsb.) dengan opsi lanjutan.[2][9]
- Integrasi gateway lokal Indonesia (Midtrans, Xendit, iPaymu, dll).[15][4]
- Recurring / subscription donation (bulanan, tahunan) dengan pengelolaan di dashboard donor.[2][9]

**Donations, donor & reporting Pro**

- Filter donasi advanced: per payment method, currency, range nominal, recurring vs one-time.[14][1]
- Export laporan lanjutan: per campaign, per donor, per periode, recurring summary.[14][1]
- Mini donor CRM: lifetime value, campaign favorit, segmentasi donor basic.[2][14]

**Donor experience Pro**

- Donor bisa download receipt/invoice PDF dari halaman “My Donations”.[16][9]
- Manage recurring donation (pause, resume, cancel) dari dashboard donor.[9]

**Dashboard & automasi Pro**

- Widget & grafik tambahan di admin dashboard (per channel, per campaign, recurring, funnel).[14][2]
- Template email konfirmasi & notifikasi bisa dikustom (multi bahasa, per campaign).[9][7]
- Reminder otomatis untuk donasi pending (email/WA jika terintegrasi).[9][7]
- Webhook & integrasi ke tools lain (CRM, Zapier/Make-style endpoint, dsb.).[14][9]

**Dev & extensibility**

- Endpoint REST API tambahan untuk analytics dan integrasi eksternal.[14][9]
- Hook/filters ekstra khusus Pro (bypass limit, integrasi sistem internal lembaga).[17]
- License key & auto-update dari server kamu sendiri, dengan kanal beta/stable.[18][19]

Kalau mau, ini bisa langsung dipecah jadi:

- section “Features” untuk landing page Free,  
- section “Why go Pro?” berisi subset feature Pro + copy marketing singkat yang bisa dibantu disusun berikutnya.

[1](https://wordpress.org/plugins/give/)
[2](https://givewp.com)
[3](https://www.wpcharitable.com)
[4](https://kinsta.com/blog/wordpress-donation-plugins/)
[5](https://wp101.com/best-wordpress-donation-plugins/)
[6](https://wordpress.com/plugins/give)
[7](https://www.interserver.net/tips/kb/allow-donate-givewp-donation-plugin/)
[8](https://easywebdesigntutorials.com/give-donation-wordpress-plugin-walk-through/)
[9](https://www.wc-donation.com/documentation/getting-started/wordpress-donation-plugin/)
[10](https://underscoretw.com/docs/wordpress-tailwind/)
[11](https://www.brontobytes.com/blog/building-powerful-wordpress-plugins-with-react-a-developers-guide/)
[12](https://belovdigital.agency/blog/wordpress-development-with-react-creating-headless-themes/)
[13](https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/)
[14](https://givewp.com/features/)
[15](https://paymentsplugin.com/blog/best-wordpress-donation-plugins/)
[16](https://givewp.com/documentation/add-ons/pdf-receipts/)
[17](https://kinsta.com/blog/pro-free-versions-wordpress-plugin/)
[18](https://freemius.com/blog/freemium-business-model-wordpress/)
[19](https://freemius.com/blog/submit-plugin-wordpress-repository/)
[20](https://donasiaja.id/)