<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Payment Success Page Template
 * Triggered by ?donation_success=1
 */

get_header();

$donasai_campaign_id = get_the_ID();
$donasai_donation_id = isset($_GET['donation_success']) ? intval($_GET['donation_success']) : 0;
// Optional method display
$donasai_gateway = isset($_GET['method']) ? sanitize_text_field(wp_unslash($_GET['method'])) : '';

// Verify Nonce
$donasai_nonce_val = isset($_GET['_wpnonce']) ? sanitize_text_field(wp_unslash($_GET['_wpnonce'])) : '';
if (!wp_verify_nonce($donasai_nonce_val, 'donasai_payment_success')) {
    // If nonce invalid, we reset donation_id to 0 to prevent displaying info
    $donasai_donation_id = 0;
}

?>

<div class="donasai-container"
    style="max-width:600px; margin:0 auto; padding:40px 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">

    <div
        style="background:white; border-radius:16px; box-shadow:0 10px 25px -5px rgba(0,0,0,0.1); border:1px solid #e5e7eb; overflow:hidden;">

        <!-- Header Success -->
        <div style="background:#ecfdf5; padding:30px; text-align:center; border-bottom:1px solid #d1fae5;">
            <div
                style="width:60px; height:60px; background:#10b981; color:white; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; box-shadow:0 4px 6px rgba(16, 185, 129, 0.4);">
                <svg style="width:32px; height:32px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h1 style="font-size:24px; font-weight:700; color:#064e3b; margin:0 0 5px 0;">
                <?php esc_html_e('Terima Kasih!', 'donasai'); ?>
            </h1>
            <p style="color:#047857; margin:0;">
                <?php esc_html_e('Donasi Anda telah berhasil kami terima.', 'donasai'); ?>
                <?php if ($donasai_gateway): ?>
                    <br>
                    <span style="font-size:0.9em; opacity:0.8;"><?php
                    /* translators: %s: Payment Gateway Name */
                    printf(esc_html__('Via %s', 'donasai'), esc_html(ucfirst($donasai_gateway)));
                    ?></span>
                <?php endif; ?>
            </p>
        </div>

        <!-- content -->
        <div style="padding:30px;">

            <?php if ($donasai_donation_id): ?>
                <div style="background:#f9fafb; padding:15px; border-radius:8px; margin-bottom:20px; text-align:center;">
                    <p style="margin:0; font-size:12px; color:#6b7280; text-transform:uppercase; letter-spacing:1px;">
                        <?php esc_html_e('ID Donasi', 'donasai'); ?>
                    </p>
                    <p style="margin:5px 0 0; font-family:monospace; font-size:18px; font-weight:bold; color:#1f2937;">
                        #<?php echo esc_html($donasai_donation_id); ?></p>
                </div>
            <?php endif; ?>

            <!-- Actions -->
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <?php if ($donasai_donation_id):
                    $donasai_receipt_url = add_query_arg([
                        'donasai_receipt' => $donasai_donation_id,
                        '_wpnonce' => wp_create_nonce('donasai_receipt_' . $donasai_donation_id)
                    ], home_url('/'));
                    ?>
                    <a href="<?php echo esc_url($donasai_receipt_url); ?>" target="_blank"
                        style="flex:1; text-align:center; padding:12px; background:#f3f4f6; color:#374151; font-weight:600; text-decoration:none; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; gap:5px;">
                        <svg style="width:18px; height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <?php esc_html_e('Cetak Kuitansi', 'donasai'); ?>
                    </a>
                <?php endif; ?>

                <a href="<?php echo esc_url(get_permalink($donasai_campaign_id)); ?>"
                    style="flex:1; text-align:center; padding:12px; background:#2563eb; color:white; font-weight:600; text-decoration:none; border-radius:8px;">
                    <?php esc_html_e('Kembali ke Campaign', 'donasai'); ?>
                </a>
            </div>

        </div>

    </div>

    <?php
    $donasai_gen_settings = get_option('donasai_settings_general', []);
    $donasai_is_branding_removed = !empty($donasai_gen_settings['remove_branding']);

    if (!$donasai_is_branding_removed): ?>
        <div class="donasai-powered-by" style="text-align:center; padding-top: 20px; color:#9ca3af; font-size:13px;">
            Powered by <a href="https://donasai.com" target="_blank"
                style="color:inherit; text-decoration:none; font-weight:600;">Donasai</a>
        </div>
    <?php endif; ?>
</div>

<?php
get_footer();
?>