<?php
/**
 * Settings API Controller
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'rest_api_init', function () {
	register_rest_route( 'wpd/v1', '/settings', array(
		'methods'             => 'GET',
		'callback'            => 'wpd_api_get_settings',
		'permission_callback' => 'wpd_api_settings_permission',
	) );

	register_rest_route( 'wpd/v1', '/settings', array(
		'methods'             => 'POST',
		'callback'            => 'wpd_api_update_settings',
		'permission_callback' => 'wpd_api_settings_permission',
	) );
} );

function wpd_api_settings_permission() {
	return current_user_can( 'manage_options' );
}

function wpd_api_get_settings() {
	$bank = get_option( 'wpd_settings_bank', array( 'bank_name' => '', 'account_number' => '', 'account_name' => '' ) );
	$midtrans = get_option( 'wpd_settings_midtrans', array( 'enabled' => false, 'is_production' => false, 'server_key' => '' ) );
	$license  = get_option( 'wpd_license', array( 'key' => '', 'status' => 'inactive' ) );
    
	$organization = get_option( 'wpd_settings_organization', array( 'org_name' => '', 'org_address' => '', 'org_phone' => '', 'org_email' => '', 'org_logo' => '' ) );
    $notifications = get_option( 'wpd_settings_notifications', array( 'opt_in_email' => get_option('admin_email'), 'opt_in_whatsapp' => '' ) );
    
    // New Settings
    $general = get_option( 'wpd_settings_general', array( 'campaign_slug' => 'campaign', 'payment_slug' => 'pay', 'remove_branding' => false ) );
    $donation = get_option( 'wpd_settings_donation', array( 'min_amount' => 10000, 'presets' => '50000,100000,200000,500000', 'anonymous_label' => 'Hamba Allah', 'create_user' => false ) );

	return rest_ensure_response( array(
		'bank'         => $bank,
		'midtrans'     => $midtrans,
        'license'      => $license,
        'organization' => $organization,
        'notifications'=> $notifications,
        'general'      => $general,
        'donation'     => $donation
	) );
}

function wpd_api_update_settings( $request ) {
	$params = $request->get_json_params();

	if ( isset( $params['bank'] ) ) {
		$bank_data = array(
			'bank_name'      => sanitize_text_field( $params['bank']['bank_name'] ?? '' ),
			'account_number' => sanitize_text_field( $params['bank']['account_number'] ?? '' ),
			'account_name'   => sanitize_text_field( $params['bank']['account_name'] ?? '' ),
		);
		update_option( 'wpd_settings_bank', $bank_data );
	}

	if ( isset( $params['midtrans'] ) ) {
		$mid_data = array(
			'enabled'       => ! empty( $params['midtrans']['enabled'] ),
			'is_production' => ! empty( $params['midtrans']['is_production'] ),
			'server_key'    => sanitize_text_field( $params['midtrans']['server_key'] ?? '' ),
		);
		update_option( 'wpd_settings_midtrans', $mid_data );
	}

	if ( isset( $params['organization'] ) ) {
		$org_data = array(
			'org_name'    => sanitize_text_field( $params['organization']['org_name'] ?? '' ),
			'org_address' => sanitize_textarea_field( $params['organization']['org_address'] ?? '' ),
			'org_phone'   => sanitize_text_field( $params['organization']['org_phone'] ?? '' ),
			'org_email'   => sanitize_email( $params['organization']['org_email'] ?? '' ),
			'org_logo'    => esc_url_raw( $params['organization']['org_logo'] ?? '' ),
		);
		update_option( 'wpd_settings_organization', $org_data );
	}

    if ( isset( $params['general'] ) ) {
        $gen_data = array(
            'campaign_slug'   => sanitize_title( $params['general']['campaign_slug'] ?? 'campaign' ),
            'payment_slug'    => sanitize_title( $params['general']['payment_slug'] ?? 'pay' ),
            'remove_branding' => ! empty( $params['general']['remove_branding'] ), // Pro
        );
        
        // Check if slugs changed, flush rewrite rules might be needed (handled via option update hook or manual flush hint)
        $old_gen = get_option('wpd_settings_general', []);
        if ( ($old_gen['campaign_slug'] ?? '') !== $gen_data['campaign_slug'] || ($old_gen['payment_slug'] ?? '') !== $gen_data['payment_slug'] ) {
            update_option( 'wpd_rewrite_flush_needed', true );
        }

        update_option( 'wpd_settings_general', $gen_data );
    }

    if ( isset( $params['donation'] ) ) {
        $don_data = array(
            'min_amount'      => intval( $params['donation']['min_amount'] ?? 10000 ),
            'presets'         => sanitize_text_field( $params['donation']['presets'] ?? '50000,100000,200000,500000' ),
            'anonymous_label' => sanitize_text_field( $params['donation']['anonymous_label'] ?? 'Hamba Allah' ),
            'create_user'     => ! empty( $params['donation']['create_user'] ), // Pro
        );
        update_option( 'wpd_settings_donation', $don_data );
    }

    if ( isset( $params['notifications'] ) ) {
        $notif_data = array(
            'opt_in_email'    => sanitize_email( $params['notifications']['opt_in_email'] ?? '' ),
            'opt_in_whatsapp' => sanitize_text_field( $params['notifications']['opt_in_whatsapp'] ?? '' ),
        );
        update_option( 'wpd_settings_notifications', $notif_data );
    }

    if ( isset( $params['license'] ) ) {
        $key = sanitize_text_field( $params['license']['key'] ?? '' );
        // Simple Stub Validation
        $status = ( strpos( $key, 'PRO-' ) === 0 ) ? 'active' : 'inactive';
        
        update_option( 'wpd_license', array( 'key' => $key, 'status' => $status ) );
    }

	return wpd_api_get_settings();
}
