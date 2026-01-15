<?php
/**
 * Dashboard Widget
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add Dashboard Widget
 */
function wpd_add_dashboard_widgets()
{
    wp_add_dashboard_widget(
        'wpd_dashboard_widget',
        'Donasai Overview',
        'wpd_dashboard_widget_render'
    );
}
add_action('wp_dashboard_setup', 'wpd_add_dashboard_widgets');

/**
 * Render Widget Content
 */
function wpd_dashboard_widget_render()
{
    global $wpdb;
    $table_donations = $wpdb->prefix . 'wpd_donations';

    // Check Cache
    $cache_key = 'wpd_dashboard_stats';
    $stats = wp_cache_get($cache_key, 'wpd_dashboard');

    if (false === $stats) {
        $stats = array();

        // Total Collected (Complete)
        $stats['total_collected'] = $wpdb->get_var($wpdb->prepare("SELECT SUM(amount) FROM {$table_donations} WHERE status = %s", 'complete')); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared

        // Total Donors
        $stats['total_donors'] = $wpdb->get_var($wpdb->prepare("SELECT COUNT(DISTINCT email) FROM {$table_donations} WHERE status = %s", 'complete'));

        // Active Campaigns
        $stats['active_campaigns'] = wp_count_posts('wpd_campaign')->publish;

        // Recent Donations
        $stats['recent'] = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_donations} WHERE status = %s ORDER BY created_at DESC LIMIT %d", 'complete', 5));

        wp_cache_set($cache_key, $stats, 'wpd_dashboard', 300); // 5 minutes
    }

    $total_collected = $stats['total_collected'] ?? 0;
    $total_donors = $stats['total_donors'] ?? 0;
    $active_campaigns = $stats['active_campaigns'] ?? 0;
    $recent = $stats['recent'] ?? [];

    ?>
    <div class="wpd-widget-stats"
        style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:10px; margin-bottom:15px; text-align:center;">
        <div style="background:#f0fdf4; padding:10px; border-radius:5px;">
            <p style="color:#166534; font-size:12px; margin:0;">Total Donasi</p>
            <strong style="font-size:18px; color:#15803d;">Rp
                <?php echo esc_html(number_format($total_collected, 0, ',', '.')); ?></strong>
        </div>
        <div style="background:#eff6ff; padding:10px; border-radius:5px;">
            <p style="color:#1e40af; font-size:12px; margin:0;">Donatur</p>
            <strong style="font-size:18px; color:#1d4ed8;"><?php echo esc_html(number_format($total_donors)); ?></strong>
        </div>
        <div style="background:#fff7ed; padding:10px; border-radius:5px;">
            <p style="color:#9a3412; font-size:12px; margin:0;">Campaigns</p>
            <strong
                style="font-size:18px; color:#c2410c;"><?php echo esc_html(number_format($active_campaigns)); ?></strong>
        </div>
    </div>

    <div class="wpd-widget-recent">
        <h4 style="margin:0 0 10px; font-size:13px; color:#666; border-bottom:1px solid #eee; padding-bottom:5px;">Donasi
            Terbaru</h4>
        <?php if ($recent): ?>
            <ul style="list-style:none; margin:0; padding:0;">
                <?php foreach ($recent as $row): ?>
                    <li style="margin-bottom:8px; display:flex; justify-content:space-between; font-size:13px;">
                        <span><?php echo esc_html($row->name); ?></span>
                        <span style="font-weight:bold; color:#059669;">+ Rp
                            <?php echo esc_html(number_format($row->amount, 0, ',', '.')); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p style="color:#999; font-style:italic;">Belum ada donasi.</p>
        <?php endif; ?>
    </div>

    <div style="margin-top:15px; padding-top:10px; border-top:1px solid #eee; text-align:right;">
        <a href="<?php echo esc_url(admin_url('admin.php?page=wpd-donations')); ?>"
            class="button button-primary button-small">Lihat Semua</a>
    </div>
    <?php
}
