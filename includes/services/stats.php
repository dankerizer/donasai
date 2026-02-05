<?php
/**
 * Statistics Service Logic
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get Stats Overview (Total Collected, Donors, Active Campaigns)
 */
function donasai_get_stats_overview()
{
    global $wpdb;

    $cache_key = 'donasai_stats_overview';
    $cached_stats = wp_cache_get($cache_key, 'donasai_stats');

    if (false === $cached_stats) {
        $total_collected = $wpdb->get_var($wpdb->prepare("SELECT SUM(amount) FROM %i WHERE status = %s", $wpdb->prefix . 'donasai_donations', 'complete'));
        $total_donors = $wpdb->get_var($wpdb->prepare("SELECT COUNT(DISTINCT email) FROM %i WHERE status = %s", $wpdb->prefix . 'donasai_donations', 'complete'));
        $active_campaigns = wp_count_posts('donasai_campaign')->publish;

        $cached_stats = array(
            'total_collected' => (float) $total_collected,
            'total_donors' => (int) $total_donors,
            'active_campaigns' => (int) $active_campaigns
        );
        wp_cache_set($cache_key, $cached_stats, 'donasai_stats', 3600);
    }

    return $cached_stats;
}

/**
 * Get Advanced Analytics (Growth, MRR, Retention)
 */
function donasai_get_advanced_analytics()
{
    global $wpdb;
    $table_donations = $wpdb->prefix . 'donasai_donations';

    // 1. Growth Rate (Month over Month)
    $cache_key_month = 'donasai_stats_month';
    $current_month_amount = wp_cache_get($cache_key_month, 'donasai_stats');
    if (false === $current_month_amount) {
        $current_month_amount = (float) $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(amount) FROM %i WHERE status = %s AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)",
            $wpdb->prefix . 'donasai_donations',
            'complete'
        ));
        wp_cache_set($cache_key_month, $current_month_amount, 'donasai_stats', 3600);
    }

    $cache_key_last_month = 'donasai_stats_last_month';
    $last_month_amount = wp_cache_get($cache_key_last_month, 'donasai_stats');
    if (false === $last_month_amount) {
        $last_month_amount = (float) $wpdb->get_var($wpdb->prepare(
            "SELECT SUM(amount) FROM %i WHERE status = %s AND created_at BETWEEN DATE_SUB(NOW(), INTERVAL 60 DAY) AND DATE_SUB(NOW(), INTERVAL 30 DAY)",
            $wpdb->prefix . 'donasai_donations',
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

    // 2. Monthly Recurring Revenue (MRR)
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

    // 3. Retention Rate
    $cache_key_repeat = 'donasai_stats_repeat_donors';
    $repeat_donors = wp_cache_get($cache_key_repeat, 'donasai_stats');
    if (false === $repeat_donors) {
        $repeat_donors = (int) $wpdb->get_var($wpdb->prepare("
            SELECT COUNT(*) FROM (
                SELECT email FROM %i 
                WHERE status = %s 
                GROUP BY email 
                HAVING COUNT(id) > 1
            ) as repeaters", $wpdb->prefix . 'donasai_donations', 'complete'));
        wp_cache_set($cache_key_repeat, $repeat_donors, 'donasai_stats', 3600);
    }

    $overview = donasai_get_stats_overview();
    $total_donors = $overview['total_donors'];
    $retention_rate = $total_donors > 0 ? ($repeat_donors / $total_donors) * 100 : 0;

    return array(
        'growth_rate' => round($growth_rate, 1),
        'recurring_revenue' => (float) $cached_mrr,
        'retention_rate' => round($retention_rate, 1)
    );
}

/**
 * Get Chart Data (Daily, Payment Methods, Top Campaigns)
 */
function donasai_get_chart_data()
{
    global $wpdb;
    $table_donations = $wpdb->prefix . 'donasai_donations';

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

    // Payment Methods
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

    // Top Campaigns
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
        ", $wpdb->prefix . 'donasai_donations', $wpdb->posts, 'complete'));
        wp_cache_set($cache_key_top, $top_campaigns, 'donasai_stats', 3600);
    }

    return array(
        'daily_stats' => $daily_stats,
        'payment_methods' => $payment_methods,
        'top_campaigns' => $top_campaigns
    );
}
