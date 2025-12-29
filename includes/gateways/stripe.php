<?php
/**
 * Stripe Gateway Implementation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPD_Gateway_Stripe implements WPD_Gateway {

    public function get_id(): string {
        return 'stripe';
    }

    public function get_name(): string {
        return 'Stripe';
    }

    public function is_active(): bool {
        $keys = get_option( 'wpd_settings_stripe', array() );
        return ! empty( $keys['publishable_key'] ) && ! empty( $keys['secret_key'] );
    }

    public function process_payment( $donation_data ): array {
        // Mock using Stripe (Not fully implemented in Free version)
        return array( 'success' => false, 'message' => 'Not implemented' );
    }

    public function get_payment_instructions( $donation_id ): string {
        return '';
    }
}
