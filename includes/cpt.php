<?php
/**
 * Custom Post Type Registration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wpd_register_cpt() {
	$labels = array(
		'name'                  => _x( 'Campaigns', 'Post Type General Name', 'wp-donasi' ),
		'singular_name'         => _x( 'Campaign', 'Post Type Singular Name', 'wp-donasi' ),
		'menu_name'             => __( 'Donasi', 'wp-donasi' ),
		'name_admin_bar'        => __( 'Campaign', 'wp-donasi' ),
		'archives'              => __( 'Campaign Archives', 'wp-donasi' ),
		'attributes'            => __( 'Campaign Attributes', 'wp-donasi' ),
		'parent_item_colon'     => __( 'Parent Campaign:', 'wp-donasi' ),
		'all_items'             => __( 'All Campaigns', 'wp-donasi' ),
		'add_new_item'          => __( 'Add New Campaign', 'wp-donasi' ),
		'add_new'               => __( 'Add New', 'wp-donasi' ),
		'new_item'              => __( 'New Campaign', 'wp-donasi' ),
		'edit_item'             => __( 'Edit Campaign', 'wp-donasi' ),
		'update_item'           => __( 'Update Campaign', 'wp-donasi' ),
		'view_item'             => __( 'View Campaign', 'wp-donasi' ),
		'view_items'            => __( 'View Campaigns', 'wp-donasi' ),
		'search_items'          => __( 'Search Campaign', 'wp-donasi' ),
		'not_found'             => __( 'Not found', 'wp-donasi' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'wp-donasi' ),
		'featured_image'        => __( 'Featured Image', 'wp-donasi' ),
		'set_featured_image'    => __( 'Set featured image', 'wp-donasi' ),
		'remove_featured_image' => __( 'Remove featured image', 'wp-donasi' ),
		'use_featured_image'    => __( 'Use as featured image', 'wp-donasi' ),
		'insert_into_item'      => __( 'Insert into campaign', 'wp-donasi' ),
		'uploaded_to_this_item' => __( 'Uploaded to this campaign', 'wp-donasi' ),
		'items_list'            => __( 'Campaigns list', 'wp-donasi' ),
		'items_list_navigation' => __( 'Campaigns list navigation', 'wp-donasi' ),
		'filter_items_list'     => __( 'Filter campaigns list', 'wp-donasi' ),
	);
	$args = array(
		'label'                 => __( 'Campaign', 'wp-donasi' ),
		'description'           => __( 'Donation Campaigns', 'wp-donasi' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => false, // We manually add it in admin/menu.php
		'menu_position'         => 20,
		'menu_icon'             => 'dashicons-heart',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'post',
		'show_in_rest'          => true,
        'rewrite'               => array( 'slug' => (get_option('wpd_settings_general')['campaign_slug'] ?? 'campaign') ),
	);
	register_post_type( 'wpd_campaign', $args );

	// Register Taxonomy
	$tax_labels = array(
		'name'              => _x( 'Categories', 'taxonomy general name', 'wp-donasi' ),
		'singular_name'     => _x( 'Category', 'taxonomy singular name', 'wp-donasi' ),
		'search_items'      => __( 'Search Categories', 'wp-donasi' ),
		'all_items'         => __( 'All Categories', 'wp-donasi' ),
		'parent_item'       => __( 'Parent Category', 'wp-donasi' ),
		'parent_item_colon' => __( 'Parent Category:', 'wp-donasi' ),
		'edit_item'         => __( 'Edit Category', 'wp-donasi' ),
		'update_item'       => __( 'Update Category', 'wp-donasi' ),
		'add_new_item'      => __( 'Add New Category', 'wp-donasi' ),
		'new_item_name'     => __( 'New Category Name', 'wp-donasi' ),
		'menu_name'         => __( 'Categories', 'wp-donasi' ),
	);

	$tax_args = array(
		'hierarchical'      => true,
		'labels'            => $tax_labels,
		'show_ui'           => true,
		'show_admin_column' => true, // AUTO-ADDS COLUMN
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'donation-category' ),
	);

    register_taxonomy( 'donation_category', array( 'wpd_campaign' ), $tax_args );
}

/**
 * Add Rewrite Endpoints
 */
add_action( 'init', 'wpd_add_rewrite_endpoints' );
function wpd_add_rewrite_endpoints() {
    $payment_slug = get_option('wpd_settings_general')['payment_slug'] ?? 'pay';
    add_rewrite_endpoint( $payment_slug, EP_PERMALINK | EP_PAGES );
    
    // Thank You Page Endpoint
    $thankyou_slug = get_option('wpd_settings_general')['thankyou_slug'] ?? 'thank-you';
    add_rewrite_endpoint( $thankyou_slug, EP_PERMALINK | EP_PAGES );
}

/**
 * Maybe Flush Rewrite Rules
 * Runs on admin_init to ensure all init hooks (CPT/Endpoints) are registered first.
 */
add_action( 'admin_init', 'wpd_maybe_flush_rewrites' );
function wpd_maybe_flush_rewrites() {
    // Check if rewrite rules need flushing (e.g. after plugin update or endpoint change)
    // For development/debugging: force flush if we are just setting this up
    if ( get_option( 'wpd_rewrite_flush_needed' ) || isset($_GET['flush_wpd']) ) {
        flush_rewrite_rules();
        delete_option( 'wpd_rewrite_flush_needed' );
    }
}
// Hook to init as well for frontend flushes if really needed during dev
add_action( 'init', 'wpd_maybe_flush_rewrites', 99 );
