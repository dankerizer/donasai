<?php
/**
 * Frontend Functions & Template Loader
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Template Loader for Custom Post Types
 */
// Debugging Template Loader
function wpd_template_loader($template)
{

    // Check for Receipt
    if (isset($_GET['wpd_receipt'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $receipt_template = WPD_PLUGIN_PATH . 'frontend/templates/receipt.php';
        if (file_exists($receipt_template)) {
            return $receipt_template;
        }
    }

    if (is_singular('wpd_campaign')) {
        $payment_slug = get_option('wpd_settings_general')['payment_slug'] ?? 'pay';

        // Payment Page (?donate=1 OR /slug/pay)
        // Payment Page (?donate=1 OR /slug/pay)
        global $wp_query;
        if (isset($_GET['donate']) || isset($wp_query->query_vars[$payment_slug])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $payment_template = WPD_PLUGIN_PATH . 'frontend/templates/donation-form.php';
            if (file_exists($payment_template)) {
                return $payment_template;
            }
        }

        // Thank You Page (?thank-you=ID OR /slug/thank-you/ID)
        $thankyou_slug = get_option('wpd_settings_general')['thankyou_slug'] ?? 'thank-you';

        // Use get_query_var to be robust
        if (get_query_var($thankyou_slug)) {
            $summary_template = WPD_PLUGIN_PATH . 'frontend/templates/donation-summary.php';
            if (file_exists($summary_template)) {
                return $summary_template;
            }
        }

        // Success Page (?donation_success=1)
        if (isset($_GET['donation_success'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $success_template = WPD_PLUGIN_PATH . 'frontend/templates/payment-success.php';
            if (file_exists($success_template)) {
                return $success_template;
            }
        }

        $plugin_template = WPD_PLUGIN_PATH . 'frontend/templates/campaign-single.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }
    return $template;
}
add_filter('template_include', 'wpd_template_loader');

/**
 * Disable Canonical Redirect for Thank You Endpoint
 * Fixes issue where /campaign/slug/thank-you/ID redirects back to /campaign/slug/
 */
function wpd_disable_canonical_redirect($redirect_url)
{
    $thankyou_slug = get_option('wpd_settings_general')['thankyou_slug'] ?? 'thank-you';

    if (get_query_var($thankyou_slug)) {
        return false;
    }
    return $redirect_url;
}
add_filter('redirect_canonical', 'wpd_disable_canonical_redirect');

/**
 * Template Loader for Custom Post Types
 */
function wpd_get_template_part($slug, $name = null)
{
    $template = '';

    // Look in yourtheme/slug-name.php
    if ($name) {
        $template = locate_template(array("donasai/{$slug}-{$name}.php", "{$slug}-{$name}.php"));
    }

    // Get default slug-name.php
    if (!$template && $name && file_exists(WPD_PLUGIN_PATH . "frontend/templates/{$slug}-{$name}.php")) {
        $template = WPD_PLUGIN_PATH . "frontend/templates/{$slug}-{$name}.php";
    }

    // If not found, look for slug.php
    if (!$template) {
        $template = locate_template(array("donasai/{$slug}.php", "{$slug}.php"));
    }

    // Default slug.php
    if (!$template && file_exists(WPD_PLUGIN_PATH . "frontend/templates/{$slug}.php")) {
        $template = WPD_PLUGIN_PATH . "frontend/templates/{$slug}.php";
    }

    if ($template) {
        load_template($template, false);
    }
}

/**
 * Enqueue Frontend Assets
 */
/**
 * Enqueue Frontend Assets
 */
function wpd_enqueue_frontend_assets()
{
    $should_load = false;

    // Check for Campaign Single
    if (is_singular('wpd_campaign')) {
        $should_load = true;
    }

    // Check for Confirmation Page
    $settings_gen = get_option('wpd_settings_general', []);
    $conf_page_id = isset($settings_gen['confirmation_page']) ? intval($settings_gen['confirmation_page']) : 0;
    if ($conf_page_id && is_page($conf_page_id)) {
        $should_load = true;
    }

    if ($should_load) {
        // Core Frontend Styles
        wp_enqueue_style('donasai-frontend', WPD_PLUGIN_URL . 'frontend/assets/frontend.css', array(), WPD_VERSION);

        // Inject Branding Variables
        $settings_app = get_option('wpd_settings_appearance', []);
        $primary_color = $settings_app['brand_color'] ?? '#059669';
        $button_color = $settings_app['button_color'] ?? '#ec4899';
        $radius = $settings_app['border_radius'] ?? '12px';
        
        // Helper to calc RGB
        $hex2rgb = function($hex) {
            $hex = str_replace("#", "", $hex);
            if(strlen($hex) == 3) {
                $r = hexdec(substr($hex,0,1).substr($hex,0,1));
                $g = hexdec(substr($hex,1,1).substr($hex,1,1));
                $b = hexdec(substr($hex,2,1).substr($hex,2,1));
            } else {
                $r = hexdec(substr($hex,0,2));
                $g = hexdec(substr($hex,2,2));
                $b = hexdec(substr($hex,4,2));
            }
            return "{$r}, {$g}, {$b}";
        };
        $primary_rgb = $hex2rgb($primary_color);

        $custom_css = "
            :root {
                --wpd-primary: {$primary_color};
                --wpd-primary-rgb: {$primary_rgb};
                --wpd-btn: {$button_color};
                --wpd-radius: {$radius};
                --wpd-bg: #f3f4f6; /* Default BG */
                --wpd-card-bg: #ffffff;
                --wpd-text-main: #1f2937;
                --wpd-text-muted: #6b7280;
                --wpd-input-bg: #ffffff;
                --wpd-input-border: #d1d5db;
                --wpd-border: #e5e7eb;
            }
            .wpd-dark {
                 --wpd-bg: #1f2937;
                 --wpd-card-bg: #111827;
                 --wpd-text-main: #f3f4f6;
                 --wpd-text-muted: #9ca3af;
                 --wpd-input-bg: #374151;
                 --wpd-input-border: #4b5563;
                 --wpd-border: #374151;
            }
        ";
        wp_add_inline_style('donasai-frontend', $custom_css);

        // Google Fonts
        $font_family = $settings_app['font_family'] ?? 'Inter';
        $fonts_map = [
            'Inter' => 'Inter:wght@400;500;600;700',
            'Roboto' => 'Roboto:wght@400;500;700',
            'Open Sans' => 'Open+Sans:wght@400;600;700',
            'Poppins' => 'Poppins:wght@400;500;600;700',
            'Lato' => 'Lato:wght@400;700'
        ];
        if (isset($fonts_map[$font_family])) {
            wp_enqueue_style('wpd-google-fonts', 'https://fonts.googleapis.com/css2?family=' . $fonts_map[$font_family] . '&display=swap', array(), WPD_VERSION);
        }

        // Campaign Specific Assets
        if (is_singular('wpd_campaign')) {
            wp_enqueue_style('donasai-campaign', WPD_PLUGIN_URL . 'frontend/assets/campaign.css', array('donasai-frontend'), WPD_VERSION);
            wp_enqueue_script('donasai-campaign', WPD_PLUGIN_URL . 'frontend/assets/campaign.js', array('jquery'), WPD_VERSION, true);

            // Check for Payment Page
            global $wp_query;
            $payment_slug = get_option('wpd_settings_general')['payment_slug'] ?? 'pay';
            if (isset($_GET['donate']) || isset($wp_query->query_vars[$payment_slug])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                wp_enqueue_style('wpd-payment', WPD_PLUGIN_URL . 'frontend/assets/payment.css', array('donasai-frontend'), WPD_VERSION);
                wp_enqueue_script('wpd-payment', WPD_PLUGIN_URL . 'frontend/assets/payment.js', array('jquery'), WPD_VERSION, true);

                // Localize Payment Script
                $midtrans = WPD_Gateway_Registry::get_gateway('midtrans');
                $snap_active = $midtrans && $midtrans->is_active();
                $client_key = $snap_active && method_exists($midtrans, 'get_client_key') ? $midtrans->get_client_key() : '';
                $is_prod = $snap_active && method_exists($midtrans, 'is_production') ? $midtrans->is_production() : false;
                $snap_url = $is_prod ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js';

                wp_localize_script('wpd-payment', 'wpd_payment_vars', array(
                    'is_midtrans_active' => $snap_active,
                    'snap_url' => $snap_url,
                    'client_key' => $client_key
                ));
            }

            // Check for Thank You Page
            $thankyou_slug = get_option('wpd_settings_general')['thankyou_slug'] ?? 'thank-you';
            if (get_query_var($thankyou_slug)) {
                wp_enqueue_script('wpd-confetti', WPD_PLUGIN_URL . 'frontend/assets/confetti.js', array(), '1.6.0', true);
                wp_enqueue_style('wpd-summary', WPD_PLUGIN_URL . 'frontend/assets/summary.css', array('donasai-frontend'), WPD_VERSION);
                wp_enqueue_script('wpd-summary', WPD_PLUGIN_URL . 'frontend/assets/summary.js', array('jquery', 'wpd-confetti'), WPD_VERSION, true);
            }
        }

        // Confirmation Page Assets
        if ($conf_page_id && is_page($conf_page_id)) {
            wp_enqueue_style('wpd-confirmation', WPD_PLUGIN_URL . 'frontend/assets/confirmation.css', array('donasai-frontend'), WPD_VERSION);
            wp_enqueue_script('wpd-confirmation', WPD_PLUGIN_URL . 'frontend/assets/confirmation.js', array('jquery'), WPD_VERSION, true);
        }
    }
}
add_action('wp_enqueue_scripts', 'wpd_enqueue_frontend_assets');

/**
 * Get Recent Donors
 */
function wpd_get_recent_donors($campaign_id, $limit = 10)
{
    global $wpdb;
    $table = esc_sql($wpdb->prefix . 'wpd_donations');

    // Only completed donations
    $cache_key = 'wpd_recent_donors_' . $campaign_id . '_limit_' . $limit;
    $results = wp_cache_get($cache_key, 'wpd_donations');

    if (false === $results) {
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE campaign_id = %d AND status = 'complete' ORDER BY created_at DESC LIMIT %d",
            $campaign_id,
            $limit
        )); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        wp_cache_set($cache_key, $results, 'wpd_donations', 300);
    }

    return $results;
}

/**
 * Get Total Donor Count
 */
function wpd_get_donor_count($campaign_id)
{
    global $wpdb;
    $table = esc_sql($wpdb->prefix . 'wpd_donations');

    $cache_key = 'wpd_donor_count_' . $campaign_id;
    $count = wp_cache_get($cache_key, 'wpd_donations');

    if (false === $count) {
        $count = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(id) FROM {$table} WHERE campaign_id = %d AND status = 'complete'",
            $campaign_id
        )); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        wp_cache_set($cache_key, $count, 'wpd_donations', 300);
    }

    return $count;
}

/**
 * Check if a gateway is active
 */
function wpd_is_gateway_active($gateway_id)
{
    if ($gateway_id === 'midtrans') {
        $settings = get_option('wpd_settings_midtrans', []);
        return !empty($settings['enabled']);
    }
    return false;
}

/**
 * Get Donation Form HTML
 */
function wpd_get_donation_form_html($campaign_id)
{
    ob_start();
    include WPD_PLUGIN_PATH . 'frontend/templates/donation-form.php';
    return ob_get_clean();
}

/**
 * Donor Dashboard Shortcode [wpd_my_donations]
 */
function wpd_shortcode_my_donations()
{
    wp_enqueue_style('donasai-frontend', WPD_PLUGIN_URL . 'frontend/assets/frontend.css', array(), WPD_VERSION);
    wp_enqueue_style('wpd-dashboard', WPD_PLUGIN_URL . 'frontend/assets/dashboard.css', array('donasai-frontend'), WPD_VERSION);
    wp_enqueue_script('wpd-dashboard', WPD_PLUGIN_URL . 'frontend/assets/dashboard.js', array(), WPD_VERSION, true);

    ob_start();
    include WPD_PLUGIN_PATH . 'frontend/templates/donor-dashboard.php';
    return ob_get_clean();
}
add_shortcode('wpd_my_donations', 'wpd_shortcode_my_donations');



/**
 * Output WhatsApp Flying Button (Footer)
 */
function wpd_footer_whatsapp()
{
    if (is_singular('wpd_campaign')) {
        $whatsapp = get_post_meta(get_the_ID(), '_wpd_whatsapp_settings', true);

        if (!empty($whatsapp['number'])) {
            $number = preg_replace('/\D/', '', $whatsapp['number']);
            $message = isset($whatsapp['message']) ? rawurlencode($whatsapp['message']) : '';
            $link = "https://wa.me/{$number}?text={$message}";
            ?>
            <a href="<?php echo esc_url($link); ?>" target="_blank"
                style="position:fixed; bottom:20px; right:20px; z-index:9999; background:#25D366; color:white; width:60px; height:60px; border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 10px rgba(0,0,0,0.3);">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                </svg>
            </a>
            <?php
        }
    }
}
add_action('wp_footer', 'wpd_footer_whatsapp');

/**
 * Handle Referral Tracking
 */
function wpd_track_referral()
{
    if (is_admin())
        return;

    if (isset($_GET['ref'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $ref_code = sanitize_text_field(wp_unslash($_GET['ref'])); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $service = new WPD_Fundraiser_Service();
        $fundraiser = $service->get_by_code($ref_code);

        if ($fundraiser) {
            // Set Cookie for 30 days
            setcookie('wpd_ref', $fundraiser->id, time() + (30 * DAY_IN_SECONDS), COOKIEPATH, COOKIE_DOMAIN);

            // Log visit if we are on a campaign page (or maybe log all?)
            // If the ref link points to a single campaign, log it.
            if (is_singular('wpd_campaign')) {
                $service->track_visit($fundraiser->id, get_the_ID());
            }
        }
    }
}
add_action('template_redirect', 'wpd_track_referral');

/**
 * Fundraiser Stats Shortcode [wpd_fundraiser_stats]
 */
function wpd_shortcode_fundraiser_stats()
{
    if (!is_user_logged_in()) {
        return '<p>' . __('Silakan login untuk melihat statistik fundraiser Anda.', 'donasai') . '</p>';
    }

    global $wpdb;
    $user_id = get_current_user_id();
    $table_fundraisers = esc_sql($wpdb->prefix . 'wpd_fundraisers');

    // Get all campaigns user is fundraising for
    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT f.*, p.post_title 
         FROM {$table_fundraisers} f
         JOIN {$wpdb->posts} p ON f.campaign_id = p.ID
         WHERE f.user_id = %d
         ORDER BY f.created_at DESC", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $user_id
    )); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery

    if (empty($results)) {
        return '<p>' . __('Anda belum mendaftar sebagai fundraiser untuk campaign apapun.', 'donasai') . '</p>';
    }

    ob_start();
    ?>
    <div class="wpd-fundraiser-dashboard">
        <h3><?php esc_attr_e('Statistik Kampanye Anda', 'donasai'); ?></h3>
        <table class="wpd-table" style="width:100%; border-collapse:collapse; margin-top:15px;">
            <thead>
                <tr style="background:#f9fafb; text-align:left; border-bottom:1px solid #ddd;">
                    <th style="padding:10px;">Campaign</th>
                    <th style="padding:10px;">Link Referral</th>
                    <th style="padding:10px;">Visit</th>
                    <th style="padding:10px;">Donasi</th>
                    <th style="padding:10px;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row):
                    // Get visit count (lazy query, ideally should act stored count or cached)
                    $table_logs = esc_sql($wpdb->prefix . 'wpd_referral_logs');
                    $visit_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM {$table_logs} WHERE fundraiser_id = %d", $row->id)); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
                    $link = add_query_arg('ref', $row->referral_code, get_permalink($row->campaign_id));
                    ?>
                    <tr style="border-bottom:1px solid #eee;">
                        <td style="padding:10px;"><strong><?php echo esc_html($row->post_title); ?></strong></td>
                        <td style="padding:10px;"><input type="text" value="<?php echo esc_url($link); ?>" readonly
                                style="width:100%; font-size:12px; padding:5px; background:#f9f9f9; border:1px solid #ddd;"
                                onclick="this.select()"></td>
                        <td style="padding:10px;"><?php echo intval($visit_count); ?></td>
                        <td style="padding:10px;"><?php echo intval($row->donation_count); ?></td>
                        <td style="padding:10px; color:#059669; font-weight:bold;">Rp
                            <?php echo esc_html(number_format($row->total_donations, 0, ',', '.')); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('wpd_fundraiser_stats', 'wpd_shortcode_fundraiser_stats');

/**
 * User Profile Shortcode [wpd_profile]
 */
function wpd_shortcode_profile()
{
    if (!is_user_logged_in()) {
        return sprintf(
            '<p>%s</p>',
            sprintf(
                /* translators: %s: Login URL */
                __('Silakan <a href="%s">login</a> untuk mengedit profil Anda.', 'donasai'),
                esc_url(wp_login_url(get_permalink()))
            )
        );
    }

    // Handle Form Submission
    if (isset($_POST['wpd_profile_submit']) && isset($_POST['wpd_profile_nonce'])) {
        if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['wpd_profile_nonce'])), 'wpd_profile_update')) {
            wp_die('Security check failed');
        }

        $user_id = get_current_user_id();
        $name = isset($_POST['display_name']) ? sanitize_text_field(wp_unslash($_POST['display_name'])) : '';
        $phone = isset($_POST['phone']) ? sanitize_text_field(wp_unslash($_POST['phone'])) : '';
        $pass1 = isset($_POST['pass1']) ? wp_unslash($_POST['pass1']) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $pass2 = isset($_POST['pass2']) ? wp_unslash($_POST['pass2']) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

        // Update User
        $user_data = array(
            'ID' => $user_id,
            'display_name' => $name,
        );

        if (!empty($pass1)) {
            if ($pass1 === $pass2) {
                $user_data['user_pass'] = $pass1;
            } else {
                $_POST['wpd_profile_error'] = 'Password tidak cocok.';
                // Allow execution to continue to display form with error
            }
        }

        if (!isset($_POST['wpd_profile_error'])) {
            wp_update_user($user_data);
            update_user_meta($user_id, '_wpd_phone', $phone);

            // Redirect to avoid resubmission
            wp_safe_redirect(add_query_arg('updated', 'true'));
            exit;
        }
    }

    // Pass error to template if exists
    if (isset($_POST['wpd_profile_error'])) {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">' . esc_html(sanitize_text_field(wp_unslash($_POST['wpd_profile_error']))) . '</div>';
    }

    ob_start();
    include WPD_PLUGIN_PATH . 'frontend/templates/profile.php';
    return ob_get_clean();
}
add_shortcode('wpd_profile', 'wpd_shortcode_profile');

/**
 * Donation Confirmation Shortcode [wpd_confirmation_form]
 */
function wpd_shortcode_confirmation_form()
{
    $success = false;
    $error = '';
    $donation_id_val = '';
    $amount_val = '';

    // Pre-fill from URL
    if (isset($_GET['donation_id'])) {
        global $wpdb;
        $d_id = intval($_GET['donation_id']);
        $table_donations = esc_sql($wpdb->prefix . 'wpd_donations');
        $donation_row = $wpdb->get_row($wpdb->prepare("SELECT amount FROM {$table_donations} WHERE id = %d", $d_id)); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        if ($donation_row) {
            $donation_id_val = $d_id;
            $amount_val = $donation_row->amount;
        }
    }

    if (isset($_POST['wpd_confirm_submit']) && isset($_POST['_wpnonce'])) {
        if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'wpd_confirm_payment')) {
            $error = 'Security check failed.';
        } else {
            global $wpdb;
            $donation_id = isset($_POST['donation_id']) ? intval($_POST['donation_id']) : 0;
            $amount_post = isset($_POST['amount']) ? wp_unslash($_POST['amount']) : '0'; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            $amount = intval(str_replace('.', '', $amount_post)); // Remove dots

            // Verify Donation Exists
            if ($donation_id > 0) {
                $table_donations = esc_sql($wpdb->prefix . 'wpd_donations');
                $donation = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_donations} WHERE id = %d", $donation_id)); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery

                if (!$donation) {
                    $error = 'ID Donasi tidak ditemukan.';
                } else {
                    // Handle File Upload
                    if (!function_exists('wp_handle_upload')) {
                        require_once(ABSPATH . 'wp-admin/includes/file.php');
                    }

                    $uploadedfile = isset($_FILES['proof_file']) ? $_FILES['proof_file'] : null; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

                    if ($uploadedfile) {
                        $upload_overrides = array('test_form' => false);
                        $movefile = wp_handle_upload($uploadedfile, $upload_overrides);

                        if ($movefile && !isset($movefile['error'])) {
                            $proof_url = $movefile['url'];

                            // Sanitize New Fields
                            $sender_bank = isset($_POST['sender_bank']) ? sanitize_text_field(wp_unslash($_POST['sender_bank'])) : '';
                            $sender_name = isset($_POST['sender_name']) ? sanitize_text_field(wp_unslash($_POST['sender_name'])) : '';

                            // Update Donation Meta
                            $metadata = json_decode($donation->metadata, true);
                            if (!is_array($metadata))
                                $metadata = array();

                            $metadata['proof_url'] = $proof_url;
                            $metadata['sender_bank'] = $sender_bank; // New Field
                            $metadata['sender_name'] = $sender_name; // New Field
                            $metadata['confirmed_at'] = current_time('mysql');
                            $metadata['confirmed_amount'] = $amount; // For verification

                            $wpdb->update(
                                $table_donations,
                                array(
                                    'metadata' => json_encode($metadata),
                                    'status' => 'processing' // Mark as Processing (Enum matches DB)
                                ),
                                array('id' => $donation_id)
                            ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery

                            $success = true;
                        } else {
                            $error = 'Gagal upload file: ' . $movefile['error'];
                        }
                    } else {
                        $error = 'Bukti transfer wajib diupload.';
                    }
                }
            } else {
                $error = 'ID Donasi tidak valid.';
            }
        }
    }


    ob_start();
    include WPD_PLUGIN_PATH . 'frontend/templates/confirmation-form.php';
    return ob_get_clean();
}
add_shortcode('wpd_confirmation_form', 'wpd_shortcode_confirmation_form');

/**
 * Campaign List Shortcode [donasai_campaign_list]
 */
function wpd_shortcode_campaign_list($atts)
{
    wp_enqueue_style('donasai-frontend', WPD_PLUGIN_URL . 'frontend/assets/frontend.css', array(), WPD_VERSION);
    wp_enqueue_style('donasai-campaign-list', WPD_PLUGIN_URL . 'frontend/assets/campaign-list.css', array('donasai-frontend'), WPD_VERSION);

    ob_start();
    include WPD_PLUGIN_PATH . 'frontend/templates/campaign-list.php';
    return ob_get_clean();
}
add_shortcode('donasai_campaign_list', 'wpd_shortcode_campaign_list');
