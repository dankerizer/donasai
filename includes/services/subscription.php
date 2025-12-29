<?php
/**
 * Subscription Service
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPD_Subscription_Service {

    /**
     * Create a new subscription
     */
    public function create_subscription( $user_id, $campaign_id, $amount, $frequency = 'monthly' ) {
        global $wpdb;
        $table = $wpdb->prefix . 'wpd_subscriptions';

        $next_date = ( $frequency === 'yearly' ) 
            ? date( 'Y-m-d H:i:s', strtotime( '+1 year' ) ) 
            : date( 'Y-m-d H:i:s', strtotime( '+1 month' ) );

        $data = array(
            'user_id'           => $user_id,
            'campaign_id'       => $campaign_id,
            'amount'            => $amount,
            'status'            => 'active',
            'frequency'         => $frequency,
            'next_payment_date' => $next_date,
            'created_at'        => current_time( 'mysql' )
        );

        $inserted = $wpdb->insert( $table, $data );

        if ( $inserted ) {
            return $wpdb->insert_id;
        }

        return false;
    }

    /**
     * Get user subscriptions
     */
    public function get_user_subscriptions( $user_id ) {
        global $wpdb;
        $table      = $wpdb->prefix . 'wpd_subscriptions';
        $table_posts = $wpdb->prefix . 'posts';
        
        $sql = "SELECT s.*, p.post_title as campaign_title 
                FROM $table s
                JOIN $table_posts p ON s.campaign_id = p.ID
                WHERE s.user_id = %d 
                ORDER BY s.created_at DESC";
                
        return $wpdb->get_results( $wpdb->prepare( $sql, $user_id ) );
    }

    /**
     * Cancel subscription
     */
    public function cancel_subscription( $subscription_id, $user_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'wpd_subscriptions';

        return $wpdb->update( 
            $table, 
            array( 'status' => 'cancelled' ), 
            array( 'id' => $subscription_id, 'user_id' => $user_id ) 
        );
    }

    /**
     * Process Renewals (CRON Stub)
     * This would check for due items and create pending donations + emails
     */
    public function process_renewals() {
        global $wpdb;
        $table = $wpdb->prefix . 'wpd_subscriptions';
        
        // Find active subscriptions due today or earlier
        $due = $wpdb->get_results( "SELECT * FROM $table WHERE status = 'active' AND next_payment_date <= NOW()" );
        
        foreach ( $due as $sub ) {
            // Logic to create a new pending donation
            // Send email to user
            // Update next_payment_date
            
            // For MVP, just update date to avoid loop
            $next_date = ( $sub->frequency === 'yearly' ) 
                ? date( 'Y-m-d H:i:s', strtotime( '+1 year' ) ) 
                : date( 'Y-m-d H:i:s', strtotime( '+1 month' ) );
                
            $wpdb->update( $table, array( 'next_payment_date' => $next_date ), array( 'id' => $sub->id ) );
            
            // Log/Create Pending Donation (Stub)
            error_log( "Processed renewal for Subscription #{$sub->id}" );
        }
    }
}
