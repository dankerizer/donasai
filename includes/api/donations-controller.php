<?php
/**
 * REST API for Donations
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('rest_api_init', function () {
    // GET /donations
    register_rest_route('donasai/v1', '/donations', array(
        'methods' => 'GET',
        'callback' => 'donasai_api_get_donations',
        'permission_callback' => function () {
            return current_user_can('manage_options');
        },
    ));

    // POST /donations/{id} (Update Status)
    register_rest_route('donasai/v1', '/donations/(?P<id>\d+)', array(
        'methods' => 'POST',
        'callback' => 'donasai_api_update_donation',
        'permission_callback' => function () {
            return current_user_can('manage_options');
        },
    ));

    // GET /export/donations
    register_rest_route('donasai/v1', '/export/donations', array(
        'methods' => 'GET',
        'callback' => 'donasai_api_export_donations',
        'permission_callback' => function () {
            return current_user_can('manage_options');
        },
    ));

    // GET /stats
    register_rest_route('donasai/v1', '/stats', array(
        'methods' => 'GET',
        'callback' => 'donasai_api_get_stats',
        'permission_callback' => function () {
            return current_user_can('manage_options');
        },
    ));

    // GET /stats/chart
    register_rest_route('donasai/v1', '/stats/chart', array(
        'methods' => 'GET',
        'callback' => 'donasai_api_get_chart_stats',
        'permission_callback' => function () {
            return current_user_can('manage_options');
        },
    ));
});

function donasai_api_get_chart_stats()
{
    global $wpdb;

    // Get last 30 days data
    // Get last 30 days data
    $cache_key = 'donasai_chart_stats_daily';
    $results = wp_cache_get($cache_key, 'donasai_stats');

    if (false === $results) {
        $results = $wpdb->get_results($wpdb->prepare("
            SELECT 
                DATE(created_at) as date, 
                SUM(amount) as total_amount,
                COUNT(id) as total_count
            FROM %i 
            WHERE status = %s 
            AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ", $wpdb->prefix . 'donasai_donations', 'complete'));
        wp_cache_set($cache_key, $results, 'donasai_stats', 3600);
    }

    // Fill missing dates with 0
    $daily_stats = array();
    $period = new DatePeriod(
        new DateTime('-30 days'),
        new DateInterval('P1D'),
        new DateTime('+1 day')
    );

    $stats_by_date = array();
    foreach ($results as $row) {
        $stats_by_date[$row->date] = $row;
    }

    foreach ($period as $date) {
        $date_str = $date->format('Y-m-d');
        if (isset($stats_by_date[$date_str])) {
            $daily_stats[] = array(
                'date' => $date->format('d M'),
                'amount' => (float) $stats_by_date[$date_str]->total_amount,
                'count' => (int) $stats_by_date[$date_str]->total_count
            );
        } else {
            $daily_stats[] = array(
                'date' => $date->format('d M'),
                'amount' => 0,
                'count' => 0
            );
        }
    }

    // --- Payment Methods ---
    $cache_key_pm = 'donasai_stats_payment_methods';
    $payment_methods = wp_cache_get($cache_key_pm, 'donasai_stats');
    if (false === $payment_methods) {
        $payment_methods = $wpdb->get_results($wpdb->prepare("
            SELECT payment_method, COUNT(*) as count 
            FROM %i 
            WHERE status = %s 
            GROUP BY payment_method
        ", $wpdb->prefix . 'donasai_donations', 'complete'));
        wp_cache_set($cache_key_pm, $payment_methods, 'donasai_stats', 3600);
    }

    // --- Top Campaigns ---
    $cache_key_top = 'donasai_stats_top_campaigns';
    $top_campaigns = wp_cache_get($cache_key_top, 'donasai_stats');
    if (false === $top_campaigns) {
        $top_campaigns = $wpdb->get_results($wpdb->prepare("
            SELECT p.post_title as name, SUM(d.amount) as value
            FROM %i d
            LEFT JOIN %i p ON d.campaign_id = p.ID
            WHERE d.status = %s AND d.campaign_id > 0
            GROUP BY d.campaign_id
            ORDER BY value DESC
            LIMIT 5
        ", $wpdb->prefix . 'donasai_donations', $wpdb->prefix . 'posts', 'complete'));
        wp_cache_set($cache_key_top, $top_campaigns, 'donasai_stats', 3600);
    }

    return rest_ensure_response(array(
        'daily_stats' => $daily_stats,
        'payment_methods' => $payment_methods,
        'top_campaigns' => $top_campaigns
    ));
}

function donasai_api_get_stats()
{
    global $wpdb;

    // Total Connected Amount (Completed)
    $cache_key = 'donasai_stats_overview';
    $cached_stats = wp_cache_get($cache_key, 'donasai_stats');

    if (false === $cached_stats) {
        $table_donations = $wpdb->prefix . 'donasai_donations';
        $total_collected = $wpdb->get_var($wpdb->prepare("SELECT SUM(amount) FROM %i WHERE status = %s", $table_donations, 'complete'));
        $total_donors = $wpdb->get_var($wpdb->prepare("SELECT COUNT(DISTINCT email) FROM %i WHERE status = %s", $table_donations, 'complete'));
        $active_campaigns = wp_count_posts('donasai_campaign')->publish;

        $cached_stats = array(
            'total_collected' => (float) $total_collected,
            'total_donors' => (int) $total_donors,
            'active_campaigns' => (int) $active_campaigns
        );
        wp_cache_set($cache_key, $cached_stats, 'donasai_stats', 3600);
    }

    $total_collected = $cached_stats['total_collected'];
    $total_donors = $cached_stats['total_donors'];
    $active_campaigns = $cached_stats['active_campaigns'];

    // --- Advanced Analytics ---

    // 1. Growth Rate (Month over Month)
    $current_month_start = wp_date('Y-m-01');
    // Current Month Collected
    $cache_key_month = 'donasai_stats_month';
    $current_month_amount = wp_cache_get($cache_key_month, 'donasai_stats');
    if (false === $current_month_amount) {
        $current_month_amount = (float) $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(amount) FROM %i WHERE status = %s AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)",
            $table_donations,
            'complete'
        ));
        wp_cache_set($cache_key_month, $current_month_amount, 'donasai_stats', 3600);
    }

    // Last Month (Growth Rate)
    $cache_key_last_month = 'donasai_stats_last_month';
    $last_month_amount = wp_cache_get($cache_key_last_month, 'donasai_stats');
    if (false === $last_month_amount) {
        $last_month_amount = (float) $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(amount) FROM %i WHERE status = %s AND created_at BETWEEN DATE_SUB(NOW(), INTERVAL 60 DAY) AND DATE_SUB(NOW(), INTERVAL 30 DAY)",
            $table_donations,
            'complete'
        ));
        wp_cache_set($cache_key_last_month, $last_month_amount, 'donasai_stats', 3600);
    }

    $growth_rate = 0;
    if ($last_month_amount > 0) {
        $growth_rate = (($current_month_amount - $last_month_amount) / $last_month_amount) * 100;
    } else {
        $growth_rate = $current_month_amount > 0 ? 100 : 0;
    }

    // Check if subscription table exists first to avoid error if Pro not fully setup
    $recurring_revenue = 0;
    $cache_key_mrr = 'donasai_stats_mrr';
    $cached_mrr = wp_cache_get($cache_key_mrr, 'donasai_stats');

    if (false === $cached_mrr) {
        $table_subscriptions = $wpdb->prefix . 'donasai_subscriptions';
        if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_subscriptions)) == $table_subscriptions) {
            $cached_mrr = $wpdb->get_var($wpdb->prepare("SELECT SUM(amount) FROM %i WHERE status = %s", $table_subscriptions, 'active')) ?: 0;
        } else {
            $cached_mrr = 0;
        }
        wp_cache_set($cache_key_mrr, $cached_mrr, 'donasai_stats', 3600);
    }
    $recurring_revenue = $cached_mrr;

    // 3. Retention Rate
    // Donors who donated more than once
    $cache_key_repeat = 'donasai_stats_repeat_donors';
    $repeat_donors = wp_cache_get($cache_key_repeat, 'donasai_stats');

    if (false === $repeat_donors) {
        $table_donations = $wpdb->prefix . 'donasai_donations';
        $repeat_donors = $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*) FROM (
                SELECT email FROM %i 
                WHERE status = %s 
                GROUP BY email 
                HAVING COUNT(id) > 1
            ) as repeaters
        ", $table_donations, 'complete'));
        wp_cache_set($cache_key_repeat, $repeat_donors, 'donasai_stats', 3600);
    }

    $retention_rate = 0;
    if ($total_donors > 0) {
        $retention_rate = ($repeat_donors / $total_donors) * 100;
    }

    return rest_ensure_response(array(
        'total_donations' => (float) $total_collected,
        'total_donors' => (int) $total_donors,
        'active_campaigns' => (int) $active_campaigns,
        // Pro Stats
        'growth_rate' => round($growth_rate, 1),
        'recurring_revenue' => (float) $recurring_revenue,
        'retention_rate' => round($retention_rate, 1)
    ));
}

// Helper to build WHERE clause
function donasai_build_donations_where_clause($params)
{
    $where = "1=1";
    $args = array();

    // Campaign ID (comma separated)
    if (!empty($params['campaign_id'])) {
        $ids = array_map('intval', explode(',', $params['campaign_id']));
        if (!empty($ids)) {
            $placeholders = implode(',', array_fill(0, count($ids), '%d'));
            $where .= " AND campaign_id IN ($placeholders)";
            $args = array_merge($args, $ids);
        }
    }

    // Status (comma separated)
    if (!empty($params['status'])) {
        $statuses = array_map('sanitize_text_field', explode(',', $params['status']));
        if (!empty($statuses)) {
            $placeholders = implode(',', array_fill(0, count($statuses), '%s'));
            $where .= " AND status IN ($placeholders)";
            $args = array_merge($args, $statuses);
        }
    }

    // Payment Method (comma separated) - NEW
    if (!empty($params['payment_method'])) {
        $methods = array_map('sanitize_text_field', explode(',', $params['payment_method']));
        if (!empty($methods)) {
            $placeholders = implode(',', array_fill(0, count($methods), '%s'));
            $where .= " AND payment_method IN ($placeholders)";
            $args = array_merge($args, $methods);
        }
    }

    // Recurring filter (has subscription_id) - NEW
    if (!empty($params['is_recurring'])) {
        if ($params['is_recurring'] === 'recurring') {
            $where .= " AND subscription_id IS NOT NULL AND subscription_id > 0";
        } elseif ($params['is_recurring'] === 'one-time') {
            $where .= " AND (subscription_id IS NULL OR subscription_id = 0)";
        }
    }

    // Start Date
    if (!empty($params['start_date'])) {
        $where .= " AND created_at >= %s";
        $args[] = sanitize_text_field($params['start_date']) . ' 00:00:00';
    }

    // End Date
    if (!empty($params['end_date'])) {
        $where .= " AND created_at <= %s";
        $args[] = sanitize_text_field($params['end_date']) . ' 23:59:59';
    }

    return array('where' => $where, 'args' => $args);
}

function donasai_api_export_donations($request)
{
    global $wpdb;

    // Check Nonce (as we using directly in href)
    $nonce = isset($_GET['_wpnonce']) ? sanitize_text_field(wp_unslash($_GET['_wpnonce'])) : '';
    if (!wp_verify_nonce($nonce, 'wp_rest')) {
        wp_die('Invalid nonce');
    }

    // Build Query
    $params = array(
        'campaign_id' => isset($_GET['campaign_id']) ? sanitize_text_field(wp_unslash($_GET['campaign_id'])) : '',
        'status' => isset($_GET['status']) ? sanitize_text_field(wp_unslash($_GET['status'])) : '',
        'start_date' => isset($_GET['start_date']) ? sanitize_text_field(wp_unslash($_GET['start_date'])) : '',
        'end_date' => isset($_GET['end_date']) ? sanitize_text_field(wp_unslash($_GET['end_date'])) : '',
        'payment_method' => isset($_GET['payment_method']) ? sanitize_text_field(wp_unslash($_GET['payment_method'])) : '',
        'is_recurring' => isset($_GET['is_recurring']) ? sanitize_text_field(wp_unslash($_GET['is_recurring'])) : '',
    );

    $query_parts = donasai_build_donations_where_clause($params);
    $table_name = $wpdb->prefix . 'donasai_donations';
    $where_sql = $query_parts['where'];
    $args = $query_parts['args'];

    // Caching Export Results (Short-lived)
    $cache_key = 'donasai_export_' . md5(json_encode($params));
    $results = wp_cache_get($cache_key, 'donasai_donations');

    if (false === $results) {
        if (!empty($args)) {
            $results = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM %i WHERE " . $where_sql . " ORDER BY created_at DESC",
                array_merge(array($table_name), $args)
            ), ARRAY_A);
        } else {
            $results = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM %i WHERE 1=1 ORDER BY created_at DESC LIMIT %d",
                $table_name,
                10000
            ), ARRAY_A);
        }
        wp_cache_set($cache_key, $results, 'donasai_donations', 60);
    }

    $filename = 'donations-export-' . wp_date('Y-m-d') . '.csv';
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');

    // Header
    fputcsv($output, array('ID', 'Campaign ID', 'Date', 'Name', 'Email', 'Amount', 'Status', 'Payment Method'));

    foreach ($results as $row) {
        // Prevent CSV Injection
        $name = $row['name'];
        if (preg_match('/^[\=\+\-\@]/', $name)) {
            $name = "'" . $name;
        }

        $note = $row['note'];
        if (preg_match('/^[\=\+\-\@]/', $note)) {
            $note = "'" . $note;
        }

        fputcsv($output, array(
            $row['id'],
            $row['campaign_id'],
            $row['created_at'],
            $name,
            $row['email'],
            $row['amount'],
            $row['status'],
            $row['payment_method']
        ));
    }

    fclose($output);
    exit;
}

function donasai_api_update_donation($request)
{
    global $wpdb;
    $id = isset($request['id']) ? intval($request['id']) : 0;
    $params = $request->get_json_params();

    $data_to_update = array();
    $format = array();

    // Fields allowed to be updated
    $allowed_fields = array('status', 'name', 'email', 'phone', 'amount', 'note');

    foreach ($allowed_fields as $field) {
        if (isset($params[$field])) {
            $value = $params[$field];
            if ('amount' === $field) {
                $data_to_update[$field] = (float) $value;
                $format[] = '%f';
            } else {
                if ('note' === $field) {
                    $data_to_update[$field] = sanitize_textarea_field($value);
                } else {
                    $data_to_update[$field] = sanitize_text_field($value);
                }
                $format[] = '%s';
            }
        }
    }

    if (empty($data_to_update)) {
        return new WP_Error('no_data', 'No data to update', array('status' => 400));
    }

    $updated = $wpdb->update(
        $wpdb->prefix . 'donasai_donations',
        $data_to_update,
        array('id' => $id),
        $format,
        array('%d')
    );

    if ($updated === false) {
        return new WP_Error('db_error', 'Could not update donation', array('status' => 500));
    }

    // Check for status change to 'complete' if status was in the payload
    if (isset($data_to_update['status']) && 'complete' === $data_to_update['status']) {
        do_action('donasai_donation_completed', $id);

        // Update Campaign Collected Amount
        $cache_key_cid = 'donasai_donation_campaign_' . $id;
        $campaign_id = wp_cache_get($cache_key_cid, 'donasai_donations');
        if (false === $campaign_id) {
            $campaign_id = (int) $wpdb->get_var($wpdb->prepare("SELECT campaign_id FROM %i WHERE id = %d", $wpdb->prefix . 'donasai_donations', $id));
            wp_cache_set($cache_key_cid, $campaign_id, 'donasai_donations', 300);
        }

        if ($campaign_id) {
            if (function_exists('donasai_update_campaign_stats')) {
                donasai_update_campaign_stats($campaign_id);
            }
        }
    }

    // Return updated data
    $cache_key_row = 'donasai_donation_' . $id;
    // Invalidate cache first as we just updated
    wp_cache_delete($cache_key_row, 'donasai_donations');
    
    $updated_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM %i WHERE id = %d", $wpdb->prefix . 'donasai_donations', $id));
    if ($updated_row) {
        wp_cache_set($cache_key_row, $updated_row, 'donasai_donations', 300);
    }

    return rest_ensure_response(array(
        'success' => true,
        'id' => $id,
        'message' => 'Donation updated',
        'data' => array(
            'id' => $updated_row->id,
            'name' => $updated_row->name,
            'email' => $updated_row->email,
            'phone' => $updated_row->phone,
            'amount' => (float) $updated_row->amount,
            'status' => $updated_row->status,
            'payment_method' => $updated_row->payment_method,
            'gateway_txn_id' => $updated_row->gateway_txn_id,
            'note' => $updated_row->note,
            'date' => $updated_row->created_at,
        )
    ));
}

function donasai_api_get_donations($request)
{
    global $wpdb;

    // Pagination Parameters
    $page = $request->get_param('page') ? intval($request->get_param('page')) : 1;
    $per_page = $request->get_param('per_page') ? intval($request->get_param('per_page')) : 20;

    if ($page < 1) $page = 1;
    if ($per_page < 1) $per_page = 20;
    if ($per_page > 100) $per_page = 100; // Hard max

    $offset = ($page - 1) * $per_page;

    // Build Query
    $params = array(
        'campaign_id' => $request->get_param('campaign_id'),
        'status' => $request->get_param('status'),
        'start_date' => $request->get_param('start_date'),
        'end_date' => $request->get_param('end_date'),
        'payment_method' => $request->get_param('payment_method'),
        'is_recurring' => $request->get_param('is_recurring'),
    );

    $query_parts = donasai_build_donations_where_clause($params);
    $table_name = $wpdb->prefix . 'donasai_donations';
    $where_sql = $query_parts['where'];
    $args = $query_parts['args'];

    // Caching for paginated results
    $cache_key = 'donasai_donations_list_' . md5(serialize($params) . $page . $per_page);
    $cached_response = wp_cache_get($cache_key, 'donasai_donations');
    if (false !== $cached_response) {
        return rest_ensure_response($cached_response);
    }

    // 1. Get Total Count
    $cache_key_count = 'donasai_donations_total_' . md5($where_sql . json_encode($args));
    $total_items = wp_cache_get($cache_key_count, 'donasai_donations');

    if (false === $total_items) {
        if (!empty($args)) {
            $total_items = (int) $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM %i WHERE " . $where_sql,
                array_merge(array($table_name), $args)
            ));
        } else {
            $total_items = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %i", $table_name));
        }
        wp_cache_set($cache_key_count, $total_items, 'donasai_donations', 3600);
    }
    $total_pages = ceil($total_items / $per_page);

    // 2. Get Data
    $cache_key_data = 'donasai_donations_data_' . md5($where_sql . json_encode($args) . $per_page . $offset);
    $results = wp_cache_get($cache_key_data, 'donasai_donations');

    if (false === $results) {
        $data_args = array_merge(array($table_name), $args);
        $data_args[] = $per_page;
        $data_args[] = $offset;

        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM %i WHERE " . $where_sql . " ORDER BY created_at DESC LIMIT %d OFFSET %d",
            $data_args
        ));
        wp_cache_set($cache_key_data, $results, 'donasai_donations', 3600);
    }

    // Format data for frontend
    $data = array_map(function ($row) {
        return array(
            'id' => (int) $row->id,
            'name' => $row->name,
            'email' => $row->email,
            'phone' => $row->phone,
            'amount' => (float) $row->amount,
            'status' => $row->status,
            'payment_method' => $row->payment_method,
            'gateway_txn_id' => $row->gateway_txn_id,
            'note' => $row->note,
            'metadata' => json_decode($row->metadata, true),
            'date' => $row->created_at,
        );
    }, $results);

    $response = array(
        'data' => $data,
        'meta' => array(
            'current_page' => $page,
            'per_page' => $per_page,
            'total' => $total_items,
            'total_pages' => $total_pages
        )
    );

    wp_cache_set($cache_key, $response, 'donasai_donations', 300);

    return rest_ensure_response($response);
}
