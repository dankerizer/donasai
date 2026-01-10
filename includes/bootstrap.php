<?php
/**
 * Bootstrap file to init the plugin and load dependencies.
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define Constants
if (!defined('WPD_VERSION')) {
    define('WPD_VERSION', '1.0.0');
}
if (!defined('WPD_PLUGIN_PATH')) {
    define('WPD_PLUGIN_PATH', plugin_dir_path(__DIR__));
}
if (!defined('WPD_PLUGIN_URL')) {
    define('WPD_PLUGIN_URL', plugin_dir_url(__DIR__) . '/'); // simplistic fallback
}

// Temporary: Force Rewrite Flush to fix 404/Routing issues
update_option('wpd_rewrite_flush_needed', true);

// Core Includes
require_once WPD_PLUGIN_PATH . 'includes/db.php';
require_once WPD_PLUGIN_PATH . 'includes/cpt.php';
require_once WPD_PLUGIN_PATH . 'includes/metabox.php';
require_once WPD_PLUGIN_PATH . 'includes/admin/menu.php';
require_once WPD_PLUGIN_PATH . 'includes/admin/dashboard-widget.php';
require_once WPD_PLUGIN_PATH . 'includes/admin/campaign-columns.php';

// Gateways
require_once WPD_PLUGIN_PATH . 'includes/gateways/interface.php';
require_once WPD_PLUGIN_PATH . 'includes/gateways/manual.php';
require_once WPD_PLUGIN_PATH . 'includes/gateways/midtrans.php';
require_once WPD_PLUGIN_PATH . 'includes/services/gateway-registry.php';

// Services
require_once WPD_PLUGIN_PATH . 'includes/services/donation.php';
require_once WPD_PLUGIN_PATH . 'includes/services/email.php';
require_once WPD_PLUGIN_PATH . 'includes/frontend/css-loader.php'; // Load Dynamic CSS
require_once WPD_PLUGIN_PATH . 'includes/functions-frontend.php';

// Register Gateways
add_action('wpd_register_gateways', function () {
    WPD_Gateway_Registry::register_gateway(new WPD_Gateway_Midtrans());
});

// Initialize
add_action('init', array('WPD_Gateway_Registry', 'init'), 5);
add_action('init', array('WPD_Email', 'init'), 5);

require_once WPD_PLUGIN_PATH . 'includes/services/subscription.php';
require_once WPD_PLUGIN_PATH . 'includes/api/donations-controller.php';
require_once WPD_PLUGIN_PATH . 'includes/api/settings-controller.php';
require_once WPD_PLUGIN_PATH . 'includes/api/campaigns-controller.php';
require_once WPD_PLUGIN_PATH . 'includes/api/fundraisers-controller.php';
require_once WPD_PLUGIN_PATH . 'includes/api/subscriptions-controller.php';
require_once WPD_PLUGIN_PATH . 'includes/api/webhook-controller.php';
// Initialize Headers/Hooks
add_action('init', 'wpd_register_cpt');

/**
 * Check if Donasai Pro is Active
 */
function wpd_is_pro_active()
{
    if (!function_exists('is_plugin_active')) {
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }
    // Check if plugin active
    $is_active = is_plugin_active('donasai-pro/donasai-pro.php');

    // Check if license is valid
    $license_status = get_option('wpd_pro_license_status');
    $is_license_valid = ($license_status === 'valid' || $license_status === 'active');

    return $is_active && $is_license_valid;
}
