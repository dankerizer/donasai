<?php
/**
 * Database Schema and Setup
 */

if (!defined('ABSPATH')) {
	exit;
}

function wpd_create_tables()
{
	global $wpdb;

	$charset_collate = $wpdb->get_charset_collate();
	$table_donations = $wpdb->prefix . 'wpd_donations';
	$table_meta = $wpdb->prefix . 'wpd_campaign_meta';

	// wpd_donations
	// wpd_donations
	$sql_donations = "CREATE TABLE {$table_donations} (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		campaign_id bigint(20) NOT NULL,
		user_id bigint(20) NULL,
		name varchar(100) NOT NULL,
		email varchar(100) NOT NULL,
		phone varchar(20) NULL,
		amount decimal(12,2) NOT NULL,
		currency varchar(3) DEFAULT 'IDR',
		payment_method varchar(50) NOT NULL,
		status enum('pending','processing','complete','failed','refunded') DEFAULT 'pending',
		gateway varchar(50) NULL,
		gateway_txn_id varchar(100) NULL,
		metadata longtext NULL,
		note text NULL,
		is_anonymous tinyint(1) DEFAULT 0,
		fundraiser_id bigint(20) DEFAULT 0,
		created_at datetime DEFAULT CURRENT_TIMESTAMP,
		updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		KEY campaign_id (campaign_id),
		KEY user_id (user_id),
		KEY status (status),
		KEY created_at (created_at)
	) $charset_collate;";

	// wpd_campaign_meta
	// wpd_campaign_meta
	$sql_meta = "CREATE TABLE {$table_meta} (
		campaign_id bigint(20) NOT NULL,
		meta_key varchar(50) NOT NULL,
		meta_value longtext,
		PRIMARY KEY (campaign_id, meta_key)
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta($sql_donations);
	dbDelta($sql_meta);

	// New Tables for Features (Fundraising & Tracking)
	$table_fundraisers = $wpdb->prefix . 'wpd_fundraisers';
	$table_logs = $wpdb->prefix . 'wpd_referral_logs';

	$sql_fundraisers = "CREATE TABLE $table_fundraisers (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		user_id bigint(20) NOT NULL,
		campaign_id bigint(20) NOT NULL,
		referral_code varchar(50) NOT NULL,
		total_donations decimal(12,2) DEFAULT 0,
		donation_count int(11) DEFAULT 0,
		is_active tinyint(1) DEFAULT 1,
		created_at datetime DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		UNIQUE KEY code (referral_code),
		KEY user_campaign (user_id, campaign_id)
	) $charset_collate;";

	$sql_logs = "CREATE TABLE $table_logs (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		fundraiser_id bigint(20) NOT NULL,
		campaign_id bigint(20) NOT NULL,
		ip_address varchar(100) NULL,
		user_agent varchar(255) NULL,
		created_at datetime DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		KEY fundraiser_id (fundraiser_id)
	) $charset_collate;";

	// wpd_subscriptions
	$table_subscriptions = $wpdb->prefix . 'wpd_subscriptions';
	$sql_subscriptions = "CREATE TABLE $table_subscriptions (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		user_id bigint(20) NOT NULL,
		campaign_id bigint(20) NOT NULL,
		amount decimal(12,2) NOT NULL,
		status enum('active','cancelled','paused') DEFAULT 'active',
		frequency enum('monthly','yearly') DEFAULT 'monthly',
		next_payment_date datetime NOT NULL,
		created_at datetime DEFAULT CURRENT_TIMESTAMP,
		updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		KEY user_id (user_id),
		KEY status (status)
	) $charset_collate;";

	dbDelta($sql_fundraisers);
	dbDelta($sql_logs);
	dbDelta($sql_subscriptions);

	// Update Donations Table with subscription_id
	if (!$wpdb->get_results("SHOW COLUMNS FROM {$table_donations} LIKE 'subscription_id'")) {
		$wpdb->query("ALTER TABLE {$table_donations} ADD COLUMN subscription_id bigint(20) DEFAULT 0 AFTER fundraiser_id");
	}
}
