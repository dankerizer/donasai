<?php
/**
 * REST API for Donations
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'rest_api_init', function () {
	// GET /donations
	register_rest_route( 'wpd/v1', '/donations', array(
		'methods'             => 'GET',
		'callback'            => 'wpd_api_get_donations',
		'permission_callback' => function () {
			return current_user_can( 'manage_options' );
		},
	) );

	// POST /donations/{id} (Update Status)
	register_rest_route( 'wpd/v1', '/donations/(?P<id>\d+)', array(
		'methods'             => 'POST',
		'callback'            => 'wpd_api_update_donation_status',
		'permission_callback' => function () {
			return current_user_can( 'manage_options' );
		},
	) );

	// GET /export/donations
	register_rest_route( 'wpd/v1', '/export/donations', array(
		'methods'             => 'GET',
		'callback'            => 'wpd_api_export_donations',
		'permission_callback' => function () {
			return current_user_can( 'manage_options' );
		},
	) );

    // GET /stats
    register_rest_route( 'wpd/v1', '/stats', array(
        'methods'             => 'GET',
        'callback'            => 'wpd_api_get_stats',
        'permission_callback' => function () {
            return current_user_can( 'manage_options' );
        },
    ) );
} );

function wpd_api_get_stats() {
    global $wpdb;
    $table_donations = $wpdb->prefix . 'wpd_donations';
    
    // Total Connected Amount (Completed)
    $total_collected = $wpdb->get_var( "SELECT SUM(amount) FROM $table_donations WHERE status = 'complete'" );
    
    // Total Donors (Unique Emails)
    $total_donors = $wpdb->get_var( "SELECT COUNT(DISTINCT email) FROM $table_donations WHERE status = 'complete'" );
    
    // Active Campaigns
    $active_campaigns = wp_count_posts( 'wpd_campaign' )->publish;

    return rest_ensure_response( array(
        'total_donations' => (float) $total_collected,
        'total_donors'    => (int) $total_donors,
        'active_campaigns' => (int) $active_campaigns
    ) );
}

function wpd_api_export_donations( $request ) {
    global $wpdb;
    $table = $wpdb->prefix . 'wpd_donations';
    $results = $wpdb->get_results( "SELECT * FROM $table ORDER BY created_at DESC", ARRAY_A );

    $filename = 'donations-export-' . date('Y-m-d') . '.csv';
    header( 'Content-Type: text/csv' );
    header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
    header( 'Pragma: no-cache' );
    header( 'Expires: 0' );

    $output = fopen( 'php://output', 'w' );
    
    // Header
    fputcsv( $output, array( 'ID', 'Campaign ID', 'Date', 'Name', 'Email', 'Amount', 'Status', 'Payment Method' ) );

    foreach ( $results as $row ) {
        fputcsv( $output, array(
            $row['id'],
            $row['campaign_id'],
            $row['created_at'],
            $row['name'],
            $row['email'],
            $row['amount'],
            $row['status'],
            $row['payment_method']
        ) );
    }

    fclose( $output );
    exit;
}

function wpd_api_update_donation_status( $request ) {
    global $wpdb;
    $table = $wpdb->prefix . 'wpd_donations';
    $id    = $request['id'];
    $params = $request->get_json_params();
    $status = isset( $params['status'] ) ? sanitize_text_field( $params['status'] ) : '';

    if ( empty( $status ) ) {
        return new WP_Error( 'missing_status', 'Status is required', array( 'status' => 400 ) );
    }

    $updated = $wpdb->update( 
        $table, 
        array( 'status' => $status ), 
        array( 'id' => $id ), 
        array( '%s' ), 
        array( '%d' ) 
    );

    if ( $updated === false ) {
        return new WP_Error( 'db_error', 'Could not update donation', array( 'status' => 500 ) );
    }

    // If status is completed, we might want to ensure campaign stats are correct or re-trigger email? 
    // For now, just simplistic update.
    
    // Also, if status becomes 'complete', we could re-send email if not sent before? 
    // Sprint 4 reqs just say "Mark Complete".
    
    // Check for status change to 'complete'
    if ( 'complete' === $status ) {
        do_action( 'wpd_donation_completed', $id );
        
        // Update Campaign Collected Amount
        $campaign_id = $wpdb->get_var( $wpdb->prepare( "SELECT campaign_id FROM $table WHERE id = %d", $id ) );
        if ( $campaign_id ) {
             // We can reuse the service/function if available, or just duplicte for speed now.
             // Using helper if exists
             if ( function_exists( 'wpd_update_campaign_stats' ) ) {
                 wpd_update_campaign_stats( $campaign_id );
             }
        }
    }

    return rest_ensure_response( array( 'success' => true, 'id' => $id, 'status' => $status ) );
}

function wpd_api_get_donations( $request ) {
	global $wpdb;
	$table = $wpdb->prefix . 'wpd_donations';
	
	$results = $wpdb->get_results( "SELECT * FROM $table ORDER BY created_at DESC" );
	
	// Format data for frontend
	$data = array_map( function( $row ) {
		return array(
			'id'      => $row->id,
			'name'    => $row->name,
			'amount'  => (float) $row->amount,
			'status'  => $row->status,
			'date'    => $row->created_at,
		);
	}, $results );

	return rest_ensure_response( $data );
}
