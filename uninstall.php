<?php
/**
 * Fired when the plugin is uninstalled.
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Check if user has opted to clean up data
$donasai_general_settings = get_option('donasai_settings_general', []);

if (empty($donasai_general_settings)) {
    return;
}

global $wpdb;

/**
 * 1. Drop Tables if requested
 */
if (!empty($donasai_general_settings['delete_on_uninstall_tables'])) {
    $donasai_tables = array(
        $wpdb->prefix . 'donasai_donations',
        $wpdb->prefix . 'donasai_campaign_meta',
        $wpdb->prefix . 'donasai_fundraisers',
        $wpdb->prefix . 'donasai_referral_logs',
        $wpdb->prefix . 'donasai_subscriptions'
    );

    foreach ($donasai_tables as $donasai_table) {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange -- Complete database cleanup on plugin uninstall as requested by user.
        $wpdb->query($wpdb->prepare("DROP TABLE IF EXISTS %i", $donasai_table));
    }
}

/**
 * 2. Delete Options if requested
 */
if (!empty($donasai_general_settings['delete_on_uninstall_settings'])) {
    $donasai_options = array(
        'donasai_settings_general',
        'donasai_settings_donation',
        'donasai_settings_appearance',
        'donasai_settings_notifications',
        'donasai_license', // Pro license
        'donasai_version',
        'donasai_rewrite_flush_needed'
    );

    // Pro specific options that might exist
    $donasai_pro_options = $wpdb->get_col($wpdb->prepare("SELECT option_name FROM %i WHERE option_name LIKE %s", $wpdb->options, 'donasai_pro_%')); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Administrative cleanup on plugin uninstall.
    if (!empty($donasai_pro_options)) {
        $donasai_options = array_merge($donasai_options, $donasai_pro_options);
    }

    foreach ($donasai_options as $donasai_option) {
        delete_option($donasai_option);
    }
}
