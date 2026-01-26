<?php
/**
 * Plugin Name:       Donasai - Platform Donasi & Penggalangan Dana
 * Plugin URI:        https://wordpress.org/plugins/donasai
 * Description:       Donasai is a complete WordPress donation and fundraising platform designed for foundations, mosques, and communities.
 * Version:           1.0.2
 * Author:            Hadie Danker
 * Author URI:        https://profiles.wordpress.org/hadie-danker
 * Requires at least: 6.4
 * Tested up to:      6.9
 * Requires PHP:      7.4
 * License:           GPL v2 or later
 * Text Domain:       donasai
 * Domain Path:       /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

// Plugin Constants
define('DONASAI_VERSION', '1.0.2');
define('DONASAI_PLUGIN_URL', plugin_dir_url(__FILE__));
define('DONASAI_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('DONASAI_TABLE_PREFIX', 'donasai_');
if (!defined('DONASAI_DEV_MODE')) {
    define('DONASAI_DEV_MODE', false);
}

// Include Bootstrap
require_once DONASAI_PLUGIN_PATH . 'includes/bootstrap.php';




// Activation Hook
register_activation_hook(__FILE__, 'donasai_activate');
function donasai_activate()
{
	// Create tables
	if (function_exists('donasai_create_tables')) {
		donasai_create_tables();
	}

	// Flush rewrite rules for CPT
	flush_rewrite_rules();
}

// Deactivation Hook
register_deactivation_hook(__FILE__, 'donasai_deactivate');
function donasai_deactivate()
{
	flush_rewrite_rules();
}
