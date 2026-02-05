<?php
/**
 * Statistics Database Repository
 * Handles all direct database interactions for the statistics logic.
 */

if (!defined('ABSPATH')) {
    exit;
}

class DONASAI_Stats_Repository
{
    /**
     * Get sum of revenue for a period
     */
    public static function get_revenue_for_period($days_interval, $offset_days = 0)
    {
        global $wpdb;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Caching is handled in the calling service layer (stats.php).
        return (float) $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(amount) FROM %i WHERE status = 'complete' AND created_at BETWEEN DATE_SUB(NOW(), INTERVAL %d DAY) AND DATE_SUB(NOW(), INTERVAL %d DAY)",
            $wpdb->prefix . 'donasai_donations',
            $days_interval + $offset_days,
            $offset_days
        ));
    }

    /**
     * Get MRR from active subscriptions
     */
    public static function get_mrr()
    {
        global $wpdb;
        $table_subscriptions = $wpdb->prefix . 'donasai_subscriptions';
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Result is checked for table existence and then cached in the service layer.
        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_subscriptions)) != $table_subscriptions) {
            return 0;
        }

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Caching is handled in the calling service layer (stats.php).
        return (float) $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(amount) FROM %i WHERE status = 'active'",
            $table_subscriptions
        ));
    }

    /**
     * Count repeat donors
     */
    public static function get_repeat_donor_count()
    {
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Caching is handled in the calling service layer (stats.php).
        return (int) $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*) FROM (
                SELECT email FROM %i 
                WHERE status = 'complete' 
                GROUP BY email 
                HAVING COUNT(id) > 1
            ) as repeaters", $wpdb->prefix . 'donasai_donations'));
    }

    /**
     * Get daily stats for last 30 days
     */
    public static function get_daily_chart_data()
    {
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Caching is handled in the calling service layer (stats.php).
        return $wpdb->get_results($wpdb->prepare("
            SELECT 
                DATE(created_at) as date, 
                SUM(amount) as total_amount,
                COUNT(id) as total_count
            FROM %i 
            WHERE status = 'complete' 
            AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ", $wpdb->prefix . 'donasai_donations'));
    }

    /**
     * Get payment method distribution
     */
    public static function get_payment_method_stats()
    {
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Caching is handled in the calling service layer (stats.php).
        return $wpdb->get_results($wpdb->prepare("
            SELECT payment_method, COUNT(*) as count 
            FROM %i 
            WHERE status = 'complete' 
            GROUP BY payment_method
        ", $wpdb->prefix . 'donasai_donations'));
    }

    /**
     * Get top campaigns by revenue
     */
    public static function get_top_campaigns($limit = 5)
    {
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Caching is handled in the calling service layer (stats.php).
        return $wpdb->get_results($wpdb->prepare(
            "SELECT p.post_title as name, SUM(d.amount) as value
            FROM %i d
            LEFT JOIN %i p ON d.campaign_id = p.ID
            WHERE d.status = 'complete' AND d.campaign_id > 0
            GROUP BY d.campaign_id
            ORDER BY value DESC
            LIMIT %d",
            $wpdb->prefix . 'donasai_donations', $wpdb->posts, $limit));
    }
}
