<?php
/**
 * Frontend Functions & Template Loader
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Template Loader for Custom Post Types
 */
function wpd_template_loader( $template ) {
	if ( is_singular( 'wpd_campaign' ) ) {
		$plugin_template = WPD_PLUGIN_PATH . 'frontend/templates/campaign-single.php';
		if ( file_exists( $plugin_template ) ) {
			return $plugin_template;
		}
	}
	return $template;
}
add_filter( 'template_include', 'wpd_template_loader' );

/**
 * Template Loader for Custom Post Types
 */
function wpd_get_template_part( $slug, $name = null ) {
    $template = '';

    // Look in yourtheme/slug-name.php
    if ( $name ) {
        $template = locate_template( array( "wp-donasi/{$slug}-{$name}.php", "{$slug}-{$name}.php" ) );
    }

    // Get default slug-name.php
    if ( ! $template && $name && file_exists( WPD_PLUGIN_PATH . "frontend/templates/{$slug}-{$name}.php" ) ) {
        $template = WPD_PLUGIN_PATH . "frontend/templates/{$slug}-{$name}.php";
    }

    // If not found, look for slug.php
    if ( ! $template ) {
        $template = locate_template( array( "wp-donasi/{$slug}.php", "{$slug}.php" ) );
    }
    
    // Default slug.php
    if ( ! $template && file_exists( WPD_PLUGIN_PATH . "frontend/templates/{$slug}.php" ) ) {
        $template = WPD_PLUGIN_PATH . "frontend/templates/{$slug}.php";
    }

    if ( $template ) {
        load_template( $template, false );
    }
}

/**
 * Enqueue Frontend Assets
 */
function wpd_enqueue_frontend_assets() {
	if ( is_singular( 'wpd_campaign' ) ) {
		wp_enqueue_style( 'wp-donasi-frontend', WPD_PLUGIN_URL . 'frontend/assets/frontend.css', array(), WPD_VERSION );
		// Ensure Tailwind/Utility classes are handled. For MVP without build step, we might write raw CSS or use CDN for quick demo (not recommended for production plugin, but okay for MVP dev).
		// Better: write custom CSS in frontend.css that mimics Tailwind utilities needed.
	}
}
add_action( 'wp_enqueue_scripts', 'wpd_enqueue_frontend_assets' );

/**
 * Get Donation Form HTML
 */
function wpd_get_donation_form_html( $campaign_id ) {
	ob_start();
	include WPD_PLUGIN_PATH . 'frontend/templates/donation-form.php';
	return ob_get_clean();
}

/**
 * Donor Dashboard Shortcode [wpd_my_donations]
 */
function wpd_shortcode_my_donations() {
	ob_start();
	include WPD_PLUGIN_PATH . 'frontend/templates/donor-dashboard.php';
	return ob_get_clean();
}
add_shortcode( 'wpd_my_donations', 'wpd_shortcode_my_donations' );
