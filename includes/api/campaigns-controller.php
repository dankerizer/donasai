<?php
/**
 * Campaigns API Controller
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'rest_api_init', function () {
	// POST /campaigns/{id}/donate
	register_rest_route( 'wpd/v1', '/campaigns/(?P<id>\d+)/donate', array(
		'methods'             => 'POST',
		'callback'            => 'wpd_api_create_donation',
		'permission_callback' => '__return_true', // Public endpoint
	) );
	// GET /campaigns/list (For Dropdowns)
	register_rest_route( 'wpd/v1', '/campaigns/list', array(
		'methods'             => 'GET',
		'callback'            => 'wpd_api_get_campaigns_list',
		'permission_callback' => function () {
            // Allow logged in users with capability (e.g. admins/donors?) 
            // For now restricted to manage_options for admin usage
			return current_user_can( 'manage_options' );
		},
	) );
} );

function wpd_api_get_campaigns_list() {
    $args = array(
        'post_type'      => 'wpd_campaign',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids' 
    );
    $query = new WP_Query( $args );
    
    $campaigns = array();
    if ( $query->have_posts() ) {
        foreach ( $query->posts as $id ) {
            $campaigns[] = array(
                'id'    => $id,
                'title' => get_the_title( $id )
            );
        }
    }
    
    return rest_ensure_response( $campaigns );
}

function wpd_api_create_donation( $request ) {
	global $wpdb;
	$campaign_id = $request['id'];
	$params      = $request->get_json_params();

	// Validation
	$amount = isset( $params['amount'] ) ? (float) $params['amount'] : 0;
	$name   = isset( $params['name'] ) ? sanitize_text_field( $params['name'] ) : '';
	$email  = isset( $params['email'] ) ? sanitize_email( $params['email'] ) : '';
	$phone  = isset( $params['phone'] ) ? sanitize_text_field( $params['phone'] ) : '';
	$method = isset( $params['payment_method'] ) ? sanitize_text_field( $params['payment_method'] ) : 'manual';
    $is_recurring = ! empty( $params['is_recurring'] );

	if ( $amount <= 0 ) {
		return new WP_Error( 'invalid_amount', 'Amount must be greater than 0', array( 'status' => 400 ) );
	}
	if ( empty( $name ) || empty( $email ) ) {
		return new WP_Error( 'missing_fields', 'Name and Email are required', array( 'status' => 400 ) );
	}

    // 1. Handle Recurring Subscription
    $subscription_id = 0;
    if ( $is_recurring && is_user_logged_in() ) {
        $sub_service = new WPD_Subscription_Service();
        $subscription_id = $sub_service->create_subscription( 
            get_current_user_id(), 
            $campaign_id, 
            $amount, 
            'monthly' // Default to monthly for now
        );
    }

	// 2. Insert Donation
	$table_donations = $wpdb->prefix . 'wpd_donations';
	$data = array(
		'campaign_id'    => $campaign_id,
		'user_id'        => get_current_user_id() ? get_current_user_id() : null,
		'name'           => $name,
		'email'          => $email,
		'phone'          => $phone,
		'amount'         => $amount,
		'payment_method' => $method,
		'status'         => 'pending',
        'subscription_id'=> $subscription_id,
        'is_anonymous'   => ! empty( $params['is_anonymous'] ) ? 1 : 0,
        'fundraiser_id'  => ! empty( $params['fundraiser_id'] ) ? intval( $params['fundraiser_id'] ) : 0,
		'created_at'     => current_time( 'mysql' ),
	);

	$inserted = $wpdb->insert( $table_donations, $data );

	if ( ! $inserted ) {
		return new WP_Error( 'db_error', 'Failed to create donation', array( 'status' => 500 ) );
	}

	$donation_id = $wpdb->insert_id;

	// 3. Process Payment Gateway
	$gateway = WPD_Gateway_Registry::get_gateway( $method );
    
    // Trigger Action
    do_action( 'wpd_donation_created', $donation_id );

	if ( $gateway ) {
		$result = $gateway->process_payment( array(
			'donation_id' => $donation_id,
			'amount'      => $amount,
			'name'        => $name,
			'email'       => $email,
            'campaign_id' => $campaign_id,
		) );

		return rest_ensure_response( $result );
	}

	return rest_ensure_response( array(
		'success'     => true,
		'donation_id' => $donation_id,
		'message'     => 'Donation created (Manual)',
	) );
}
