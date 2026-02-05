<?php
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Printable Receipt Template - Professional Design
 */

$donasai_donation_id = isset($_GET['donasai_receipt']) ? intval($_GET['donasai_receipt']) : 0;
$donasai_donation = null;

// 1. Fetch Donation
if ($donasai_donation_id) {
    if (function_exists('donasai_get_donation')) {
        $donasai_donation = donasai_get_donation($donasai_donation_id);
    } else {
        global $wpdb;
        $donasai_table = $wpdb->prefix . 'donasai_donations';
        $donasai_cache_key = 'donasai_donation_' . $donasai_donation_id;
        $donasai_donation = wp_cache_get($donasai_cache_key, 'donasai_donations');

        if (false === $donasai_donation) {
            $donasai_donation = $wpdb->get_row($wpdb->prepare("SELECT * FROM %i WHERE id = %d", $donasai_table, $donasai_donation_id));
            if ($donasai_donation) {
                wp_cache_set($donasai_cache_key, $donasai_donation, 'donasai_donations', 3600);
            }
        }
    }
}

if (!$donasai_donation) {
    wp_die('Donation not found');
}

// 2. Security Check (Token OR Nonce)
$donasai_is_valid_access = false;

// Check Persistent Token
if (isset($_GET['token'])) {
    $donasai_token_seed = $donasai_donation->id . ($donasai_donation->created_at ?? '') . wp_salt('auth');
    $donasai_expected_token = hash('sha256', $donasai_token_seed);
    $donasai_provided_token = sanitize_text_field(wp_unslash($_GET['token']));

    if (hash_equals($donasai_expected_token, $donasai_provided_token)) {
        $donasai_is_valid_access = true;
    }
}

// Fallback: Check WordPress Nonce
if (!$donasai_is_valid_access) {
    $donasai_nonce = isset($_GET['_wpnonce']) ? sanitize_text_field(wp_unslash($_GET['_wpnonce'])) : '';
    if (wp_verify_nonce($donasai_nonce, 'donasai_receipt_' . $donasai_donation_id)) {
        $donasai_is_valid_access = true;
    }
}

// Allow Admin Access
if (!$donasai_is_valid_access && current_user_can('manage_options')) {
    $donasai_is_valid_access = true;
}

// Allow Donation Owner Access
if (!$donasai_is_valid_access && is_user_logged_in() && isset($donasai_donation->user_id) && $donasai_donation->user_id == get_current_user_id()) {
    $donasai_is_valid_access = true;
}

if (!$donasai_is_valid_access) {
    wp_die('Invalid receipt signature.');
}

// Prepare Data
$donasai_campaign_title = get_the_title($donasai_donation->campaign_id);
$donasai_donor_name = !empty($donasai_donation->donor_name) ? $donasai_donation->donor_name : 'Hamba Allah';
$donasai_donor_email = !empty($donasai_donation->donor_email) ? $donasai_donation->donor_email : '-';
$donasai_donor_phone = !empty($donasai_donation->donor_phone) ? $donasai_donation->donor_phone : '-';
$donasai_donation_date = date_i18n(get_option('date_format'), strtotime($donasai_donation->created_at));
$donasai_amount = number_format($donasai_donation->amount, 0, ',', '.');
$donasai_payment_method = ucfirst($donasai_donation->payment_method);

// Fetch Organization Settings
$donasai_settings = get_option('donasai_settings_organization', []);
$donasai_org_name = !empty($donasai_settings['org_name']) ? $donasai_settings['org_name'] : get_bloginfo('name');
$donasai_org_address = !empty($donasai_settings['org_address']) ? $donasai_settings['org_address'] : get_bloginfo('description');
$donasai_org_email = !empty($donasai_settings['org_email']) ? $donasai_settings['org_email'] : get_option('admin_email');
$donasai_org_phone = !empty($donasai_settings['org_phone']) ? $donasai_settings['org_phone'] : '';
$donasai_org_logo = !empty($donasai_settings['org_logo']) ? $donasai_settings['org_logo'] : '';

// Determine Logo (Prefer settings logo > Theme logo)
if (empty($donasai_org_logo)) {
    $donasai_custom_logo_id = get_theme_mod('custom_logo');
    $donasai_org_logo = $donasai_custom_logo_id ? wp_get_attachment_image_src($donasai_custom_logo_id, 'full')[0] : '';
}

// Pro Feature Check
$donasai_is_pro = function_exists('donasai_is_pro_active') && donasai_is_pro_active();

// Dark Mode Check
$donasai_appearance = get_option('donasai_settings_appearance', []);
$donasai_dark_mode = !empty($donasai_appearance['dark_mode']) && $donasai_is_pro;

// Helper to adjust brightness (copied from css-loader for standalone use)
if (!function_exists('donasai_receipt_adjust_brightness')) {
    function donasai_receipt_adjust_brightness($hex, $steps)
    {
        $steps = max(-255, min(255, $steps));
        $hex = str_replace('#', '', $hex);
        if (strlen($hex) == 3) {
            $hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat(substr($hex, 1, 1), 2) . str_repeat(substr($hex, 2, 1), 2);
        }
        $color_parts = str_split($hex, 2);
        $return = '#';
        foreach ($color_parts as $color) {
            $color   = hexdec($color);
            $color   = max(0, min(255, $color + $steps));
            $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT);
        }
        return $return;
    }
}

$donasai_brand_color = !empty($donasai_appearance['brand_color']) ? $donasai_appearance['brand_color'] : '#0ea5e9'; // Default Sky
// Generate Shades
$donasai_brand_50  = donasai_receipt_adjust_brightness($donasai_brand_color, 180);
$donasai_brand_100 = donasai_receipt_adjust_brightness($donasai_brand_color, 150);
$donasai_brand_500 = $donasai_brand_color;
$donasai_brand_600 = donasai_receipt_adjust_brightness($donasai_brand_color, -10);
$donasai_brand_700 = donasai_receipt_adjust_brightness($donasai_brand_color, -30);
$donasai_brand_800 = donasai_receipt_adjust_brightness($donasai_brand_color, -50);
$donasai_brand_900 = donasai_receipt_adjust_brightness($donasai_brand_color, -80);

// Inject Custom CSS & Tailwind - Moved to functions-frontend.php donasai_enqueue_receipt_assets
?>
<!DOCTYPE html>
<html lang="id" class="<?php echo $donasai_dark_mode ? 'dark' : ''; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php esc_html_e('Kuitansi Donasi', 'donasai'); ?> #<?php echo esc_html($donasai_donation->id); ?></title>

    <?php wp_head(); ?>
</head>

<body class="bg-gray-100 dark:bg-gray-900 min-h-screen text-slate-800 dark:text-gray-200 font-sans flex flex-col items-center transition-colors duration-300">

    <!-- Action Bar (No Print) -->
    <div class="no-print w-full max-w-3xl mx-auto mt-6 mb-4 flex justify-between items-center px-4">
        <a href="<?php echo esc_url(get_home_url()); ?>" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 flex items-center gap-1">
            &larr; Kembali ke Beranda
        </a>
        <button onclick="window.print()" class="bg-brand-600 hover:bg-brand-700 text-white px-6 py-2 rounded-full shadow-lg font-medium transition flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 00-2 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Cetak Bukti Donasi
        </button>
    </div>

    <!-- Receipt Container -->
    <div class="print-container bg-white dark:bg-gray-950 w-full max-w-3xl shadow-2xl rounded-xl overflow-hidden relative mb-10 transition-colors duration-300">

        <!-- Decorative Background Pattern -->
        <div class="header-curve no-print"></div>
        <div class="wave-decoration"></div>

        <!-- Header Content -->
        <div class="relative z-10 px-10 pt-10 pb-6 flex justify-between items-start border-b border-gray-100 dark:border-gray-800">
            <!-- Identity -->
            <div class="flex items-center gap-4">
                <?php if ($donasai_org_logo): ?>
                    <img src="<?php echo esc_url($donasai_org_logo); ?>" alt="<?php echo esc_attr($donasai_org_name); ?>" class="h-16 w-auto object-contain">
                <?php else: ?>
                    <div class="h-16 w-16 bg-brand-100 dark:bg-brand-900/30 rounded-lg flex items-center justify-center text-brand-600 font-bold text-2xl">
                        <?php echo esc_html(substr($donasai_org_name, 0, 1)); ?>
                    </div>
                <?php endif; ?>
                <div>
                    <h2 class="font-bold text-xl text-gray-900 dark:text-gray-100 leading-tight">
                        <?php echo esc_html($donasai_org_name); ?>
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 max-w-xs mt-1 leading-relaxed">
                        <?php echo esc_html($donasai_org_address); ?><br>
                        <?php if ($donasai_org_email)
                            echo esc_html($donasai_org_email); ?>
                        <?php if ($donasai_org_phone)
                            echo ' | ' . esc_html($donasai_org_phone); ?>
                    </p>
                </div>
            </div>

            <!-- Receipt Title -->
            <div class="text-right mt-2">
                <h1 class="text-lg font-bold text-gray-900 dark:text-white tracking-wide uppercase border-b-2 border-brand-500 pb-1 inline-block">
                    Bukti Terima Donasi
                </h1>
                <p class="text-xs text-gray-400 mt-1 uppercase tracking-widest">Official Receipt</p>
            </div>
        </div>

        <!-- Main Content -->
        <div class="p-10 relative z-10">

            <!-- Reference Details -->
            <div class="grid grid-cols-2 gap-x-12 mb-10 text-sm">
                <!-- Col 1 -->
                <div class="space-y-3">
                    <div class="flex">
                        <span class="w-32 text-gray-500 dark:text-gray-400">No. Donasi</span>
                        <span class="font-semibold text-gray-900 dark:text-gray-200">:
                            #<?php echo esc_html($donasai_donation->id); ?></span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-gray-500 dark:text-gray-400">Tanggal</span>
                        <span class="font-semibold text-gray-900 dark:text-gray-200">:
                            <?php echo esc_html($donasai_donation_date); ?></span>
                    </div>
                    <?php if (!empty($donasai_donation->created_at)): ?>
                        <div class="flex">
                            <span class="w-32 text-gray-500 dark:text-gray-400">Jam</span>
                            <span class="font-semibold text-gray-900 dark:text-gray-200">:
                                <?php echo esc_html(date_i18n(get_option('time_format'), strtotime($donasai_donation->created_at))); ?>
                                WIB</span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Col 2 -->
                <div class="space-y-3">
                    <?php if ($donasai_is_pro && !empty($donasai_donation->user_id)): ?>
                        <div class="flex">
                            <span class="w-28 text-gray-500 dark:text-gray-400">ID Donatur</span>
                            <span class="font-medium text-gray-900 dark:text-gray-200">:
                                <?php echo esc_html($donasai_donation->user_id); ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="flex">
                        <span class="w-28 text-gray-500 dark:text-gray-400">Nama</span>
                        <span class="font-medium text-gray-900 dark:text-gray-200">:
                            <?php echo esc_html($donasai_donor_name); ?></span>
                    </div>
                    <?php if ($donasai_donor_email !== '-'): ?>
                        <div class="flex">
                            <span class="w-28 text-gray-500 dark:text-gray-400">Email</span>
                            <span class="font-medium text-gray-900 dark:text-gray-200">:
                                <?php echo esc_html($donasai_donor_email); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Donation breakdown -->
            <div class="mb-10">
                <div class="border-t-2 border-gray-100 dark:border-gray-800"></div>
                <div class="grid grid-cols-12 py-3 border-b border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-900 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    <div class="col-span-8 px-4">Jenis Donasi / Program</div>
                    <div class="col-span-4 px-4 text-right">Jumlah (IDR)</div>
                </div>

                <!-- Item Row -->
                <div class="grid grid-cols-12 py-4 border-b border-gray-100 dark:border-gray-800">
                    <div class="col-span-8 px-4">
                        <p class="font-bold text-gray-800 dark:text-gray-100 text-base">
                            <?php echo esc_html($donasai_campaign_title); ?>
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Via:
                            <?php echo esc_html($donasai_payment_method); ?>
                        </p>
                    </div>
                    <div class="col-span-4 px-4 text-right">
                        <span class="font-bold text-lg text-gray-900 dark:text-white">Rp
                            <?php echo esc_html($donasai_amount); ?></span>
                    </div>
                </div>

                <!-- Total Row -->
                <div class="grid grid-cols-12 py-4">
                    <div class="col-span-8 px-4 text-right pt-2">
                        <span class="text-sm font-bold text-gray-600 dark:text-gray-400 uppercase">Total Donasi</span>
                    </div>
                    <div class="col-span-4 px-4 text-right">
                        <span class="font-bold text-2xl text-brand-600 dark:text-brand-400">Rp
                            <?php echo esc_html($donasai_amount); ?></span>
                    </div>
                </div>
            </div>

            <!-- Footer Section (Pro) -->
            <?php if ($donasai_is_pro): ?>
                <div class="flex justify-between items-end mt-16 pt-10 border-t border-dashed border-gray-200 dark:border-gray-700">
                    <!-- QR Code & Validation -->
                    <div class="text-left">
                        <div class="h-16 w-32 mb-2"></div> <!-- Space for signature -->
                        <p class="text-[10px] text-gray-400 mt-3 max-w-[200px] leading-tight">
                            Dokumen ini sah dan diterbitkan secara otomatis oleh sistem sebagai bukti donasi yang valid.
                        </p>
                    </div>

                    <!-- Signature Section -->
                    <div class="text-center pr-8">
                        <div class="h-16 w-32 mb-2"></div> <!-- Space for signature -->
                        <p class="text-sm font-bold text-gray-800 dark:text-gray-200 border-t border-gray-300 dark:border-gray-600 pt-2 min-w-[180px]">
                            Bagian Keuangan
                        </p>
                        <p class="text-[10px] text-gray-400 mt-1"><?php echo esc_html($donasai_org_name); ?></p>
                    </div>
                </div>
            <?php endif; ?>

        </div>

        <!-- Footer Brand Bar -->
        <div class="bg-gray-50 dark:bg-gray-900 px-10 py-4 flex justify-between items-center border-t border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-2 opacity-60">
                <span class="text-xs text-gray-500 dark:text-gray-400">dibuat dengan donasai</span>
            </div>
            <p class="text-[10px] text-gray-400">
                &copy; <?php echo esc_html(wp_date('Y')); ?> <?php echo esc_html($donasai_org_name); ?>. All rights reserved.
            </p>
        </div>

    </div>

    <!-- Screen-only Confetti Canvas - Logic moved to functions-frontend.php -->
    <canvas id="confetti" class="fixed top-0 left-0 w-full h-full pointer-events-none z-50 no-print"></canvas>

    <?php wp_footer(); ?>
</body>

</html>
<?php exit; ?>