<?php
/**
 * Custom Post Type Registration
 */

if (!defined('ABSPATH')) {
	exit;
}

function wpd_register_cpt()
{
	$labels = array(
		'name' => _x('Campaigns', 'Post Type General Name', 'donasai'),
		'singular_name' => _x('Campaign', 'Post Type Singular Name', 'donasai'),
		'menu_name' => __('Donasi', 'donasai'),
		'name_admin_bar' => __('Campaign', 'donasai'),
		'archives' => __('Campaign Archives', 'donasai'),
		'attributes' => __('Campaign Attributes', 'donasai'),
		'parent_item_colon' => __('Parent Campaign:', 'donasai'),
		'all_items' => __('All Campaigns', 'donasai'),
		'add_new_item' => __('Add New Campaign', 'donasai'),
		'add_new' => __('Add New', 'donasai'),
		'new_item' => __('New Campaign', 'donasai'),
		'edit_item' => __('Edit Campaign', 'donasai'),
		'update_item' => __('Update Campaign', 'donasai'),
		'view_item' => __('View Campaign', 'donasai'),
		'view_items' => __('View Campaigns', 'donasai'),
		'search_items' => __('Search Campaign', 'donasai'),
		'not_found' => __('Not found', 'donasai'),
		'not_found_in_trash' => __('Not found in Trash', 'donasai'),
		'featured_image' => __('Featured Image', 'donasai'),
		'set_featured_image' => __('Set featured image', 'donasai'),
		'remove_featured_image' => __('Remove featured image', 'donasai'),
		'use_featured_image' => __('Use as featured image', 'donasai'),
		'insert_into_item' => __('Insert into campaign', 'donasai'),
		'uploaded_to_this_item' => __('Uploaded to this campaign', 'donasai'),
		'items_list' => __('Campaigns list', 'donasai'),
		'items_list_navigation' => __('Campaigns list navigation', 'donasai'),
		'filter_items_list' => __('Filter campaigns list', 'donasai'),
	);
	$args = array(
		'label' => __('Campaign', 'donasai'),
		'description' => __('Donation Campaigns', 'donasai'),
		'labels' => $labels,
		'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
		'hierarchical' => false,
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => false, // We manually add it in admin/menu.php
		'menu_position' => 20,
		'menu_icon' => 'dashicons-heart',
		'show_in_admin_bar' => true,
		'show_in_nav_menus' => true,
		'can_export' => true,
		'has_archive' => true,
		'exclude_from_search' => false,
		'publicly_queryable' => true,
		'capability_type' => 'post',
		'show_in_rest' => true,
		'rewrite' => array('slug' => (get_option('wpd_settings_general')['campaign_slug'] ?? 'campaign')),
	);
	register_post_type('wpd_campaign', $args);

	// Register Taxonomy
	$tax_labels = array(
		'name' => _x('Categories', 'taxonomy general name', 'donasai'),
		'singular_name' => _x('Category', 'taxonomy singular name', 'donasai'),
		'search_items' => __('Search Categories', 'donasai'),
		'all_items' => __('All Categories', 'donasai'),
		'parent_item' => __('Parent Category', 'donasai'),
		'parent_item_colon' => __('Parent Category:', 'donasai'),
		'edit_item' => __('Edit Category', 'donasai'),
		'update_item' => __('Update Category', 'donasai'),
		'add_new_item' => __('Add New Category', 'donasai'),
		'new_item_name' => __('New Category Name', 'donasai'),
		'menu_name' => __('Categories', 'donasai'),
	);

	$tax_args = array(
		'hierarchical' => true,
		'labels' => $tax_labels,
		'show_ui' => true,
		'show_admin_column' => true, // AUTO-ADDS COLUMN
		'query_var' => true,
		'rewrite' => array('slug' => 'donation-category'),
	);

	register_taxonomy('donation_category', array('wpd_campaign'), $tax_args);
}

/**
 * Add Rewrite Endpoints
 */
add_action('init', 'wpd_add_rewrite_endpoints');
function wpd_add_rewrite_endpoints()
{
	$payment_slug = get_option('wpd_settings_general')['payment_slug'] ?? 'pay';
	add_rewrite_endpoint($payment_slug, EP_PERMALINK | EP_PAGES);

	// Thank You Page Endpoint
	$thankyou_slug = get_option('wpd_settings_general')['thankyou_slug'] ?? 'thank-you';
	add_rewrite_endpoint($thankyou_slug, EP_PERMALINK | EP_PAGES);
}

/**
 * Maybe Flush Rewrite Rules
 * Runs on admin_init to ensure all init hooks (CPT/Endpoints) are registered first.
 */
add_action('admin_init', 'wpd_maybe_flush_rewrites');
function wpd_maybe_flush_rewrites()
{
	// Check if rewrite rules need flushing (e.g. after plugin update or endpoint change)
	// For development/debugging: force flush if we are just setting this up
	if (get_option('wpd_rewrite_flush_needed')) {
		flush_rewrite_rules();
		delete_option('wpd_rewrite_flush_needed');
	}
}
// Hook to init as well for frontend flushes if really needed during dev
add_action('init', 'wpd_maybe_flush_rewrites', 99);
