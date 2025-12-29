<?php
/**
 * Midtrans Webhook Controller
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'rest_api_init', function () {
	// POST /wpd/v1/midtrans/webhook
	register_rest_route( 'wpd/v1', '/midtrans/webhook', array(
		'methods'             => 'POST',
		'callback'            => 'wpd_api_midtrans_webhook',
		'permission_callback' => '__return_true', // Public endpoint
	) );
} );

function wpd_api_midtrans_webhook( $request ) {
    $params = $request->get_json_params();
    
    // Log for debugging
    // if ( defined('WP_DEBUG') && WP_DEBUG ) {
    //     error_log( 'Midtrans Webhook: ' . print_r( $params, true ) );
    // }

    $gateway = new WPD_Gateway_Midtrans();
    $result = $gateway->handle_webhook( $params );

    if ( is_wp_error( $result ) ) {
        return $result;
    }

    return rest_ensure_response( array( 'success' => true ) );
}
