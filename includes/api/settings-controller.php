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
	return rest_ensure_response( array(
		'bank' => $bank,
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

	return wpd_api_get_settings();
}
