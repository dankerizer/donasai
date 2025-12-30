<?php
/**
 * Manual (Bank Transfer) Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPD_Gateway_Manual implements WPD_Gateway {
    
    public function get_id(): string {
        return 'manual';
    }

    public function get_name(): string {
        return 'Transfer Bank (Manual)';
    }

    public function is_active(): bool {
        return true; // Always active for now
    }

    public function process_payment( $donation_data ): array {
        global $wpdb;
        $table_donations = $wpdb->prefix . 'wpd_donations';

        // Insert Donation
        $data = array(
            'campaign_id'    => $donation_data['campaign_id'],
            'user_id'        => get_current_user_id() ? get_current_user_id() : null,
            'name'           => $donation_data['name'],
            'email'          => $donation_data['email'],
            'phone'          => $donation_data['phone'],
            'amount'         => $donation_data['amount'],
            'currency'       => 'IDR',
            'payment_method' => $this->get_id(),
            'status'         => 'pending', // Manual is always pending first
            'note'           => $donation_data['note'],
            'is_anonymous'   => $donation_data['is_anonymous'],
            'created_at'     => current_time( 'mysql' ),
        );

        $format = array( '%d', '%d', '%s', '%s', '%s', '%f', '%s', '%s', '%s', '%s', '%d', '%s' );

        $inserted = $wpdb->insert( $table_donations, $data, $format );

        if ( ! $inserted ) {
            return array( 'success' => false, 'message' => 'Database error' );
        }

        $donation_id = $wpdb->insert_id;

        // Email Sending is handled by 'wpd_donation_created' action in controller/services

        // Return success with redirect URL
        // We leave redirect_url empty so the Service handles the default "Thank You" page redirect.
        // Unless we want to override it here.
        
        return array(
            'success'      => true,
            'donation_id'  => $donation_id,
            'redirect_url' => null // Let service handle redirect to /thank-you
        );
    }

    public function get_payment_instructions( $donation_id ): string {
		$options = get_option( 'wpd_settings_bank', array() );
        $bank_name = isset($options['bank_name']) ? $options['bank_name'] : 'BCA';
        $account_number = isset($options['account_number']) ? $options['account_number'] : '1234567890';
        $account_name = isset($options['account_name']) ? $options['account_name'] : 'Yayasan';

        return "
            <div style='margin-bottom:10px; color:#475569; font-size:14px; font-weight:500;'>Silakan transfer ke rekening berikut:</div>
            
            <div class='wpd-bank-info'>
                <div class='wpd-bank-logo'>" . esc_html( $bank_name ) . "</div>
                <div style='text-align:right'>
                    <div class='wpd-account-number'>" . esc_html( $account_number ) . "</div>
                    <div style='font-size:12px; color:#64748b;'>a.n " . esc_html( $account_name ) . "</div>
                </div>
                <button class='wpd-copy-btn' onclick='copyToClipboard(\"" . esc_attr( $account_number ) . "\")'>
                    <svg width='16' height='16' fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z'/></svg>
                </button>
            </div>
            
            <div style='margin-top:15px; font-size:13px; color:#64748b; line-height:1.4;'>
                Donasi akan diverifikasi otomatis setelah bukti transfer dikirim/dikonfirmasi.
            </div>
        ";
    }
}
