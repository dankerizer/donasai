=== Donasai - Platform Donasi & Penggalangan Dana ===
Contributors: hadie danker
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
*   **Complete Donation Types**: Supports fixed amounts, custom amounts, and package options (e.g., Food Packages).
*   **Payment System**:
    *   **Manual Bank Transfer**: Manual verification with payment proof upload.
    *   **Midtrans Payment Gateway**: Automatic integration for Virtual Accounts, QRIS, E-Wallets (GoPay, OVO, ShopeePay), and Credit Cards.
*   **Fundraising**: Feature for volunteers/fundraisers to share campaign links and track their donation achievements.
*   **Receipts & Notifications**: Automatic email notifications and professional donation receipts.
*   **Donor Management**: Dashboard for donors to view their donation history.
*   **Reports**: Daily, monthly, and per-campaign donation recaps.

### Why Choose Donasai?

1.  **Modern UI/UX**: Mobile-friendly and easy-to-use donation forms.
2.  **Lightweight & Fast**: Built with modern WordPress coding standards (React + PHP).
3.  **Localized**: Terminology and flows adapted to Indonesian donation habits.

== Installation ==

1.  Upload the `donasai` folder to the `/wp-content/plugins/` directory or install via Plugins > Add New in WordPress.
2.  Activate the plugin through the 'Plugins' menu.
3.  The **Donasai** menu will appear in the admin sidebar.
4.  Go to **Donasai > Settings** to configure Payment Gateways (Midtrans) and Bank Accounts.
5.  Create your first campaign in **Donasai > Campaigns**.
6.  Use the shortcode `[wpd_donation_form]` (if manual) or let the plugin create pages automatically.

== Screenshots ==

1. **Campaign Dashboard** - Informative campaign management view.
2. **Donation Form** - Clean and easy-to-use donation form.
3. **Payment Options** - Supports Bank Transfer and E-Wallets via Midtrans.
4. **Zakat Calculator** - Automatic zakat calculation feature.

== Frequently Asked Questions ==

= Is this plugin free? =
Yes, the basic version (Donasai Free) is free forever and sufficient for accepting donations. A PRO version is available for advanced features like Recurring Donations and WhatsApp Notifications.

= How to set up Midtrans? =
You need to register an account at [Midtrans](https://midtrans.com), then get the **Server Key** and **Client Key**. Enter these keys in Donasai > Settings > Payment.

= Does it support currencies other than Rupiah? =
Currently, Donasai is focused on users in Indonesia with Rupiah (IDR) currency.

== Changelog ==

= 1.0.0 =
*   Initial release.
*   Fitur dasar: Kampanye, Donasi, Zakat, Midtrans, Transfer Manual.

