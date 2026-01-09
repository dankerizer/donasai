<?php
/**
 * Fundraiser Service
 * Handles business logic for fundraisers and affiliate tracking.
 */

if (!defined('ABSPATH')) {
	exit;
}

class WPD_Fundraiser_Service
{
	private $table_name;

	public function __construct()
	{
		global $wpdb;
		$this->table_name = $wpdb->prefix . 'wpd_fundraisers';
	}

	/**
	 * Register a user as fundraiser for a campaign
	 */
	public function register_fundraiser($user_id, $campaign_id)
	{
		global $wpdb;

		// Check if already registered
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$existing = $wpdb->get_row($wpdb->prepare(
			"SELECT * FROM $this->table_name WHERE user_id = %d AND campaign_id = %d",
			$user_id,
			$campaign_id
		));

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

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->insert($this->table_name, [
			'user_id' => $user_id,
			'campaign_id' => $campaign_id,
			'referral_code' => $code,
			'created_at' => current_time('mysql')
		]);

		return $this->get_by_id($wpdb->insert_id);
	}

	/**
	 * Get fundraiser by referral code
	 */
	public function get_by_code($code)
	{
		global $wpdb;
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery
		return $wpdb->get_row($wpdb->prepare(
			"SELECT * FROM $this->table_name WHERE referral_code = %s",
			$code
		));
	}

	/**
	 * Get fundraiser by ID
	 */
	public function get_by_id($id)
	{
		global $wpdb;
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery
		return $wpdb->get_row($wpdb->prepare(
			"SELECT * FROM $this->table_name WHERE id = %d",
			$id
		));
	}

	/**
	 * Update stats when a donation is made
	 */
	public function record_donation($fundraiser_id, $amount)
	{
		global $wpdb;
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->query($wpdb->prepare(
			"UPDATE $this->table_name 
			 SET total_donations = total_donations + %f, 
			     donation_count = donation_count + 1 
			 WHERE id = %d",
			$amount,
			$fundraiser_id
		));
	}

	/**
	 * Get top fundraisers for a campaign
	 */
	public function get_leaderboard($campaign_id, $limit = 10)
	{
		global $wpdb;
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery
		return $wpdb->get_results($wpdb->prepare(
			"SELECT f.*, u.display_name, u.user_email 
			 FROM $this->table_name f
			 JOIN {$wpdb->users} u ON f.user_id = u.ID
			 WHERE f.campaign_id = %d AND f.total_donations > 0
			 ORDER BY f.total_donations DESC
			 LIMIT %d",
			$campaign_id,
			$limit
		));
	}

	/**
	 * Log a visit from a referral link
	 */
	public function track_visit($fundraiser_id, $campaign_id)
	{
		global $wpdb;
		$table_logs = $wpdb->prefix . 'wpd_referral_logs';

		// Simple unique check: limit 1 log per IP per hour? Or just log all?
		// For MVP log all, but in prod we'd want to throttle.

		$ip_address = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
		$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])) : '';

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$wpdb->insert($table_logs, [
			'fundraiser_id' => $fundraiser_id,
			'campaign_id' => $campaign_id,
			'ip_address' => substr($ip_address, 0, 100),
			'user_agent' => substr($user_agent, 0, 255),
			'created_at' => current_time('mysql')
		]);
	}
}
