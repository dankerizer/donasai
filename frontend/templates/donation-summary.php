<?php
/**
 * Donation Summary / Thank You Page
 * Endpoint: /campaign-slug/thank-you/donation_id
 */

if (!defined('ABSPATH')) {
    exit;
}

$donasai_thankyou_slug = get_option('donasai_settings_general')['thankyou_slug'] ?? 'thank-you';
$donasai_donation_id = get_query_var($donasai_thankyou_slug);

if (!$donasai_donation_id) {
    wp_safe_redirect(home_url());
    exit;
}

global $wpdb;
$donasai_table_donations = $wpdb->prefix . 'donasai_donations';
$donasai_donation = $wpdb->get_row($wpdb->prepare("SELECT * FROM %i WHERE id = %d", $donasai_table_donations, $donasai_donation_id)); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

if (!$donasai_donation) {
    wp_die('Donasi tidak ditemukan.', 'Error', array('response' => 404));
}

$donasai_campaign_id = $donasai_donation->campaign_id;
$donasai_title = get_the_title($donasai_campaign_id);
$donasai_amount = $donasai_donation->amount;
$donasai_status = $donasai_donation->status; // pending, complete, failed, on-hold
// Use 'gateway' if set (e.g. midtrans), otherwise fallback to 'payment_method' (e.g. manual)
$donasai_gateway_id = !empty($donasai_donation->gateway) ? $donasai_donation->gateway : $donasai_donation->payment_method;

// Get Gateway Instance
$donasai_gateway = DONASAI_Gateway_Registry::get_gateway($donasai_gateway_id);

// Colors from settings
$donasai_primary_color = get_option('donasai_appearance_brand_color', '#059669'); // Emerald
$donasai_button_color = get_option('donasai_appearance_button_color', '#ec4899'); // Pink

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terima Kasih - <?php echo esc_html($donasai_title); ?></title>
    <?php wp_head(); ?>
</head>

<body class="status-<?php echo esc_attr($donasai_status); ?>">

    <div class="donasai-container">
        <div class="donasai-card">

            <!-- Hero Status -->
            <div class="donasai-status-hero">
                <div class="donasai-icon-wrapper">
                    <?php if ($donasai_status == 'complete'): ?>
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    <?php elseif ($donasai_status == 'failed'): ?>
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

                <h1 class="donasai-title">
                    <?php
                    if ($donasai_status == 'complete')
                        esc_html_e('Donasi Berhasil!', 'donasai');
                    elseif ($donasai_status == 'failed')
                        esc_html_e('Mohon Maaf', 'donasai');
                    else
                        esc_html_e('Selesaikan Pembayaran', 'donasai');
                    ?>
                </h1>

                <p class="donasai-subtitle">
                    <?php
                    if ($donasai_status == 'complete')
                        esc_html_e('Terima kasih atas kontribusi Anda.', 'donasai');
                    elseif ($donasai_status == 'failed')
                        esc_html_e('Transaksi Anda gagal diproses.', 'donasai');
                    else
                        esc_html_e('Transfer sesuai nominal di bawah ini.', 'donasai');
                    ?>
                </p>
            </div>

            <!-- Payment Instructions (Ticket Style) -->
            <?php if ($donasai_status == 'pending' && $donasai_gateway): ?>
                <div class="donasai-ticket">
                    <span class="donasai-label"><?php esc_html_e('Instruksi Pembayaran', 'donasai'); ?></span>

                    <div class="gateway-instruction-content">
                        <?php
                        echo wp_kses_post($donasai_gateway->get_payment_instructions($donasai_donation_id));
                        ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Details -->
            <div class="donasai-details">
                <div class="donasai-row">
                    <span class="donasai-row-label"><?php esc_html_e('No. Referensi', 'donasai'); ?></span>
                    <span class="donasai-row-value">#<?php echo esc_html($donasai_donation_id); ?></span>
                </div>
                <div class="donasai-row">
                    <span class="donasai-row-label"><?php esc_html_e('Program', 'donasai'); ?></span>
                    <span class="donasai-row-value"><?php echo esc_html($donasai_title); ?></span>
                </div>
                <div class="donasai-row">
                    <span class="donasai-row-label"><?php esc_html_e('Tanggal', 'donasai'); ?></span>
                    <span
                        class="donasai-row-value"><?php echo esc_html(date_i18n('d M Y, H:i', strtotime($donasai_donation->created_at))); ?></span>
                </div>
                <div class="donasai-row">
                    <span class="donasai-row-label"><?php esc_html_e('Atas Nama', 'donasai'); ?></span>
                    <span
                        class="donasai-row-value"><?php echo $donasai_donation->is_anonymous ? esc_html__('Hamba Allah', 'donasai') : esc_html($donasai_donation->name); ?></span>
                </div>

                <div class="donasai-total">
                    <span class="donasai-total-label"><?php esc_html_e('Total Donasi', 'donasai'); ?></span>
                    <span class="donasai-total-value">Rp
                        <?php echo esc_html(number_format($donasai_amount, 0, ',', '.')); ?></span>
                </div>
            </div>

            <!-- Actions -->
            <div class="donasai-actions">
                <?php if ($donasai_status == 'complete'): ?>
                    <a href="<?php echo esc_url(add_query_arg('donasai_receipt', $donasai_donation_id, home_url('/'))); ?>"
                        target="_blank" class="btn btn-secondary">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            style="margin-right:8px">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <?php esc_html_e('Download Kwitansi', 'donasai'); ?>
                    </a>
                <?php endif; ?>

                <?php if ($donasai_status == 'pending' && $donasai_gateway_id == 'manual'):
                    // Get Confirmation URL
                    $donasai_settings_gen = get_option('donasai_settings_general');
                    $donasai_conf_page_id = isset($donasai_settings_gen['confirmation_page']) ? intval($donasai_settings_gen['confirmation_page']) : 0;
                    $donasai_conf_url = $donasai_conf_page_id ? get_permalink($donasai_conf_page_id) : '';

                    // Build values
                    $donasai_conf_url_final = $donasai_conf_url ? add_query_arg('donation_id', $donasai_donation_id, $donasai_conf_url) : '';
                    $donasai_raw_phone = get_option('donasai_settings_general')['whatsapp_number'] ?? '';
                    $donasai_fmt_amount = number_format($donasai_amount, 0, ',', '.');
                    
                    if ($donasai_conf_page_id) {
                        printf(
                            '<a href="%s" class="btn btn-primary">%s</a>',
                            esc_url($donasai_conf_url_final),
                            esc_html__('Konfirmasi Pembayaran', 'donasai')
                        );
                    } else {
                         // Use ID and Data Attributes for JS handler
                        printf(
                            '<a href="#" id="donasai-confirm-btn" data-url="%s" data-phone="%s" data-id="%s" data-amount="%s" class="btn btn-primary">%s</a>',
                            esc_url($donasai_conf_url_final),
                            esc_attr($donasai_raw_phone),
                            esc_attr($donasai_donation_id),
                            esc_attr($donasai_fmt_amount),
                            esc_html__('Konfirmasi Pembayaran', 'donasai')
                        );
                    }
                    ?>
                <?php endif; ?>

                <a href="<?php echo esc_url(get_permalink($donasai_campaign_id)); ?>" class="btn btn-ghost">
                    <?php esc_html_e('Kembali ke Halaman Program', 'donasai'); ?>
                </a>
            </div>

            <?php
            $donasai_gen_settings = get_option('donasai_settings_general', []);
            $donasai_is_branding_removed = !empty($donasai_gen_settings['remove_branding']);

            if (!$donasai_is_branding_removed): ?>
                <div class="donasai-powered-by">
                    Powered by <a href="https://donasai.com" target="_blank">Donasai</a>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <?php 
    // Pass status to JS for Confetti
    wp_add_inline_script('donasai-summary', 'window.donasai_donation_status = "' . esc_js($donasai_status) . '";', 'before');
    ?>

    <?php wp_footer(); ?>
</body>

</html>