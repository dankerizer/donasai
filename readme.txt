=== Donasai - Platform Donasi & Penggalangan Dana ===
Contributors: hadie-danker
Tags: donation, fundraising, zakat, qurban, midtrans
Requires at least: 6.4
Tested up to: 6.9
Stable tag: 1.0.2
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Donasai is a complete WordPress donation and fundraising platform designed for foundations, mosques, and communities.

== Description ==

**Donasai** is a donation plugin designed specifically for fundraising needs in Indonesia. Suitable for Foundations, Mosques, Zakat Institutions, or communities that want to receive donations professionally and transparently.

This plugin supports various types of donations such as **Zakat Maal**, **Zakat Penghasilan** (Income Zakat), **Qurban**, **Infaq**, **Sodaqoh**, and **Waqf**.

### Key Features

*   **Campaign Management**: Create unlimited donation campaigns with targets, deadlines, and categories.
*   **Zakat Calculator**: Automatically calculate Zakat Maal and Income Zakat directly in the donation form.
*   **Complete Donation Types**: Supports fixed amounts and custom amounts.
*   **Payment System**:
    *   **Manual Bank Transfer**: Simple manual verification with payment proof upload.
*   **Receipts & Notifications**: Automatic email notifications (Pending/Success).
*   **Donor Management**: Dashboard for donors to view their donation history.
*   **Reports**: Simple dashboard overview and donation list management.

### Why Choose Donasai?

1.  **Modern UI/UX**: Mobile-friendly and easy-to-use donation forms.
2.  **Lightweight & Fast**: Built with modern WordPress coding standards (React + PHP).
3.  **Localized**: Terminology and flows adapted to Indonesian donation habits.

### Pro Features (Available in Premium Version)

*   **Recurring Donations**: Accept monthly/yearly subscriptions.
*   **Payment Gateways**: Automatic payments via Midtrans, Xendit, and Tripay (QRIS, VA, E-Wallet).
*   **WhatsApp Notifications**: Real-time updates via WhatsApp.
*   **PDF Receipts**: Downloadable professional receipts.
*   **Fee Coverage**: Option for donors to cover admin fees.
*   **Advanced Reporting**: Detailed charts and CSV exports.

== Installation ==

1.  Upload the `donasai` folder to the `/wp-content/plugins/` directory or install via Plugins > Add New in WordPress.
2.  Activate the plugin through the 'Plugins' menu.
3.  The **Donasai** menu will appear in the admin sidebar.
4.  Go to **Donasai > Settings** to configure Bank Accounts, appearance, and Your Organization Profile.
5.  Create your first campaign in **Donasai > Campaigns**.
6.  Use the shortcode `[donasai_donation_form]` (if manual) or let the plugin create pages automatically.

== Screenshots ==

1. **Campaign Dashboard** - Informative campaign management view.
2. **Donation Form** - Clean and easy-to-use donation form.
3. **Manual Transfer** - Easy proof of payment upload.
4. **Zakat Calculator** - Automatic zakat calculation feature.

== Frequently Asked Questions ==

= Is this plugin free? =
Yes, the basic version (Donasai Free) is free forever and sufficient for accepting donations via Manual Transfer. A PRO version is available for advanced features like Payment Gateways and Recurring Donations.

= How to setup Payment? =
Go to **Donasai > Settings > Payment** to input your Bank Account details for manual transfer instructions.

= Does it support currencies other than Rupiah? =
Currently, Donasai is focused on users in Indonesia with Rupiah (IDR) currency.

== External services ==

This plugin relies on external services to provide certain features. Below are the details of the third-party services used:

1. **Google Fonts**
This plugin connects to Google Fonts (fonts.googleapis.com and fonts.gstatic.com) to load typography for the admin dashboard and donation templates. It is needed to provide a professional and consistent UI. It sends standard request headers (like IP address and User Agent) when font files are requested by the browser. 
Service provided by Google LLC: [terms of service](https://developers.google.com/terms), [privacy policy](https://policies.google.com/privacy).

2. **QR Server API**
This plugin connects to api.qrserver.com to generate QR codes on donation receipts, allowing for easy validation of donation campaigns. It sends the campaign URL as a query parameter to generate the QR code graphic. No personal donor data is sent to this service.
Service provided by "goQR.me": [privacy policy](https://goqr.me/de/rechtliches/datenschutz-goqrme.html).

3. **Tailwind CSS (CDN)**
This plugin uses the Tailwind CSS CDN (cdn.tailwindcss.com) to style internal receipt templates. It is needed to render professional-looking printable receipts. It sends standard request headers when the CSS library is loaded in the donor's browser.
Service provided by Tailwind Labs Inc.: [Official Documentation](https://tailwindcss.com/docs/installation/play-cdn).

== Source Code ==

This plugin includes minified JavaScript and CSS files in the `build/` directory for the admin application.
The full source code for these assets is available in the `admin-app/` directory within this plugin, and also publicly at:
[https://github.com/dankerizer/donasai](https://github.com/dankerizer/donasai)

Developers can inspect, modify, and rebuild the assets using the provided `package.json` and build scripts.

== Changelog ==

= 1.0.2 =
*   Refactor prefixes for wp.org compliance.
*   Hardened security (nonces, sanitization).
*   Corrected asset enqueuing.
*   Improved REST API permissions.

= 1.0.0 =
*   Initial release.

