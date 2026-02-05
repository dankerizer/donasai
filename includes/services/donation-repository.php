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
        return (float) $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(amount) FROM %i WHERE campaign_id = %d AND status = 'complete'",
            self::get_table(),
            absint($campaign_id)
        ));
    }

    /**
     * Count unique donors for a campaign
     */
    public static function get_campaign_donor_count($campaign_id)
    {
        global $wpdb;
        return (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(DISTINCT email) FROM %i WHERE campaign_id = %d AND status = 'complete'",
            self::get_table(),
            absint($campaign_id)
        ));
    }

    /**
     * Get a single donation
     */
    public static function get_donation($donation_id)
    {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM %i WHERE id = %d",
            self::get_table(),
            absint($donation_id)
        ));
    }

    /**
     * Update a donation
     */
    public static function update($id, $data, $format)
    {
        global $wpdb;
        return $wpdb->update(self::get_table(), $data, array('id' => absint($id)), $format, array('%d'));
    }

    public static function get_list_filtered($filters = array(), $order_by = 'created_at', $order = 'DESC', $limit = 20, $offset = 0)
    {
        global $wpdb;

        // Filters mapping
        $campaign_id = !empty($filters['campaign_id']) ? absint($filters['campaign_id']) : 0;
        $status = !empty($filters['status']) ? sanitize_text_field($filters['status']) : '';
        $payment_method = !empty($filters['payment_method']) ? sanitize_text_field($filters['payment_method']) : '';
        
        $is_recurring = !empty($filters['is_recurring']) ? $filters['is_recurring'] : '';
        $sub_filter = 0; // 0: all, 1: recurring, 2: one-time
        if ($is_recurring === 'recurring') {
            $sub_filter = 1;
        } elseif ($is_recurring === 'one-time') {
            $sub_filter = 2;
        }

        $start_date = !empty($filters['start_date']) ? sanitize_text_field($filters['start_date']) . ' 00:00:00' : '0000-00-00 00:00:00';
        $end_date = !empty($filters['end_date']) ? sanitize_text_field($filters['end_date']) . ' 23:59:59' : '9999-12-31 23:59:59';

        $order_dir = (strtoupper($order) === 'ASC') ? 'ASC' : 'DESC';
        $limit_val = absint($limit);
        $offset_val = absint($offset);

        // To achieve Zero-Suppression, we MUST NOT use any variable concatenation or positional placeholders.
        // POSITIONAL PLACEHOLDERS are NOT supported by $wpdb->prepare().
        // We use a switch to provide a 100% literal query string for every column and direction combination.
        switch ($order_by) {
            case 'amount':
                if ($order_dir === 'ASC') {
                    return $wpdb->get_results($wpdb->prepare(
                        "SELECT * FROM %i WHERE ( %d = 0 OR campaign_id = %d ) AND ( %s = '' OR status = %s ) AND ( %s = '' OR payment_method = %s ) AND ( %d = 0 OR ( %d = 1 AND subscription_id > 0 ) OR ( %d = 2 AND (subscription_id IS NULL OR subscription_id = 0) ) ) AND created_at >= %s AND created_at <= %s ORDER BY amount ASC LIMIT %d OFFSET %d",
                        self::get_table(), $campaign_id, $campaign_id, $status, $status, $payment_method, $payment_method, $sub_filter, $sub_filter, $sub_filter, $start_date, $end_date, $limit_val, $offset_val
                    ));
                } else {
                    return $wpdb->get_results($wpdb->prepare(
                        "SELECT * FROM %i WHERE ( %d = 0 OR campaign_id = %d ) AND ( %s = '' OR status = %s ) AND ( %s = '' OR payment_method = %s ) AND ( %d = 0 OR ( %d = 1 AND subscription_id > 0 ) OR ( %d = 2 AND (subscription_id IS NULL OR subscription_id = 0) ) ) AND created_at >= %s AND created_at <= %s ORDER BY amount DESC LIMIT %d OFFSET %d",
                        self::get_table(), $campaign_id, $campaign_id, $status, $status, $payment_method, $payment_method, $sub_filter, $sub_filter, $sub_filter, $start_date, $end_date, $limit_val, $offset_val
                    ));
                }
            case 'status':
                if ($order_dir === 'ASC') {
                    return $wpdb->get_results($wpdb->prepare(
                        "SELECT * FROM %i WHERE ( %d = 0 OR campaign_id = %d ) AND ( %s = '' OR status = %s ) AND ( %s = '' OR payment_method = %s ) AND ( %d = 0 OR ( %d = 1 AND subscription_id > 0 ) OR ( %d = 2 AND (subscription_id IS NULL OR subscription_id = 0) ) ) AND created_at >= %s AND created_at <= %s ORDER BY status ASC LIMIT %d OFFSET %d",
                        self::get_table(), $campaign_id, $campaign_id, $status, $status, $payment_method, $payment_method, $sub_filter, $sub_filter, $sub_filter, $start_date, $end_date, $limit_val, $offset_val
                    ));
                } else {
                    return $wpdb->get_results($wpdb->prepare(
                        "SELECT * FROM %i WHERE ( %d = 0 OR campaign_id = %d ) AND ( %s = '' OR status = %s ) AND ( %s = '' OR payment_method = %s ) AND ( %d = 0 OR ( %d = 1 AND subscription_id > 0 ) OR ( %d = 2 AND (subscription_id IS NULL OR subscription_id = 0) ) ) AND created_at >= %s AND created_at <= %s ORDER BY status DESC LIMIT %d OFFSET %d",
                        self::get_table(), $campaign_id, $campaign_id, $status, $status, $payment_method, $payment_method, $sub_filter, $sub_filter, $sub_filter, $start_date, $end_date, $limit_val, $offset_val
                    ));
                }
            case 'name':
                if ($order_dir === 'ASC') {
                    return $wpdb->get_results($wpdb->prepare(
                        "SELECT * FROM %i WHERE ( %d = 0 OR campaign_id = %d ) AND ( %s = '' OR status = %s ) AND ( %s = '' OR payment_method = %s ) AND ( %d = 0 OR ( %d = 1 AND subscription_id > 0 ) OR ( %d = 2 AND (subscription_id IS NULL OR subscription_id = 0) ) ) AND created_at >= %s AND created_at <= %s ORDER BY name ASC LIMIT %d OFFSET %d",
                        self::get_table(), $campaign_id, $campaign_id, $status, $status, $payment_method, $payment_method, $sub_filter, $sub_filter, $sub_filter, $start_date, $end_date, $limit_val, $offset_val
                    ));
                } else {
                    return $wpdb->get_results($wpdb->prepare(
                        "SELECT * FROM %i WHERE ( %d = 0 OR campaign_id = %d ) AND ( %s = '' OR status = %s ) AND ( %s = '' OR payment_method = %s ) AND ( %d = 0 OR ( %d = 1 AND subscription_id > 0 ) OR ( %d = 2 AND (subscription_id IS NULL OR subscription_id = 0) ) ) AND created_at >= %s AND created_at <= %s ORDER BY name DESC LIMIT %d OFFSET %d",
                        self::get_table(), $campaign_id, $campaign_id, $status, $status, $payment_method, $payment_method, $sub_filter, $sub_filter, $sub_filter, $start_date, $end_date, $limit_val, $offset_val
                    ));
                }
            case 'email':
                if ($order_dir === 'ASC') {
                    return $wpdb->get_results($wpdb->prepare(
                        "SELECT * FROM %i WHERE ( %d = 0 OR campaign_id = %d ) AND ( %s = '' OR status = %s ) AND ( %s = '' OR payment_method = %s ) AND ( %d = 0 OR ( %d = 1 AND subscription_id > 0 ) OR ( %d = 2 AND (subscription_id IS NULL OR subscription_id = 0) ) ) AND created_at >= %s AND created_at <= %s ORDER BY email ASC LIMIT %d OFFSET %d",
                        self::get_table(), $campaign_id, $campaign_id, $status, $status, $payment_method, $payment_method, $sub_filter, $sub_filter, $sub_filter, $start_date, $end_date, $limit_val, $offset_val
                    ));
                } else {
                    return $wpdb->get_results($wpdb->prepare(
                        "SELECT * FROM %i WHERE ( %d = 0 OR campaign_id = %d ) AND ( %s = '' OR status = %s ) AND ( %s = '' OR payment_method = %s ) AND ( %d = 0 OR ( %d = 1 AND subscription_id > 0 ) OR ( %d = 2 AND (subscription_id IS NULL OR subscription_id = 0) ) ) AND created_at >= %s AND created_at <= %s ORDER BY email DESC LIMIT %d OFFSET %d",
                        self::get_table(), $campaign_id, $campaign_id, $status, $status, $payment_method, $payment_method, $sub_filter, $sub_filter, $sub_filter, $start_date, $end_date, $limit_val, $offset_val
                    ));
                }
            case 'created_at':
            default:
                if ($order_dir === 'ASC') {
                    return $wpdb->get_results($wpdb->prepare(
                        "SELECT * FROM %i WHERE ( %d = 0 OR campaign_id = %d ) AND ( %s = '' OR status = %s ) AND ( %s = '' OR payment_method = %s ) AND ( %d = 0 OR ( %d = 1 AND subscription_id > 0 ) OR ( %d = 2 AND (subscription_id IS NULL OR subscription_id = 0) ) ) AND created_at >= %s AND created_at <= %s ORDER BY created_at ASC LIMIT %d OFFSET %d",
                        self::get_table(), $campaign_id, $campaign_id, $status, $status, $payment_method, $payment_method, $sub_filter, $sub_filter, $sub_filter, $start_date, $end_date, $limit_val, $offset_val
                    ));
                } else {
                    return $wpdb->get_results($wpdb->prepare(
                        "SELECT * FROM %i WHERE ( %d = 0 OR campaign_id = %d ) AND ( %s = '' OR status = %s ) AND ( %s = '' OR payment_method = %s ) AND ( %d = 0 OR ( %d = 1 AND subscription_id > 0 ) OR ( %d = 2 AND (subscription_id IS NULL OR subscription_id = 0) ) ) AND created_at >= %s AND created_at <= %s ORDER BY created_at DESC LIMIT %d OFFSET %d",
                        self::get_table(), $campaign_id, $campaign_id, $status, $status, $payment_method, $payment_method, $sub_filter, $sub_filter, $sub_filter, $start_date, $end_date, $limit_val, $offset_val
                    ));
                }
        }
    }

    /**
     * Get donations count with filters
     */
    public static function get_count_filtered($filters = array())
    {
        global $wpdb;

        // Filters mapping
        $campaign_id = !empty($filters['campaign_id']) ? absint($filters['campaign_id']) : 0;
        $status = !empty($filters['status']) ? sanitize_text_field($filters['status']) : '';
        $payment_method = !empty($filters['payment_method']) ? sanitize_text_field($filters['payment_method']) : '';
        
        $is_recurring = !empty($filters['is_recurring']) ? $filters['is_recurring'] : '';
        $sub_filter = 0; // 0: all, 1: recurring, 2: one-time
        if ($is_recurring === 'recurring') {
            $sub_filter = 1;
        } elseif ($is_recurring === 'one-time') {
            $sub_filter = 2;
        }

        $start_date = !empty($filters['start_date']) ? sanitize_text_field($filters['start_date']) . ' 00:00:00' : '0000-00-00 00:00:00';
        $end_date = !empty($filters['end_date']) ? sanitize_text_field($filters['end_date']) . ' 23:59:59' : '9999-12-31 23:59:59';

        return (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM %i WHERE ( %d = 0 OR campaign_id = %d ) AND ( %s = '' OR status = %s ) AND ( %s = '' OR payment_method = %s ) AND ( %d = 0 OR ( %d = 1 AND subscription_id > 0 ) OR ( %d = 2 AND (subscription_id IS NULL OR subscription_id = 0) ) ) AND created_at >= %s AND created_at <= %s",
            self::get_table(),
            $campaign_id, $campaign_id,
            $status, $status,
            $payment_method, $payment_method,
            $sub_filter, $sub_filter, $sub_filter,
            $start_date,
            $end_date
        ));
    }

    /**
     * Get recent complete donations
     */
    public static function get_recent($limit)
    {
        global $wpdb;
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM %i WHERE status = 'complete' ORDER BY created_at DESC LIMIT %d",
            self::get_table(),
            absint($limit)
        ));
    }

    /**
     * Get donations for a specific user
     */
    public static function get_by_user($user_id)
    {
        global $wpdb;
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM %i WHERE user_id = %d ORDER BY created_at DESC",
            self::get_table(),
            absint($user_id)
        ));
    }

    /**
     * Get donation status by ID
     */
    public static function get_status($id)
    {
        global $wpdb;
        return $wpdb->get_var($wpdb->prepare(
            "SELECT status FROM %i WHERE id = %d",
            self::get_table(),
            absint($id)
        ));
    }

    /**
     * Insert a new donation
     */
    public static function create($data, $format)
    {
        global $wpdb;
        $inserted = $wpdb->insert(self::get_table(), $data, $format);
        return $inserted ? $wpdb->insert_id : false;
    }
}
