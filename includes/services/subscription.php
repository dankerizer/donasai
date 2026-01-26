<?php
/**
 * Subscription Service
 */

if (!defined('ABSPATH')) {
    exit;
}

class DONASAI_Subscription_Service
{

    /**
     * Create a new subscription
     */
    public function create_subscription($user_id, $campaign_id, $amount, $frequency = 'month')
    {
        global $wpdb;

        $next_date = ($frequency === 'year')
            ? wp_date('Y-m-d H:i:s', strtotime('+1 year'))
            : wp_date('Y-m-d H:i:s', strtotime('+1 month'));

        $data = array(
            'user_id' => $user_id,
            'campaign_id' => $campaign_id,
            'amount' => $amount,
            'status' => 'active',
            'billing_interval' => $frequency,
            'next_payment_date' => $next_date,
            'created_at' => current_time('mysql')
        );

        $inserted = $wpdb->insert($wpdb->prefix . 'donasai_subscriptions', $data);

        if ($inserted) {
            return $wpdb->insert_id;
        }

        return false;
    }

    /**
     * Get user subscriptions
     */
    public function get_user_subscriptions($user_id)
    {
        global $wpdb;

        $cache_key = 'donasai_user_subscriptions_' . $user_id;
        $subscriptions = wp_cache_get($cache_key, 'donasai_subscriptions');

        if (false === $subscriptions) {
            $subscriptions = $wpdb->get_results($wpdb->prepare(
                "SELECT s.*, p.post_title as campaign_title 
                 FROM {$wpdb->prefix}donasai_subscriptions s
                 JOIN {$wpdb->prefix}posts p ON s.campaign_id = p.ID
                 WHERE s.user_id = %d 
                 ORDER BY s.created_at DESC",
                $user_id
            )); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
            wp_cache_set($cache_key, $subscriptions, 'donasai_subscriptions', 300);
        }
        return $subscriptions;
    }

    /**
     * Cancel subscription
     */
    public function cancel_subscription($subscription_id, $user_id)
    {
        global $wpdb;

        $updated = $wpdb->update(
            $wpdb->prefix . 'donasai_subscriptions',
            array('status' => 'cancelled'),
            array('id' => $subscription_id, 'user_id' => $user_id)
        );

        // Invalidate cache
        wp_cache_delete('donasai_user_subscriptions_' . $user_id, 'donasai_subscriptions');

        return $updated;
    }

    /**
     * Process Renewals (CRON Stub)
     * This would check for due items and create pending donations + emails
     */
    public function process_renewals()
    {
        global $wpdb;

        // Find active subscriptions due today or earlier
        $due = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}donasai_subscriptions WHERE status = %s AND next_payment_date <= NOW()", 'active'));

        foreach ($due as $sub) {
            // Logic to create a new pending donation
            // Send email to user
            // Update next_payment_date

            // For MVP, just update date to avoid loop
            $next_date = ($sub->billing_interval === 'year')
                ? wp_date('Y-m-d H:i:s', strtotime('+1 year'))
                : wp_date('Y-m-d H:i:s', strtotime('+1 month'));

            $wpdb->update($wpdb->prefix . 'donasai_subscriptions', array('next_payment_date' => $next_date), array('id' => $sub->id));

            // Log/Create Pending Donation (Stub)
            // error_log( "Processed renewal for Subscription #{$sub->id}" );
        }
    }
}
