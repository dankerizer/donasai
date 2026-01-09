<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Payment Page Template
 * Triggered by ?donate=1 on Campaign URL
 */

get_header();

$campaign_id = get_the_ID();
?>

<div class="wpd-container"
    style="max-width:600px; margin:0 auto; padding:40px 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">

    <!-- Back Button -->
    <a href="<?php echo esc_url(get_permalink()); ?>"
        style="display:inline-flex; align-items:center; text-decoration:none; color:#6b7280; font-size:14px; margin-bottom:20px;">
        <svg style="width:16px; height:16px; margin-right:5px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        <?php esc_html_e('Kembali ke Campaign', 'donasai'); ?>
    </a>

    <!-- Header -->
    <div style="text-align:center; margin-bottom:30px;">
        <h1 style="font-size:24px; font-weight:700; color:#111827; margin:0 0 10px 0;">
            <?php esc_html_e('Data Donasi', 'donasai'); ?>
        </h1>
        <p style="color:#6b7280; margin:0; font-size:16px;">
            <?php esc_html_e('Anda akan berdonasi untuk', 'donasai'); ?> <br>
            <strong style="color:#059669;"><?php echo esc_html(get_the_title()); ?></strong>
        </p>
    </div>

    <!-- Form Container -->
    <div
        style="background:white; border-radius:16px; box-shadow:0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1); border:1px solid #e5e7eb; padding:30px; overflow:hidden;">

        <?php
        // Ensure the form output is properly handled. 
        // wpd_get_donation_form_html should ideally return escaped/sanitized HTML, or we trust it as internal function.
        // Given it likely contains form inputs, standard escaping might break it.
        echo wp_kses_post(wpd_get_donation_form_html($campaign_id));
        ?>

    </div>

    <!-- Footer Trust -->
    <div style="text-align:center; margin-top:30px; color:#9ca3af; font-size:12px;">
        <div style="display:flex; justify-content:center; gap:15px; margin-bottom:10px;">
            <!-- Icons placeholders -->
            <span>🔒 <?php esc_html_e('Secure Payment', 'donasai'); ?></span>
            <span>🛡️ <?php esc_html_e('Verified Campaign', 'donasai'); ?></span>
        </div>
        &copy; <?php echo esc_html(gmdate('Y')); ?> <?php echo esc_html(get_bloginfo('name')); ?>
    </div>

</div>

<?php
get_footer();
?>