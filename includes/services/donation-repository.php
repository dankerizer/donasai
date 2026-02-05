<?php
/**
 * Donation Database Repository
 * Handles all direct database interactions for the donations table.
 */

if (!defined('ABSPATH')) {
    exit;
}

class DONASAI_Donation_Repository
{
    /**
     * Get the table name with prefix
     */
    private static function get_table()
    {
        global $wpdb;
        return $wpdb->prefix . 'donasai_donations';
    }

    /**
     * Sum amount for a campaign
     */
    public static function get_campaign_total($campaign_id)
    {
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Caching is handled in the calling service layer (donation.php).
        return (float) $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(amount) FROM %i WHERE campaign_id = %d AND status = 'complete'",
            self::get_table(),
            $campaign_id
        ));
    }

    /**
     * Count unique donors for a campaign
     */
    public static function get_campaign_donor_count($campaign_id)
    {
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Caching is handled in the calling service layer (donation.php).
        return (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(DISTINCT email) FROM %i WHERE campaign_id = %d AND status = 'complete'",
            self::get_table(),
            $campaign_id
        ));
    }

    /**
     * Get a single donation
     */
    public static function get_donation($donation_id)
    {
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Caching is handled in the calling service layer (donation.php).
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM %i WHERE id = %d",
            self::get_table(),
            $donation_id
        ));
    }

    /**
     * Update a donation
     */
    public static function update($id, $data, $format)
    {
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Cache invalidation is handled in the calling service layer (donation.php).
        return $wpdb->update(self::get_table(), $data, array('id' => $id), $format, array('%d'));
    }

    /**
     * Get donations list with filters
     */
    public static function get_list($where, $prepare_args, $order_by, $order, $limit, $offset)
    {
        global $wpdb;
        $prepare_args = array_merge(array(self::get_table()), $prepare_args);
        $prepare_args[] = $limit;
        $prepare_args[] = $offset;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter, WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber -- Dynamic query construction for filtered list. Arguments are validated and escaped in the calling service.
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM %i WHERE " . $where . " ORDER BY " . esc_sql($order_by) . " " . esc_sql($order) . " LIMIT %d OFFSET %d",
            ...$prepare_args
        ));
    }

    /**
     * Get donations count with filters
     */
    public static function get_count($where, $prepare_args)
    {
        global $wpdb;
        $prepare_args = array_merge(array(self::get_table()), $prepare_args);

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, PluginCheck.Security.DirectDB.UnescapedDBParameter -- Dynamic query construction for filtered count. Arguments are validated and escaped in the calling service.
        return (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM %i WHERE " . $where,
            ...$prepare_args
        ));
    }

    /**
     * Get recent complete donations
     */
    public static function get_recent($limit)
    {
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Caching is handled in the calling service layer.
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM %i WHERE status = 'complete' ORDER BY created_at DESC LIMIT %d",
            self::get_table(),
            $limit
        ));
    }

    /**
     * Get donations for a specific user
     */
    public static function get_by_user($user_id)
    {
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Caching is handled in the calling service layer.
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM %i WHERE user_id = %d ORDER BY created_at DESC",
            self::get_table(),
            $user_id
        ));
    }

    /**
     * Get donation status by ID
     */
    public static function get_status($id)
    {
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Dynamic status check; result is typically used for logic branching.
        return $wpdb->get_var($wpdb->prepare(
            "SELECT status FROM %i WHERE id = %d",
            self::get_table(),
            $id
        ));
    }

    /**
     * Insert a new donation
     */
    public static function create($data, $format)
    {
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Write operation; caching not applicable.
        $inserted = $wpdb->insert(self::get_table(), $data, $format);
        return $inserted ? $wpdb->insert_id : false;
    }
}
