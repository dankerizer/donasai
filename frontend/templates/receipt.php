<?php
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Printable Receipt Template - Professional Design
 */

$donation_id = isset($_GET['wpd_receipt']) ? intval($_GET['wpd_receipt']) : 0;
$donation = null;

// 1. Fetch Donation
if ($donation_id) {
    if (function_exists('wpd_get_donation')) {
        $donation = wpd_get_donation($donation_id);
    } else {
        global $wpdb;
        $table = esc_sql($wpdb->prefix . 'wpd_donations');
        $donation = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $donation_id)); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
    }
}

if (!$donation) {
    wp_die('Donation not found');
}

// 2. Security Check (Token OR Nonce)
$is_valid_access = false;

// Check Persistent Token
if (isset($_GET['token'])) {
    $token_seed = $donation->id . ($donation->created_at ?? '') . wp_salt('auth');
    $expected_token = hash('sha256', $token_seed);
    $provided_token = sanitize_text_field(wp_unslash($_GET['token']));

    if (hash_equals($expected_token, $provided_token)) {
        $is_valid_access = true;
    }
}

// Fallback: Check WordPress Nonce
if (!$is_valid_access) {
    $nonce = isset($_GET['_wpnonce']) ? sanitize_text_field(wp_unslash($_GET['_wpnonce'])) : '';
    if (wp_verify_nonce($nonce, 'wpd_receipt_' . $donation_id)) {
        $is_valid_access = true;
    }
}

// Allow Admin Access
if (!$is_valid_access && current_user_can('manage_options')) {
    $is_valid_access = true;
}

// Allow Donation Owner Access
if (!$is_valid_access && is_user_logged_in() && isset($donation->user_id) && $donation->user_id == get_current_user_id()) {
    $is_valid_access = true;
}

if (!$is_valid_access) {
    wp_die('Invalid receipt signature.');
}

// Prepare Data
$campaign_title = get_the_title($donation->campaign_id);
$donor_name = !empty($donation->donor_name) ? $donation->donor_name : 'Hamba Allah';
$donor_email = !empty($donation->donor_email) ? $donation->donor_email : '-';
$donor_phone = !empty($donation->donor_phone) ? $donation->donor_phone : '-';
$donation_date = date_i18n(get_option('date_format'), strtotime($donation->created_at));
$amount = number_format($donation->amount, 0, ',', '.');
$payment_method = ucfirst($donation->payment_method);

// Fetch Organization Settings
$settings = get_option('wpd_settings_organization', []);
$org_name = !empty($settings['org_name']) ? $settings['org_name'] : get_bloginfo('name');
$org_address = !empty($settings['org_address']) ? $settings['org_address'] : get_bloginfo('description');
$org_email = !empty($settings['org_email']) ? $settings['org_email'] : get_option('admin_email');
$org_phone = !empty($settings['org_phone']) ? $settings['org_phone'] : '';
$org_logo = !empty($settings['org_logo']) ? $settings['org_logo'] : '';

// Determine Logo (Prefer settings logo > Theme logo)
if (empty($org_logo)) {
    $custom_logo_id = get_theme_mod('custom_logo');
    $org_logo = $custom_logo_id ? wp_get_attachment_image_src($custom_logo_id, 'full')[0] : '';
}

// Pro Feature Check
$is_pro = function_exists('wpd_is_pro_active') && wpd_is_pro_active();

// Dark Mode Check
$appearance = get_option('wpd_settings_appearance', []);
$dark_mode = !empty($appearance['dark_mode']) && $is_pro;

// Helper to adjust brightness (copied from css-loader for standalone use)
if (!function_exists('wpd_receipt_adjust_brightness')) {
    function wpd_receipt_adjust_brightness($hex, $steps) {
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

$brand_color = !empty($appearance['brand_color']) ? $appearance['brand_color'] : '#0ea5e9'; // Default Sky
// Generate Shades
$brand_50  = wpd_receipt_adjust_brightness($brand_color, 180);
$brand_100 = wpd_receipt_adjust_brightness($brand_color, 150);
$brand_500 = $brand_color;
$brand_600 = wpd_receipt_adjust_brightness($brand_color, -10);
$brand_700 = wpd_receipt_adjust_brightness($brand_color, -30);
$brand_800 = wpd_receipt_adjust_brightness($brand_color, -50);
$brand_900 = wpd_receipt_adjust_brightness($brand_color, -80);
?>
<!DOCTYPE html>
<html lang="id" class="<?php echo $dark_mode ? 'dark' : ''; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php esc_html_e('Kuitansi Donasi', 'donasai'); ?> #<?php echo esc_html($donation->id); ?></title>
    
    <?php wp_head(); ?>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                    },
                    colors: {
                        brand: {
                            50: '<?php echo esc_attr($brand_50); ?>',
                            100: '<?php echo esc_attr($brand_100); ?>',
                            500: '<?php echo esc_attr($brand_500); ?>', 
                            600: '<?php echo esc_attr($brand_600); ?>', 
                            700: '<?php echo esc_attr($brand_700); ?>', 
                            800: '<?php echo esc_attr($brand_800); ?>', 
                            900: '<?php echo esc_attr($brand_900); ?>', 
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Print Overrides */
        @media print {
            @page {
                margin: 0;
                size: auto;
            }

            body {
                background: white;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print {
                display: none !important;
            }

            .print-container {
                box-shadow: none !important;
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                /* Minimized padding */
                border: none !important;
            }

            .wave-decoration,
            .header-curve {
                /* Keep decorations but ensure they don't break print layout */
                z-index: -1;
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            canvas {
                display: none !important;
            }

            /* Force light mode in print */
            html.dark body {
                background: white !important;
                color: black !important;
            }

            html.dark .print-container {
                background: white !important;
                color: black !important;
            }

            html.dark .text-gray-100 {
                color: #f3f4f6 !important;
            }

            /* Reset */
        }

        /* Decorative Curve CSS */
        .header-curve {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 120px;
            background: linear-gradient(135deg, <?php echo esc_attr($brand_50); ?> 0%, #ffffff 100%);
            border-bottom-right-radius: 50% 20px;
            border-bottom-left-radius: 50% 20px;
            z-index: 0;
        }

        .dark .header-curve {
            background: linear-gradient(135deg, <?php echo esc_attr($brand_900); ?> 0%, #0f172a 100%);
        }

        .wave-decoration {
            position: absolute;
            top: -50px;
            left: -50px;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, <?php echo esc_attr($brand_100); ?> 0%, rgba(255, 255, 255, 0) 70%);
            border-radius: 50%;
            z-index: 0;
        }

        .dark .wave-decoration {
            background: radial-gradient(circle, <?php echo esc_attr($brand_900); ?> 0%, rgba(15, 23, 42, 0) 70%);
        }
    </style>
</head>

<body
    class="bg-gray-100 dark:bg-gray-900 min-h-screen text-slate-800 dark:text-gray-200 font-sans flex flex-col items-center transition-colors duration-300">

    <!-- Action Bar (No Print) -->
    <div class="no-print w-full max-w-3xl mx-auto mt-6 mb-4 flex justify-between items-center px-4">
        <a href="<?php echo esc_url(get_home_url()); ?>"
            class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 flex items-center gap-1">
            &larr; Kembali ke Beranda
        </a>
        <button onclick="window.print()"
            class="bg-brand-600 hover:bg-brand-700 text-white px-6 py-2 rounded-full shadow-lg font-medium transition flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Cetak Bukti Donasi
        </button>
    </div>

    <!-- Receipt Container -->
    <div
        class="print-container bg-white dark:bg-gray-950 w-full max-w-3xl shadow-2xl rounded-xl overflow-hidden relative mb-10 transition-colors duration-300">

        <!-- Decorative Background Pattern -->
        <div class="header-curve no-print"></div>
        <div class="wave-decoration"></div>

        <!-- Header Content -->
        <div
            class="relative z-10 px-10 pt-10 pb-6 flex justify-between items-start border-b border-gray-100 dark:border-gray-800">
            <!-- Identity -->
            <div class="flex items-center gap-4">
                <?php if ($org_logo): ?>
                    <img src="<?php echo esc_url($org_logo); ?>" alt="<?php echo esc_attr($org_name); ?>"
                        class="h-16 w-auto object-contain">
                <?php else: ?>
                    <div
                        class="h-16 w-16 bg-brand-100 dark:bg-brand-900/30 rounded-lg flex items-center justify-center text-brand-600 font-bold text-2xl">
                        <?php echo esc_html(substr($org_name, 0, 1)); ?>
                    </div>
                <?php endif; ?>
                <div>
                    <h2 class="font-bold text-xl text-gray-900 dark:text-gray-100 leading-tight">
                        <?php echo esc_html($org_name); ?>
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 max-w-xs mt-1 leading-relaxed">
                        <?php echo esc_html($org_address); ?><br>
                        <?php if ($org_email)
                            echo esc_html($org_email); ?>
                        <?php if ($org_phone)
                            echo ' | ' . esc_html($org_phone); ?>
                    </p>
                </div>
            </div>

            <!-- Receipt Title -->
            <div class="text-right mt-2">
                <h1
                    class="text-lg font-bold text-gray-900 dark:text-white tracking-wide uppercase border-b-2 border-brand-500 pb-1 inline-block">
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
                            #<?php echo esc_html($donation->id); ?></span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-gray-500 dark:text-gray-400">Tanggal</span>
                        <span class="font-semibold text-gray-900 dark:text-gray-200">:
                            <?php echo esc_html($donation_date); ?></span>
                    </div>
                    <?php if (!empty($donation->created_at)): ?>
                        <div class="flex">
                            <span class="w-32 text-gray-500 dark:text-gray-400">Jam</span>
                            <span class="font-semibold text-gray-900 dark:text-gray-200">:
                                <?php echo esc_html(date_i18n(get_option('time_format'), strtotime($donation->created_at))); ?>
                                WIB</span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Col 2 -->
                <div class="space-y-3">
                    <?php if ($is_pro && !empty($donation->user_id)): ?>
                        <div class="flex">
                            <span class="w-28 text-gray-500 dark:text-gray-400">ID Donatur</span>
                            <span class="font-medium text-gray-900 dark:text-gray-200">:
                                <?php echo esc_html($donation->user_id); ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="flex">
                        <span class="w-28 text-gray-500 dark:text-gray-400">Nama</span>
                        <span class="font-medium text-gray-900 dark:text-gray-200">:
                            <?php echo esc_html($donor_name); ?></span>
                    </div>
                    <?php if ($donor_email !== '-'): ?>
                        <div class="flex">
                            <span class="w-28 text-gray-500 dark:text-gray-400">Email</span>
                            <span class="font-medium text-gray-900 dark:text-gray-200">:
                                <?php echo esc_html($donor_email); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Donation breakdown -->
            <div class="mb-10">
                <div class="border-t-2 border-gray-100 dark:border-gray-800"></div>
                <div
                    class="grid grid-cols-12 py-3 border-b border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-900 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    <div class="col-span-8 px-4">Jenis Donasi / Program</div>
                    <div class="col-span-4 px-4 text-right">Jumlah (IDR)</div>
                </div>

                <!-- Item Row -->
                <div class="grid grid-cols-12 py-4 border-b border-gray-100 dark:border-gray-800">
                    <div class="col-span-8 px-4">
                        <p class="font-bold text-gray-800 dark:text-gray-100 text-base">
                            <?php echo esc_html($campaign_title); ?>
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Via:
                            <?php echo esc_html($payment_method); ?>
                        </p>
                    </div>
                    <div class="col-span-4 px-4 text-right">
                        <span class="font-bold text-lg text-gray-900 dark:text-white">Rp
                            <?php echo esc_html($amount); ?></span>
                    </div>
                </div>

                <!-- Total Row -->
                <div class="grid grid-cols-12 py-4">
                    <div class="col-span-8 px-4 text-right pt-2">
                        <span class="text-sm font-bold text-gray-600 dark:text-gray-400 uppercase">Total Donasi</span>
                    </div>
                    <div class="col-span-4 px-4 text-right">
                        <span class="font-bold text-2xl text-brand-600 dark:text-brand-400">Rp
                            <?php echo esc_html($amount); ?></span>
                    </div>
                </div>
            </div>

            <!-- Footer Section (Pro) -->
            <?php if ($is_pro): ?>
                <div
                    class="flex justify-between items-end mt-16 pt-10 border-t border-dashed border-gray-200 dark:border-gray-700">
                    <!-- QR Code & Validation -->
                    <div class="text-left">
                        <div class="bg-white p-2 rounded inline-block">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?php echo urlencode(get_permalink($donation->campaign_id)); ?>"
                                alt="QR Validation" class="w-24 h-24 opacity-90">
                        </div>
                        <p class="text-[10px] text-gray-400 mt-3 max-w-[200px] leading-tight">
                            Dokumen ini sah dan diterbitkan secara otomatis oleh sistem. Scan QR Code untuk validasi
                            keaslian donasi.
                        </p>
                    </div>

                    <!-- Signature Section -->
                    <div class="text-center pr-8">
                        <div class="h-16 w-32 mb-2"></div> <!-- Space for signature -->
                        <p
                            class="text-sm font-bold text-gray-800 dark:text-gray-200 border-t border-gray-300 dark:border-gray-600 pt-2 min-w-[180px]">
                            Bagian Keuangan
                        </p>
                        <p class="text-[10px] text-gray-400 mt-1"><?php echo esc_html($org_name); ?></p>
                    </div>
                </div>
            <?php endif; ?>

        </div>

        <!-- Footer Brand Bar -->
        <div
            class="bg-gray-50 dark:bg-gray-900 px-10 py-4 flex justify-between items-center border-t border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-2 opacity-60">
                <span class="text-xs text-gray-500 dark:text-gray-400">dibuat dengan donasai</span>
            </div>
            <p class="text-[10px] text-gray-400">
                &copy; <?php echo esc_html(wp_date('Y')); ?> <?php echo esc_html($org_name); ?>. All rights reserved.
            </p>
        </div>

    </div>

    <!-- Screen-only Confetti -->
    <canvas id="confetti" class="fixed top-0 left-0 w-full h-full pointer-events-none z-50 no-print"></canvas>

    <script>
            (function () {
                if ('<?php echo esc_js($donation->status); ?>' !== 'complete') return;
                // Simple confetti script
                const canvas = document.getElementById('confetti');
                const ctx = canvas.getContext('2d');
                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;
                const pieces = [];
                const colors = ['#0ea5e9', '#0284c7', '#38bdf8', '#f0f9ff'];
                for (let i = 0; i < 80; i++) pieces.push({
                    x: Math.random() * canvas.width, y: Math.random() * canvas.height - canvas.height,
                    color: colors[Math.floor(Math.random() * colors.length)],
                    size: Math.random() * 6 + 4, speed: Math.random() * 4 + 2
                });
                function draw() {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    for (let i = 0; i < pieces.length; i++) {
                        const p = pieces[i];
                        ctx.fillStyle = p.color;
                        ctx.fillRect(p.x, p.y, p.size, p.size);
                        p.y += p.speed;
                        if (p.y > canvas.height) p.y = -20;
                    }
                    requestAnimationFrame(draw);
                }
                draw();
                setTimeout(() => { canvas.style.display = 'none'; }, 4000);
            })();
    </script>
    </script>
    
    <?php wp_footer(); ?>
</body>

</html>
<?php exit; ?>