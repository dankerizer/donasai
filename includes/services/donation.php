<?php
/**
 * Donation Service Logic
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle Donation Form Submission
 */
function wpd_handle_donation_submission() {
	if ( ! isset( $_POST['wpd_donate_nonce'] ) || ! wp_verify_nonce( $_POST['wpd_donate_nonce'], 'wpd_donate_action' ) ) {
		return;
	}

	if ( ! isset( $_POST['wpd_action'] ) || 'submit_donation' !== $_POST['wpd_action'] ) {
		return;
	}

	// Validate inputs
	$campaign_id = isset( $_POST['campaign_id'] ) ? intval( $_POST['campaign_id'] ) : 0;
	$amount      = isset( $_POST['amount'] ) ? str_replace( '.', '', sanitize_text_field( $_POST['amount'] ) ) : 0;
	$amount      = floatval( $amount );
	$name        = isset( $_POST['donor_name'] ) ? sanitize_text_field( $_POST['donor_name'] ) : '';
	$email       = isset( $_POST['donor_email'] ) ? sanitize_email( $_POST['donor_email'] ) : '';
	$phone       = isset( $_POST['donor_phone'] ) ? sanitize_text_field( $_POST['donor_phone'] ) : '';
	$note        = isset( $_POST['donor_note'] ) ? sanitize_textarea_field( $_POST['donor_note'] ) : '';
	$is_anon     = isset( $_POST['is_anonymous'] ) ? 1 : 0;

	if ( $amount <= 0 || empty( $name ) || empty( $email ) ) {
		wp_die( 'Please provide valid amount, name, and email.' );
	}

	// Manual Payment Only (Free Version)
    $gateway_id = 'manual';

	global $wpdb;
	$table_donations = $wpdb->prefix . 'wpd_donations';

	$data = array(
		'campaign_id'    => $campaign_id,
		'user_id'        => get_current_user_id() ? get_current_user_id() : null,
		'name'           => $name,
		'email'          => $email,
		'phone'          => $phone,
		'amount'         => $amount,
		'currency'       => 'IDR',
		'payment_method' => $gateway_id,
		'status'         => 'pending',
		'note'           => $note,
		'is_anonymous'   => $is_anon,
		'created_at'     => current_time( 'mysql' ),
	);

	$format = array( '%d', '%d', '%s', '%s', '%s', '%f', '%s', '%s', '%s', '%s', '%d', '%s' );

	$wpdb->insert( $table_donations, $data, $format );
	$donation_id = $wpdb->insert_id;

	if ( $donation_id ) {
		// Update Campaign Collected Amount
		wpd_update_campaign_stats( $campaign_id );
		
        // Send Email Notification
        WPD_Email::send_confirmation( $donation_id );

        // Redirect to success URL with payment instruction flag
        $redirect_url = add_query_arg( array( 'donation_success' => 1, 'donation_id' => $donation_id, 'method' => 'manual' ), get_permalink( $campaign_id ) );
        wp_safe_redirect( $redirect_url );
        exit;
	}
}
add_action( 'init', 'wpd_handle_donation_submission' );

/**
 * Update Campaign Stats (Collected Amount)
 */
function wpd_update_campaign_stats( $campaign_id ) {
	global $wpdb;
	$table = $wpdb->prefix . 'wpd_donations';
	
	// Sum only completed (or pending for offline/testing) donations
	// For MVP: let's include 'pending' as 'collected' for offline demo purposes? 
	// Or strictly 'complete'. Let's stick to 'complete' usually, but for Offline, usually admin marks it complete.
	// But to show progress immediately in demo, maybe we can optionally count pending.
	// Let's stick to standard: only 'complete' counts for progress.
	
	$total = $wpdb->get_var( $wpdb->prepare( "SELECT SUM(amount) FROM $table WHERE campaign_id = %d AND status = 'complete'", $campaign_id ) );
	
	update_post_meta( $campaign_id, '_wpd_collected_amount', $total );
}

/**
 * Get Campaign Progress Data
 */
function wpd_get_campaign_progress( $campaign_id ) {
	$target    = get_post_meta( $campaign_id, '_wpd_target_amount', true );
	$collected = get_post_meta( $campaign_id, '_wpd_collected_amount', true );
	
	if ( ! $target ) $target = 0;
	if ( ! $collected ) $collected = 0;
	
	$percentage = $target > 0 ? ( $collected / $target ) * 100 : 0;
	$percentage = min( 100, max( 0, $percentage ) ); // Clamp between 0-100
	
	return array(
		'target'     => $target,
		'collected'  => $collected,
		'percentage' => $percentage,
	);
}
