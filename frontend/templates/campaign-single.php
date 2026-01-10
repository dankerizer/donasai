<?php
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Single Campaign Template
 */

global $wp_query;
$payment_slug = get_option('wpd_settings_general')['payment_slug'] ?? 'pay';

// Check if viewing payment page
if (isset($wp_query->query_vars[$payment_slug])) {
    // Standalone Page Layout
    ?>
    <!DOCTYPE html>
    <html <?php language_attributes(); ?>>

    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php wp_head(); ?>
        <style>
            /* Reset margin for standalone page to avoid theme interference */
            html,
            body {
                margin: 0;
                padding: 0;
            }
        </style>
    </head>

    if (file_exists(WPD_PLUGIN_PATH . 'frontend/templates/donation-form.php')) {
    include WPD_PLUGIN_PATH . 'frontend/templates/donation-form.php';
    }
    return;

    </html>
    <?php
    return;
}

get_header();

// Initialize Campaign Data
$campaign_id = get_the_ID();
$is_verified = get_post_meta($campaign_id, '_wpd_is_verified', true);
$progress = function_exists('wpd_get_campaign_progress') ? wpd_get_campaign_progress($campaign_id) : array('collected' => 0, 'target' => 0, 'percentage' => 0);
$progress = function_exists('wpd_get_campaign_progress') ? wpd_get_campaign_progress($campaign_id) : array('collected' => 0, 'target' => 0, 'percentage' => 0);
$settings_app = get_option('wpd_settings_appearance', []);
$sidebar_limit_init = isset($settings_app['sidebar_count']) ? intval($settings_app['sidebar_count']) : 5;
$donors = function_exists('wpd_get_recent_donors') ? wpd_get_recent_donors($campaign_id, $sidebar_limit_init) : array();
$total_donors = function_exists('wpd_get_donor_count') ? wpd_get_donor_count($campaign_id) : count($donors);

// Layout Settings
$settings_app = get_option('wpd_settings_appearance', []);
$container_width = $settings_app['container_width'] ?? '1100px';
$border_radius = $settings_app['border_radius'] ?? '12px';
$layout_mode = $settings_app['campaign_layout'] ?? 'sidebar-right';
$primary_color = $settings_app['brand_color'] ?? '#059669';
$button_color = $settings_app['button_color'] ?? '#ec4899';
$sidebar_limit = isset($settings_app['sidebar_count']) ? intval($settings_app['sidebar_count']) : 5;
$per_page_limit = isset($settings_app['donor_per_page']) ? intval($settings_app['donor_per_page']) : 10;

// Typography & Dark Mode
$font_family = $settings_app['font_family'] ?? 'Inter';
$font_size = $settings_app['font_size'] ?? '16px';
$dark_mode = $settings_app['dark_mode'] ?? false;

// Google Fonts Map
$fonts_map = [
    'Inter' => 'Inter:wght@400;500;600;700',
    'Roboto' => 'Roboto:wght@400;500;700',
    'Open Sans' => 'Open+Sans:wght@400;600;700',
    'Poppins' => 'Poppins:wght@400;500;600;700',
    'Lato' => 'Lato:wght@400;700'
];
$font_url = isset($fonts_map[$font_family]) ? "https://fonts.googleapis.com/css2?family={$fonts_map[$font_family]}&display=swap" : "";
?>

<?php // Fonts enqueued via functions ?>

<div class="wpd-container <?php echo $dark_mode ? 'wpd-dark' : ''; ?>"
    style="max-width:<?php echo esc_attr($container_width); ?>; margin:0 auto; padding:20px; font-family: '<?php echo esc_attr($font_family); ?>', sans-serif; font-size: <?php echo esc_attr($font_size); ?>;">

    <style>
        :root {
            --wpd-radius:
                <?php echo esc_attr($border_radius); ?>
            ;
            --wpd-primary:
                <?php echo esc_attr($primary_color); ?>
            ;
            --wpd-btn:
                <?php echo esc_attr($button_color); ?>
            ;
        }

        <?php if ($layout_mode === 'sidebar-left'): ?>
            .wpd-sidebar-col {
                order: -1;
            }

        <?php elseif ($layout_mode === 'full-width'): ?>
            .wpd-main-col {
                flex: 0 0 100%;
                max-width: 100%;
            }

            .wpd-sidebar-col {
                display: none !important;
            }

            .wpd-mobile-cta {
                display: flex !important;
            }

        <?php endif; ?>
    </style>

    <!-- Main Layout Grid -->
    <div class="wpd-campaign-grid">

        <!-- Left Column: Content (65%) -->
        <div class="wpd-main-col" style="max-width:100%;">

            <!-- Featured Image -->
            <?php if (has_post_thumbnail()): ?>
                <div class="wpd-featured-image"
                    style="width:100%; border-radius:12px; overflow:hidden; margin-bottom:20px; box-shadow:0 10px 15px -3px rgba(0,0,0,0.1);">
                    <?php the_post_thumbnail('full', array('style' => 'width:100%; height:auto; display:block;')); ?>
                </div>
            <?php endif; ?>

            <!-- Title & Header -->
            <div class="wpd-campaign-header" style="margin-bottom:25px;">
                <h1 style="font-size:28px; line-height:1.3; color:#111827; margin:0 0 10px 0; font-weight:700;">
                    <?php the_title(); ?>
                </h1>

                <div style="display:flex; align-items:center; color:#6b7280; font-size:14px;">
                    <svg style="width:16px; height:16px; margin-right:5px;" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Lokasi: Indonesia</span>

                    <?php if ($is_verified): ?>
                        <span
                            style="margin-left:15px; display:inline-flex; align-items:center; color:#2563eb; background:#eff6ff; padding:2px 8px; border-radius:10px; font-size:12px; font-weight:500;">
                            <svg style="width:12px; height:12px; margin-right:3px;" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            Kampanye Terverifikasi
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <div class="wpd-tabs" style="border-bottom:1px solid #e5e7eb; margin-bottom:20px;">
                <button onclick="openWpdTab('desc')" id="tab-btn-desc" class="wpd-tab-btn active"
                    style="background:none; border:none; border-bottom:2px solid #2563eb; color:#2563eb; font-weight:600; padding:10px 20px; font-size:16px; cursor:pointer;">Detail
                    Program</button>
                <?php if (function_exists('wpd_is_pro_active') && wpd_is_pro_active()): ?>
                    <button onclick="openWpdTab('updates')" id="tab-btn-updates" class="wpd-tab-btn"
                        style="background:none; border:none; border-bottom:2px solid transparent; color:#6b7280; font-weight:500; padding:10px 20px; font-size:16px; cursor:pointer;">Kabar
                        Terbaru (0)</button>
                <?php endif; ?>
                <button onclick="openWpdTab('donors')" id="tab-btn-donors" class="wpd-tab-btn"
                    style="background:none; border:none; border-bottom:2px solid transparent; color:#6b7280; font-weight:500; padding:10px 20px; font-size:16px; cursor:pointer;">Donatur
                    (<?php echo esc_html($total_donors); ?>)</button>
            </div>

            <!-- Tab Content: Description -->
            <div id="wpd-tab-desc" class="wpd-tab-content" style="color:#374151; line-height:1.7; font-size:16px;">
                <?php the_content(); ?>
            </div>

            <!-- Tab Content: Updates -->
            <?php if (function_exists('wpd_is_pro_active') && wpd_is_pro_active()): ?>
                <div id="wpd-tab-updates" class="wpd-tab-content"
                    style="display:none; color:#6b7280; padding:40px 0; text-align:center;">
                    <svg style="width:48px; height:48px; margin:0 auto 10px; color:#d1d5db;" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                    <p>Belum ada kabar terbaru.</p>
                </div>
            <?php endif; ?>

            <!-- Tab Content: Donors -->
            <div id="wpd-tab-donors" class="wpd-tab-content" style="display:none;">
                <?php if (empty($donors)): ?>
                    <p style="color:#6b7280; padding:30px 0; text-align:center;">Belum ada donatur. Jadilah donatur pertama!
                    </p>
                <?php else: ?>
                    <div id="wpd-all-donors-list" class="wpd-donor-list">
                        <!-- Donors will be loaded here via AJAX or initial loop -->
                        <?php foreach ($donors as $donor):
                            $name = $donor->is_anonymous ? 'Hamba Allah' : $donor->name;
                            $time = human_time_diff(strtotime($donor->created_at), current_time('timestamp')) . ' yang lalu';
                            $initial = strtoupper(substr($name, 0, 1));
                            ?>
                            <div
                                style="display:flex; gap:15px; margin-bottom:20px; border-bottom:1px solid #f3f4f6; padding-bottom:20px;">
                                <div
                                    style="width:40px; height:40px; background:#e0e7ff; color:#4f46e5; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold; flex-shrink:0;">
                                    <?php echo esc_html($initial); ?>
                                </div>
                                <div>
                                    <h4 style="margin:0; font-size:16px; font-weight:600; color:#111827;">
                                        <?php echo esc_html($name); ?>
                                    </h4>
                                    <div style="font-size:12px; color:#6b7280; margin-top:2px;">
                                        Berdonasi <span style="font-weight:600; color:#059669;">Rp
                                            <?php echo number_format($donor->amount, 0, ',', '.'); ?></span> &bull;
                                        <?php echo esc_html($time); ?>
                                    </div>
                                    <?php if (!empty($donor->note)): ?>
                                        <p
                                            style="margin:8px 0 0; font-size:14px; color:#4b5563; background:#f9fafb; padding:10px; border-radius:8px;">
                                            "<?php echo esc_html($donor->note); ?>"
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($total_donors > count($donors)): ?>
                        <div style="text-align:center; margin-top:20px;">
                            <button id="wpd-load-more-donors" onclick="wpdLoadMoreDonors()" data-page="1"
                                data-campaign="<?php echo $campaign_id; ?>"
                                style="padding:8px 20px; background:#f3f4f6; color:#4b5563; border:none; border-radius:6px; cursor:pointer; font-weight:500;">
                                Muat Lebih Banyak
                            </button>
                            <span id="wpd-donors-loading" style="display:none; color:#6b7280; font-size:14px;">Memuat...</span>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

        </div>

        <!-- Right Column: Sidebar (35%) -->
        <div class="wpd-sidebar-col" style="max-width:100%;">
            <div style="position:sticky; top:20px;">

                <!-- Donation Card -->
                <div
                    style="background:white; border-radius:12px; box-shadow:0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06); padding:24px; border:1px solid #e5e7eb; margin-bottom:20px;">

                    <div style="margin-bottom:20px;">
                        <div style="font-size:32px; font-weight:700; color:var(--wpd-primary); line-height:1;">
                            Rp <?php echo esc_html(number_format($progress['collected'], 0, ',', '.')); ?>
                        </div>
                        <div style="font-size:14px; color:#6b7280; margin-top:5px;">
                            terkumpul dari <span style="font-weight:500;">Rp
                                <?php echo esc_html(number_format($progress['target'], 0, ',', '.')); ?></span>
                        </div>
                    </div>

                    <div
                        style="background:#e5e7eb; height:8px; border-radius:4px; margin-bottom:24px; overflow:hidden;">
                        <div class="wpd-progress-bar-fill"
                            style="background:var(--wpd-primary); height:100%; border-radius:4px; --wpd-progress-width:<?php echo esc_attr($progress['percentage']); ?>%; width:0;">
                        </div>
                    </div>

                    <!-- CTA Buttons -->
                    <?php
                    global $wp_rewrite;
                    if ($wp_rewrite->using_permalinks()) {
                        $donate_link = user_trailingslashit(get_permalink() . $payment_slug);
                    } else {
                        $donate_link = add_query_arg($payment_slug, '1', get_permalink());
                    }
                    ?>
                    <a href="<?php echo esc_url($donate_link); ?>"
                        style="display:block; max-width:100%; background:var(--wpd-btn); color:white; font-weight:700; text-align:center; padding:14px; border-radius:8px; text-decoration:none; font-size:18px; margin-bottom:15px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                        Donasi Sekarang
                    </a>

                    <!-- Fundraiser Button -->
                    <?php if (function_exists('wpd_is_pro_active') && wpd_is_pro_active()): ?>
                        <?php if (is_user_logged_in()): ?>
                            <button onclick="wpdRegisterFundraiser(<?php echo intval($campaign_id); ?>)"
                                style="width:100%; background:white; color:var(--wpd-primary); border:1px solid var(--wpd-primary); font-weight:600; padding:10px; border-radius:8px; cursor:pointer;">
                                Daftar sebagai Penggalang Dana
                            </button>
                        <?php else: ?>
                            <div style="text-align:center; font-size:13px; color:#6b7280;">
                                <a href="<?php echo esc_url(wp_login_url(get_permalink())); ?>"
                                    style="color:var(--wpd-primary);">Masuk</a> untuk mendaftar Penggalang Dana
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                </div>

                <!-- Fundraiser Profile -->
                <?php
                $org_settings = get_option('wpd_settings_organization', []);
                $fundraiser_name = !empty($org_settings['org_name']) ? $org_settings['org_name'] : get_the_author();
                $fundraiser_avatar = !empty($org_settings['org_logo']) ? $org_settings['org_logo'] : 'https://ui-avatars.com/api/?name=' . urlencode($fundraiser_name) . '&background=random';
                ?>
                <div
                    style="background:white; border-radius:12px; border:1px solid #e5e7eb; padding:20px; display:flex; align-items:center; gap:15px; margin-bottom:20px;">
                    <div
                        style="width:50px; height:50px; border-radius:50%; background:#f3f4f6; flex-shrink:0; overflow:hidden;">
                        <img src="<?php echo esc_url($fundraiser_avatar); ?>"
                            style="width:100%; height:100%; object-fit:cover;">
                    </div>
                    <div>
                        <div style="font-size:12px; text-transform:uppercase; color:#6b7280; font-weight:600;">
                            Penggalang Dana</div>
                        <div style="font-weight:700; color:#111827; display:flex; align-items:center;">
                            <?php echo esc_html($fundraiser_name); ?>
                            <svg style="width:14px; height:14px; color:#2563eb; margin-left:5px;" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Recent Donors Sidebar -->
                <div style="background:white; border-radius:12px; border:1px solid #e5e7eb; overflow:hidden;">
                    <div
                        style="padding:15px 20px; background:#f9fafb; border-bottom:1px solid #e5e7eb; font-weight:700; color:#374151;">
                        Doa dan Dukungan (<?php echo esc_html($total_donors); ?>)
                    </div>
                    <div style="max-height:400px; overflow-y:auto;">
                        <?php if (empty($donors)): ?>
                            <div style="padding:20px; text-align:center; color:#9ca3af; font-size:14px;">Belum ada donatur.
                            </div>
                        <?php else: ?>
                            <?php foreach ($donors as $donor):
                                $name = $donor->is_anonymous ? 'Hamba Allah' : $donor->name;
                                $time = human_time_diff(strtotime($donor->created_at), current_time('timestamp')) . ' yang lalu';
                                ?>
                                <div style="padding:15px 20px; border-bottom:1px solid #f3f4f6;">
                                    <div style="font-weight:600; color:#111827; font-size:14px;">
                                        <?php echo esc_html($name); ?>
                                    </div>
                                    <div style="font-size:12px; color:#6b7280; margin-bottom:5px;">Berdonasi <span
                                            style="color:var(--wpd-primary); font-weight:600;">Rp
                                            <?php echo number_format($donor->amount, 0, ',', '.'); ?></span> &bull;
                                        <?php echo esc_html($time); ?>
                                    </div>
                                    <?php if (!empty($donor->note)): ?>
                                        <div style="font-size:13px; color:#4b5563; font-style:italic;">
                                            "<?php echo esc_html($donor->note); ?>"</div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <div style="padding:10px; text-align:center; border-top:1px solid #e5e7eb;">
                            <button
                                onclick="openWpdTab('donors'); window.scrollTo({top: document.querySelector('.wpd-tabs').offsetTop - 100, behavior: 'smooth'});"
                                style="background:none; border:none; color:#2563eb; font-size:13px; font-weight:500; cursor:pointer;">Lihat
                                Semua</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <!-- Fundraiser Modal (Hidden) -->
    <div id="wpd-fundraiser-modal"
        style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:99999; align-items:center; justify-content:center;">
        <div style="background:white; padding:25px; border-radius:10px; width:90%; max-width:400px; position:relative;">
            <button onclick="document.getElementById('wpd-fundraiser-modal').style.display='none'"
                style="position:absolute; top:10px; right:15px; border:none; background:none; font-size:20px; cursor:pointer;">&times;</button>
            <h3 style="margin-top:0;">Pendaftaran Berhasil!</h3>
            <p>Bagikan tautan ini untuk mengajak orang lain berdonasi:</p>
            <input type="text" id="wpd-ref-link" readonly
                style="width:100%; padding:10px; background:#f9f9f9; border:1px solid #ddd; margin-bottom:15px; font-size:14px;">
            <button class="button" onclick="wpdCopyRef()"
                style="width:100%; margin-bottom:10px; background:#2563eb; color:white; border:none; padding:10px; border-radius:4px;">Salin
                Tautan</button>
            <a id="wpd-wa-share" href="#" target="_blank" class="button"
                style="display:block; width:100%; text-align:center; background:#25D366; color:white; border:none; padding:10px; border-radius:4px; text-decoration:none;">Bagikan
                ke WhatsApp</a>
        </div>
    </div>

    <!-- Mobile Sticky CTA -->
    <?php
    $payment_slug = get_option('wpd_settings_general')['payment_slug'] ?? 'pay';
    $donate_link_sticky = $wp_rewrite->using_permalinks()
        ? user_trailingslashit(get_permalink() . $payment_slug)
        : add_query_arg($payment_slug, '1', get_permalink());
    ?>
    <div class="wpd-mobile-cta">
        <div
            style="width:100%; max-width:<?php echo esc_attr($container_width); ?>; margin:0 auto; display:flex; align-items:center; justify-content:space-between; padding:0 15px;">
            <div>
                <div style="font-size:12px; color:#6b7280; margin-bottom:2px;">Terkumpul</div>
                <div style="font-size:16px; font-weight:700; color:#059669;">Rp
                    <?php echo esc_html(number_format($progress['collected'], 0, ',', '.')); ?>
                </div>
            </div>
            <a href="<?php echo esc_url($donate_link_sticky); ?>"
                style="background:var(--wpd-btn); color:white; font-weight:700; padding:12px 24px; border-radius:8px; text-decoration:none; font-size:16px;">
                Donasi Sekarang
            </a>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Use helper function from campaign.js
        function wpdRegisterFundraiser(campaignId) {
            var nonce = '<?php echo esc_js(wp_create_nonce('wp_rest')); ?>';
            wpdRegisterFundraiserHelper(campaignId, nonce);
        }

        function wpdLoadMoreDonors() {
            var btn = document.getElementById('wpd-load-more-donors');
            var loading = document.getElementById('wpd-donors-loading');
            var campaignId = btn.getAttribute('data-campaign');
            var page = parseInt(btn.getAttribute('data-page')) + 1;

            btn.style.display = 'none';
            loading.style.display = 'inline-block';

            // API Call
            fetch('<?php echo get_rest_url(null, 'wpd/v1/campaigns/'); ?>' + campaignId + '/donors?page=' + page + '&per_page=<?php echo esc_js($per_page_limit); ?>')
                .then(response => response.json())
                .then(data => {
                    loading.style.display = 'none';
                    if (data.data && data.data.length > 0) {
                        var list = document.getElementById('wpd-all-donors-list');
                        data.data.forEach(donor => {
                            var html = `
                             <div style="display:flex; gap:15px; margin-bottom:20px; border-bottom:1px solid #f3f4f6; padding-bottom:20px;">
                                <div style="width:40px; height:40px; background:#e0e7ff; color:#4f46e5; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold; flex-shrink:0;">
                                    ${donor.initial}
                                </div>
                                <div>
                                    <h4 style="margin:0; font-size:16px; font-weight:600; color:#111827;">
                                        ${donor.name}
                                    </h4>
                                    <div style="font-size:12px; color:#6b7280; margin-top:2px;">
                                        Berdonasi <span style="font-weight:600; color:#059669;">Rp ${donor.amount_fmt}</span> &bull; ${donor.time_ago}
                                    </div>
                                    ${donor.note ? `<p style="margin:8px 0 0; font-size:14px; color:#4b5563; background:#f9fafb; padding:10px; border-radius:8px;">"${donor.note}"</p>` : ''}
                                </div>
                            </div>
                            `;
                            list.insertAdjacentHTML('beforeend', html);
                        });

                        // Update Page
                        btn.setAttribute('data-page', page);

                        // Check if more
                        if (page < data.pagination.total_pages) {
                            btn.style.display = 'inline-block';
                        } else {
                            // Hide if no more pages
                            btn.style.display = 'none';
                        }
                    } else {
                        btn.style.display = 'none';
                    }
                })
                .catch(err => {
                    console.error('Error loading donors', err);
                    loading.style.display = 'none';
                    btn.style.display = 'inline-block';
                    alert('Gagal memuat donatur.');
                });
        }
    </script>

    <!-- styles moved to head -->
</div>

<?php
get_footer();
?>