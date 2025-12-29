<?php
/**
 * Payment Gateway Interface
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

interface WPD_Gateway {
	/**
	 * Get Gateway ID
	 * @return string
	 */
	public function get_id();

	/**
	 * Get Gateway Name
	 * @return string
	 */
	public function get_name();

	/**
	 * Is Gateway Active?
	 * @return bool
	 */
	public function is_active();

	/**
	 * Process Payment
	 * @param array $donation_data
	 * @return array ['success' => bool, 'redirect_url' => string, 'message' => string]
	 */
	public function process_payment( $donation_data );
}
