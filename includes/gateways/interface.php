<?php
/**
 * Payment Gateway Interface
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

interface DONASAI_Gateway {
    /**
     * Get unique gateway ID (e.g. 'manual', 'midtrans')
     */
    public function get_id(): string;

    /**
     * Get display name (e.g. 'Bank Transfer')
     */
    public function get_name(): string;

    /**
     * Is this gateway active?
     */
    public function is_active(): bool;

    /**
     * Process the payment
     * Should return array with 'success' => bool, 'donation_id' => int, 'redirect_url' => string
     */
    public function process_payment( $donation_data ): array;

    /**
     * Get payment instructions HTML
     */
    public function get_payment_instructions( $donation_id ): string;
}
