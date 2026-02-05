<?php
/**
 * Fundraiser Service
 * Handles business logic for fundraisers and affiliate tracking.
 */

if (!defined('ABSPATH')) {
	exit;
}

class DONASAI_Fundraiser_Service
{
	/**
	 * Register a user as fundraiser for a campaign
	 */
	public function register_fundraiser($user_id, $campaign_id)
	{
		// Check if already registered
		$cache_key = 'donasai_fundraiser_user_v2_' . $user_id . '_' . $campaign_id;
		$existing = wp_cache_get($cache_key, 'donasai_fundraisers');

		if (false === $existing) {
			$existing = DONASAI_Fundraiser_Repository::get_by_user_campaign($user_id, $campaign_id);
			if ($existing) {
				wp_cache_set($cache_key, $existing, 'donasai_fundraisers', 3600);
			}
		}

		if ($existing) {
			return $existing;
		}

		// Generate unique referral code
		$user = get_userdata($user_id);
		$base_code = sanitize_title($user->user_login);
		$code = $base_code;
		$counter = 1;

		while (DONASAI_Fundraiser_Repository::get_by_code($code)) {
			$code = $base_code . $counter;
			$counter++;
		}

		$inserted_id = DONASAI_Fundraiser_Repository::create([
			'user_id' => $user_id,
			'campaign_id' => $campaign_id,
			'referral_code' => $code,
			'created_at' => current_time('mysql')
		]);

		// Invalidate caches
		wp_cache_delete($cache_key, 'donasai_fundraisers');
		
		return $this->get_by_id($inserted_id);
	}

	/**
	 * Get fundraiser by referral code
	 */
	public function get_by_code($code)
	{
		$cache_key = 'donasai_fundraiser_code_' . $code;
		$found = wp_cache_get($cache_key, 'donasai_fundraisers');

		if (false === $found) {
			$found = DONASAI_Fundraiser_Repository::get_by_code($code);
			wp_cache_set($cache_key, $found, 'donasai_fundraisers', 3600);
		}
		return $found;
	}

	/**
	 * Get fundraiser by ID
	 */
	public function get_by_id($id)
	{
		$id = intval($id);
		$cache_key = 'donasai_fundraiser_id_' . $id;
		$found = wp_cache_get($cache_key, 'donasai_fundraisers');

		if (false === $found) {
			$found = DONASAI_Fundraiser_Repository::get_by_id($id);
			wp_cache_set($cache_key, $found, 'donasai_fundraisers', 3600);
		}
		return $found;
	}

	/**
	 * Update stats when a donation is made
	 */
	public function record_donation($fundraiser_id, $amount)
	{
		DONASAI_Fundraiser_Repository::update_stats($fundraiser_id, $amount);
		// Invalidate cache
		wp_cache_delete('donasai_fundraiser_id_' . $fundraiser_id, 'donasai_fundraisers');
	}

	/**
	 * Get top fundraisers for a campaign
	 */
	public function get_leaderboard($campaign_id, $limit = 10)
	{
		$cache_key = 'donasai_leaderboard_' . $campaign_id . '_' . $limit;
		$results = wp_cache_get($cache_key, 'donasai_fundraisers');

		if (false === $results) {
			$results = DONASAI_Fundraiser_Repository::get_leaderboard($campaign_id, $limit);
			wp_cache_set($cache_key, $results, 'donasai_fundraisers', 300);
		}
		return $results;
	}

	/**
	 * Log a visit from a referral link
	 */
	public function track_visit($fundraiser_id, $campaign_id)
	{
		$ip_address = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
		$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])) : '';

		DONASAI_Fundraiser_Repository::log_visit([
			'fundraiser_id' => $fundraiser_id,
			'campaign_id' => $campaign_id,
			'ip_address' => substr($ip_address, 0, 100),
			'user_agent' => substr($user_agent, 0, 255),
			'created_at' => current_time('mysql')
		]);
	}
}
