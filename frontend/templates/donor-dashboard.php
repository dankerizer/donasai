<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Donor Dashboard Template
 */

$donasai_user_id = get_current_user_id();

if (!$donasai_user_id) {
    echo '<p>' . esc_html__('Please login to view your donations.', 'donasai') . '</p>';
    return;
}

global $wpdb;

$donasai_cache_key = 'donasai_user_donations_' . $donasai_user_id;
$donasai_donations = wp_cache_get($donasai_cache_key, 'donasai_donations');

if (false === $donasai_donations) {
    $donasai_table_name = $wpdb->prefix . 'donasai_donations';
    $donasai_donations = $wpdb->get_results($wpdb->prepare("SELECT * FROM %i WHERE user_id = %d ORDER BY created_at DESC", $donasai_table_name, $donasai_user_id));
    wp_cache_set($donasai_cache_key, $donasai_donations, 'donasai_donations', 300);
}
?>

<div class="donasai-donor-dashboard">
    <h3><?php esc_html_e('Riwayat Donasi Saya', 'donasai'); ?></h3>

    <?php if (empty($donasai_donations)): ?>
        <div style="background:var(--donasai-bg); padding:40px; text-align:center; border-radius:var(--donasai-radius); border:1px solid var(--donasai-border);">
            <p style="color:var(--donasai-text-muted); font-size:16px; margin-bottom:20px;">
                <?php esc_html_e('Belum ada riwayat donasi.', 'donasai'); ?>
            </p>
            <a href="<?php echo esc_url(home_url('/campaigns')); ?>" class="button"
                style="background:var(--donasai-btn); color:white; padding:10px 20px; text-decoration:none; border-radius:var(--donasai-radius);"><?php esc_html_e('Mulai Berdonasi', 'donasai'); ?></a>
        </div>
    <?php else: ?>
        <div style="overflow-x:auto;">
            <table class="donasai-table"
                style="width:100%; border-collapse:separate; border-spacing:0; border:1px solid var(--donasai-border); border-radius:var(--donasai-radius); overflow:hidden;">
                <thead style="background:var(--donasai-bg); color:var(--donasai-text-main);">
                    <tr>
                        <th
                            style="padding:12px 16px; text-align:left; border-bottom:1px solid #e5e7eb; font-weight:600; font-size:14px;">
                            #ID</th>
                        <th
                            style="padding:12px 16px; text-align:left; border-bottom:1px solid var(--donasai-border); font-weight:600; font-size:14px;">
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
                <tbody style="background:var(--donasai-card-bg);">
                    <?php foreach ($donasai_donations as $donasai_donation):
                        $donasai_campaign_title = get_the_title($donasai_donation->campaign_id);
                        ?>
                        <tr style="border-bottom:1px solid var(--donasai-border);">
                            <td style="padding:12px 16px; border-bottom:1px solid var(--donasai-bg); color:var(--donasai-text-muted); font-size:13px;">
                                #<?php echo esc_html($donasai_donation->id); ?></td>
                            <td style="padding:12px 16px; border-bottom:1px solid var(--donasai-bg); color:var(--donasai-text-main); font-size:14px;">
                                <?php echo esc_html(date_i18n('d M Y', strtotime($donasai_donation->created_at))); ?>
                            </td>
                            <td style="padding:12px 16px; border-bottom:1px solid var(--donasai-bg);">
                                <a href="<?php echo esc_url(get_permalink($donasai_donation->campaign_id)); ?>"
                                    style="color:var(--donasai-primary); text-decoration:none; font-weight:500; font-size:14px;"><?php echo esc_html($donasai_campaign_title); ?></a>
                            </td>
                            <td style="padding:12px 16px; border-bottom:1px solid var(--donasai-bg); font-weight:600; color:var(--donasai-text-main);">Rp
                                <?php echo esc_html(number_format($donasai_donation->amount, 0, ',', '.')); ?>
                            </td>
                            <td style="padding:12px 16px; border-bottom:1px solid #f3f4f6;">
                                <span class="donasai-status-badge donasai-status-<?php echo esc_attr($donasai_donation->status); ?>">
                                    <?php echo esc_html(ucfirst($donasai_donation->status)); ?>
                                </span>
                            </td>
                            <td style="padding:12px 16px; border-bottom:1px solid var(--donasai-bg); text-align:right;">
                                <?php if ('pending' === $donasai_donation->status && 'midtrans' === $donasai_donation->payment_method): ?>
                                    <!-- Ideally link to payment -->
                                    <button disabled class="button-small"
                                        style="font-size:11px; padding:4px 8px; background:#e5e7eb; color:#9ca3af; border:none; border-radius:4px;">Pending</button>
                                <?php else: ?>
                                    <a href="<?php echo esc_url(add_query_arg('donasai_receipt', $donasai_donation->id, home_url('/'))); ?>"
                                        target="_blank" class="button-small"
                                        style="font-size:12px; color:var(--donasai-primary); text-decoration:none; margin-right:5px;"><?php esc_html_e('Receipt', 'donasai'); ?></a>
                                    <a href="<?php echo esc_url(get_permalink($donasai_donation->campaign_id)); ?>" class="button-small"
                                        style="font-size:12px; color:var(--donasai-text-muted); text-decoration:none;"><?php esc_html_e('View', 'donasai'); ?></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<div class="donasai-subscription-dashboard" style="margin-top:40px;">
    <h3><?php esc_html_e('Langganan Rutin Saya', 'donasai'); ?></h3>

    <?php
    $donasai_cache_key_subs = 'donasai_user_subscriptions_' . $donasai_user_id;
    $donasai_subscriptions = wp_cache_get($donasai_cache_key_subs, 'donasai_subscriptions');

    if (false === $donasai_subscriptions) {
        $donasai_table_subs = $wpdb->prefix . 'donasai_subscriptions';
        $donasai_table_posts = $wpdb->prefix . 'posts';
        $donasai_subscriptions = $wpdb->get_results($wpdb->prepare(
            "SELECT s.*, p.post_title as campaign_title 
             FROM %i s
             JOIN %i p ON s.campaign_id = p.ID
             WHERE s.user_id = %d 
             WHERE s.user_id = %d 
             ORDER BY s.created_at DESC",
            $donasai_table_subs,
            $donasai_table_posts,
            $donasai_user_id
        ));
        wp_cache_set($donasai_cache_key_subs, $donasai_subscriptions, 'donasai_subscriptions', 300);
    }
    ?>

    <?php if (empty($donasai_subscriptions)): ?>
        <p style="color:#6b7280; font-size:14px;"><?php esc_html_e('Belum ada donasi rutin aktif.', 'donasai'); ?></p>
    <?php else: ?>
        <div style="overflow-x:auto;">
            <table class="donasai-table"
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
                <tbody style="background:var(--donasai-card-bg);">
                    <?php foreach ($donasai_subscriptions as $donasai_sub): ?>
                        <tr style="border-bottom:1px solid var(--donasai-border);">
                            <td style="padding:12px 16px; border-bottom:1px solid var(--donasai-bg); color:var(--donasai-text-muted); font-size:13px;">
                                #<?php echo esc_html($donasai_sub->id); ?></td>
                            <td style="padding:12px 16px; border-bottom:1px solid #f3f4f6;">
                                <a href="<?php echo esc_url(get_permalink($donasai_sub->campaign_id)); ?>"
                                    style="color:var(--donasai-primary); text-decoration:none; font-weight:500; font-size:14px;"><?php echo esc_html($donasai_sub->campaign_title); ?></a>
                            </td>
                            <td style="padding:12px 16px; border-bottom:1px solid var(--donasai-bg); font-weight:600; color:var(--donasai-text-main);">Rp
                                <?php echo esc_html(number_format($donasai_sub->amount, 0, ',', '.')); ?>
                            </td>
                            <td style="padding:12px 16px; border-bottom:1px solid #f3f4f6; font-size:13px;">
                                / <?php echo esc_html(ucfirst($donasai_sub->billing_interval)); ?><br>
                                <span
                                    style="font-size:11px; color:#6b7280;"><?php echo esc_html__('Berikutnya:', 'donasai') . ' ' . esc_html(date_i18n('d M Y', strtotime($donasai_sub->next_payment_date))); ?></span>
                            </td>
                            <td style="padding:12px 16px; border-bottom:1px solid #f3f4f6;">
                                <span class="donasai-status-badge donasai-status-<?php echo esc_attr($donasai_sub->status); ?>">
                                    <?php echo esc_html(ucfirst($donasai_sub->status)); ?>
                                </span>
                            </td>
                            <td style="padding:12px 16px; border-bottom:1px solid #f3f4f6; text-align:right;">
                                <?php if ('active' === $donasai_sub->status): ?>
                                    <button onclick="donasaiCancelSub(
                                        <?php echo esc_attr($donasai_sub->id); ?>,
                                        '<?php echo esc_js(__('Yakin ingin membatalkan donasi rutin ini?', 'donasai')); ?>',
                                        '<?php echo esc_js(__('Berhasil dibatalkan.', 'donasai')); ?>',
                                        '<?php echo esc_js(__('Gagal membatalkan.', 'donasai')); ?>',
                                        '<?php echo esc_js(wp_create_nonce('wp_rest')); ?>'
                                    )" class="button-small"
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
</div>