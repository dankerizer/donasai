<?php
/**
 * Donation Summary / Thank You Page
 * Endpoint: /campaign-slug/thank-you/donation_id
 */

if (!defined('ABSPATH')) {
    exit;
}

$thankyou_slug = get_option('wpd_settings_general')['thankyou_slug'] ?? 'thank-you';
$donation_id = get_query_var($thankyou_slug);

if (!$donation_id) {
    wp_safe_redirect(home_url());
    exit;
}

global $wpdb;
$table_donations = esc_sql($wpdb->prefix . 'wpd_donations');
$donation = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_donations} WHERE id = %d", $donation_id));

if (!$donation) {
    wp_die('Donasi tidak ditemukan.', 'Error', array('response' => 404));
}

$campaign_id = $donation->campaign_id;
$title = get_the_title($campaign_id);
$amount = $donation->amount;
$status = $donation->status; // pending, complete, failed, on-hold
// Use 'gateway' if set (e.g. midtrans), otherwise fallback to 'payment_method' (e.g. manual)
$gateway_id = !empty($donation->gateway) ? $donation->gateway : $donation->payment_method;

// Get Gateway Instance
$gateway = WPD_Gateway_Registry::get_gateway($gateway_id);

// Colors from settings
$primary_color = get_option('wpd_appearance_brand_color', '#059669'); // Emerald
$button_color = get_option('wpd_appearance_button_color', '#ec4899'); // Pink

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terima Kasih - <?php echo esc_html($title); ?></title>
    <?php wp_head(); ?>
</head>

<body class="status-<?php echo esc_attr($status); ?>">

    <div class="wpd-container">
        <div class="wpd-card">

            <!-- Hero Status -->
            <div class="wpd-status-hero">
                <div class="wpd-icon-wrapper">
                    <?php if ($status == 'complete'): ?>
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    <?php elseif ($status == 'failed'): ?>
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    <?php else: ?>
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    <?php endif; ?>
                </div>

                <h1 class="wpd-title">
                    <?php
                    if ($status == 'complete')
                        esc_html_e('Donasi Berhasil!', 'donasai');
                    elseif ($status == 'failed')
                        esc_html_e('Mohon Maaf', 'donasai');
                    else
                        esc_html_e('Selesaikan Pembayaran', 'donasai');
                    ?>
                </h1>

                <p class="wpd-subtitle">
                    <?php
                    if ($status == 'complete')
                        esc_html_e('Terima kasih atas kontribusi Anda.', 'donasai');
                    elseif ($status == 'failed')
                        esc_html_e('Transaksi Anda gagal diproses.', 'donasai');
                    else
                        esc_html_e('Transfer sesuai nominal di bawah ini.', 'donasai');
                    ?>
                </p>
            </div>

            <!-- Payment Instructions (Ticket Style) -->
            <?php if ($status == 'pending' && $gateway): ?>
                <div class="wpd-ticket">
                    <span class="wpd-label"><?php esc_html_e('Instruksi Pembayaran', 'donasai'); ?></span>

                    <div class="gateway-instruction-content">
                        <?php
                        echo wp_kses_post($gateway->get_payment_instructions($donation_id));
                        ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Details -->
            <div class="wpd-details">
                <div class="wpd-row">
                    <span class="wpd-row-label"><?php esc_html_e('No. Referensi', 'donasai'); ?></span>
                    <span class="wpd-row-value">#<?php echo esc_html($donation_id); ?></span>
                </div>
                <div class="wpd-row">
                    <span class="wpd-row-label"><?php esc_html_e('Program', 'donasai'); ?></span>
                    <span class="wpd-row-value"><?php echo esc_html($title); ?></span>
                </div>
                <div class="wpd-row">
                    <span class="wpd-row-label"><?php esc_html_e('Tanggal', 'donasai'); ?></span>
                    <span
                        class="wpd-row-value"><?php echo esc_html(date_i18n('d M Y, H:i', strtotime($donation->created_at))); ?></span>
                </div>
                <div class="wpd-row">
                    <span class="wpd-row-label"><?php esc_html_e('Atas Nama', 'donasai'); ?></span>
                    <span
                        class="wpd-row-value"><?php echo $donation->is_anonymous ? esc_html__('Hamba Allah', 'donasai') : esc_html($donation->name); ?></span>
                </div>

                <div class="wpd-total">
                    <span class="wpd-total-label"><?php esc_html_e('Total Donasi', 'donasai'); ?></span>
                    <span class="wpd-total-value">Rp
                        <?php echo esc_html(number_format($amount, 0, ',', '.')); ?></span>
                </div>
            </div>

            <!-- Actions -->
            <div class="wpd-actions">
                <?php if ($status == 'complete'): ?>
                    <a href="<?php echo esc_url(add_query_arg('wpd_receipt', $donation_id, home_url('/'))); ?>"
                        target="_blank" class="btn btn-secondary">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            style="margin-right:8px">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <?php esc_html_e('Download Kwitansi', 'donasai'); ?>
                    </a>
                <?php endif; ?>

                <?php if ($status == 'pending' && $gateway_id == 'manual'):
                    // Get Confirmation URL
                    $settings_gen = get_option('wpd_settings_general');
                    $conf_page_id = isset($settings_gen['confirmation_page']) ? intval($settings_gen['confirmation_page']) : 0;
                    $conf_url = $conf_page_id ? get_permalink($conf_page_id) : '';

                    // Build values
                    $conf_url_final = $conf_url ? add_query_arg('donation_id', $donation_id, $conf_url) : '';
                    $raw_phone = get_option('wpd_settings_general')['whatsapp_number'] ?? '';
                    $fmt_amount = number_format($amount, 0, ',', '.');
                    
                    if ($conf_page_id) {
                        printf(
                            '<a href="%s" class="btn btn-primary">%s</a>',
                            esc_url($conf_url_final),
                            esc_html__('Konfirmasi Pembayaran', 'donasai')
                        );
                    } else {
                         // Use ID and Data Attributes for JS handler
                        printf(
                            '<a href="#" id="wpd-confirm-btn" data-url="%s" data-phone="%s" data-id="%s" data-amount="%s" class="btn btn-primary">%s</a>',
                            esc_url($conf_url_final),
                            esc_attr($raw_phone),
                            esc_attr($donation_id),
                            esc_attr($fmt_amount),
                            esc_html__('Konfirmasi Pembayaran', 'donasai')
                        );
                    }
                    ?>
                <?php endif; ?>

                <a href="<?php echo esc_url(get_permalink($campaign_id)); ?>" class="btn btn-ghost">
                    <?php esc_html_e('Kembali ke Halaman Program', 'donasai'); ?>
                </a>
            </div>

            <?php
            $gen_settings = get_option('wpd_settings_general', []);
            $is_branding_removed = !empty($gen_settings['remove_branding']);

            if (!$is_branding_removed): ?>
                <div class="wpd-powered-by">
                    Powered by <a href="https://donasai.com" target="_blank">Donasai</a>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <?php 
    // Pass status to JS for Confetti
    wp_add_inline_script('wpd-summary', 'window.wpd_donation_status = "' . esc_js($status) . '";', 'before');
    ?>

    <?php wp_footer(); ?>
</body>

</html>