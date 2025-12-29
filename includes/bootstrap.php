<?php
/**
 * Bootstrap file to init the plugin and load dependencies.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Core Includes
require_once WPD_PLUGIN_PATH . 'includes/db.php';
require_once WPD_PLUGIN_PATH . 'includes/cpt.php';
require_once WPD_PLUGIN_PATH . 'includes/metabox.php';
require_once WPD_PLUGIN_PATH . 'includes/admin/menu.php';

// Gateways
require_once WPD_PLUGIN_PATH . 'includes/gateways/interface.php';
require_once WPD_PLUGIN_PATH . 'includes/gateways/manual.php';
require_once WPD_PLUGIN_PATH . 'includes/gateways/midtrans.php';
require_once WPD_PLUGIN_PATH . 'includes/services/gateway-registry.php';

// Services
require_once WPD_PLUGIN_PATH . 'includes/services/donation.php';
require_once WPD_PLUGIN_PATH . 'includes/services/email.php';
require_once WPD_PLUGIN_PATH . 'includes/functions-frontend.php';

// Register Gateways
add_action( 'wpd_register_gateways', function() {
    WPD_Gateway_Registry::register_gateway( new WPD_Gateway_Midtrans() );
});

// Initialize
add_action( 'init', array( 'WPD_Gateway_Registry', 'init' ), 5 );
add_action( 'init', array( 'WPD_Email', 'init' ), 5 );

require_once WPD_PLUGIN_PATH . 'includes/services/subscription.php';
require_once WPD_PLUGIN_PATH . 'includes/api/donations-controller.php';
require_once WPD_PLUGIN_PATH . 'includes/api/settings-controller.php';
require_once WPD_PLUGIN_PATH . 'includes/api/campaigns-controller.php';
require_once WPD_PLUGIN_PATH . 'includes/api/fundraisers-controller.php';
require_once WPD_PLUGIN_PATH . 'includes/api/subscriptions-controller.php';
require_once WPD_PLUGIN_PATH . 'includes/api/webhook-controller.php';
// Initialize Headers/Hooks
add_action( 'init', 'wpd_register_cpt' );
