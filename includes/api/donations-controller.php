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
		'callback'            => 'wpd_api_update_donation',
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
    
    // Check Nonce (as we using directly in href)
    $nonce = isset( $_GET['_wpnonce'] ) ? $_GET['_wpnonce'] : '';
    if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
        // Just die if direct access without valid nonce
        wp_die( 'Invalid nonce' );
    }

    $where = "1=1";
    $args = array();

    if ( isset( $_GET['campaign_id'] ) && !empty( $_GET['campaign_id'] ) ) {
        $ids = array_map( 'intval', explode( ',', $_GET['campaign_id'] ) );
        if ( ! empty( $ids ) ) {
            $placeholders = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
            $where .= " AND campaign_id IN ($placeholders)";
            $args = array_merge( $args, $ids );
        }
    }

    if ( isset( $_GET['status'] ) && !empty( $_GET['status'] ) ) {
        $statuses = array_map( 'sanitize_text_field', explode( ',', $_GET['status'] ) );
        if ( ! empty( $statuses ) ) {
            $placeholders = implode( ',', array_fill( 0, count( $statuses ), '%s' ) );
            $where .= " AND status IN ($placeholders)";
            $args = array_merge( $args, $statuses );
        }
    }

    if ( isset( $_GET['start_date'] ) && !empty( $_GET['start_date'] ) ) {
        $where .= " AND created_at >= %s";
        $args[] = sanitize_text_field( $_GET['start_date'] ) . ' 00:00:00';
    }

    if ( isset( $_GET['end_date'] ) && !empty( $_GET['end_date'] ) ) {
        $where .= " AND created_at <= %s";
        $args[] = sanitize_text_field( $_GET['end_date'] ) . ' 23:59:59';
    }

    if ( ! empty( $args ) ) {
        $query = $wpdb->prepare( "SELECT * FROM $table WHERE $where ORDER BY created_at DESC", $args );
    } else {
        $query = "SELECT * FROM $table ORDER BY created_at DESC";
    }

    $results = $wpdb->get_results( $query, ARRAY_A );

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

function wpd_api_update_donation( $request ) {
    global $wpdb;
    $table = $wpdb->prefix . 'wpd_donations';
    $id    = $request['id'];
    $params = $request->get_json_params();
    
    $data_to_update = array();
    $format = array();

    // Fields allowed to be updated
    $allowed_fields = array( 'status', 'name', 'email', 'phone', 'amount', 'note' );

    foreach ( $allowed_fields as $field ) {
        if ( isset( $params[ $field ] ) ) {
            $value = $params[ $field ];
            if ( 'amount' === $field ) {
                $data_to_update[ $field ] = (float) $value;
                $format[] = '%f';
            } else {
                $data_to_update[ $field ] = sanitize_text_field( $value );
                $format[] = '%s';
            }
        }
    }

    if ( empty( $data_to_update ) ) {
        return new WP_Error( 'no_data', 'No data to update', array( 'status' => 400 ) );
    }

    $updated = $wpdb->update( 
        $table, 
        $data_to_update, 
        array( 'id' => $id ), 
        $format, 
        array( '%d' ) 
    );

    if ( $updated === false ) {
        return new WP_Error( 'db_error', 'Could not update donation', array( 'status' => 500 ) );
    }

    // Check for status change to 'complete' if status was in the payload
    if ( isset( $data_to_update['status'] ) && 'complete' === $data_to_update['status'] ) {
        do_action( 'wpd_donation_completed', $id );
        
        // Update Campaign Collected Amount
        $campaign_id = $wpdb->get_var( $wpdb->prepare( "SELECT campaign_id FROM $table WHERE id = %d", $id ) );
        if ( $campaign_id ) {
             if ( function_exists( 'wpd_update_campaign_stats' ) ) {
                 wpd_update_campaign_stats( $campaign_id );
             }
        }
    }

    // Return updated data
    $updated_row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $id ) );
    
    return rest_ensure_response( array( 
        'success' => true, 
        'id'      => $id, 
        'message' => 'Donation updated',
        'data'    => array(
			'id'              => $updated_row->id,
			'name'            => $updated_row->name,
            'email'           => $updated_row->email,
            'phone'           => $updated_row->phone,
			'amount'          => (float) $updated_row->amount,
			'status'          => $updated_row->status,
            'payment_method'  => $updated_row->payment_method,
            'gateway_txn_id'  => $updated_row->gateway_txn_id,
            'note'            => $updated_row->note,
			'date'            => $updated_row->created_at,
        )
    ) );
}

function wpd_api_get_donations( $request ) {
	global $wpdb;
	$table = $wpdb->prefix . 'wpd_donations';
	
	$results = $wpdb->get_results( "SELECT * FROM $table ORDER BY created_at DESC" );
	
	// Format data for frontend
	$data = array_map( function( $row ) {
		return array(
			'id'              => $row->id,
			'name'            => $row->name,
            'email'           => $row->email,
            'phone'           => $row->phone,
			'amount'          => (float) $row->amount,
			'status'          => $row->status,
            'payment_method'  => $row->payment_method,
            'gateway_txn_id'  => $row->gateway_txn_id,
            'note'            => $row->note,
            'metadata'        => json_decode( $row->metadata, true ),
			'date'            => $row->created_at,
		);
	}, $results );

	return rest_ensure_response( $data );
}
