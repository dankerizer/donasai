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

        return DONASAI_Subscription_Repository::create($data);
    }

    /**
     * Get user subscriptions
     */
    public function get_user_subscriptions($user_id)
    {
        $cache_key = 'donasai_user_subscriptions_' . $user_id;
        $subscriptions = wp_cache_get($cache_key, 'donasai_subscriptions');

        if (false === $subscriptions) {
            $subscriptions = DONASAI_Subscription_Repository::get_user_subscriptions($user_id);
            wp_cache_set($cache_key, $subscriptions, 'donasai_subscriptions', 300);
        }
        return $subscriptions;
    }

    /**
     * Cancel subscription
     */
    public function cancel_subscription($subscription_id, $user_id)
    {
        $updated = DONASAI_Subscription_Repository::update_status($subscription_id, $user_id, 'cancelled');

        // Invalidate cache
        wp_cache_delete('donasai_user_subscriptions_' . $user_id, 'donasai_subscriptions');

        return $updated;
    }

    /**
     * Process Renewals (CRON Stub)
     */
    public function process_renewals()
    {
        // Find active subscriptions due today or earlier
        $cache_key = 'donasai_due_subscriptions';
        $due = wp_cache_get($cache_key, 'donasai_subscriptions');

        if (false === $due) {
            $due = DONASAI_Subscription_Repository::get_due_subscriptions();
            wp_cache_set($cache_key, $due, 'donasai_subscriptions', 300);
        }

        foreach ($due as $sub) {
            // For MVP, just update date to avoid loop
            $next_date = ($sub->billing_interval === 'year')
                ? wp_date('Y-m-d H:i:s', strtotime('+1 year'))
                : wp_date('Y-m-d H:i:s', strtotime('+1 month'));

            DONASAI_Subscription_Repository::update_next_payment_date($sub->id, $next_date);
        }
    }
}
