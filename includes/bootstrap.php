<?php
/**
 * Bootstrap file to init the plugin and load dependencies.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load Includes
require_once WPD_PLUGIN_PATH . 'includes/cpt.php';
require_once WPD_PLUGIN_PATH . 'includes/db.php';
require_once WPD_PLUGIN_PATH . 'includes/services/donation.php';
require_once WPD_PLUGIN_PATH . 'includes/services/email.php';
require_once WPD_PLUGIN_PATH . 'includes/functions-frontend.php';
require_once WPD_PLUGIN_PATH . 'includes/metabox.php';
require_once WPD_PLUGIN_PATH . 'includes/admin/menu.php';
require_once WPD_PLUGIN_PATH . 'includes/api/donations-controller.php';
require_once WPD_PLUGIN_PATH . 'includes/api/settings-controller.php';
require_once WPD_PLUGIN_PATH . 'includes/gateways/interface.php';
require_once WPD_PLUGIN_PATH . 'includes/gateways/stripe.php';

// Initialize Headers/Hooks
add_action( 'init', 'wpd_register_cpt' );
