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
    
	return rest_ensure_response( array(
		'bank'     => $bank,
		'midtrans' => $midtrans,
        'license'  => $license,
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

    if ( isset( $params['license'] ) ) {
        $key = sanitize_text_field( $params['license']['key'] ?? '' );
        // Simple Stub Validation
        $status = ( strpos( $key, 'PRO-' ) === 0 ) ? 'active' : 'inactive';
        
        update_option( 'wpd_license', array( 'key' => $key, 'status' => $status ) );
    }

	return wpd_api_get_settings();
}
