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
function donasai_handle_donation_submission()
{
    if (!isset($_POST['donasai_donate_nonce'])) {
        return;
    }
    $nonce = sanitize_text_field(wp_unslash($_POST['donasai_donate_nonce']));
    if (!wp_verify_nonce($nonce, 'donasai_donate_action')) {
        return;
    }

    if (!isset($_POST['donasai_action'])) {
        return;
    }
    $action = sanitize_text_field(wp_unslash($_POST['donasai_action']));
    if ('submit_donation' !== $action) {
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

    $gateway = DONASAI_Gateway_Registry::get_gateway($gateway_id);

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
        $names = wp_unslash($_POST['qurban_names']); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $metadata['qurban_names'] = array_map('sanitize_text_field', $names);
    }

    // Check for Fundraiser Cookie
    $fundraiser_id = isset($_COOKIE['donasai_ref']) ? intval($_COOKIE['donasai_ref']) : 0;

    // User Handling (Auto-Register if Setting Enabled)
    $user_id = get_current_user_id();
    $donation_settings = get_option('donasai_settings_donation', []);
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

    // Apply Filter for Pro (Subscriptions, etc)
    // Allows Pro to create subscription and return subscription_id in the data
    // Only pass necessary POST data
    $post_data = array();
    if (isset($_POST['recurring_period'])) {
        $post_data['recurring_period'] = sanitize_text_field(wp_unslash($_POST['recurring_period']));
    }
    
    $donation_data = apply_filters('donasai_donation_data_before_insert', array(
        'campaign_id' => $campaign_id,
        'user_id' => $user_id,
        'amount' => $amount,
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'note' => $note,
        'is_anonymous' => $is_anon,
        'fundraiser_id' => $fundraiser_id,
        'subscription_id' => 0, // Default
        'metadata' => json_encode($metadata),
    ), $post_data);

    $result = $gateway->process_payment($donation_data);

    if ($result['success']) {
        // Update Campaign Collected Amount
        donasai_update_campaign_stats($campaign_id);

        // Trigger Created Action (for Emails)
        if (isset($result['donation_id'])) {
            do_action('donasai_donation_created', $result['donation_id']);
        }

        // Update Fundraiser Stats if applicable
        if ($fundraiser_id > 0) {
            $fundraiser_service = new DONASAI_Fundraiser_Service();
            $fundraiser_service->record_donation($fundraiser_id, $amount);
        }

        // Handle AJAX/JSON Response
        if (isset($_POST['donasai_ajax']) && $_POST['donasai_ajax'] == '1') {
            wp_send_json_success($result);
        }

        // Redirect
        if (!empty($result['redirect_url'])) {
            wp_safe_redirect($result['redirect_url']);
            exit;
        }

        // Default Redirect to Thank You Page
        $thankyou_slug = get_option('donasai_settings_general')['thankyou_slug'] ?? 'thank-you';

        $campaign_slug = get_option('donasai_settings_general')['campaign_slug'] ?? 'campaign';
        $post_name = get_post_field('post_name', $campaign_id);

        if ($post_name) {
            // Force Pretty URL Construction: /campaign/post-slug/thank-you/donation-id/
            $base_path = "$campaign_slug/$post_name/$thankyou_slug/" . $result['donation_id'];
            $redirect_url = home_url(user_trailingslashit($base_path));
        } else {
            // Fallback to plain if post_name missing (rare)
            $redirect_url = add_query_arg($thankyou_slug, $result['donation_id'], get_permalink($campaign_id));
        }

        // Add Nonce for security verification on success page
        $redirect_url = add_query_arg('_wpnonce', wp_create_nonce('donasai_payment_success'), $redirect_url);

        wp_safe_redirect($redirect_url);
        exit;
    } else {
        if (isset($_POST['donasai_ajax']) && $_POST['donasai_ajax'] == '1') {
            wp_send_json_error(array('message' => $result['message']));
        }
        wp_die('Payment failed: ' . esc_html($result['message']));
    }
}
add_action('init', 'donasai_handle_donation_submission');

/**
 * Update Campaign Stats (Collected Amount)
 */
function donasai_update_campaign_stats($campaign_id)
{
    global $wpdb;
    $table = esc_sql($wpdb->prefix . 'donasai_donations');

    // Sum only completed donations
    $total = $wpdb->get_var($wpdb->prepare("SELECT SUM(amount) FROM {$table} WHERE campaign_id = %d AND status = 'complete'", $campaign_id)); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery

    // Count Unique Donors (by email) for completed donations
    $donor_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(DISTINCT email) FROM {$table} WHERE campaign_id = %d AND status = 'complete'", $campaign_id)); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery

    update_post_meta($campaign_id, '_donasai_collected_amount', $total);
    update_post_meta($campaign_id, '_donasai_donor_count', $donor_count);
}

/**
 * Get Donation by ID (Cached)
 */
function donasai_get_donation($donation_id)
{
    global $wpdb;
    $donation_id = intval($donation_id);
    if (!$donation_id) {
        return null;
    }

    $cache_key = 'donasai_donation_' . $donation_id;
    $donation = wp_cache_get($cache_key, 'donasai_donations');

    if (false === $donation) {
        $table = esc_sql($wpdb->prefix . 'donasai_donations');
        $donation = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $donation_id)); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        if ($donation) {
            wp_cache_set($cache_key, $donation, 'donasai_donations', 3600);
        }
    }
    return $donation;
}

/**
 * Get Campaign Progress Data
 */
function donasai_get_campaign_progress($campaign_id)
{
    $target = get_post_meta($campaign_id, '_donasai_target_amount', true);
    $collected = get_post_meta($campaign_id, '_donasai_collected_amount', true);

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

