<?php
/**
 * Plugin Name: wp-donasi - Donation & Fundraising
 * Plugin URI: https://donasi.xyz/wp
 * Description: Modern WordPress donation plugin with campaign management, payment gateways, and React dashboard.
 * Version: 1.0.0
 * Author: Hadie Danker
 * Requires at least: 6.4
 * Tested up to: 6.6
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * Text Domain: wp-donasi
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin Constants
define( 'WPD_VERSION', '1.0.0' );
define( 'WPD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WPD_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPD_TABLE_PREFIX', 'wpd_' );
if(!defined('WPD_DEV_MODE')) {
    define('WPD_DEV_MODE', false);
}

// Include Bootstrap
require_once WPD_PLUGIN_PATH . 'includes/bootstrap.php';

// Activation Hook
register_activation_hook( __FILE__, 'wpd_activate' );
function wpd_activate() {
	// Create tables
	if ( function_exists( 'wpd_create_tables' ) ) {
		wpd_create_tables();
	}
	
	// Flush rewrite rules for CPT
	flush_rewrite_rules();
}

// Deactivation Hook
register_deactivation_hook( __FILE__, 'wpd_deactivate' );
function wpd_deactivate() {
	flush_rewrite_rules();
}
