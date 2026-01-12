<?php
/**
 * Campaign List Template
 * Shortcode: [donasai_campaign_list]
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get Appearance Settings
$settings_app = get_option('wpd_settings_appearance', []);
$primary_color = $settings_app['brand_color'] ?? '#059669';
$button_color = $settings_app['button_color'] ?? '#ec4899';
$font_family = $settings_app['font_family'] ?? 'Inter';
$border_radius = $settings_app['border_radius'] ?? '12px';

// Query Campaigns
$args = array(
    'post_type' => 'wpd_campaign',
    'post_status' => 'publish',
    'posts_per_page' => 12, // Limitation to prevent overload
);
$query = new WP_Query($args);

?>

    <!-- CSS Variables are now handled by css-loader.php -->
    <div class="wpd-campaign-list-wrapper">



    <?php if ($query->have_posts()): ?>
        <div class="wpd-campaign-grid">
            <?php while ($query->have_posts()):
                $query->the_post();
                $campaign_id = get_the_ID();
                $progress = function_exists('wpd_get_campaign_progress') ? wpd_get_campaign_progress($campaign_id) : ['collected' => 0, 'target' => 0, 'percentage' => 0];

                // Expiry Logic (Placeholder as meta isn't strictly defined yet for expiry, assume unlimited for mvp or until implemented)
                // If you had an expiry date meta: $expiry = get_post_meta($campaign_id, '_wpd_expiry', true);
                $is_unlimited = true;
                ?>
                <div class="wpd-card">
                    <div class="wpd-card-image">
                        <a href="<?php echo esc_url(get_permalink()); ?>">
                            <?php if (has_post_thumbnail()): ?>
                                <?php the_post_thumbnail('medium_large'); // Output trusted HTML ?>
                            <?php else: ?>
                                <div class="wpd-no-image">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                        stroke="currentColor" style="width:40px;height:40px;">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </a>
                    </div>

                    <div class="wpd-card-content">
                        <h3 class="wpd-card-title">
                            <a href="<?php echo esc_url(get_permalink()); ?>">
                                <?php the_title(); // Already safe, but typically get_the_title() + esc_html() is safer if we want full control. The_title() is fine. ?>
                            </a>
                        </h3>

                        <div class="wpd-progress-wrap">
                            <div class="wpd-progress-bar">
                                <div class="wpd-progress-fill"
                                    style="width: <?php echo esc_attr($progress['percentage']); ?>%;"></div>
                            </div>
                            <div
                                style="display:flex; justify-content:flex-end; font-size:12px; font-weight:600; color:var(--wpd-primary);">
                                <?php echo esc_html($progress['percentage']); ?>%
                            </div>
                        </div>

                        <div class="wpd-stats-grid">
                            <div>
                                <div class="wpd-stat-label"><?php esc_html_e('Terkumpul', 'donasai'); ?></div>
                                <div class="wpd-stat-value">Rp
                                    <?php echo esc_html(number_format($progress['collected'], 0, ',', '.')); ?>
                                </div>
                            </div>
                            <div style="text-align:right;">
                                <div class="wpd-stat-label"><?php esc_html_e('Target', 'donasai'); ?></div>
                                <div class="wpd-stat-value">Rp
                                    <?php echo esc_html(number_format($progress['target'], 0, ',', '.')); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="wpd-card-footer">
                        <div style="font-size:13px; color:#6b7280;">
                            <span
                                style="display:block; margin-bottom:2px;"><?php esc_html_e('Batas Waktu', 'donasai'); ?></span>
                            <?php if ($is_unlimited): ?>
                                <span class="wpd-infinity">∞</span>
                            <?php else: ?>
                                <!-- Add countdown logic here if needed -->
                            <?php endif; ?>
                        </div>
                        <a href="<?php echo esc_url(get_permalink()); ?>" class="wpd-btn-donate">
                            <?php esc_html_e('Donasi', 'donasai'); ?>
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <?php wp_reset_postdata(); ?>
    <?php else: ?>
        <p><?php esc_html_e('Belum ada kampanye yang tersedia saat ini.', 'donasai'); ?></p>
    <?php endif; ?>

</div>