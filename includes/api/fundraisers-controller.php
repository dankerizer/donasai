<?php
/**
 * REST API for Fundraisers
 */

if (!defined('ABSPATH')) {
	exit;
}

require_once WPD_PLUGIN_PATH . 'includes/services/fundraiser.php';

add_action('rest_api_init', function () {
	// POST /fundraisers (Register)
	register_rest_route('wpd/v1', '/fundraisers', array(
		'methods' => 'POST',
		'callback' => 'wpd_api_register_fundraiser',
		'permission_callback' => function () {
			return is_user_logged_in(); // Only logged-in users can become fundraisers
		},
	));

	// GET /fundraisers (List/Stats)
	register_rest_route('wpd/v1', '/fundraisers', array(
		'methods' => 'GET',
		'callback' => 'wpd_api_get_fundraisers',
		// Public route for leaderboard? Or admin only? 
		// If accessing specific user stats -> auth required.
		// If accessing leaderboard -> public.
		'permission_callback' => '__return_true',
	));
});

function wpd_api_register_fundraiser($request)
{
	$params = $request->get_json_params();
	$campaign_id = isset($params['campaign_id']) ? absint($params['campaign_id']) : 0;
	$user_id = get_current_user_id();

	if (!$campaign_id) {
		return new WP_Error('missing_campaign', 'Campaign ID is required', array('status' => 400));
	}

	$service = new WPD_Fundraiser_Service();
	$fundraiser = $service->register_fundraiser($user_id, $campaign_id);

	if (!$fundraiser) {
		return new WP_Error('registration_failed', 'Could not register fundraiser', array('status' => 500));
	}

	return rest_ensure_response(array(
		'success' => true,
		'referral_code' => $fundraiser->referral_code,
		'referral_link' => get_permalink($campaign_id) . '?ref=' . $fundraiser->referral_code
	));
}

function wpd_api_get_fundraisers($request)
{
	$service = new WPD_Fundraiser_Service();
	$params = $request->get_params(); // GET params

	// If campaign_id provided, return leaderboard
	if (isset($params['campaign_id'])) {
		$campaign_id = absint($params['campaign_id']);
		$limit = isset($params['limit']) ? absint($params['limit']) : 10;
		$leaderboard = $service->get_leaderboard($campaign_id, $limit);
		return rest_ensure_response($leaderboard);
	}

	// If 'mine' param, return current user's fundraiser stats
	if (isset($params['mine']) && is_user_logged_in()) {
		// return list of campaigns I am fundraising for
		// Need a new method in Service for this.
		// For now, return empty or implement later.
		global $wpdb;
		$user_id = get_current_user_id();

		$cache_key = 'wpd_user_fundraisers_' . $user_id;
		$results = wp_cache_get($cache_key, 'wpd_fundraisers');

		if (false === $results) {
			$results = $wpdb->get_results($wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}wpd_fundraisers WHERE user_id = %d",
				$user_id
			));
			wp_cache_set($cache_key, $results, 'wpd_fundraisers', 300);
		}
		return rest_ensure_response($results);
	}

	// Admin view: List all (Requires auth)
	if (current_user_can('manage_options')) {
		// Implement full list logic here
		// For now simple select all limited
		global $wpdb;

		$cache_key = 'wpd_admin_fundraisers_list';
		$results = wp_cache_get($cache_key, 'wpd_fundraisers');

		if (false === $results) {
			$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}wpd_fundraisers ORDER BY created_at DESC LIMIT %d", 50));
			wp_cache_set($cache_key, $results, 'wpd_fundraisers', 60);
		}
		return rest_ensure_response($results);
	}

	return new WP_Error('forbidden', 'You are not allowed to view this data', array('status' => 403));
}
