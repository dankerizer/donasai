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
    $cache_key = 'donasai_stats_overview';
    $cached_stats = wp_cache_get($cache_key, 'donasai_stats');

    if (false === $cached_stats) {
        $overview = donasai_get_advanced_analytics(); // Advanced covers similar ground
        $total_collected = DONASAI_Donation_Repository::get_campaign_total(0); // 0 = all
        $total_donors = DONASAI_Donation_Repository::get_campaign_donor_count(0);
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
    // 1. Growth Rate (Month over Month)
    $cache_key_month = 'donasai_stats_month';
    $current_month_amount = wp_cache_get($cache_key_month, 'donasai_stats');
    if (false === $current_month_amount) {
        $current_month_amount = DONASAI_Stats_Repository::get_revenue_for_period(30);
        wp_cache_set($cache_key_month, $current_month_amount, 'donasai_stats', 3600);
    }

    $cache_key_last_month = 'donasai_stats_last_month';
    $last_month_amount = wp_cache_get($cache_key_last_month, 'donasai_stats');
    if (false === $last_month_amount) {
        $last_month_amount = DONASAI_Stats_Repository::get_revenue_for_period(30, 30);
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
        $cached_mrr = DONASAI_Stats_Repository::get_mrr();
        wp_cache_set($cache_key_mrr, $cached_mrr, 'donasai_stats', 3600);
    }

    // 3. Retention Rate
    $cache_key_repeat = 'donasai_stats_repeat_donors';
    $repeat_donors = wp_cache_get($cache_key_repeat, 'donasai_stats');
    if (false === $repeat_donors) {
        $repeat_donors = DONASAI_Stats_Repository::get_repeat_donor_count();
        wp_cache_set($cache_key_repeat, $repeat_donors, 'donasai_stats', 3600);
    }

    $total_donors = DONASAI_Donation_Repository::get_campaign_donor_count(0);
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
    $cache_key = 'donasai_chart_stats_daily';
    $results = wp_cache_get($cache_key, 'donasai_stats');

    if (false === $results) {
        $results = DONASAI_Stats_Repository::get_daily_chart_data();
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
        $payment_methods = DONASAI_Stats_Repository::get_payment_method_stats();
        wp_cache_set($cache_key_pm, $payment_methods, 'donasai_stats', 3600);
    }

    // Top Campaigns
    $cache_key_top = 'donasai_stats_top_campaigns';
    $top_campaigns = wp_cache_get($cache_key_top, 'donasai_stats');
    if (false === $top_campaigns) {
        $top_campaigns = DONASAI_Stats_Repository::get_top_campaigns(5);
        wp_cache_set($cache_key_top, $top_campaigns, 'donasai_stats', 3600);
    }

    return array(
        'daily_stats' => $daily_stats,
        'payment_methods' => $payment_methods,
        'top_campaigns' => $top_campaigns
    );
}
