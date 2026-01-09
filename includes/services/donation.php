<?php
/**
 * Donation Service Logic
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handle Donation Form Submission
 */
function wpd_handle_donation_submission()
{
    // DEBUG LOG
    if (isset($_POST['wpd_action'])) {
        $log = "Action: " . sanitize_text_field(wp_unslash($_POST['wpd_action'])) . "\n";
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
        $log .= "Nonce Check: " . (wp_verify_nonce($_POST['wpd_donate_nonce'], 'wpd_donate_action') ? 'PASS' : 'FAIL') . "\n";
        file_put_contents(WP_CONTENT_DIR . '/wpd-debug.log', $log, FILE_APPEND);
    }

    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
    if (!isset($_POST['wpd_donate_nonce']) || !wp_verify_nonce($_POST['wpd_donate_nonce'], 'wpd_donate_action')) {
        return;
    }

    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
    if (!isset($_POST['wpd_action']) || 'submit_donation' !== $_POST['wpd_action']) {
        return;
    }

    // Validate inputs
    $campaign_id = isset($_POST['campaign_id']) ? intval($_POST['campaign_id']) : 0;
    $amount = isset($_POST['amount']) ? str_replace('.', '', sanitize_text_field(wp_unslash($_POST['amount']))) : 0;
    $amount = floatval($amount);
    $name = isset($_POST['donor_name']) ? sanitize_text_field(wp_unslash($_POST['donor_name'])) : '';
    $email = isset($_POST['donor_email']) ? sanitize_email(wp_unslash($_POST['donor_email'])) : '';
    $phone = isset($_POST['donor_phone']) ? sanitize_text_field(wp_unslash($_POST['donor_phone'])) : '';
    $note = isset($_POST['donor_note']) ? sanitize_textarea_field(wp_unslash($_POST['donor_note'])) : '';
    $is_anon = isset($_POST['is_anonymous']) ? 1 : 0;

    if ($amount <= 0 || empty($name) || empty($email)) {
        wp_die('Please provide valid amount, name, and email.');
    }

    if ($amount <= 0 || empty($name) || empty($email)) {
        wp_die('Please provide valid amount, name, and email.');
    }

    // Get Gateway
    $gateway_id = isset($_POST['payment_method']) ? sanitize_text_field(wp_unslash($_POST['payment_method'])) : 'manual';

    // Check for specific bank selection (e.g. manual_123)
    $selected_bank_id = null;
    if (strpos($gateway_id, 'manual_') === 0) {
        $parts = explode('_', $gateway_id);
        if (count($parts) > 1) {
            $selected_bank_id = $parts[1]; // The ID
            $gateway_id = 'manual'; // Reset to manual gateway
        }
    }

    $gateway = WPD_Gateway_Registry::get_gateway($gateway_id);

    if (!$gateway) {
        wp_die('Invalid payment method.');
    }

    // Capture Metadata
    $metadata = [];
    if ($selected_bank_id) {
        $metadata['selected_bank'] = sanitize_text_field($selected_bank_id);
    }
    if (isset($_POST['qurban_package'])) {
        $metadata['qurban_package'] = sanitize_text_field(wp_unslash($_POST['qurban_package']));
    }
    if (isset($_POST['qurban_qty'])) {
        $metadata['qurban_qty'] = intval($_POST['qurban_qty']);
    }
    if (isset($_POST['qurban_names']) && is_array($_POST['qurban_names'])) {
        $names = wp_unslash($_POST['qurban_names']);
        $metadata['qurban_names'] = array_map('sanitize_text_field', $names);
    }

    // Check for Fundraiser Cookie
    $fundraiser_id = isset($_COOKIE['wpd_ref']) ? intval($_COOKIE['wpd_ref']) : 0;

    // User Handling (Auto-Register if Setting Enabled)
    $user_id = get_current_user_id();
    $donation_settings = get_option('wpd_settings_donation', []);
    $should_create_user = isset($donation_settings['create_user']) && ($donation_settings['create_user'] == '1' || $donation_settings['create_user'] === true);

    if (!$user_id && $should_create_user && email_exists($email)) {
        $user_id = email_exists($email);
    } elseif (!$user_id && $should_create_user) {
        $password = wp_generate_password(12, false);
        $new_user_id = wp_create_user($email, $password, $email);

        if (!is_wp_error($new_user_id)) {
            $user_id = $new_user_id;
            wp_update_user([
                'ID' => $user_id,
                'display_name' => $name,
                'nickname' => $name
            ]);

            // Send Login Details
            wp_new_user_notification($user_id, null, 'user');
        }
    }

    // Check Subscription
    $subscription_id = 0;
    if (isset($_POST['is_recurring']) && $_POST['is_recurring'] == 1 && $user_id > 0) {
        $sub_service = new WPD_Subscription_Service();
        $subscription_id = $sub_service->create_subscription(
            $user_id,
            $campaign_id,
            $amount,
            isset($_POST['recurring_interval']) ? sanitize_text_field(wp_unslash($_POST['recurring_interval'])) : 'month'
        );
    }

    // Process Payment
    $donation_data = array(
        'campaign_id' => $campaign_id,
        'user_id' => $user_id,
        'amount' => $amount,
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'note' => $note,
        'is_anonymous' => $is_anon,
        'fundraiser_id' => $fundraiser_id,
        'subscription_id' => $subscription_id,
        'metadata' => json_encode($metadata),
    );

    $result = $gateway->process_payment($donation_data);

    if ($result['success']) {
        // Update Campaign Collected Amount
        wpd_update_campaign_stats($campaign_id);

        // Trigger Created Action (for Emails)
        if (isset($result['donation_id'])) {
            do_action('wpd_donation_created', $result['donation_id']);
        }

        // Update Fundraiser Stats if applicable
        if ($fundraiser_id > 0) {
            $fundraiser_service = new WPD_Fundraiser_Service();
            $fundraiser_service->record_donation($fundraiser_id, $amount);
        }

        // Handle AJAX/JSON Response
        if (isset($_POST['wpd_ajax']) && $_POST['wpd_ajax'] == '1') {
            wp_send_json_success($result);
        }

        // Redirect
        if (!empty($result['redirect_url'])) {
            wp_safe_redirect($result['redirect_url']);
            exit;
        }

        // Default Redirect to Thank You Page
        $thankyou_slug = get_option('wpd_settings_general')['thankyou_slug'] ?? 'thank-you';

        $campaign_slug = get_option('wpd_settings_general')['campaign_slug'] ?? 'campaign';
        $post_name = get_post_field('post_name', $campaign_id);

        if ($post_name) {
            // Force Pretty URL Construction: /campaign/post-slug/thank-you/donation-id/
            $base_path = "$campaign_slug/$post_name/$thankyou_slug/" . $result['donation_id'];
            $redirect_url = home_url(user_trailingslashit($base_path));
        } else {
            // Fallback to plain if post_name missing (rare)
            $redirect_url = add_query_arg($thankyou_slug, $result['donation_id'], get_permalink($campaign_id));
        }

        wp_safe_redirect($redirect_url);
        exit;
    } else {
        if (isset($_POST['wpd_ajax']) && $_POST['wpd_ajax'] == '1') {
            wp_send_json_error(array('message' => $result['message']));
        }
        wp_die('Payment failed: ' . esc_html($result['message']));
    }
}
add_action('init', 'wpd_handle_donation_submission');

/**
 * Update Campaign Stats (Collected Amount)
 */
function wpd_update_campaign_stats($campaign_id)
{
    global $wpdb;
    $table = $wpdb->prefix . 'wpd_donations';

    // Sum only completed (or pending for offline/testing) donations
    // For MVP: let's include 'pending' as 'collected' for offline demo purposes? 
    // Or strictly 'complete'. Let's stick to 'complete' usually, but for Offline, usually admin marks it complete.
    // But to show progress immediately in demo, maybe we can optionally count pending.
    // Let's stick to standard: only 'complete' counts for progress.

    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
    $total = $wpdb->get_var($wpdb->prepare("SELECT SUM(amount) FROM $table WHERE campaign_id = %d AND status = 'complete'", $campaign_id));

    // Count Unique Donors (by email) for completed donations
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
    $donor_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(DISTINCT email) FROM $table WHERE campaign_id = %d AND status = 'complete'", $campaign_id));

    update_post_meta($campaign_id, '_wpd_collected_amount', $total);
    update_post_meta($campaign_id, '_wpd_donor_count', $donor_count);
}

/**
 * Get Campaign Progress Data
 */
function wpd_get_campaign_progress($campaign_id)
{
    $target = get_post_meta($campaign_id, '_wpd_target_amount', true);
    $collected = get_post_meta($campaign_id, '_wpd_collected_amount', true);

    if (!$target)
        $target = 0;
    if (!$collected)
        $collected = 0;

    $percentage = $target > 0 ? ($collected / $target) * 100 : 0;
    $percentage = min(100, max(0, $percentage)); // Clamp between 0-100

    return array(
        'target' => $target,
        'collected' => $collected,
        'percentage' => $percentage,
    );
}
