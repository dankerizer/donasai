<?php
/**
 * Manual (Bank Transfer) Gateway
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPD_Gateway_Manual implements WPD_Gateway
{

    public function get_id(): string
    {
        return 'manual';
    }

    public function get_name(): string
    {
        return 'Transfer Bank (Manual)';
    }

    public function is_active(): bool
    {
        return true; // Always active for now
    }

    public function process_payment($donation_data): array
    {
        global $wpdb;
        $table_donations = $wpdb->prefix . 'wpd_donations';

        // Generate Unique Code (1-999) to ease verification
        $unique_code = wp_rand(1, 999);
        $final_amount = $donation_data['amount'] + $unique_code;

        // Update Metadata
        $metadata = json_decode($donation_data['metadata'], true);
        if (!is_array($metadata))
            $metadata = [];
        $metadata['unique_code'] = $unique_code;
        $metadata['original_amount'] = $donation_data['amount'];

        // Insert Donation
        $data = array(
            'campaign_id' => intval($donation_data['campaign_id']),
            'user_id' => isset($donation_data['user_id']) ? intval($donation_data['user_id']) : (get_current_user_id() ? get_current_user_id() : 0),
            'name' => sanitize_text_field(wp_unslash($donation_data['name'])),
            'email' => sanitize_email(wp_unslash($donation_data['email'])),
            'phone' => sanitize_text_field(wp_unslash($donation_data['phone'])),
            'amount' => $final_amount, // Total with unique code
            'currency' => 'IDR',
            'payment_method' => $this->get_id(),
            'status' => 'pending',
            'note' => sanitize_textarea_field(wp_unslash($donation_data['note'])),
            'is_anonymous' => intval($donation_data['is_anonymous']),
            'created_at' => current_time('mysql'),
            'metadata' => json_encode($metadata)
        );

        $format = array('%d', '%d', '%s', '%s', '%s', '%f', '%s', '%s', '%s', '%s', '%d', '%s', '%s');

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        $inserted = $wpdb->insert(
            $table_donations,
            $data,
            $format
        );

        if (!$inserted) {
            return array('success' => false, 'message' => 'Database error');
        }

        $donation_id = $wpdb->insert_id;

        return array(
            'success' => true,
            'donation_id' => $donation_id,
            'redirect_url' => null
        );
    }

    /**
     * Get active banks for a campaign (or global defaults)
     */
    public function get_active_banks($campaign_id)
    {
        $banks_to_show = [];
        $pro_accounts = get_option('wpd_pro_bank_accounts', []);

        // 1. Check Campaign Specific
        if (!empty($pro_accounts)) {
            $campaign_banks = get_post_meta($campaign_id, '_wpd_campaign_banks', true);

            if (!empty($campaign_banks) && is_array($campaign_banks)) {
                // Show ONLY selected banks
                foreach ($pro_accounts as $acc) {
                    if (in_array($acc['id'], $campaign_banks)) {
                        $banks_to_show[] = $acc;
                    }
                }
            } else {
                // Defaults
                $defaults = array_filter($pro_accounts, function ($a) {
                    return !empty($a['is_default']);
                });
                $banks_to_show = !empty($defaults) ? $defaults : $pro_accounts;
            }
        }

        // 2. Fallback to Free Settings if no Pro banks to show
        if (empty($banks_to_show)) {
            $options = get_option('wpd_settings_bank', array());
            if (!empty($options['account_number'])) {
                $banks_to_show[] = array(
                    'bank_name' => isset($options['bank_name']) ? $options['bank_name'] : 'BCA',
                    'account_number' => isset($options['account_number']) ? $options['account_number'] : '-',
                    'account_name' => isset($options['account_name']) ? $options['account_name'] : '',
                );
            }
        }

        return $banks_to_show;
    }

    public function get_payment_instructions($donation_id): string
    {
        global $wpdb;
        $table = $wpdb->prefix . 'wpd_donations';
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $donation = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $donation_id));

        $total_amount = $donation ? $donation->amount : 0;

        // Extract unique code
        $unique_code = 0;
        if ($donation && $donation->metadata) {
            $meta = json_decode($donation->metadata, true);
            if (isset($meta['unique_code'])) {
                $unique_code = $meta['unique_code'];
            }
        }

        // Get Banks
        $banks_to_show = $this->get_active_banks($donation ? $donation->campaign_id : 0);

        // Filter if specific bank selected
        if ($donation && $donation->metadata) {
            $meta = json_decode($donation->metadata, true);
            if (!empty($meta['selected_bank'])) {
                // Filter banks to show only the selected one
                $selected_id = $meta['selected_bank'];
                $banks_to_show = array_filter($banks_to_show, function ($b) use ($selected_id) {
                    return (isset($b['id']) && $b['id'] == $selected_id) || (isset($b['bank_name']) && $b['bank_name'] == $selected_id); // robust check
                });
                // Re-index
                $banks_to_show = array_values($banks_to_show);
            }
        }

        $instructions = "
            <div style='background-color:#fffbeb; padding:15px; border:1px solid #fcd34d; border-radius:8px; margin-bottom:20px; text-align:center;'>
                <div style='font-size:13px; color:#92400e; margin-bottom:5px;'>" . esc_html__('Total yang harus ditransfer:', 'donasai') . "</div>
                <div style='font-size:24px; font-weight:bold; color:#d97706;'>
                    Rp " . esc_html(number_format($total_amount, 0, ',', '.')) . "
                </div>
                <div style='font-size:12px; color:#b45309; margin-top:5px;'>
                    " . /* translators: 1: strong open tag, 2: strong close tag */ sprintf(esc_html__('*Pastikan transfer %1$sTEPAT%2$s sampai 3 digit terakhir untuk verifikasi otomatis.', 'donasai'), '<strong>', '</strong>') . "
                </div>
            </div>

            <div style='margin-bottom:10px; color:#475569; font-size:14px; font-weight:500;'>" . esc_html__('Silakan transfer ke rekening berikut:', 'donasai') . "</div>
            
            <div class='wpd-bank-list' style='display:grid; gap:10px;'>";

        foreach ($banks_to_show as $bank) {
            $instructions .= "
            <div class='wpd-bank-info' style='border:1px solid #e2e8f0; padding:12px; border-radius:8px; display:flex; justify-content:space-between; items-align:center; background:#fff;'>
                <div style='display:flex; flex-direction:column; justify-content:center;'>
                    <div class='wpd-bank-logo' style='font-weight:bold; font-size:16px; color:#1e293b;'>" . esc_html($bank['bank_name']) . "</div>
                    <div style='font-size:12px; color:#64748b;'>" . esc_html($bank['account_name']) . "</div>
                </div>
                <div style='text-align:right'>
                    <div class='wpd-account-number' style='font-family:monospace; font-size:15px; font-weight:500; color:#334155; margin-bottom:4px;'>" . esc_html($bank['account_number']) . "</div>
                    <button class='wpd-copy-btn' onclick='copyToClipboard(\"" . esc_attr($bank['account_number']) . "\")' style='font-size:11px; padding:2px 8px; border:1px solid #cbd5e1; border-radius:4px; background:#f8fafc; cursor:pointer;'>
                        " . esc_html__('Salin', 'donasai') . "
                    </button>
                </div>
            </div>";
        }

        $instructions .= "</div>
            
            <div style='margin-top:15px; font-size:13px; color:#64748b; line-height:1.4;'>
                " . esc_html__('Donasi akan diverifikasi otomatis setelah bukti transfer dikirim/dikonfirmasi.', 'donasai') . "
            </div>
        ";

        return $instructions;
    }
}
