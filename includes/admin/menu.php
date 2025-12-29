<?php
/**
 * Admin Menu & Assets
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Admin Menu
 */
function wpd_register_admin_menu() {
	add_menu_page(
		__( 'Donasi', 'wp-donasi' ),
		__( 'Donasi', 'wp-donasi' ),
		'manage_options',
		'wpd-dashboard',
		'wpd_render_admin_app',
		'dashicons-heart', // Icon
		5
	);

	add_submenu_page(
		'wpd-dashboard',
		__( 'Dashboard', 'wp-donasi' ),
		__( 'Dashboard', 'wp-donasi' ),
		'manage_options',
		'wpd-dashboard',
		'wpd_render_admin_app'
	);

	/* CPT adds its own submenus here automatically via show_in_menu = 'wpd-dashboard' */
    // Manual add to ensure order
	add_submenu_page(
		'wpd-dashboard',
		__( 'Campaigns', 'wp-donasi' ),
		__( 'Campaigns', 'wp-donasi' ),
		'manage_options',
		'edit.php?post_type=wpd_campaign'
	);


	add_submenu_page(
		'wpd-dashboard',
		__( 'Donations', 'wp-donasi' ),
		__( 'Donations', 'wp-donasi' ),
		'manage_options',
		'wpd-donations',
		'wpd_render_admin_app' // Same React App
	);

	add_submenu_page(
		'wpd-dashboard',
		__( 'Settings', 'wp-donasi' ),
		__( 'Settings', 'wp-donasi' ),
		'manage_options',
		'wpd-settings',
		'wpd_render_admin_app' // Same React App
	);
}
add_action( 'admin_menu', 'wpd_register_admin_menu' );

/**
 * Render Admin App Container
 */
function wpd_render_admin_app() {
	?>
	<div id="wpd-admin-app"></div>
	<?php
}

/**
 * Enqueue Admin Assets
 */
function wpd_enqueue_admin_assets( $hook ) {
	// Only load on plugin pages
	if ( strpos( $hook, 'wpd-' ) === false && 'toplevel_page_wpd-dashboard' !== $hook ) {
		return;
	}

	// Dev Mode (needs WPD_DEV_MODE constant)
	if ( defined( 'WPD_DEV_MODE' ) && WPD_DEV_MODE ) {
		// Vite Dev Server
		wp_enqueue_script( 'wpd-admin-dev', 'http://localhost:3001/src/main.tsx', array(), WPD_VERSION, true );
		// Need to inject React Refresh for Vite HMR
		add_action('admin_head', function() {
			echo '<script type="module">
				import RefreshRuntime from "http://localhost:3001/@react-refresh"
				RefreshRuntime.injectIntoGlobalHook(window)
				window.$RefreshReg$ = () => {}
				window.$RefreshSig$ = () => (type) => type
				window.__vite_plugin_react_preamble_installed__ = true
			</script>';
		});
	} else {
		// Production Build
		$manifest_path = WPD_PLUGIN_PATH . 'build/.vite/manifest.json';
		if ( file_exists( $manifest_path ) ) {
			$manifest = json_decode( file_get_contents( $manifest_path ), true );
			$entry = $manifest['src/main.tsx'];
			
			if ( isset( $entry['file'] ) ) {
				wp_enqueue_script( 'wpd-admin-app', WPD_PLUGIN_URL . 'build/' . $entry['file'], array(), WPD_VERSION, true );
			}
			if ( isset( $entry['css'] ) ) {
				foreach ( $entry['css'] as $css_file ) {
					wp_enqueue_style( 'wpd-admin-css-' . $css_file, WPD_PLUGIN_URL . 'build/' . $css_file, array(), WPD_VERSION );
				}
			}
		}
	}

	// Localize Script for Nonce/Settings
	wp_localize_script( defined( 'WPD_DEV_MODE' ) && WPD_DEV_MODE ? 'wpd-admin-dev' : 'wpd-admin-app', 'wpdSettings', array(
		'root'  => esc_url_raw( rest_url() ),
		'nonce' => wp_create_nonce( 'wp_rest' ),
	) );
}
add_action( 'admin_enqueue_scripts', 'wpd_enqueue_admin_assets' );
