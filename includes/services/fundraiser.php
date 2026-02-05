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
	private $table_name;

	public function __construct()
	{
		global $wpdb;
		$this->table_name = $wpdb->prefix . 'donasai_fundraisers';
	}

	/**
	 * Register a user as fundraiser for a campaign
	 */
	public function register_fundraiser($user_id, $campaign_id)
	{
		global $wpdb;

		// Check if already registered
		$cache_key = 'donasai_fundraiser_user_' . $user_id . '_' . $campaign_id;
		$existing = wp_cache_get($cache_key, 'donasai_fundraisers');

		if (false === $existing) {
			$existing = $wpdb->get_row($wpdb->prepare(
				"SELECT * FROM %i WHERE user_id = %d AND campaign_id = %d",
				$this->table_name,
				$user_id,
				$campaign_id
			));
			if ($existing) {
				wp_cache_set($cache_key, $existing, 'donasai_fundraisers', 3600);
			}
		}

		if ($existing) {
			return $existing;
		}

		// Generate unique referral code (username + random suffix or just username)
		$user = get_userdata($user_id);
		$base_code = sanitize_title($user->user_login);
		$code = $base_code;
		$counter = 1;

		while ($this->get_by_code($code)) {
			$code = $base_code . $counter;
			$counter++;
		}

		$wpdb->insert($this->table_name, [
			'user_id' => $user_id,
			'campaign_id' => $campaign_id,
			'referral_code' => $code,
			'created_at' => current_time('mysql')
		]);

		$inserted_id = $wpdb->insert_id;
		// Invalidate caches
		wp_cache_delete('donasai_fundraiser_user_' . $user_id . '_' . $campaign_id, 'donasai_fundraisers');
		
		return $this->get_by_id($inserted_id);
	}

	/**
	 * Get fundraiser by referral code
	 */
	public function get_by_code($code)
	{
		global $wpdb;
		$cache_key = 'donasai_fundraiser_code_' . $code;
		$found = wp_cache_get($cache_key, 'donasai_fundraisers');

		if (false === $found) {
			$found = $wpdb->get_row($wpdb->prepare(
				"SELECT * FROM %i WHERE referral_code = %s",
				$this->table_name,
				$code
			));
			wp_cache_set($cache_key, $found, 'donasai_fundraisers', 3600);
		}
		return $found;
	}

	/**
	 * Get fundraiser by ID
	 */
	public function get_by_id($id)
	{
		global $wpdb;
		$cache_key = 'donasai_fundraiser_id_' . $id;
		$found = wp_cache_get($cache_key, 'donasai_fundraisers');

		if (false === $found) {
			$found = $wpdb->get_row($wpdb->prepare(
				"SELECT * FROM %i WHERE id = %d",
				$this->table_name,
				$id
			));
			wp_cache_set($cache_key, $found, 'donasai_fundraisers', 3600);
		}
		return $found;
	}

	/**
	 * Update stats when a donation is made
	 */
	public function record_donation($fundraiser_id, $amount)
	{
		global $wpdb;
		$wpdb->query($wpdb->prepare(
			"UPDATE %i 
			 SET total_donations = total_donations + %f, 
			     donation_count = donation_count + 1 
			 WHERE id = %d",
			$this->table_name,
			$amount,
			$fundraiser_id
		));
		// Invalidate cache
		wp_cache_delete('donasai_fundraiser_id_' . $fundraiser_id, 'donasai_fundraisers');
	}

	/**
	 * Get top fundraisers for a campaign
	 */
	public function get_leaderboard($campaign_id, $limit = 10)
	{
		global $wpdb;

		$cache_key = 'donasai_leaderboard_' . $campaign_id . '_' . $limit;
		$results = wp_cache_get($cache_key, 'donasai_fundraisers');

		if (false === $results) {
			$results = $wpdb->get_results($wpdb->prepare(
				"SELECT f.*, u.display_name, u.user_email 
				 FROM %i f
				 JOIN %i u ON f.user_id = u.ID
				 WHERE f.campaign_id = %d AND f.total_donations > 0
				 ORDER BY f.total_donations DESC
				 LIMIT %d",
				$this->table_name,
				$wpdb->users,
				$campaign_id,
				$limit
			));
			wp_cache_set($cache_key, $results, 'donasai_fundraisers', 300);
		}
		return $results;
	}

	/**
	 * Log a visit from a referral link
	 */
	public function track_visit($fundraiser_id, $campaign_id)
	{
		global $wpdb;
		$table_logs = $wpdb->prefix . 'donasai_referral_logs';

		// Simple unique check: limit 1 log per IP per hour? Or just log all?
		// For MVP log all, but in prod we'd want to throttle.

		$ip_address = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
		$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])) : '';

		$wpdb->insert($table_logs, [
			'fundraiser_id' => $fundraiser_id,
			'campaign_id' => $campaign_id,
			'ip_address' => substr($ip_address, 0, 100),
			'user_agent' => substr($user_agent, 0, 255),
			'created_at' => current_time('mysql')
		]);
		// Logs don't necessarily need caching as they are write-only for tracking
	}
}
