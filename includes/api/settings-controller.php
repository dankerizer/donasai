<?php
/**
 * Settings API Controller
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('rest_api_init', function () {
    register_rest_route('wpd/v1', '/settings', array(
        'methods' => 'GET',
        'callback' => 'wpd_api_get_settings',
        'permission_callback' => 'wpd_api_settings_permission',
    ));

    register_rest_route('wpd/v1', '/settings', array(
        'methods' => 'POST',
        'callback' => 'wpd_api_update_settings',
        'permission_callback' => 'wpd_api_settings_permission',
    ));
});

function wpd_api_settings_permission()
{
    return current_user_can('manage_options');
}

function wpd_api_get_settings()
{
    $bank = get_option('wpd_settings_bank', array('bank_name' => '', 'account_number' => '', 'account_name' => ''));
    $midtrans = get_option('wpd_settings_midtrans', array('enabled' => false, 'is_production' => false, 'server_key' => ''));
    $xendit = array('api_key' => '');
    $tripay = array('api_key' => '', 'private_key' => '', 'merchant_code' => '', 'is_production' => false);
    $license = get_option('wpd_license', array('key' => '', 'status' => 'inactive'));

    // Check real Pro License from Pro Plugin if available
    if (defined('WPD_PRO_VERSION')) {
        $real_license_status = get_option('wpd_pro_license_status');
        if ($real_license_status === 'valid') {
            $license['status'] = 'active';
            $license['key'] = get_option('wpd_pro_license_key', '');
        }
    }

    $organization = get_option('wpd_settings_organization', array('org_name' => '', 'org_address' => '', 'org_phone' => '', 'org_email' => '', 'org_logo' => ''));
    $notifications = get_option('wpd_settings_notifications', array('opt_in_email' => get_option('admin_email'), 'opt_in_whatsapp' => ''));

    // New Settings
    $general = get_option('wpd_settings_general', array(
        'campaign_slug' => 'campaign',
        'payment_slug' => 'pay',
        'remove_branding' => true,
        'confirmation_page' => '',
        'delete_on_uninstall_settings' => false,
        'delete_on_uninstall_tables' => false
    ));
    $donation = get_option('wpd_settings_donation', array('min_amount' => 10000, 'presets' => '50000,100000,200000,500000', 'anonymous_label' => 'Hamba Allah', 'create_user' => false, 'preset_emoji' => '💖'));
    $appearance = get_option('wpd_settings_appearance', array(
        'brand_color' => '#059669',
        'button_color' => '#ec4899',
        'container_width' => '1100px',
        'border_radius' => '12px',
        'campaign_layout' => 'sidebar-right', // sidebar-right, sidebar-left, full-width
        'hero_style' => 'standard', // standard, wide, overlay
        'font_family' => 'Inter', // Inter, Roboto, Open Sans, Poppins, Lato
        'font_size' => '16px',
        'sidebar_count' => 5, // donors in sidebar
        'donor_per_page' => 10,
        'donation_layout' => 'default', // default, split
        'dark_mode' => false,
        'custom_css' => '',
        'show_countdown' => true,
        'show_prayer_tab' => true,
        'show_updates_tab' => true,
        'show_donor_list' => true
    ));

    // Pro Settings (Midtrans Override)
    if ($license['status'] === 'active') {
        $pro_server = get_option('wpd_pro_midtrans_server_key');
        $pro_client = get_option('wpd_pro_midtrans_client_key');
        $pro_is_prod = get_option('wpd_pro_midtrans_is_production');
        $pro_intervals = get_option('wpd_pro_recurring_intervals', ['month', 'year']);

        if ($pro_server)
            $midtrans['pro_server_key'] = $pro_server;
        if ($pro_client)
            $midtrans['pro_client_key'] = $pro_client;
        if ($pro_is_prod)
            $midtrans['pro_is_production'] = ($pro_is_prod == '1');

        $donation['recurring_intervals'] = $pro_intervals;

        $bank['pro_accounts'] = get_option('wpd_pro_bank_accounts', []);

        $xendit['api_key'] = get_option('wpd_pro_xendit_api_key', '');

        $tripay = array(
            'api_key' => get_option('wpd_pro_tripay_api_key', ''),
            'private_key' => get_option('wpd_pro_tripay_private_key', ''),
            'merchant_code' => get_option('wpd_pro_tripay_merchant_code', ''),
            'is_production' => get_option('wpd_pro_tripay_is_production') == '1'
        );
    }

    // Get all pages for dropdown
    $pages = get_pages();
    $pages_list = array();
    foreach ($pages as $page) {
        $pages_list[] = array(
            'id' => $page->ID,
            'title' => $page->post_title
        );
    }

    return rest_ensure_response(array(
        'bank' => $bank,
        'midtrans' => $midtrans,
        'xendit' => $xendit,
        'tripay' => $tripay,
        'license' => $license,
        'organization' => $organization,
        'notifications' => $notifications,
        'general' => $general,
        'donation' => $donation,
        'appearance' => $appearance,
        'pages' => $pages_list, // Return pages list
        'is_pro_installed' => defined('WPD_PRO_VERSION')
    ));
}

function wpd_api_update_settings($request)
{
    $params = $request->get_json_params();

    if (isset($params['bank'])) {
        $bank_data = array(
            'bank_name' => sanitize_text_field($params['bank']['bank_name'] ?? ''),
            'account_number' => sanitize_text_field($params['bank']['account_number'] ?? ''),
            'account_name' => sanitize_text_field($params['bank']['account_name'] ?? ''),
        );
        update_option('wpd_settings_bank', $bank_data);

        if (isset($params['bank']['pro_accounts']) && is_array($params['bank']['pro_accounts'])) {
            $accounts = [];
            foreach ($params['bank']['pro_accounts'] as $acc) {
                $accounts[] = array(
                    'id' => isset($acc['id']) ? sanitize_text_field($acc['id']) : uniqid(),
                    'bank_name' => sanitize_text_field($acc['bank_name'] ?? ''),
                    'account_number' => sanitize_text_field($acc['account_number'] ?? ''),
                    'account_name' => sanitize_text_field($acc['account_name'] ?? ''),
                    'is_default' => !empty($acc['is_default']),
                );
            }
            update_option('wpd_pro_bank_accounts', $accounts);
        }
    }

    if (isset($params['midtrans'])) {
        $mid_data = array(
            'enabled' => !empty($params['midtrans']['enabled']),
            'is_production' => !empty($params['midtrans']['is_production']),
            'server_key' => sanitize_text_field($params['midtrans']['server_key'] ?? ''),
        );
        update_option('wpd_settings_midtrans', $mid_data);

        // Save Pro fields if present
        if (isset($params['midtrans']['pro_server_key']))
            update_option('wpd_pro_midtrans_server_key', sanitize_text_field($params['midtrans']['pro_server_key']));
        if (isset($params['midtrans']['pro_client_key']))
            update_option('wpd_pro_midtrans_client_key', sanitize_text_field($params['midtrans']['pro_client_key']));
        if (isset($params['midtrans']['pro_is_production']))
            update_option('wpd_pro_midtrans_is_production', !empty($params['midtrans']['pro_is_production']) ? '1' : '0');
    }

    if (isset($params['xendit'])) {
        if (isset($params['xendit']['api_key'])) {
            update_option('wpd_pro_xendit_api_key', sanitize_text_field($params['xendit']['api_key']));
        }
    }

    if (isset($params['tripay'])) {
        update_option('wpd_pro_tripay_api_key', sanitize_text_field($params['tripay']['api_key'] ?? ''));
        update_option('wpd_pro_tripay_private_key', sanitize_text_field($params['tripay']['private_key'] ?? ''));
        update_option('wpd_pro_tripay_merchant_code', sanitize_text_field($params['tripay']['merchant_code'] ?? ''));
        update_option('wpd_pro_tripay_is_production', !empty($params['tripay']['is_production']) ? '1' : '0');
    }

    if (isset($params['organization'])) {
        $org_data = array(
            'org_name' => sanitize_text_field($params['organization']['org_name'] ?? ''),
            'org_address' => sanitize_textarea_field($params['organization']['org_address'] ?? ''),
            'org_phone' => sanitize_text_field($params['organization']['org_phone'] ?? ''),
            'org_email' => sanitize_email($params['organization']['org_email'] ?? ''),
            'org_logo' => esc_url_raw($params['organization']['org_logo'] ?? ''),
        );
        update_option('wpd_settings_organization', $org_data);
    }

    if (isset($params['general'])) {
        $gen_data = array(
            'campaign_slug' => sanitize_title($params['general']['campaign_slug'] ?? 'campaign'),
            'payment_slug' => sanitize_title($params['general']['payment_slug'] ?? 'pay'),
            'remove_branding' => !empty($params['general']['remove_branding']),
            'confirmation_page' => isset($params['general']['confirmation_page']) ? intval($params['general']['confirmation_page']) : '',
            'delete_on_uninstall_settings' => !empty($params['general']['delete_on_uninstall_settings']),
            'delete_on_uninstall_tables' => !empty($params['general']['delete_on_uninstall_tables']),
        );

        // Check if slugs changed, flush rewrite rules might be needed (handled via option update hook or manual flush hint)
        $old_gen = get_option('wpd_settings_general', []);
        if (($old_gen['campaign_slug'] ?? '') !== $gen_data['campaign_slug'] || ($old_gen['payment_slug'] ?? '') !== $gen_data['payment_slug']) {
            update_option('wpd_rewrite_flush_needed', true);
        }

        update_option('wpd_settings_general', $gen_data);
    }

    if (isset($params['donation'])) {
        $don_data = array(
            'min_amount' => intval($params['donation']['min_amount'] ?? 10000),
            'presets' => sanitize_text_field($params['donation']['presets'] ?? '50000,100000,200000,500000'),
            'preset_emoji' => sanitize_text_field($params['donation']['preset_emoji'] ?? '💖'),
            'anonymous_label' => sanitize_text_field($params['donation']['anonymous_label'] ?? 'Hamba Allah'),
            'create_user' => !empty($params['donation']['create_user']), // Pro
        );
        update_option('wpd_settings_donation', $don_data);

        if (isset($params['donation']['recurring_intervals']) && is_array($params['donation']['recurring_intervals'])) {
            update_option('wpd_pro_recurring_intervals', array_map('sanitize_text_field', $params['donation']['recurring_intervals']));
        }
    }

    if (isset($params['notifications'])) {
        $notif_data = array(
            'opt_in_email' => sanitize_email($params['notifications']['opt_in_email'] ?? ''),
            'opt_in_whatsapp' => sanitize_text_field($params['notifications']['opt_in_whatsapp'] ?? ''),
        );
        update_option('wpd_settings_notifications', $notif_data);
    }

    if (isset($params['appearance'])) {
        $appearance_data = array(
            'brand_color' => sanitize_hex_color($params['appearance']['brand_color'] ?? '#059669'),
            'button_color' => sanitize_hex_color($params['appearance']['button_color'] ?? '#ec4899'),
            'container_width' => sanitize_text_field($params['appearance']['container_width'] ?? '1100px'),
            'border_radius' => sanitize_text_field($params['appearance']['border_radius'] ?? '12px'),
            'campaign_layout' => sanitize_text_field($params['appearance']['campaign_layout'] ?? 'sidebar-right'),
            'hero_style' => sanitize_text_field($params['appearance']['hero_style'] ?? 'standard'),
            'font_family' => sanitize_text_field($params['appearance']['font_family'] ?? 'Inter'),
            'font_size' => sanitize_text_field($params['appearance']['font_size'] ?? '16px'),
            'dark_mode' => $params['appearance']['dark_mode'] ?? false,
            'donation_layout' => sanitize_text_field($params['appearance']['donation_layout'] ?? 'default'),
            'sidebar_count' => intval($params['appearance']['sidebar_count'] ?? 5),
            'donor_per_page' => intval($params['appearance']['donor_per_page'] ?? 10),
            'show_countdown' => !empty($params['appearance']['show_countdown']),
            'show_prayer_tab' => !empty($params['appearance']['show_prayer_tab']),
            'show_updates_tab' => !empty($params['appearance']['show_updates_tab']),
            'show_donor_list' => !empty($params['appearance']['show_donor_list']),
        );
        update_option('wpd_settings_appearance', $appearance_data);
    }

    if (isset($params['license'])) {
        $key = sanitize_text_field($params['license']['key'] ?? '');

        // If Pro Plugin is active, use its real validation stub
        if (defined('WPD_PRO_VERSION')) {
            update_option('wpd_pro_license_key', $key);
            // Simulate Validation for now (or call Pro class)
            if (!empty($key)) {
                update_option('wpd_pro_license_status', 'valid');
                $status = 'active';
            } else {
                update_option('wpd_pro_license_status', 'invalid');
                $status = 'inactive';
            }
        } else {
            // Legacy Stub from Free
            $status = (strpos($key, 'PRO-') === 0) ? 'active' : 'inactive';
            update_option('wpd_license', array('key' => $key, 'status' => $status));
        }
    }

    return wpd_api_get_settings();
}
