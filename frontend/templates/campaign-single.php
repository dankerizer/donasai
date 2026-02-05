<?php
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Single Campaign Template
 */

global $wp_query;
$donasai_payment_slug = get_option('donasai_settings_general')['payment_slug'] ?? 'pay';

// Check if viewing payment page
if (isset($wp_query->query_vars[$donasai_payment_slug])) {
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

    <?php
    if (file_exists(DONASAI_PLUGIN_PATH . 'frontend/templates/donation-form.php')) {
        include DONASAI_PLUGIN_PATH . 'frontend/templates/donation-form.php';
    } else {
        echo "Template donation-form.php tidak ditemukan.";
    }
    ?>

    </html>
    <?php
    return;
}

get_header();

// Initialize Campaign Data
$donasai_campaign_id = get_the_ID();
$donasai_is_verified = get_post_meta($donasai_campaign_id, '_donasai_is_verified', true);
$donasai_progress = function_exists('donasai_get_campaign_progress') ? donasai_get_campaign_progress($donasai_campaign_id) : array('collected' => 0, 'target' => 0, 'percentage' => 0);
$donasai_settings_app = get_option('donasai_settings_appearance', []);
$donasai_sidebar_limit_init = isset($donasai_settings_app['sidebar_count']) ? intval($donasai_settings_app['sidebar_count']) : 5;
$donasai_donors = function_exists('donasai_get_recent_donors') ? donasai_get_recent_donors($donasai_campaign_id, $donasai_sidebar_limit_init) : array();
$donasai_total_donors = function_exists('donasai_get_donor_count') ? donasai_get_donor_count($donasai_campaign_id) : count($donasai_donors);

// Layout Settings
// $donasai_settings_app already fetched above
$donasai_container_width = $donasai_settings_app['container_width'] ?? '1100px';
$donasai_border_radius = $donasai_settings_app['border_radius'] ?? '12px';
$donasai_layout_mode = $donasai_settings_app['campaign_layout'] ?? 'sidebar-right';
$donasai_primary_color = $donasai_settings_app['brand_color'] ?? '#059669';
$donasai_button_color = $donasai_settings_app['button_color'] ?? '#ec4899';
$donasai_sidebar_limit = isset($donasai_settings_app['sidebar_count']) ? intval($donasai_settings_app['sidebar_count']) : 5;
$donasai_per_page_limit = isset($donasai_settings_app['donor_per_page']) ? intval($donasai_settings_app['donor_per_page']) : 10;
$donasai_dark_mode = !empty($donasai_settings_app['dark_mode']);


// Check Pro Status
$donasai_is_pro = function_exists('donasai_is_pro_active') && donasai_is_pro_active();

// Hero Style
$donasai_hero_style = $donasai_settings_app['hero_style'] ?? 'standard';
if (!$donasai_is_pro) {
    $donasai_hero_style = 'standard';
}


// Feature Toggles// Defaults
$donasai_show_countdown = $donasai_is_pro && (isset($donasai_settings_app['show_countdown']) ? filter_var($donasai_settings_app['show_countdown'], FILTER_VALIDATE_BOOLEAN) : true);
$donasai_show_prayer_tab = $donasai_is_pro && (isset($donasai_settings_app['show_prayer_tab']) ? filter_var($donasai_settings_app['show_prayer_tab'], FILTER_VALIDATE_BOOLEAN) : true);
$donasai_show_updates_tab = $donasai_is_pro && (isset($donasai_settings_app['show_updates_tab']) ? filter_var($donasai_settings_app['show_updates_tab'], FILTER_VALIDATE_BOOLEAN) : true);
$donasai_show_donor_list = isset($donasai_settings_app['show_donor_list']) ? filter_var($donasai_settings_app['show_donor_list'], FILTER_VALIDATE_BOOLEAN) : true;
$donasai_show_leaderboard = $donasai_is_pro && (isset($donasai_settings_app['show_leaderboard']) ? filter_var($donasai_settings_app['show_leaderboard'], FILTER_VALIDATE_BOOLEAN) : true);

if (!$donasai_is_pro) {
    $donasai_show_countdown = false;
    $donasai_show_prayer_tab = false;
    $donasai_show_updates_tab = false;
    // Donor List is a Free feature, so we don't force-disable it.
    // The toggle in admin is Pro-only, so Free users stick to default (true).
}
?>

<?php // Fonts enqueued via functions ?>

<!-- Hero: Wide Style (Outside Container) -->
<?php if ($donasai_hero_style === 'wide' && has_post_thumbnail()): ?>
    <div class="donasai-hero-wide" style="width:100%; height:400px; overflow:hidden; position:relative; margin-bottom: -40px;">
        <?php the_post_thumbnail('full', array('style' => 'width:100%; height:100%; object-fit:cover;')); ?>
    </div>
<?php endif; ?>

<div class="donasai-container <?php echo $donasai_dark_mode ? 'donasai-dark' : ''; ?>"
    style="max-width:<?php echo esc_attr($donasai_container_width); ?>; margin:0 auto; padding:20px; position:relative; z-index:2;">

    <!-- Main Layout Grid -->

    <!-- Main Layout Grid -->
    <div class="donasai-campaign-grid">

        <!-- Left Column: Content (65%) -->
        <div class="donasai-main-col" style="max-width:100%;">

            <!-- Featured Image -->
            <?php if (has_post_thumbnail()): ?>
                <?php if ($donasai_hero_style === 'standard'): ?>
                    <div class="donasai-featured-image"
                        style="width:100%; border-radius:12px; overflow:hidden; margin-bottom:20px; box-shadow:0 10px 15px -3px rgba(0,0,0,0.1);">
                        <?php the_post_thumbnail('full', array('style' => 'width:100%; height:auto; display:block;')); ?>
                    </div>
                <?php elseif ($donasai_hero_style === 'overlay'): ?>
                    <div class="donasai-hero-overlay">
                        <?php the_post_thumbnail('full'); ?>
                        <div class="donasai-hero-content">
                            <h1 class="donasai-heading"
                                style="font-size:28px; line-height:1.3; margin:0 0 10px 0; font-weight:700;">
                                <?php the_title(); ?>
                            </h1>

                            <div class="donasai-subheading" style="display:flex; align-items:center; font-size:14px;">
                                <svg style="width:16px; height:16px; margin-right:5px;" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>Lokasi: Indonesia</span>
                                <?php if ($donasai_is_verified): ?>
                                    <span
                                        style="margin-left:15px; display:inline-flex; align-items:center; color:white; background:rgba(37, 99, 235, 0.9); padding:2px 8px; border-radius:10px; font-size:12px; font-weight:500;">
                                        <svg style="width:12px; height:12px; margin-right:3px;" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Kampanye Terverifikasi
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; // End standard/overlay check ?>
            <?php endif; // End has_thumbnail ?>

            <?php if ($donasai_hero_style !== 'overlay'): ?>
                <!-- Title & Header (Standard/Wide) -->
                <div class="donasai-campaign-header"
                    style="margin-bottom:25px; <?php echo ($donasai_hero_style === 'wide') ? 'background:var(--donasai-bg-card); padding:20px; border-radius:var(--donasai-radius); border:1px solid var(--donasai-border); position:relative; margin-top:-60px; box-shadow:0 4px 6px rgba(0,0,0,0.05);' : ''; ?>">
                    <h1 class="donasai-heading" style="font-size:28px; line-height:1.3; margin:0 0 10px 0; font-weight:700;">
                        <?php the_title(); ?>
                    </h1>

                    <div class="donasai-subheading" style="display:flex; align-items:center; font-size:14px;">
                        <svg style="width:16px; height:16px; margin-right:5px;" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Lokasi: Indonesia</span>
                        <?php if ($donasai_is_verified): ?>
                            <span
                                style="margin-left:15px; display:inline-flex; align-items:center; color:#2563eb; background:var(--donasai-bg-blue-light); padding:2px 8px; border-radius:10px; font-size:12px; font-weight:500;">
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
            <?php endif; ?>

            <!-- Tabs Navigation -->
            <div class="donasai-tabs"
                style="border-bottom:1px solid var(--donasai-border); margin-bottom:20px; display:flex; gap:10px; overflow-x:auto;">
                <button onclick="openWpdTab('desc')" id="tab-btn-desc" class="donasai-tab-btn active"
                    style="background:none; border:none; border-bottom:2px solid var(--donasai-primary); color:var(--donasai-primary); font-weight:600; padding:10px 15px; font-size:16px; cursor:pointer; white-space:nowrap;">Cerita</button>

                <?php if (function_exists('donasai_is_pro_active') && donasai_is_pro_active()): ?>
                    <?php if ($donasai_show_updates_tab): ?>
                        <button onclick="openWpdTab('updates')" id="tab-btn-updates" class="donasai-tab-btn"
                            style="background:none; border:none; border-bottom:2px solid transparent; color:var(--donasai-text-muted); font-weight:500; padding:10px 15px; font-size:16px; cursor:pointer; white-space:nowrap;">Kabar
                            Terbaru</button>
                    <?php endif; ?>

                    <!-- Doa Tab (Pro) -->
                    <?php if ($donasai_show_prayer_tab): ?>
                        <button onclick="openWpdTab('doa')" id="tab-btn-doa" class="donasai-tab-btn"
                            style="background:none; border:none; border-bottom:2px solid transparent; color:var(--donasai-text-muted); font-weight:500; padding:10px 15px; font-size:16px; cursor:pointer; white-space:nowrap;">Doa</button>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($donasai_show_donor_list): ?>
                    <button onclick="openWpdTab('donors')" id="tab-btn-donors" class="donasai-tab-btn"
                        style="background:none; border:none; border-bottom:2px solid transparent; color:var(--donasai-text-muted); font-weight:500; padding:10px 15px; font-size:16px; cursor:pointer; white-space:nowrap;">Donatur
                        (<?php echo esc_html($donasai_total_donors); ?>)</button>
                <?php endif; ?>
            </div>

            <!-- Tab Content: Description -->
            <div id="donasai-tab-desc" class="donasai-tab-content"
                style="color:var(--donasai-text-body); line-height:1.7; font-size:16px;">
                <?php the_content(); ?>
            </div>

            <!-- Tab Content: Updates -->
            <?php if (function_exists('donasai_is_pro_active') && donasai_is_pro_active() && $donasai_show_updates_tab): ?>
                <div id="donasai-tab-updates" class="donasai-tab-content"
                    style="display:none; color:var(--donasai-text-muted); padding:40px 0; text-align:center;">
                    <svg style="width:48px; height:48px; margin:0 auto 10px; color:var(--donasai-border);" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                    <p>Belum ada kabar terbaru.</p>
                </div>
            <?php endif; ?>

            <!-- Tab Content: Doa -->
            <?php if (function_exists('donasai_is_pro_active') && donasai_is_pro_active() && $donasai_show_prayer_tab): ?>
                <div id="donasai-tab-doa" class="donasai-tab-content" style="display:none;">
                    <?php
                    $donasai_donors_with_notes = array_filter($donasai_donors, function ($d) {
                        return !empty($d->note);
                    });
                    if (empty($donasai_donors_with_notes)):
                        ?>
                        <p style="color:var(--donasai-text-muted); padding:30px 0; text-align:center;">Belum ada doa dan dukungan.
                        </p>
                    <?php else: ?>
                        <div class="donasai-prayer-list">
                            <?php foreach ($donasai_donors_with_notes as $donasai_donor):
                                $donasai_name = $donasai_donor->is_anonymous ? 'Hamba Allah' : $donasai_donor->name;
                                $donasai_time = human_time_diff(strtotime($donasai_donor->created_at), current_time('timestamp')) . ' yang lalu';
                                $donasai_initial = strtoupper(substr($donasai_name, 0, 1));
                                ?>
                                <div
                                    style="display:flex; gap:15px; margin-bottom:20px; border-bottom:1px solid var(--donasai-border); padding-bottom:20px;">
                                    <div
                                        style="width:40px; height:40px; background:var(--donasai-bg-blue-accent); color:white; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold; flex-shrink:0;">
                                        <?php echo esc_html($donasai_initial); ?>
                                    </div>
                                    <div>
                                        <h4 style="margin:0; font-size:16px; font-weight:600; color:var(--donasai-text-main);">
                                            <?php echo esc_html($donasai_name); ?>
                                        </h4>
                                        <div style="font-size:12px; color:var(--donasai-text-muted); margin-top:2px;">
                                            <?php echo esc_html($donasai_time); ?>
                                        </div>
                                        <p style="margin:8px 0 0; font-size:15px; color:var(--donasai-text-body); font-style:italic;">
                                            "<?php echo esc_html($donasai_donor->note); ?>"
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Tab Content: Donors -->
            <?php if ($donasai_show_donor_list): ?>
                <div id="donasai-tab-donors" class="donasai-tab-content" style="display:none;">
                    <?php if (empty($donasai_donors)): ?>
                        <p style="color:var(--donasai-text-muted); padding:30px 0; text-align:center;">Belum ada donatur. Jadilah
                            donatur pertama!
                        </p>
                    <?php else: ?>
                        <div id="donasai-all-donors-list" class="donasai-donor-list">
                            <!-- Donors will be loaded here via AJAX or initial loop -->
                            <?php foreach ($donasai_donors as $donasai_donor):
                                $donasai_name = $donasai_donor->is_anonymous ? 'Hamba Allah' : $donasai_donor->name;
                                $donasai_time = human_time_diff(strtotime($donasai_donor->created_at), current_time('timestamp')) . ' yang lalu';
                                $donasai_initial = strtoupper(substr($donasai_name, 0, 1));
                                ?>
                                <div
                                    style="display:flex; gap:15px; margin-bottom:20px; border-bottom:1px solid var(--donasai-border); padding-bottom:20px;">
                                    <div
                                        style="width:40px; height:40px; background:var(--donasai-bg-blue-accent); color:white; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold; flex-shrink:0;">
                                        <?php echo esc_html($donasai_initial); ?>
                                    </div>
                                    <div>
                                        <h4 style="margin:0; font-size:16px; font-weight:600; color:var(--donasai-text-main);">
                                            <?php echo esc_html($donasai_name); ?>
                                        </h4>
                                        <div style="font-size:12px; color:var(--donasai-text-muted); margin-top:2px;">
                                            Berdonasi <span style="font-weight:600; color:var(--donasai-primary);">Rp
                                                <?php echo number_format($donasai_donor->amount, 0, ',', '.'); ?></span> &bull;
                                            <?php echo esc_html($donasai_time); ?>
                                        </div>
                                        <?php if (!empty($donasai_donor->note)): ?>
                                            <p
                                                style="margin:8px 0 0; font-size:14px; color:var(--donasai-text-body); background:var(--donasai-bg-tertiary); padding:10px; border-radius:8px;">
                                                "<?php echo esc_html($donasai_donor->note); ?>"
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if ($donasai_total_donors > count($donasai_donors)): ?>
                            <div style="text-align:center; margin-top:20px;">
                                    <button id="donasai-load-more-donors" onclick="wpdLoadMoreDonors()" data-page="1"
                                    data-campaign="<?php echo esc_attr($donasai_campaign_id); ?>"
                                    style="padding:8px 20px; background:var(--donasai-bg-secondary); color:var(--donasai-text-body); border:none; border-radius:6px; cursor:pointer; font-weight:500;">
                                    Muat Lebih Banyak
                                </button>
                                <span id="donasai-donors-loading"
                                    style="display:none; color:var(--donasai-text-muted); font-size:14px;">Memuat...</span>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>

        <!-- Right Column: Sidebar (35%) -->
        <div class="donasai-sidebar-col" style="max-width:100%;">
            <div style="position:sticky; top:20px;">

                <!-- Countdown Timer (Pro) -->
                <?php
                $donasai_donate_rest_url = esc_url(get_rest_url(null, 'donasai/v1/campaigns/'));

                $donasai_campaign_js = "
                    function wpdRegisterFundraiser(campaignId) {
                        var nonce = '" . esc_js(wp_create_nonce('wp_rest')) . "';
                        if (typeof wpdRegisterFundraiserHelper === 'function') {
                            wpdRegisterFundraiserHelper(campaignId, nonce);
                        }
                    }

                    function wpdLoadMoreDonors() {
                        var btn = document.getElementById('donasai-load-more-donors');
                        var loading = document.getElementById('donasai-donors-loading');
                        if (!btn) return;
                        var campaignId = btn.getAttribute('data-campaign');
                        var page = parseInt(btn.getAttribute('data-page')) + 1;

                        btn.style.display = 'none';
                        loading.style.display = 'inline-block';

                        fetch('" . esc_js($donasai_donate_rest_url) . "' + campaignId + '/donors?page=' + page + '&per_page=" . (int)$donasai_per_page_limit . "')
                            .then(response => response.json())
                            .then(data => {
                                if (data.success && data.donors.length > 0) {
                                    var donorList = document.getElementById('donasai-all-donors-list');
                                    data.donors.forEach(donor => {
                                        var name = donor.is_anonymous ? 'Hamba Allah' : donor.name;
                                        var time = donor.time_ago; // Assuming time_ago is returned by API
                                        var initial = name.charAt(0).toUpperCase();
                                        var amount = new Intl.NumberFormat('id-ID').format(donor.amount);
                                        var noteHtml = donor.note ? `<p style=\"margin:8px 0 0; font-size:14px; color:var(--donasai-text-body); background:var(--donasai-bg-tertiary); padding:10px; border-radius:8px;\">\"${donor.note}\"</p>` : '';

                                        donorList.innerHTML += `
                                            <div style=\"display:flex; gap:15px; margin-bottom:20px; border-bottom:1px solid var(--donasai-border); padding-bottom:20px;\">
                                                <div style=\"width:40px; height:40px; background:var(--donasai-bg-blue-accent); color:white; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold; flex-shrink:0;\">
                                                    ${initial}
                                                </div>
                                                <div>
                                                    <h4 style=\"margin:0; font-size:16px; font-weight:600; color:var(--donasai-text-main);\">
                                                        ${name}
                                                    </h4>
                                                    <div style=\"font-size:12px; color:var(--donasai-text-muted); margin-top:2px;\">
                                                        Berdonasi <span style=\"font-weight:600; color:var(--donasai-primary);\">Rp ${amount}</span> &bull;
                                                        ${time}
                                                    </div>
                                                    ${noteHtml}
                                                </div>
                                            </div>
                                        `;
                                    });
                                    btn.setAttribute('data-page', page);
                                    if (data.donors.length < " . (int)$donasai_per_page_limit . ") {
                                        btn.style.display = 'none';
                                    } else {
                                        btn.style.display = 'block';
                                    }
                                } else {
                                    btn.style.display = 'none';
                                }
                            })
                            .catch(error => {
                                console.error('Error loading more donors:', error);
                                btn.style.display = 'block';
                            })
                            .finally(() => {
                                loading.style.display = 'none';
                            });
                    }
                ";
                wp_add_inline_script('donasai-public-script', $donasai_campaign_js);

                if ($donasai_show_countdown) {
                    $donasai_deadline_str = get_post_meta($donasai_campaign_id, '_donasai_deadline', true);
                    if (!empty($donasai_deadline_str)) {
                        $donasai_deadline = strtotime($donasai_deadline_str);
                        $donasai_now = current_time('timestamp');
                        $donasai_diff = $donasai_deadline - $donasai_now;

                        if ($donasai_diff > 0) {
                            $donasai_days = floor($donasai_diff / (60 * 60 * 24));
                            $donasai_hours = floor(($donasai_diff - ($donasai_days * 60 * 60 * 24)) / (60 * 60));
                            $donasai_minutes = floor(($donasai_diff - ($donasai_days * 60 * 60 * 24) - ($donasai_hours * 60 * 60)) / 60);
                            ?>
                            <div class="donasai-card-style"
                                style="padding:15px; margin-bottom:20px; background:var(--donasai-bg-blue-light); border:1px solid rgba(37, 99, 235, 0.2); text-align:center;">
                                <div style="font-size:13px; color:var(--donasai-text-muted); margin-bottom:5px;">Sisa Waktu Campaign
                                </div>
                                    <div
                                    style="display:flex; justify-content:center; gap:10px; font-weight:700; color:var(--donasai-primary); font-size:18px;">
                                    <div>
                                        <span><?php echo esc_html($donasai_days); ?></span>
                                        <span
                                            style="display:block; font-size:10px; color:var(--donasai-text-muted); font-weight:400;">Hari</span>
                                    </div>
                                    <div style="color:var(--donasai-text-muted);">:</div>
                                    <div>
                                        <span><?php echo esc_html($donasai_hours); ?></span>
                                        <span
                                            style="display:block; font-size:10px; color:var(--donasai-text-muted); font-weight:400;">Jam</span>
                                    </div>
                                    <div style="color:var(--donasai-text-muted);">:</div>
                                    <div>
                                        <span><?php echo esc_html($donasai_minutes); ?></span>
                                        <span
                                            style="display:block; font-size:10px; color:var(--donasai-text-muted); font-weight:400;">Menit</span>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                }
                ?>

                <!-- Donation Card -->
                <div class="donasai-card-style" style="padding:24px; margin-bottom:20px;">

                    <div style="margin-bottom:20px;">
                        <div style="font-size:32px; font-weight:700; color:var(--donasai-primary); line-height:1;">
                            Rp <?php echo esc_html(number_format($donasai_progress['collected'], 0, ',', '.')); ?>
                        </div>
                        <div style="font-size:14px; color:var(--donasai-text-muted); margin-top:5px;">
                            terkumpul dari <span style="font-weight:500;">Rp
                                <?php echo esc_html(number_format($donasai_progress['target'], 0, ',', '.')); ?></span>
                        </div>
                    </div>

                    <div
                        style="background:var(--donasai-border); height:8px; border-radius:4px; margin-bottom:24px; overflow:hidden;">
                        <div class="donasai-progress-bar-fill"
                            style="background:var(--donasai-primary); height:100%; border-radius:4px; --donasai-progress-width:<?php echo esc_attr($donasai_progress['percentage']); ?>%; width:0;">
                        </div>
                    </div>

                    <!-- CTA Buttons -->
                    <?php
                    global $wp_rewrite;
                    if ($wp_rewrite->using_permalinks()) {
                        $donasai_donate_link = user_trailingslashit(get_permalink() . $donasai_payment_slug);
                    } else {
                        $donasai_donate_link = add_query_arg($donasai_payment_slug, '1', get_permalink());
                    }
                    ?>
                    <a href="<?php echo esc_url($donasai_donate_link); ?>"
                        style="display:block; max-width:100%; background:var(--donasai-btn); color:white; font-weight:700; text-align:center; padding:14px; border-radius:var(--donasai-radius); text-decoration:none; font-size:18px; margin-bottom:15px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                        Donasi Sekarang
                    </a>

                    <!-- Fundraiser Button -->
                    <?php if (function_exists('donasai_is_pro_active') && donasai_is_pro_active()): ?>
                        <?php if (is_user_logged_in()): ?>
                            <button onclick="wpdRegisterFundraiser(<?php echo intval($donasai_campaign_id); ?>)"
                                style="width:100%; background:var(--donasai-bg-card); color:var(--donasai-primary); border:1px solid var(--donasai-primary); font-weight:600; padding:10px; border-radius:var(--donasai-radius); cursor:pointer;">
                                Daftar sebagai Penggalang Dana
                            </button>
                        <?php else: ?>
                            <div style="text-align:center; font-size:13px; color:var(--donasai-text-muted);">
                                <a href="<?php echo esc_url(wp_login_url(get_permalink())); ?>"
                                    style="color:var(--donasai-primary);">Masuk</a> untuk mendaftar Penggalang Dana
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                </div>

                <!-- Fundraiser Profile -->
                <?php
                $donasai_org_settings = get_option('donasai_settings_organization', []);
                $donasai_fundraiser_name = !empty($donasai_org_settings['org_name']) ? $donasai_org_settings['org_name'] : get_the_author();
                $donasai_fundraiser_avatar = !empty($donasai_org_settings['org_logo']) ? $donasai_org_settings['org_logo'] : 'https://ui-avatars.com/api/?name=' . urlencode($donasai_fundraiser_name) . '&background=random';
                ?>
                <div class="donasai-card-style"
                    style="padding:20px; display:flex; align-items:center; gap:15px; margin-bottom:20px;">
                    <div
                        style="width:50px; height:50px; border-radius:50%; background:var(--donasai-bg-secondary); flex-shrink:0; overflow:hidden;">
                        <img src="<?php echo esc_url($donasai_fundraiser_avatar); ?>"
                            style="width:100%; height:100%; object-fit:cover;">
                    </div>
                    <div>
                        <div
                            style="font-size:12px; text-transform:uppercase; color:var(--donasai-text-muted); font-weight:600;">
                            Penggalang Dana</div>
                        <div style="font-weight:700; color:var(--donasai-text-main); display:flex; align-items:center;">
                            <?php echo esc_html($donasai_fundraiser_name); ?>
                            <svg style="width:14px; height:14px; color:#2563eb; margin-left:5px;" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Fundraiser Leaderboard (Pro) -->
                <?php if ($donasai_show_leaderboard):
                    $donasai_fundraiser_service = new DONASAI_Fundraiser_Service();
                    $donasai_leaderboard = $donasai_fundraiser_service->get_leaderboard($donasai_campaign_id, 5);
                    if (!empty($donasai_leaderboard)):
                ?>
                    <div class="donasai-card-style" style="overflow:hidden; margin-bottom:20px;">
                        <div style="padding:15px 20px; background:var(--donasai-bg-tertiary); border-bottom:1px solid var(--donasai-border); font-weight:700; color:var(--donasai-text-body); display:flex; align-items:center; gap:8px;">
                            <svg style="width:18px; height:18px; color:var(--donasai-primary);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Top Penggalang Dana
                        </div>
                        <div style="max-height:350px; overflow-y:auto;">
                            <?php 
                            $donasai_rank = 1;
                            foreach ($donasai_leaderboard as $donasai_fr):
                                $donasai_fr_name = esc_html($donasai_fr->display_name);
                                $donasai_fr_initial = strtoupper(substr($donasai_fr_name, 0, 1));
                                $donasai_fr_total = number_format($donasai_fr->total_donations, 0, ',', '.');
                                $donasai_rank_color = $donasai_rank === 1 ? '#f59e0b' : ($donasai_rank === 2 ? '#9ca3af' : ($donasai_rank === 3 ? '#cd7f32' : 'var(--donasai-text-muted)'));
                            ?>
                                <div style="padding:12px 20px; border-bottom:1px solid var(--donasai-border); display:flex; align-items:center; gap:12px;">
                                    <!-- Rank Badge -->
                                    <div style="width:24px; height:24px; background:<?php echo esc_attr($donasai_rank_color); ?>; color:white; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:12px; flex-shrink:0;">
                                        <?php echo esc_html($donasai_rank); ?>
                                    </div>
                                    <!-- Avatar -->
                                    <div style="width:36px; height:36px; background:var(--donasai-bg-blue-accent); color:white; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:14px; flex-shrink:0;">
                                        <?php echo esc_html($donasai_fr_initial); ?>
                                    </div>
                                    <!-- Name & Stats -->
                                    <div style="flex:1; min-width:0;">
                                        <div style="font-weight:600; color:var(--donasai-text-main); font-size:14px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                            <?php echo esc_html($donasai_fr_name); ?>
                                        </div>
                                        <div style="font-size:12px; color:var(--donasai-text-muted);">
                                            <span style="color:var(--donasai-primary); font-weight:600;">Rp <?php echo esc_html($donasai_fr_total); ?></span>
                                            &bull; <?php echo intval($donasai_fr->donation_count); ?> donasi
                                        </div>
                                    </div>
                                </div>
                            <?php 
                            $donasai_rank++;
                            endforeach; 
                            ?>
                        </div>
                    </div>
                <?php endif; endif; ?>

                <!-- Recent Donors Sidebar -->
                <?php if ($donasai_show_donor_list): ?>
                    <div class="donasai-card-style" style="overflow:hidden;">
                        <div
                            style="padding:15px 20px; background:var(--donasai-bg-tertiary); border-bottom:1px solid var(--donasai-border); font-weight:700; color:var(--donasai-text-body);">
                            Doa dan Dukungan (<?php echo esc_html($donasai_total_donors); ?>)
                        </div>
                        <div style="max-height:400px; overflow-y:auto;">
                            <?php if (empty($donasai_donors)): ?>
                                <div style="padding:20px; text-align:center; color:var(--donasai-text-muted); font-size:14px;">Belum
                                    ada donatur.
                                </div>
                            <?php else: ?>
                                <?php foreach ($donasai_donors as $donasai_donor):
                                    $donasai_name = $donasai_donor->is_anonymous ? 'Hamba Allah' : $donasai_donor->name;
                                    $donasai_time = human_time_diff(strtotime($donasai_donor->created_at), current_time('timestamp')) . ' yang lalu';
                                    ?>
                                    <div style="padding:15px 20px; border-bottom:1px solid var(--donasai-border);">
                                        <div style="font-weight:600; color:var(--donasai-text-main); font-size:14px;">
                                            <?php echo esc_html($donasai_name); ?>
                                        </div>
                                        <div style="font-size:12px; color:var(--donasai-text-muted); margin-bottom:5px;">Berdonasi <span
                                                style="color:var(--donasai-primary); font-weight:600;">Rp
                                                <?php echo number_format($donasai_donor->amount, 0, ',', '.'); ?></span> &bull;
                                            <?php echo esc_html($donasai_time); ?>
                                        </div>
                                        <?php if (!empty($donasai_donor->note)): ?>
                                            <div style="font-size:13px; color:var(--donasai-text-body); font-style:italic;">
                                                "<?php echo esc_html($donasai_donor->note); ?>"</div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <div style="padding:10px; text-align:center; border-top:1px solid var(--donasai-border);">
                                <button
                                    onclick="openWpdTab('donors'); window.scrollTo({top: document.querySelector('.donasai-tabs').offsetTop - 100, behavior: 'smooth'});"
                                    style="background:none; border:none; color:var(--donasai-primary); font-size:13px; font-weight:500; cursor:pointer;">Lihat
                                    Semua</button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>


    <!-- Fundraiser Modal (Hidden) -->
    <div id="donasai-fundraiser-modal"
        style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:99999; align-items:center; justify-content:center;">
        <div
            style="background:var(--donasai-bg-card); padding:25px; border-radius:10px; width:90%; max-width:400px; position:relative;">
            <button onclick="document.getElementById('donasai-fundraiser-modal').style.display='none'"
                style="position:absolute; top:10px; right:15px; border:none; background:none; font-size:20px; cursor:pointer; color:var(--donasai-text-main);">&times;</button>
            <h3 style="margin-top:0; color:var(--donasai-text-main);">Pendaftaran Berhasil!</h3>
            <p style="color:var(--donasai-text-body);">Bagikan tautan ini untuk mengajak orang lain berdonasi:</p>
            <input type="text" id="donasai-ref-link" readonly
                style="width:100%; padding:10px; background:var(--donasai-bg-secondary); border:1px solid var(--donasai-border); margin-bottom:15px; font-size:14px; color:var(--donasai-text-main);">
            <button class="button" onclick="wpdCopyRef()"
                style="width:100%; margin-bottom:10px; background:#2563eb; color:white; border:none; padding:10px; border-radius:4px;">Salin
                Tautan</button>
            <a id="donasai-wa-share" href="#" target="_blank" class="button"
                style="display:block; width:100%; text-align:center; background:#25D366; color:white; border:none; padding:10px; border-radius:4px; text-decoration:none;">Bagikan
                ke WhatsApp</a>
        </div>
    </div>

    <!-- Mobile Sticky CTA -->
    <?php
    $donasai_payment_slug = get_option('donasai_settings_general')['payment_slug'] ?? 'pay';
    $donasai_donate_link_sticky = $wp_rewrite->using_permalinks()
        ? user_trailingslashit(get_permalink() . $donasai_payment_slug)
        : add_query_arg($donasai_payment_slug, '1', get_permalink());
    ?>
    <div class="donasai-mobile-cta" style="background:var(--donasai-bg-card); border-top:1px solid var(--donasai-border);">
        <div
            style="width:100%; max-width:<?php echo esc_attr($donasai_container_width); ?>; margin:0 auto; display:flex; align-items:center; justify-content:space-between; padding:0 15px;">
            <div>
                <div style="font-size:12px; color:var(--donasai-text-muted); margin-bottom:2px;">Terkumpul</div>
                <div style="font-size:16px; font-weight:700; color:var(--donasai-primary);">Rp
                    <?php echo esc_html(number_format($donasai_progress['collected'], 0, ',', '.')); ?>
                </div>
            </div>
            <a href="<?php echo esc_url($donasai_donate_link_sticky); ?>"
                style="background:var(--donasai-btn); color:white; font-weight:700; padding:12px 24px; border-radius:8px; text-decoration:none; font-size:16px;">
                Donasi Sekarang
            </a>
        </div>
    </div>

    <!-- Scripts Moved to functions-frontend.php via wp_add_inline_script -->

    <!-- styles moved to head -->
</div>

<?php
get_footer();
?>