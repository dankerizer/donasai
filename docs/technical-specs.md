
## 1. Plugin Metadata & Requirements

```php
// donasai.php header
/*
Plugin Name: donasai - Donation & Fundraising
Plugin URI: https://donasi.xyz/wp
Description: Modern WordPress donation plugin with campaign management, payment gateways, and React dashboard.
Version: 1.0.0
Author: Hadie Danker
Requires at least: 6.4
Tested up to: 6.6
Requires PHP: 7.4
License: GPL v2 or later
Text Domain: donasai
Domain Path: /languages
*/

Constants:
- WPD_VERSION = '1.0.0'
- WPD_PLUGIN_URL
- WPD_PLUGIN_PATH
- WPD_TABLE_PREFIX = 'wpd_'
```

**Requirements:**
- WordPress 6.4+[4][5]
- PHP 7.4+ 
- MySQL 5.7+
- `wp_remote_post()` enabled

***

## 2. Folder Structure

```
donasai/
├── donasai.php                 # Main plugin file
├── readme.txt                    # WP.org readme
├── uninstall.php                 # Cleanup on uninstall
├── includes/
│   ├── bootstrap.php             # Init & hooks
│   ├── cpt.php                   # Custom Post Types
│   ├── db.php                    # Database schema
│   ├── rest-api.php              # Custom REST endpoints
│   ├── gateways/                 # Payment gateways
│   │   ├── interface.php
│   │   ├── offline.php
│   │   └── stripe.php           # Free gateway example
│   └── services/                 # Business logic
│       ├── campaign.php
│       ├── donation.php
│       └── email.php
├── admin-app/                    # React + TS source
│   ├── src/
│   │   ├── components/
│   │   ├── pages/
│   │   ├── hooks/
│   │   └── types/
│   ├── package.json
│   ├── vite.config.ts
│   ├── tailwind.config.js
│   └── tsconfig.json
├── build/                        # Compiled admin assets
│   ├── admin.js
│   └── admin.css
├── frontend/                     # Public templates + CSS
│   ├── templates/
│   │   ├── campaign-list.php
│   │   ├── campaign-single.php
│   │   └── donation-form.php
│   └── assets/
│       └── frontend.css         # Tailwind compiled
├── languages/                    # .pot files
└── pro/                         # Pro add-on (separate plugin)
```

***

## 3. Database Schema

### 3.1 Custom Tables (via dbDelta)

```sql
-- wpd_donations
CREATE TABLE {$wpdb->prefix}wpd_donations (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    campaign_id bigint(20) NOT NULL,
    user_id bigint(20) NULL,
    name varchar(100) NOT NULL,
    email varchar(100) NOT NULL,
    phone varchar(20) NULL,
    amount decimal(12,2) NOT NULL,
    currency varchar(3) DEFAULT 'IDR',
    payment_method varchar(50) NOT NULL,  -- 'offline', 'stripe', etc
    status enum('pending','processing','complete','failed','refunded','expired') DEFAULT 'pending',
    gateway varchar(50) NULL,
    gateway_txn_id varchar(100) NULL,
    metadata longtext NULL,
    note text NULL,
    is_anonymous tinyint(1) DEFAULT 0,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    fundraiser_id bigint(20) DEFAULT 0,
    PRIMARY KEY (id),
    KEY campaign_id (campaign_id),
	KEY user_id (user_id),
    KEY status (status),
    KEY created_at (created_at)
) {$wpdb->get_charset_collate()};

-- wpd_campaign_meta (optional for Pro)
CREATE TABLE {$wpdb->prefix}wpd_campaign_meta (
    campaign_id bigint(20) NOT NULL,
    meta_key varchar(50) NOT NULL,
    meta_value longtext,
    PRIMARY KEY (campaign_id, meta_key)
);

-- wpd_fundraisers (New: Affiliate/Fundraiser System)
CREATE TABLE {$wpdb->prefix}wpd_fundraisers (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    user_id bigint(20) NOT NULL,
    campaign_id bigint(20) NOT NULL,
    referral_code varchar(50) NOT NULL, -- e.g. "ahmad123"
    total_donations decimal(12,2) DEFAULT 0,
    donation_count int(11) DEFAULT 0,
    is_active tinyint(1) DEFAULT 1,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY code (referral_code),
    KEY user_campaign (user_id, campaign_id)
);

-- wpd_referral_logs (New: Tracking clicks/views)
CREATE TABLE {$wpdb->prefix}wpd_referral_logs (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    fundraiser_id bigint(20) NOT NULL,
    campaign_id bigint(20) NOT NULL,
    ip_address varchar(100) NULL,
    user_agent varchar(255) NULL,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY fundraiser_id (fundraiser_id)
);
```

### 3.2 Custom Post Type: `wpd_campaign`

```php
// Meta fields (postmeta)
- _wpd_target_amount (decimal)
- _wpd_category (zakat,infak,wakaf,qurban,umum)
- _wpd_deadline (date)
- _wpd_status (active,closed)
- _wpd_collected_amount (decimal, auto-updated)
- _featured (yes/no)
- _wpd_type (donation, zakat, qurban, wakaf)
- _wpd_packages (json: [{name, price}, ...]) -- for Qurban
- _wpd_zakat_settings (json)
- _wpd_pixel_ids (json: {fb, tiktok, gtm})
- _wpd_whatsapp_settings (json: {number, message})
```

***

## 4. REST API Endpoints (`wpd/v1`)

```php
/register_rest_route('wpd/v1', '/campaigns', [
    'methods' => 'GET',
    'callback' => 'wpd_get_campaigns',
    'permission_callback' => '__return_true'  // Public read
]);

/campaigns/{id}    GET  - Single campaign
/donations         GET  - Donor donations (auth required)
/stats             GET  - Admin stats (auth + capability)
/fundraisers       GET, POST - Fundraiser registration & stats
/track             POST - Log referral visits
```

**Authentication:** WP REST nonce + user capability check.

***

## 5. Admin App (React + TypeScript + shadcn)

### 5.1 package.json

```json
{
  "name": "donasai-admin",
  "scripts": {
    "dev": "vite",
    "build": "tsc && vite build",
    "preview": "vite preview"
  },
  "dependencies": {
    "react": "^18.2.0",
    "react-dom": "^18.2.0",
    "@tanstack/react-query": "^5.0.0",
    "lucide-react": "^0.400.0",
    "recharts": "^2.12.0",
    "class-variance-authority": "^0.7.0",
    "clsx": "^2.1.0",
    "tailwind-merge": "^2.3.0"
  },
  "devDependencies": {
    "@types/react": "^18.2.0",
    "@types/react-dom": "^18.2.0",
    "@vitejs/plugin-react": "^4.2.0",
    "autoprefixer": "^10.4.19",
    "postcss": "^8.4.38",
    "tailwindcss": "^3.4.0",
    "typescript": "^5.3.0",
    "vite": "^5.2.0"
  }
}
```

### 5.2 vite.config.ts (HMR)

```ts
import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [react()],
  server: {
    port: 3001,
    host: 'localhost',
    hmr: {
      host: 'localhost'
    }
  },
  build: {
    outDir: '../../build',
    rollupOptions: {
      input: './src/admin-root.tsx'
    }
  }
})
```

### 5.3 Enqueue (PHP)

```php
// Development mode
if (WP_DEBUG && defined('WPD_DEV_MODE')) {
    wp_enqueue_script('donasai-admin', 'http://localhost:3001/admin.js');
} else {
    // Production
    wp_enqueue_script('donasai-admin', WPD_PLUGIN_URL . 'build/admin.js', ['wp-element']);
    wp_enqueue_style('donasai-admin', WPD_PLUGIN_URL . 'build/admin.css');
}
```

***

## 6. Frontend Templates (Tailwind)

### 6.1 donation-form.php

```php
<div class="wpd-form-container max-w-md mx-auto p-6 bg-white rounded-lg shadow-lg">
  <form method="POST" class="space-y-4">
    <?php wp_nonce_field('wpd_donate_nonce'); ?>
    
    <div>
      <label class="block text-sm font-medium mb-2">Pilih Nominal</label>
      <div class="grid grid-cols-2 gap-3 mb-4">
        <button type="button" class="preset-amount p-3 border rounded-lg hover:bg-blue-50" data-amount="50000">Rp 50.000</button>
        <!-- ... more presets -->
      </div>
      <input type="number" name="donation_amount" class="w-full p-3 border rounded-lg" placeholder="Nominal lain">
    </div>
    
    <div>
      <input type="text" name="donor_name" required class="w-full p-3 border rounded-lg">
    </div>
    
    <!-- ... more fields -->
    
    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold">
      Donasi Sekarang
    </button>
  </form>
</div>
```

### 6.2 Tailwind Config (frontend)

```js
// frontend/tailwind.config.js
module.exports = {
  content: ['./templates/**/*.php'],
  theme: { extend: {} },
  plugins: [],
}
```

***

## 7. Payment Gateway Architecture

### 7.1 Interface

```php
// includes/gateways/interface.php
interface WPD_Gateway {
    public function get_id(): string;
    public function get_name(): string;
    public function is_active(): bool;
    public function process_payment(array $donation_data): array;
    public function handle_webhook(WP_REST_Request $request): bool;
    public function get_payment_instructions(int $donation_id): string;
}
```

### 7.2 Registry

```php
// Free gateways
add_filter('wpd_gateways', function($gateways) {
    $gateways[] = new WPD_Gateway_Offline();
    $gateways[] = new WPD_Gateway_Stripe();  // Free global gateway
    return $gateways;
});
```

***

## 8. Hooks & Filters (Extensible)

```php
// Campaigns
do_action('wpd_campaign_created', $campaign_id, $campaign_data);
apply_filters('wpd_campaign_form_html', $html, $campaign_id);

// Donations
do_action('wpd_donation_created', $donation_id, $donation_data);
do_action('wpd_donation_status_updated', $donation_id, $old_status, $new_status);

// Templates
apply_filters('wpd_template_path', $template_path, $template_name);

// Gateways (Pro extensible)
apply_filters('wpd_register_gateways', []);
```

***

## 9. Activation / Deactivation

```php
// Activation
register_activation_hook(__FILE__, 'wpd_activate');
function wpd_activate() {
    wpd_create_tables();
    flush_rewrite_rules();
}

// Deactivation  
register_deactivation_hook(__FILE__, 'wpd_deactivate');
function wpd_deactivate() {
    flush_rewrite_rules();
}
```

***

## 10. Admin Pages Structure

```
WP Admin → Donasi (main menu)
├── Dashboard (React SPA)          /admin.php?page=wpd-dashboard
├── Campaigns (WP native + React)  /edit.php?post_type=wpd_campaign
├── Donations (React SPA)          /admin.php?page=wpd-donations  
└── Settings (React SPA)           /admin.php?page=wpd-settings
```

***

## 11. Shortcodes

| Shortcode | Description | Attributes |
|-----------|-------------|------------|
| `[wpd_campaign id="123"]` | Embeds a donation form and progress bar for a specific campaign. | `id` (required): Campaign ID |
| `[wpd_my_donations]` | Displays a dashboard for the logged-in user showing their donation history. | None |
| `[wpd_fundraiser_stats]` | Displays the fundraiser dashboard (referral links, stats) for the logged-in user. | None |

***


[1](https://www.brontobytes.com/blog/building-powerful-wordpress-plugins-with-react-a-developers-guide/)
[2](https://developer.wordpress.org/news/2024/03/how-to-use-wordpress-react-components-for-plugin-pages/)
[3](https://underscoretw.com/docs/wordpress-tailwind/)
[4](https://wordpress.org/plugins/give/)
[5](https://givewp.com/documentation/core/requirements/)
[6](https://donasiaja.id/)