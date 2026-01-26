# Response to Review #1

**To:** plugins@wordpress.org
**Subject:** Re: [WordPress Plugin Directory] Review in Progress: Donasai - Platform Donasi & Penggalangan Dana
**Slug:** donasai

Hello WordPress Plugin Review Team,

Thank you for the detailed feedback. We have addressed all the issues mentioned in the review. Here is a summary of the changes we have made:

## 1. Ownership & Identity
I understand the concern regarding the email address `hadie87@gmail.com` not matching the plugin's domain `donasai.com`, and the reported timeout on `donasai.com`.

> **Action Taken:** 
> 1. I have updated the `Contributors` field in `readme.txt` to strictly match my WordPress.org username (`hadie-danker`).
> 2. I have changed the **Plugin URI** in `donasai.php` to `https://wordpress.org/plugins/donasai` for now, as the domain `donasai.com` is currently maintenance/not publicly accessible (hence the timeout).
> 3. I confirm I am the sole developer/owner of this plugin.

## 2. Trialware & Locked Features
We have removed the features that were previously restricted or appeared to be "upsells" to ensure full compliance with Guideline 5.
> **Action Taken:**
> - Removed **Midtrans Payment Gateway** from this plugin entirely. It is no longer a "pro-only" feature in the settings; it's simply gone to keep this version lightweight and focused on Manual Transfers.
> - Removed **Tracking Pixels** (Facebook/TikTok) to avoid complexity with external scripts.
> - The plugin is now fully functional as a basic donation platform with Manual Bank Transfer.

## 3. Data Sanitization & Security
We have conducted a thorough audit and fixed all sanitization and security issues.
> **Action Taken:**
> - **Input Sanitization**: Ensured all `$_POST` and `$_GET` data is sanitized using `sanitize_text_field()`, `sanitize_textarea_field()`, `absint()`, etc. across all files (`metabox.php`, `donations-controller.php`, etc.).
> - **SQL Escaping**: We replaced `phpcs:ignore` suppressions with actual `esc_sql()` wrappers for dynamic table names in `$wpdb->prepare()` queries. Table names are constructed using `$wpdb->prefix` and now properly escaped.
> - **Nonces**: Added nonce checks for all form submissions and actions (e.g. `donasai_receipt_` nonce for viewing receipts).

## 4. Enqueued Resources
We have fixed the instances where scripts and styles were output directly.
> **Action Taken:**
> - Removed inline `<link>` and `<script>` tags from `receipt.php`.
> - Created `donasai_enqueue_receipt_assets()` function attached to `wp_enqueue_scripts` hook to properly load Tailwind CSS (CDN) and Google Fonts on the receipt page.
> - Ensured all admin scripts verify `DONASAI_DEV_MODE` before loading development assets.

## 5. External Services
We have documented all external services in `readme.txt` as requested.
> **Action Taken:**
> - Added "External Services" section to `readme.txt`.
> - Documented usage of **Google Fonts** (for typography) and **Tailwind CSS CDN** (for receipt styling).

## 6. REST API Permissions
Regarding the `permission_callback` warning:
> **Clarification:**
> - `POST /campaigns/{id}/donate`: Intentional `__return_true` because donation submission is public.
> - `GET /fundraisers`: Intentional `__return_true` because it serves public leaderboard data when `campaign_id` is present. Authorization for private data (user stats) is handled inside the callback returning 403 if unauthorized.

## 7. Code Quality & Prefixing
> **Action Taken:**
> - Verified all functions and classes use the `donasai_` prefix or `Donasai` namespace to avoid collisions.
> - Removed the `includes/gateways/stripe.php` file mentioned in the potential library conflict warning (we are not using Stripe in this version).


## 8. Readme Accuracy
We reviewed our `readme.txt` and realized some features (like Midtrans and Fundraising) were listed prematurely.
> **Action Taken:**
> - Updated `readme.txt` to accurately reflect the Free version capabilities (Manual Transfer, Zakat Calculator, etc.).
> - Added a "Pro Features" section to clearly distinguish between Free and Pro offerings.
> - Removed "Midtrans" setup guides from FAQ as it is no longer relevant for this Free version.

We believe the plugin is now fully compliant. We have uploaded the updated `donasai.zip`.

Thank you for your time and guidance!

Best regards,
Hadie Danker
