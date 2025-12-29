<?php
/**
 * Midtrans Gateway Implementation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPD_Gateway_Midtrans implements WPD_Gateway {
    
    public function get_id(): string {
        return 'midtrans';
    }

    public function get_name(): string {
        return 'Midtrans (E-Wallet, VA, CC)';
    }

    public function is_active(): bool {
        $settings = get_option( 'wpd_settings_midtrans', [] );
        return ! empty( $settings['enabled'] );
    }

    public function process_payment( $donation_data ): array {
        global $wpdb;
        $table_donations = $wpdb->prefix . 'wpd_donations';
        $settings = get_option( 'wpd_settings_midtrans', [] );
        $server_key = isset( $settings['server_key'] ) ? $settings['server_key'] : '';
        $is_production = isset( $settings['is_production'] ) ? $settings['is_production'] : false;

        // 1. Save Donation as Pending First
        $data = array(
            'campaign_id'    => $donation_data['campaign_id'],
            'user_id'        => get_current_user_id() ? get_current_user_id() : null,
            'name'           => $donation_data['name'],
            'email'          => $donation_data['email'],
            'phone'          => $donation_data['phone'],
            'amount'         => $donation_data['amount'],
            'currency'       => 'IDR',
            'payment_method' => $this->get_id(),
            'status'         => 'pending',
            'note'           => $donation_data['note'],
            'is_anonymous'   => $donation_data['is_anonymous'],
            'fundraiser_id'  => isset( $donation_data['fundraiser_id'] ) ? $donation_data['fundraiser_id'] : 0,
            'metadata'       => $donation_data['metadata'],
            'created_at'     => current_time( 'mysql' ),
        );

        $format = array( '%d', '%d', '%s', '%s', '%s', '%f', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s' );
        $inserted = $wpdb->insert( $table_donations, $data, $format );

        if ( ! $inserted ) {
            return array( 'success' => false, 'message' => 'Database error' );
        }

        $donation_id = $wpdb->insert_id;
        $order_id = 'WPD-' . $donation_id . '-' . time();

        // 2. Generate Snap Token
        if ( ! empty( $server_key ) ) {
            $api_url = $is_production 
                ? 'https://app.midtrans.com/snap/v1/transactions' 
                : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

            $payload = [
                'transaction_details' => [
                    'order_id'     => $order_id,
                    'gross_amount' => (int) $donation_data['amount'],
                ],
                'customer_details' => [
                    'first_name' => $donation_data['name'],
                    'email'      => $donation_data['email'],
                    'phone'      => $donation_data['phone'],
                ],
                'callbacks' => [
                    'finish' => get_permalink( $donation_data['campaign_id'] ) . '?donation_success=1&method=midtrans&donation_id=' . $donation_id
                ]
            ];

            $response = wp_remote_post( $api_url, [
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json',
                    'Authorization' => 'Basic ' . base64_encode( $server_key . ':' )
                ],
                'body'    => json_encode( $payload ),
                'timeout' => 15
            ] );

            if ( is_wp_error( $response ) ) {
                return array( 'success' => false, 'message' => 'Midtrans Error: ' . $response->get_error_message() );
            }

            $body = json_decode( wp_remote_retrieve_body( $response ), true );
            
            if ( isset( $body['redirect_url'] ) ) {
                // Update donation with gateway info
                $wpdb->update( 
                    $table_donations, 
                    [ 'gateway_txn_id' => $order_id, 'gateway' => 'midtrans' ], 
                    [ 'id' => $donation_id ] 
                );

                return array(
                    'success'      => true,
                    'donation_id'  => $donation_id,
                    'redirect_url' => $body['redirect_url']
                );
            } else {
                return array( 'success' => false, 'message' => 'Failed to get Snap URL' );
            }

        } else {
            // MOCK MODE (Sandbox Simulation if no key)
            $mock_url = get_permalink( $donation_data['campaign_id'] ) . '?donation_success=1&method=midtrans&donation_id=' . $donation_id . '&mock=true';
            
            return array(
                'success'      => true,
                'donation_id'  => $donation_id,
                'redirect_url' => $mock_url
            );
        }
    }

    public function get_payment_instructions( $donation_id ): string {
        $settings = get_option( 'wpd_settings_midtrans', [] );
        $server_key = isset( $settings['server_key'] ) ? $settings['server_key'] : '';

        if ( empty( $server_key ) ) {
            return "
            <div class='bg-blue-50 p-4 rounded-lg border border-blue-200 mb-6'>
                <h3 class='font-bold text-lg mb-2 text-blue-800'>Simulasi Pembayaran (Mock)</h3>
                <p class='text-sm text-blue-700 mb-2'>Ini adalah simulasi karena Server Key belum diatur.</p>
                <p>Donasi ID: #$donation_id telah berhasil dibuat (Status: Pending).</p>
            </div>
            ";
        }
        
        return "
            <div class='bg-blue-50 p-4 rounded-lg border border-blue-200 mb-6'>
                <h3 class='font-bold text-lg mb-2 text-blue-800'>Menunggu Pembayaran</h3>
                <p class='text-sm text-blue-700 mb-2'>Silakan selesaikan pembayaran Anda via Midtrans.</p>
            </div>
        ";
    }
}
