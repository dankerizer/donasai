<?php
/**
 * Database Schema and Setup
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wpd_create_tables() {
	global $wpdb;

	$charset_collate = $wpdb->get_charset_collate();
	$table_donations = $wpdb->prefix . 'wpd_donations';
	$table_meta      = $wpdb->prefix . 'wpd_campaign_meta';

	// wpd_donations
	$sql_donations = "CREATE TABLE $table_donations (
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
		created_at datetime DEFAULT CURRENT_TIMESTAMP,
		updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (id),
		KEY campaign_id (campaign_id),
		KEY user_id (user_id),
		KEY status (status),
		KEY created_at (created_at)
	) $charset_collate;";

	// wpd_campaign_meta
	$sql_meta = "CREATE TABLE $table_meta (
		campaign_id bigint(20) NOT NULL,
		meta_key varchar(50) NOT NULL,
		meta_value longtext,
		PRIMARY KEY (campaign_id, meta_key)
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql_donations );
	dbDelta( $sql_meta );
}
