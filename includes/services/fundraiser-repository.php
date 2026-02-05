<?php
/**
 * Fundraiser Database Repository
 */

if (!defined('ABSPATH')) {
    exit;
}

class DONASAI_Fundraiser_Repository
{
    private static function get_table()
    {
        global $wpdb;
        return $wpdb->prefix . 'donasai_fundraisers';
    }

    public static function get_by_user_campaign($user_id, $campaign_id)
    {
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Caching is handled in the calling service layer (fundraiser.php).
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM %i WHERE user_id = %d AND campaign_id = %d",
            self::get_table(),
            $user_id,
            $campaign_id
        ));
    }

    public static function get_by_code($code)
    {
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Caching is handled in the calling service layer (fundraiser.php).
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM %i WHERE referral_code = %s",
            self::get_table(),
            $code
        ));
    }

    public static function get_by_id($id)
    {
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Caching is handled in the calling service layer (fundraiser.php).
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM %i WHERE id = %d",
            self::get_table(),
            $id
        ));
    }

    public static function create($data)
    {
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- New record creation; caching not applicable here.
        $inserted = $wpdb->insert(self::get_table(), $data);
        return $inserted ? $wpdb->insert_id : false;
    }

    public static function update_stats($id, $amount)
    {
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Cache invalidation is handled in the calling service layer (fundraiser.php).
        return $wpdb->query($wpdb->prepare(
            "UPDATE %i SET total_donations = total_donations + %f, donation_count = donation_count + 1 WHERE id = %d",
            self::get_table(),
            $amount,
            $id
        ));
    }

    public static function get_leaderboard($campaign_id, $limit = 10)
    {
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Caching is handled in the calling service layer (fundraiser.php).
        return $wpdb->get_results($wpdb->prepare(
            "SELECT f.*, u.display_name, u.user_email 
             FROM %i f
             JOIN %i u ON f.user_id = u.ID
             WHERE f.campaign_id = %d AND f.total_donations > 0
             ORDER BY f.total_donations DESC
             LIMIT %d",
            self::get_table(),
            $wpdb->users,
            $campaign_id,
            $limit
        ));
    }

    public static function log_visit($data)
    {
        global $wpdb;
        $table_logs = $wpdb->prefix . 'donasai_referral_logs';
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Logging is a write-only operation; caching not applicable.
        return $wpdb->insert($table_logs, $data);
    }

    /**
     * Get fundraisers for a specific user
     */
    public static function get_by_user($user_id)
    {
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Caching is handled in the calling service layer.
        return $wpdb->get_results($wpdb->prepare(
            "SELECT f.*, p.post_title 
             FROM %i f
             JOIN %i p ON f.campaign_id = p.ID
             WHERE f.user_id = %d
             ORDER BY f.created_at DESC",
            self::get_table(),
            $wpdb->posts,
            $user_id
        ));
    }

    /**
     * Get visit count for a fundraiser
     */
    public static function get_visit_count($fundraiser_id)
    {
        global $wpdb;
        $table_logs = $wpdb->prefix . 'donasai_referral_logs';
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Caching is handled in the calling service layer.
        return (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(id) FROM %i WHERE fundraiser_id = %d",
            $table_logs,
            $fundraiser_id
        ));
    }

    /**
     * Get recent fundraisers
     */
    public static function get_recent($limit)
    {
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Caching is handled in the calling service layer.
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM %i ORDER BY created_at DESC LIMIT %d",
            self::get_table(),
            $limit
        ));
    }
}
