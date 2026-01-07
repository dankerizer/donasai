<?php
/**
 * Campaign List Template
 * Shortcode: [donasai_campaign_list]
 */

if ( ! defined( 'ABSPATH' ) ) {
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
    'post_type'      => 'wpd_campaign',
    'post_status'    => 'publish',
    'posts_per_page' => 12, // Limitation to prevent overload
);
$query = new WP_Query( $args );

?>

<div class="wpd-campaign-list-wrapper" style="font-family: '<?php echo esc_attr($font_family); ?>', sans-serif;">
    
    <style>
        .wpd-campaign-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 24px;
        }
        .wpd-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: <?php echo esc_attr($border_radius); ?>;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .wpd-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .wpd-card-image {
            position: relative;
            padding-top: 56.25%; /* 16:9 Aspect Ratio */
            background: #f3f4f6;
        }
        .wpd-card-image img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .wpd-card-content {
            padding: 16px;
        }
        .wpd-card-title {
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 12px 0;
            line-height: 1.4;
            color: #111827;
        }
        .wpd-card-title a {
            color: inherit;
            text-decoration: none;
        }
        .wpd-card-title a:hover {
            color: <?php echo esc_attr($primary_color); ?>;
        }
        .wpd-progress-wrap {
            margin-bottom: 16px;
        }
        .wpd-progress-bar {
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 8px;
        }
        .wpd-progress-fill {
            height: 100%;
            background: <?php echo esc_attr($primary_color); ?>;
            border-radius: 4px;
            width: 0;
            transition: width 1s ease-out;
        }
        .wpd-stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            font-size: 13px;
        }
        .wpd-stat-label {
            color: #6b7280;
            margin-bottom: 2px;
        }
        .wpd-stat-value {
            font-weight: 600;
            color: #374151;
        }
        .wpd-card-footer {
            padding: 12px 16px;
            border-top: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .wpd-btn-donate {
            display: inline-block;
            background: <?php echo esc_attr($button_color); ?>;
            color: #fff;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: opacity 0.2s;
        }
        .wpd-btn-donate:hover {
            opacity: 0.9;
            color: #fff;
        }
        .wpd-infinity {
            font-size: 18px;
            line-height: 1;
        }
    </style>

    <?php if ( $query->have_posts() ) : ?>
        <div class="wpd-campaign-grid">
            <?php while ( $query->have_posts() ) : $query->the_post(); 
                $campaign_id = get_the_ID();
                $progress = function_exists('wpd_get_campaign_progress') ? wpd_get_campaign_progress($campaign_id) : ['collected' => 0, 'target' => 0, 'percentage' => 0];
                
                // Expiry Logic (Placeholder as meta isn't strictly defined yet for expiry, assume unlimited for mvp or until implemented)
                // If you had an expiry date meta: $expiry = get_post_meta($campaign_id, '_wpd_expiry', true);
                $is_unlimited = true; 
            ?>
                <div class="wpd-card">
                    <div class="wpd-card-image">
                        <a href="<?php the_permalink(); ?>">
                            <?php if ( has_post_thumbnail() ) : ?>
                                <?php the_post_thumbnail('medium_large'); ?>
                            <?php else : ?>
                                <img src="https://via.placeholder.com/600x400?text=No+Image" alt="<?php the_title_attribute(); ?>">
                            <?php endif; ?>
                        </a>
                    </div>
                    
                    <div class="wpd-card-content">
                        <h3 class="wpd-card-title">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_title(); ?>
                            </a>
                        </h3>
                        
                        <div class="wpd-progress-wrap">
                            <div class="wpd-progress-bar">
                                <div class="wpd-progress-fill" style="width: <?php echo esc_attr($progress['percentage']); ?>%;"></div>
                            </div>
                            <div style="display:flex; justify-content:flex-end; font-size:12px; font-weight:600; color:<?php echo esc_attr($primary_color); ?>;">
                                <?php echo esc_html($progress['percentage']); ?>%
                            </div>
                        </div>

                        <div class="wpd-stats-grid">
                            <div>
                                <div class="wpd-stat-label">Terkumpul</div>
                                <div class="wpd-stat-value">Rp <?php echo number_format($progress['collected'], 0, ',', '.'); ?></div>
                            </div>
                            <div style="text-align:right;">
                                <div class="wpd-stat-label">Target</div>
                                <div class="wpd-stat-value">Rp <?php echo number_format($progress['target'], 0, ',', '.'); ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="wpd-card-footer">
                        <div style="font-size:13px; color:#6b7280;">
                            <span style="display:block; margin-bottom:2px;">Batas Waktu</span>
                            <?php if ( $is_unlimited ) : ?>
                                <span class="wpd-infinity">∞</span>
                            <?php else : ?>
                                <!-- Add countdown logic here if needed -->
                            <?php endif; ?>
                        </div>
                        <a href="<?php the_permalink(); ?>" class="wpd-btn-donate">
                            Donasi
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <?php wp_reset_postdata(); ?>
    <?php else : ?>
        <p>Belum ada kampanye yang tersedia saat ini.</p>
    <?php endif; ?>

</div>
