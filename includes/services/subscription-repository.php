<?php
/**
 * Subscription Database Repository
 */

if (!defined('ABSPATH')) {
    exit;
}

class DONASAI_Subscription_Repository
{
    private static function get_table()
    {
        global $wpdb;
        return $wpdb->prefix . 'donasai_subscriptions';
    }

    public static function create($data)
    {
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- New record creation; caching not applicable here.
        $inserted = $wpdb->insert(self::get_table(), $data);
        return $inserted ? $wpdb->insert_id : false;
    }

    public static function get_user_subscriptions($user_id)
    {
        global $wpdb;
        $donasai_table_subs = self::get_table();
        $donasai_table_posts = $wpdb->prefix . 'posts';
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Caching is handled in the calling service layer (subscription.php).
        return $wpdb->get_results($wpdb->prepare(
            "SELECT s.*, p.post_title as campaign_title 
             FROM %i s
             JOIN %i p ON s.campaign_id = p.ID
             WHERE s.user_id = %d 
             ORDER BY s.created_at DESC",
            $donasai_table_subs,
            $donasai_table_posts,
            $user_id
        ));
    }

    public static function update_status($subscription_id, $user_id, $status)
    {
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Cache invalidation is handled in the calling service layer (subscription.php).
        return $wpdb->update(
            self::get_table(),
            array('status' => $status),
            array('id' => $subscription_id, 'user_id' => $user_id)
        );
    }

    public static function get_due_subscriptions()
    {
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Caching is handled in the calling service layer (subscription.php).
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM %i WHERE status = 'active' AND next_payment_date <= NOW()",
            self::get_table()
        ));
    }

    public static function update_next_payment_date($id, $next_date)
    {
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- This is a direct update; high-level caches are invalidated or not affected.
        return $wpdb->update(
            self::get_table(),
            array('next_payment_date' => $next_date),
            array('id' => $id)
        );
    }
}
