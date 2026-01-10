<?php
/**
 * Admin Menu & Assets
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Register Admin Menu
 */
function wpd_register_admin_menu()
{
	add_menu_page(
		__('Donasai', 'donasai'),
		__('Donasai', 'donasai'),
		'manage_options',
		'wpd-dashboard',
		'wpd_render_admin_app',
		'dashicons-heart', // Icon
		5
	);

	add_submenu_page(
		'wpd-dashboard',
		__('Dashboard', 'donasai'),
		__('Dashboard', 'donasai'),
		'manage_options',
		'wpd-dashboard',
		'wpd_render_admin_app'
	);

	/* CPT adds its own submenus here automatically via show_in_menu = 'wpd-dashboard' */
	// Manual add to ensure order
	add_submenu_page(
		'wpd-dashboard',
		__('Campaigns', 'donasai'),
		__('Campaigns', 'donasai'),
		'manage_options',
		'edit.php?post_type=wpd_campaign'
	);

	add_submenu_page(
		'wpd-dashboard',
		__('Categories', 'donasai'),
		__('Categories', 'donasai'),
		'manage_options',
		'edit-tags.php?taxonomy=donation_category&post_type=wpd_campaign'
	);


	add_submenu_page(
		'wpd-dashboard',
		__('Donations', 'donasai'),
		__('Donations', 'donasai'),
		'manage_options',
		'wpd-donations',
		'wpd_render_admin_app' // Same React App
	);

	add_submenu_page(
		'wpd-dashboard',
		__('Fundraisers', 'donasai'),
		__('Fundraisers', 'donasai'),
		'manage_options',
		'wpd-fundraisers',
		'wpd_render_admin_app' // Same React App
	);

	add_submenu_page(
		'wpd-dashboard',
		__('Settings', 'donasai'),
		__('Settings', 'donasai'),
		'manage_options',
		'wpd-settings',
		'wpd_render_admin_app' // Same React App
	);
}
add_action('admin_menu', 'wpd_register_admin_menu');

/**
 * Keep Menu Open for Taxonomy
 */
function wpd_menu_highlight($parent_file)
{
	global $current_screen;
	if (isset($current_screen->taxonomy) && 'donation_category' === $current_screen->taxonomy) {
		return 'wpd-dashboard';
	}
	return $parent_file;
}
add_filter('parent_file', 'wpd_menu_highlight');

/**
 * Render Admin App Container
 */
function wpd_render_admin_app()
{
	?>
	<div id="wpd-admin-app"></div>
	<?php
}

/**
 * Enqueue Admin Assets
 */
function wpd_enqueue_admin_assets($hook)
{
	// Only load on plugin pages
	if (strpos($hook, 'wpd-') === false && 'toplevel_page_wpd-dashboard' !== $hook) {
		return;
	}

	// Enqueue Media Scripts
	wp_enqueue_media();

	// Dev Mode (needs WPD_DEV_MODE constant)
	if (defined('WPD_DEV_MODE') && WPD_DEV_MODE) {
		// Vite Dev Server
		wp_enqueue_script('wpd-vite-client', 'http://localhost:3001/@vite/client', array(), null, true);
		wp_enqueue_script('wpd-admin-dev', 'http://localhost:3001/src/main.tsx', array('wpd-vite-client'), null, true);

		// Need to inject React Refresh for Vite HMR
		add_action('admin_head', function () {
			wp_print_inline_script_tag(
				'import RefreshRuntime from "http://localhost:3001/@react-refresh"
				RefreshRuntime.injectIntoGlobalHook(window)
				window.$RefreshReg$ = () => {}
				window.$RefreshSig$ = () => (type) => type
				window.__vite_plugin_react_preamble_installed__ = true',
				array('type' => 'module')
			);
		});


	} else {
		// Production Build
		$manifest_path = WPD_PLUGIN_PATH . 'build/.vite/manifest.json';
		if (file_exists($manifest_path)) {
			$manifest = json_decode(file_get_contents($manifest_path), true);
			$entry = $manifest['src/main.tsx'];

			if (isset($entry['file'])) {
				wp_enqueue_script('wpd-admin-app', WPD_PLUGIN_URL . 'build/' . $entry['file'], array(), WPD_VERSION, true);
			}
			if (isset($entry['css'])) {
				foreach ($entry['css'] as $css_file) {
					wp_enqueue_style('wpd-admin-css-' . $css_file, WPD_PLUGIN_URL . 'build/' . $css_file, array(), WPD_VERSION);
				}
			}
		}
	}

	// Determine Initial Path based on Page Slug
	$current_page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : 'wpd-dashboard';
	$initial_path = '/';

	switch ($current_page) {
		case 'wpd-donations':
			$initial_path = '/donations';
			break;
		case 'wpd-fundraisers':
			$initial_path = '/fundraisers';
			break;
		case 'wpd-settings':
			$initial_path = '/settings';
			break;
		default:
			$initial_path = '/';
			break;
	}

	// Localize Script for Nonce/Settings
	wp_localize_script(defined('WPD_DEV_MODE') && WPD_DEV_MODE ? 'wpd-admin-dev' : 'wpd-admin-app', 'wpdSettings', array(
		'root' => esc_url_raw(rest_url()),
		'nonce' => wp_create_nonce('wp_rest'),
		'initialPath' => $initial_path,
		'isPro' => wpd_is_pro_installed(),
	));
	// Add type="module" to dev and production scripts
	add_filter('script_loader_tag', function ($tag, $handle, $src) {
		if (in_array($handle, array('wpd-vite-client', 'wpd-admin-dev', 'wpd-admin-app'), true)) {
			// Modify existing tag to add type="module" without constructing raw script tag
			$tag = str_replace(' src=', ' type="module" src=', $tag);
		}
		return $tag;
	}, 10, 3);
}
add_action('admin_enqueue_scripts', 'wpd_enqueue_admin_assets');
