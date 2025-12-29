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
        $redirect_url = add_query_arg( 
            array( 
                'donation_success' => 1, 
                'donation_id'      => $donation_id, 
                'method'           => $this->get_id() 
            ), 
            get_permalink( $donation_data['campaign_id'] ) 
        );

        return array(
            'success'      => true,
            'donation_id'  => $donation_id,
            'redirect_url' => $redirect_url
        );
    }

    public function get_payment_instructions( $donation_id ): string {
		$options = get_option( 'wpd_settings_bank', array() );
        $bank_name = isset($options['bank_name']) ? $options['bank_name'] : 'BCA';
        $account_number = isset($options['account_number']) ? $options['account_number'] : '1234567890';
        $account_name = isset($options['account_name']) ? $options['account_name'] : 'Yayasan';

        return "
            <div class='bg-yellow-50 p-4 rounded-lg border border-yellow-200 mb-6'>
                <h3 class='font-bold text-lg mb-2 text-yellow-800'>Instruksi Pembayaran</h3>
                <p class='text-sm text-yellow-700 mb-2'>Silakan transfer ke rekening berikut:</p>
                <div class='font-mono bg-white p-3 rounded border border-yellow-100'>
                    <p><strong>{$bank_name}</strong></p>
                    <p class='text-xl'>{$account_number}</p>
                    <p>a.n {$account_name}</p>
                </div>
                <p class='text-sm text-yellow-700 mt-2'>Setelah transfer, donasi Anda akan diverifikasi admin dalam 1x24 jam.</p>
            </div>
        ";
    }
}
