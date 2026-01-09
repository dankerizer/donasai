<?php
/**
 * Fired when the plugin is uninstalled.
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Check if user has opted to clean up data
$general_settings = get_option('wpd_settings_general', []);

if (empty($general_settings)) {
    return;
}

global $wpdb;

/**
 * 1. Drop Tables if requested
 */
if (!empty($general_settings['delete_on_uninstall_tables'])) {
    $tables = array(
        $wpdb->prefix . 'wpd_donations',
        $wpdb->prefix . 'wpd_campaign_meta',
        $wpdb->prefix . 'wpd_fundraisers',
        $wpdb->prefix . 'wpd_referral_logs',
        $wpdb->prefix . 'wpd_subscriptions'
    );

    foreach ($tables as $table) {
        $wpdb->query("DROP TABLE IF EXISTS {$table}");
    }
}

/**
 * 2. Delete Options if requested
 */
if (!empty($general_settings['delete_on_uninstall_settings'])) {
    $options = array(
        'wpd_settings_general',
        'wpd_settings_donation',
        'wpd_settings_appearance',
        'wpd_settings_notifications',
        'wpd_license', // Pro license
        'wpd_version',
        'wpd_rewrite_flush_needed'
    );

    // Pro specific options that might exist
    $pro_options = $wpdb->get_col("SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE 'wpd_pro_%'");
    if (!empty($pro_options)) {
        $options = array_merge($options, $pro_options);
    }

    foreach ($options as $option) {
        delete_option($option);
    }
}
