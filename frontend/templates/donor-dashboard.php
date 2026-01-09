<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Donor Dashboard Template
 */

$user_id = get_current_user_id();

if (!$user_id) {
    echo '<p>' . esc_html__('Please login to view your donations.', 'donasai') . '</p>';
    return;
}

global $wpdb;

$cache_key = 'wpd_user_donations_' . $user_id;
$donations = wp_cache_get($cache_key, 'wpd_donations');

if (false === $donations) {
    $donations = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}wpd_donations WHERE user_id = %d ORDER BY created_at DESC", $user_id));
    wp_cache_set($cache_key, $donations, 'wpd_donations', 300);
}
?>

<div class="wpd-donor-dashboard">
    <h3><?php esc_html_e('Riwayat Donasi Saya', 'donasai'); ?></h3>

    <?php if (empty($donations)): ?>
        <div style="background:#f9fafb; padding:40px; text-align:center; border-radius:8px; border:1px solid #e5e7eb;">
            <p style="color:#6b7280; font-size:16px; margin-bottom:20px;">
                <?php esc_html_e('Belum ada riwayat donasi.', 'donasai'); ?>
            </p>
            <a href="<?php echo esc_url(home_url('/campaigns')); ?>" class="button"
                style="background:#2563eb; color:white; padding:10px 20px; text-decoration:none; border-radius:6px;"><?php esc_html_e('Mulai Berdonasi', 'donasai'); ?></a>
        </div>
    <?php else: ?>
        <div style="overflow-x:auto;">
            <table class="wpd-table"
                style="width:100%; border-collapse:separate; border-spacing:0; border:1px solid #e5e7eb; border-radius:8px; overflow:hidden;">
                <thead style="background:#f9fafb; color:#374151;">
                    <tr>
                        <th
                            style="padding:12px 16px; text-align:left; border-bottom:1px solid #e5e7eb; font-weight:600; font-size:14px;">
                            #ID</th>
                        <th
                            style="padding:12px 16px; text-align:left; border-bottom:1px solid #e5e7eb; font-weight:600; font-size:14px;">
                            <?php esc_html_e('Tanggal', 'donasai'); ?>
                        </th>
                        <th
                            style="padding:12px 16px; text-align:left; border-bottom:1px solid #e5e7eb; font-weight:600; font-size:14px;">
                            <?php esc_html_e('Campaign', 'donasai'); ?>
                        </th>
                        <th
                            style="padding:12px 16px; text-align:left; border-bottom:1px solid #e5e7eb; font-weight:600; font-size:14px;">
                            <?php esc_html_e('Nominal', 'donasai'); ?>
                        </th>
                        <th
                            style="padding:12px 16px; text-align:left; border-bottom:1px solid #e5e7eb; font-weight:600; font-size:14px;">
                            <?php esc_html_e('Status', 'donasai'); ?>
                        </th>
                        <th
                            style="padding:12px 16px; text-align:right; border-bottom:1px solid #e5e7eb; font-weight:600; font-size:14px;">
                            <?php esc_html_e('Aksi', 'donasai'); ?>
                        </th>
                    </tr>
                </thead>
                <tbody style="background:white;">
                    <?php foreach ($donations as $donation):
                        $campaign_title = get_the_title($donation->campaign_id);
                        ?>
                        <tr style="border-bottom:1px solid #e5e7eb;">
                            <td style="padding:12px 16px; border-bottom:1px solid #f3f4f6; color:#6b7280; font-size:13px;">
                                #<?php echo esc_html($donation->id); ?></td>
                            <td style="padding:12px 16px; border-bottom:1px solid #f3f4f6; color:#111827; font-size:14px;">
                                <?php echo esc_html(date_i18n('d M Y', strtotime($donation->created_at))); ?>
                            </td>
                            <td style="padding:12px 16px; border-bottom:1px solid #f3f4f6;">
                                <a href="<?php echo esc_url(get_permalink($donation->campaign_id)); ?>"
                                    style="color:#2563eb; text-decoration:none; font-weight:500; font-size:14px;"><?php echo esc_html($campaign_title); ?></a>
                            </td>
                            <td style="padding:12px 16px; border-bottom:1px solid #f3f4f6; font-weight:600; color:#111827;">Rp
                                <?php echo esc_html(number_format($donation->amount, 0, ',', '.')); ?>
                            </td>
                            <td style="padding:12px 16px; border-bottom:1px solid #f3f4f6;">
                                <span class="wpd-status-badge wpd-status-<?php echo esc_attr($donation->status); ?>">
                                    <?php echo esc_html(ucfirst($donation->status)); ?>
                                </span>
                            </td>
                            <td style="padding:12px 16px; border-bottom:1px solid #f3f4f6; text-align:right;">
                                <?php if ('pending' === $donation->status && 'midtrans' === $donation->payment_method): ?>
                                    <!-- Ideally link to payment -->
                                    <button disabled class="button-small"
                                        style="font-size:11px; padding:4px 8px; background:#e5e7eb; color:#9ca3af; border:none; border-radius:4px;">Pending</button>
                                <?php else: ?>
                                    <a href="<?php echo esc_url(add_query_arg('wpd_receipt', $donation->id, home_url('/'))); ?>"
                                        target="_blank" class="button-small"
                                        style="font-size:12px; color:#2563eb; text-decoration:none; margin-right:5px;"><?php esc_html_e('Receipt', 'donasai'); ?></a>
                                    <a href="<?php echo esc_url(get_permalink($donation->campaign_id)); ?>" class="button-small"
                                        style="font-size:12px; color:#4b5563; text-decoration:none;"><?php esc_html_e('View', 'donasai'); ?></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<div class="wpd-subscription-dashboard" style="margin-top:40px;">
    <h3><?php esc_html_e('Langganan Rutin Saya', 'donasai'); ?></h3>

    <?php
    $cache_key_subs = 'wpd_user_subscriptions_' . $user_id;
    $subscriptions = wp_cache_get($cache_key_subs, 'wpd_subscriptions');

    if (false === $subscriptions) {
        $subscriptions = $wpdb->get_results($wpdb->prepare(
            "SELECT s.*, p.post_title as campaign_title 
             FROM {$wpdb->prefix}wpd_subscriptions s
             JOIN {$wpdb->prefix}posts p ON s.campaign_id = p.ID
             WHERE s.user_id = %d 
             ORDER BY s.created_at DESC",
            $user_id
        ));
        wp_cache_set($cache_key_subs, $subscriptions, 'wpd_subscriptions', 300);
    }
    ?>

    <?php if (empty($subscriptions)): ?>
        <p style="color:#6b7280; font-size:14px;"><?php esc_html_e('Belum ada donasi rutin aktif.', 'donasai'); ?></p>
    <?php else: ?>
        <div style="overflow-x:auto;">
            <table class="wpd-table"
                style="width:100%; border-collapse:separate; border-spacing:0; border:1px solid #e5e7eb; border-radius:8px; overflow:hidden;">
                <thead style="background:#f9fafb; color:#374151;">
                    <tr>
                        <th
                            style="padding:12px 16px; text-align:left; border-bottom:1px solid #e5e7eb; font-weight:600; font-size:14px;">
                            #ID</th>
                        <th
                            style="padding:12px 16px; text-align:left; border-bottom:1px solid #e5e7eb; font-weight:600; font-size:14px;">
                            <?php esc_html_e('Campaign', 'donasai'); ?>
                        </th>
                        <th
                            style="padding:12px 16px; text-align:left; border-bottom:1px solid #e5e7eb; font-weight:600; font-size:14px;">
                            <?php esc_html_e('Nominal', 'donasai'); ?>
                        </th>
                        <th
                            style="padding:12px 16px; text-align:left; border-bottom:1px solid #e5e7eb; font-weight:600; font-size:14px;">
                            <?php esc_html_e('Jadwal', 'donasai'); ?>
                        </th>
                        <th
                            style="padding:12px 16px; text-align:left; border-bottom:1px solid #e5e7eb; font-weight:600; font-size:14px;">
                            <?php esc_html_e('Status', 'donasai'); ?>
                        </th>
                        <th
                            style="padding:12px 16px; text-align:right; border-bottom:1px solid #e5e7eb; font-weight:600; font-size:14px;">
                            <?php esc_html_e('Aksi', 'donasai'); ?>
                        </th>
                    </tr>
                </thead>
                <tbody style="background:white;">
                    <?php foreach ($subscriptions as $sub): ?>
                        <tr style="border-bottom:1px solid #e5e7eb;">
                            <td style="padding:12px 16px; border-bottom:1px solid #f3f4f6; color:#6b7280; font-size:13px;">
                                #<?php echo esc_html($sub->id); ?></td>
                            <td style="padding:12px 16px; border-bottom:1px solid #f3f4f6;">
                                <a href="<?php echo esc_url(get_permalink($sub->campaign_id)); ?>"
                                    style="color:#2563eb; text-decoration:none; font-weight:500; font-size:14px;"><?php echo esc_html($sub->campaign_title); ?></a>
                            </td>
                            <td style="padding:12px 16px; border-bottom:1px solid #f3f4f6; font-weight:600; color:#111827;">Rp
                                <?php echo esc_html(number_format($sub->amount, 0, ',', '.')); ?>
                            </td>
                            <td style="padding:12px 16px; border-bottom:1px solid #f3f4f6; font-size:13px;">
                                / <?php echo esc_html(ucfirst($sub->billing_interval)); ?><br>
                                <span
                                    style="font-size:11px; color:#6b7280;"><?php echo esc_html__('Berikutnya:', 'donasai') . ' ' . esc_html(date_i18n('d M Y', strtotime($sub->next_payment_date))); ?></span>
                            </td>
                            <td style="padding:12px 16px; border-bottom:1px solid #f3f4f6;">
                                <span class="wpd-status-badge wpd-status-<?php echo esc_attr($sub->status); ?>">
                                    <?php echo esc_html(ucfirst($sub->status)); ?>
                                </span>
                            </td>
                            <td style="padding:12px 16px; border-bottom:1px solid #f3f4f6; text-align:right;">
                                <?php if ('active' === $sub->status): ?>
                                    <button onclick="wpdCancelSub(<?php echo esc_attr($sub->id); ?>)" class="button-small"
                                        style="background:#fee2e2; color:#991b1b; border:none; padding:4px 8px; border-radius:4px; font-size:11px; cursor:pointer;"><?php esc_html_e('Batalkan', 'donasai'); ?></button>
                                <?php else: ?>
                                    <span style="color:#9ca3af; font-size:11px;">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <script>
        function wpdCancelSub(id) {
            if (!confirm('<?php echo esc_js(__('Yakin ingin membatalkan donasi rutin ini?', 'donasai')); ?>')) return;

            var nonce = '<?php echo esc_js(wp_create_nonce('wp_rest')); ?>';
            fetch('/wp-json/wpd/v1/subscriptions/' + id + '/cancel', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': nonce
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('<?php echo esc_js(__('Berhasil dibatalkan.', 'donasai')); ?>');
                        location.reload();
                    } else {
                        alert('<?php echo esc_js(__('Gagal membatalkan.', 'donasai')); ?>');
                    }
                });
        }
    </script>
</div>

<style>
    .wpd-table tr:last-child td {
        border-bottom: none;
    }

    .wpd-table tr:hover {
        background-color: #f9fafb;
    }

    .wpd-status-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: capitalize;
    }

    .wpd-status-complete {
        background: #d1fae5;
        color: #065f46;
    }

    .wpd-status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .wpd-status-failed {
        background: #fee2e2;
        color: #991b1b;
    }
</style>