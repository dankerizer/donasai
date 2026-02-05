<?php
/**
 * Frontend Functions & Template Loader
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Query Vars
 */
function donasai_register_query_vars($vars)
{
    $vars[] = 'donasai_receipt';
    $vars[] = 'donation_success';
    $vars[] = 'ref';
    $vars[] = 'updated';
    return $vars;
}
add_filter('query_vars', 'donasai_register_query_vars');

/**
 * Template Loader for Custom Post Types
 */
// Debugging Template Loader
function donasai_template_loader($template)
{

    // Check for Receipt
    $receipt_id = get_query_var('donasai_receipt');
    if ($receipt_id) {
        $donation_id = intval($receipt_id);
        $receipt_template = DONASAI_PLUGIN_PATH . 'frontend/templates/receipt.php';
        if (file_exists($receipt_template)) {
            return $receipt_template;
        }
    }

    if (is_singular('donasai_campaign')) {
        $payment_slug = get_option('donasai_settings_general')['payment_slug'] ?? 'pay';

        // Payment Page (?donate=1 OR /slug/pay)
        // Payment Page (?donate=1 OR /slug/pay)
        global $wp_query;
        if (get_query_var('donate') || isset($wp_query->query_vars[$payment_slug])) {
            $payment_template = DONASAI_PLUGIN_PATH . 'frontend/templates/donation-form.php';
            if (file_exists($payment_template)) {
                return $payment_template;
            }
        }

        // Thank You Page (?thank-you=ID OR /slug/thank-you/ID)
        $thankyou_slug = get_option('donasai_settings_general')['thankyou_slug'] ?? 'thank-you';

        // Use get_query_var to be robust
        if (get_query_var($thankyou_slug)) {
            $summary_template = DONASAI_PLUGIN_PATH . 'frontend/templates/donation-summary.php';
            if (file_exists($summary_template)) {
                return $summary_template;
            }
        }

        // Success Page (?donation_success=1)
        if (get_query_var('donation_success')) {
            $success_template = DONASAI_PLUGIN_PATH . 'frontend/templates/payment-success.php';
            if (file_exists($success_template)) {
                return $success_template;
            }
        }

        $plugin_template = DONASAI_PLUGIN_PATH . 'frontend/templates/campaign-single.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }
    return $template;
}
add_filter('template_include', 'donasai_template_loader');

/**
 * Disable Canonical Redirect for Thank You Endpoint
 * Fixes issue where /campaign/slug/thank-you/ID redirects back to /campaign/slug/
 */
function donasai_disable_canonical_redirect($redirect_url)
{
    $thankyou_slug = get_option('donasai_settings_general')['thankyou_slug'] ?? 'thank-you';

    if (get_query_var($thankyou_slug)) {
        return false;
    }
    return $redirect_url;
}
add_filter('redirect_canonical', 'donasai_disable_canonical_redirect');

/**
 * Template Loader for Custom Post Types
 */
function donasai_get_template_part($slug, $name = null)
{
    $template = '';

    // Look in yourtheme/slug-name.php
    if ($name) {
        $template = locate_template(array("donasai/{$slug}-{$name}.php", "{$slug}-{$name}.php"));
    }

    // Get default slug-name.php
    if (!$template && $name && file_exists(DONASAI_PLUGIN_PATH . "frontend/templates/{$slug}-{$name}.php")) {
        $template = DONASAI_PLUGIN_PATH . "frontend/templates/{$slug}-{$name}.php";
    }

    // If not found, look for slug.php
    if (!$template) {
        $template = locate_template(array("donasai/{$slug}.php", "{$slug}.php"));
    }

    // Default slug.php
    if (!$template && file_exists(DONASAI_PLUGIN_PATH . "frontend/templates/{$slug}.php")) {
        $template = DONASAI_PLUGIN_PATH . "frontend/templates/{$slug}.php";
    }

    if ($template) {
        load_template($template, false);
    }
}

/**
 * Enqueue Frontend Assets
 */
/**
 * Enqueue Frontend Assets
 */
function donasai_enqueue_frontend_assets()
{
    $should_load = false;

    // Check for Campaign Single
    if (is_singular('donasai_campaign')) {
        $should_load = true;
    }

    // Check for Confirmation Page
    $settings_gen = get_option('donasai_settings_general', []);
    $conf_page_id = isset($settings_gen['confirmation_page']) ? intval($settings_gen['confirmation_page']) : 0;
    if ($conf_page_id && is_page($conf_page_id)) {
        $should_load = true;
    }

    if ($should_load) {
        // Core Frontend Styles
        wp_enqueue_style('donasai-frontend', DONASAI_PLUGIN_URL . 'frontend/assets/frontend.css', array(), DONASAI_VERSION);

        // Inject Branding Variables
        $settings_app = get_option('donasai_settings_appearance', []);
        $primary_color = $settings_app['brand_color'] ?? '#059669';
        $button_color = $settings_app['button_color'] ?? '#ec4899';
        $radius = $settings_app['border_radius'] ?? '12px';
        
        // Helper to calc RGB
        $hex2rgb = function($hex) {
            $hex = str_replace("#", "", $hex);
            if(strlen($hex) == 3) {
                $r = hexdec(substr($hex,0,1).substr($hex,0,1));
                $g = hexdec(substr($hex,1,1).substr($hex,1,1));
                $b = hexdec(substr($hex,2,1).substr($hex,2,1));
            } else {
                $r = hexdec(substr($hex,0,2));
                $g = hexdec(substr($hex,2,2));
                $b = hexdec(substr($hex,4,2));
            }
            return "{$r}, {$g}, {$b}";
        };
        $primary_rgb = $hex2rgb($primary_color);

        $custom_css = "
            :root {
                --donasai-primary: " . sanitize_hex_color($primary_color) . ";
                --donasai-primary-rgb: " . esc_attr($primary_rgb) . ";
                --donasai-btn: " . sanitize_hex_color($button_color) . ";
                --donasai-radius: " . esc_attr($radius) . ";
                --donasai-font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif;
                --donasai-bg: #f3f4f6;
                --donasai-card-bg: #ffffff;
                --donasai-text-main: #1f2937;
                --donasai-text-muted: #6b7280;
                --donasai-input-bg: #ffffff;
                --donasai-input-border: #d1d5db;
                --donasai-border: #e5e7eb;
            }
            .donasai-dark {
                 --donasai-bg: #1f2937;
                 --donasai-card-bg: #111827;
                 --donasai-text-main: #f3f4f6;
                 --donasai-text-muted: #9ca3af;
                 --donasai-input-bg: #374151;
                 --donasai-input-border: #4b5563;
                 --donasai-border: #374151;
            }
        ";
        wp_add_inline_style('donasai-frontend', $custom_css);

        // Third-party fonts (like Google Fonts) are removed to comply with WordPress.org guidelines.
        // We use a modern system font stack that looks great on all platforms.

        // Campaign Specific Assets
        if (is_singular('donasai_campaign')) {
            wp_enqueue_style('donasai-campaign', DONASAI_PLUGIN_URL . 'frontend/assets/campaign.css', array('donasai-frontend'), DONASAI_VERSION);
            wp_enqueue_script('donasai-campaign', DONASAI_PLUGIN_URL . 'frontend/assets/campaign.js', array('jquery'), DONASAI_VERSION, true);
            
            // Localize Campaign Script
            wp_localize_script('donasai-campaign', 'donasaiSettings', array(
                'root' => esc_url(rest_url()),
                'nonce' => wp_create_nonce('wp_rest')
            ));

            // Check for Payment Page
            global $wp_query;
            $payment_slug = get_option('donasai_settings_general')['payment_slug'] ?? 'pay';
            if (get_query_var('donate') || isset($wp_query->query_vars[$payment_slug])) {
                wp_enqueue_style('donasai-payment', DONASAI_PLUGIN_URL . 'frontend/assets/payment.css', array('donasai-frontend'), DONASAI_VERSION);
                wp_enqueue_script('donasai-payment', DONASAI_PLUGIN_URL . 'frontend/assets/payment.js', array('jquery'), DONASAI_VERSION, true);

                // Localize Payment Script
                $midtrans = DONASAI_Gateway_Registry::get_gateway('midtrans');
                $snap_active = $midtrans && $midtrans->is_active();
                $client_key = $snap_active && method_exists($midtrans, 'get_client_key') ? $midtrans->get_client_key() : '';
                $is_prod = $snap_active && method_exists($midtrans, 'is_production') ? $midtrans->is_production() : false;
                $snap_url = $is_prod ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js';

                wp_localize_script('donasai-payment', 'donasai_payment_vars', array(
                    'is_midtrans_active' => $snap_active,
                    'snap_url' => esc_url($snap_url),
                    'client_key' => esc_attr($client_key),
                    'root' => esc_url(rest_url()),
                    'nonce' => wp_create_nonce('wp_rest')
                ));
            }

            // Add Admin Bar Adjustment
            if (is_admin_bar_showing()) {
                wp_add_inline_style('donasai-frontend', '.donasai-header-mobile { top: 32px; }');
            }

            // Move dynamic styles from campaign-single.php here
            $settings_app = get_option('donasai_settings_appearance', []);
            $font_family = $settings_app['font_family'] ?? 'Inter';
            $font_size = $settings_app['font_size'] ?? '16px';
            $layout_mode = $settings_app['campaign_layout'] ?? 'sidebar-right';
            $hero_style = $settings_app['hero_style'] ?? 'standard';
            
            $campaign_css = "
                :root {
                    --donasai-bg-main: #f3f4f6;
                    --donasai-bg-card: #ffffff;
                    --donasai-bg-secondary: #f3f4f6;
                    --donasai-bg-tertiary: #f9fafb;
                    --donasai-bg-blue-light: #eff6ff;
                    --donasai-bg-blue-accent: #e0e7ff;
                    --donasai-text-main: #111827;
                    --donasai-text-body: #374151;
                    --donasai-text-muted: #6b7280;
                    --donasai-text-inverse: #ffffff;
                    --donasai-border: #e5e7eb;
                    --donasai-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                }
                .donasai-dark {
                    --donasai-bg-main: #111827;
                    --donasai-bg-card: #1f2937;
                    --donasai-bg-secondary: #111827;
                    --donasai-bg-tertiary: #374151;
                    --donasai-bg-blue-light: #1e3a8a;
                    --donasai-bg-blue-accent: #3730a3;
                    --donasai-text-main: #f9fafb;
                    --donasai-text-body: #d1d5db;
                    --donasai-text-muted: #9ca3af;
                    --donasai-border: #374151;
                    --donasai-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.5);
                }
                .donasai-container {
                    font-family: var(--donasai-font-family);
                    font-size: " . esc_attr($font_size) . ";
                }
            ";

            if ($layout_mode === 'sidebar-left') {
                $campaign_css .= ".donasai-sidebar-col { order: -1; }";
            } elseif ($layout_mode === 'full-width') {
                $campaign_css .= ".donasai-main-col { flex: 0 0 100%; max-width: 100%; } .donasai-sidebar-col { display: none !important; } .donasai-mobile-cta { display: flex !important; }";
            }

            if ($hero_style === 'overlay') {
                $campaign_css .= "
                    .donasai-hero-overlay { position: relative; border-radius: var(--donasai-radius); overflow: hidden; margin-bottom: 25px; color: white; box-shadow: var(--donasai-shadow); }
                    .donasai-hero-overlay img { width: 100%; height: auto; display: block; }
                    .donasai-hero-content { position: absolute; bottom: 0; left: 0; right: 0; padding: 30px; background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent); }
                    .donasai-hero-content .donasai-heading { color: white !important; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5); }
                    .donasai-hero-content .donasai-subheading { color: rgba(255, 255, 255, 0.9) !important; }
                ";
            }

            wp_add_inline_style('donasai-campaign', $campaign_css);

            $per_page_limit = isset($settings_app['donor_per_page']) ? intval($settings_app['donor_per_page']) : 10;
            $donate_rest_url = esc_url(get_rest_url(null, 'donasai/v1/campaigns/'));

            $campaign_js = "
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

                    fetch('" . $donate_rest_url . "' + campaignId + '/donors?page=' + page + '&per_page=" . (int)$per_page_limit . "')
                        .then(response => response.json())
                        .then(data => {
                            loading.style.display = 'none';
                            if (data.data && data.data.length > 0) {
                                var list = document.getElementById('donasai-all-donors-list');
                                data.data.forEach(donor => {
                                    var html = `
                                     <div style='display:flex; gap:15px; margin-bottom:20px; border-bottom:1px solid var(--donasai-border); padding-bottom:20px;'>
                                        <div style='width:40px; height:40px; background:var(--donasai-bg-blue-accent); color:white; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold; flex-shrink:0;'>
                                            \${donor.initial}
                                        </div>
                                        <div>
                                            <h4 style='margin:0; font-size:16px; font-weight:600; color:var(--donasai-text-main);'>
                                                \${donor.name}
                                            </h4>
                                            <div style='font-size:12px; color:var(--donasai-text-muted); margin-top:2px;'>
                                                Berdonasi <span style='font-weight:600; color:var(--donasai-primary);'>Rp \${donor.amount_fmt}</span> &bull; \${donor.time_ago}
                                            </div>
                                            \${donor.note ? `<p style='margin:8px 0 0; font-size:14px; color:var(--donasai-text-body); background:var(--donasai-bg-tertiary); padding:10px; border-radius:8px;'>\"\${donor.note}\"</p>` : ''}
                                        </div>
                                    </div>
                                    `;
                                    list.insertAdjacentHTML('beforeend', html);
                                });

                                btn.setAttribute('data-page', page);
                                if (page < data.pagination.total_pages) {
                                    btn.style.display = 'inline-block';
                                }
                            }
                        })
                        .catch(err => {
                            console.error('Donors error:', err);
                            loading.style.display = 'none';
                            btn.style.display = 'inline-block';
                        });
                }

                function wpdCopyRef() {
                    var copyText = document.getElementById('donasai-ref-link');
                    if (!copyText) return;
                    copyText.select();
                    copyText.setSelectionRange(0, 99999);
                    document.execCommand('copy');

                    var x = document.getElementById('donasai-toast');
                    if (x) {
                        x.innerHTML = 'Link berhasil disalin!';
                        x.className = 'show';
                        setTimeout(function () { x.className = x.className.replace('show', ''); }, 3000);
                    }
                }

                function openWpdTab(tabName) {
                    var i, tabcontent, tablinks;
                    tabcontent = document.getElementsByClassName('donasai-tab-content');
                    for (i = 0; i < tabcontent.length; i++) {
                        tabcontent[i].style.display = 'none';
                    }
                    tablinks = document.getElementsByClassName('donasai-tab-btn');
                    for (i = 0; i < tablinks.length; i++) {
                        tablinks[i].className = tablinks[i].className.replace(' active', '');
                        tablinks[i].style.color = 'var(--donasai-text-muted)';
                        tablinks[i].style.borderBottomColor = 'transparent';
                    }
                    var selectedTab = document.getElementById('donasai-tab-' + tabName);
                    if (selectedTab) selectedTab.style.display = 'block';
                    var selectedBtn = document.getElementById('tab-btn-' + tabName);
                    if (selectedBtn) {
                        selectedBtn.className += ' active';
                        selectedBtn.style.color = 'var(--donasai-primary)';
                        selectedBtn.style.borderBottomColor = 'var(--donasai-primary)';
                    }
                }
            ";
            wp_add_inline_script('donasai-campaign', $campaign_js);
        }

        // Confirmation Page Assets
        if ($conf_page_id && is_page($conf_page_id)) {
            wp_enqueue_style('donasai-confirmation', DONASAI_PLUGIN_URL . 'frontend/assets/confirmation.css', array('donasai-frontend'), DONASAI_VERSION);
            wp_enqueue_script('donasai-confirmation', DONASAI_PLUGIN_URL . 'frontend/assets/confirmation.js', array('jquery'), DONASAI_VERSION, true);
        }
    }
}
add_action('wp_enqueue_scripts', 'donasai_enqueue_frontend_assets');

/**
 * Get Recent Donors
 */
function donasai_get_recent_donors($campaign_id, $limit = 10)
{
    global $wpdb;
    $table = $wpdb->prefix . 'donasai_donations';

    // Only completed donations
    $cache_key = 'donasai_recent_donors_' . $campaign_id . '_limit_' . $limit;
    $results = wp_cache_get($cache_key, 'donasai_donations');

    if (false === $results) {
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM %i WHERE campaign_id = %d AND status = 'complete' ORDER BY created_at DESC LIMIT %d",
            $table,
            $campaign_id,
            $limit
        ));
        wp_cache_set($cache_key, $results, 'donasai_donations', 300);
    }

    return $results;
}

/**
 * Get Total Donor Count
 */
function donasai_get_donor_count($campaign_id)
{
    global $wpdb;
    $table = $wpdb->prefix . 'donasai_donations';

    $cache_key = 'donasai_donor_count_' . $campaign_id;
    $count = wp_cache_get($cache_key, 'donasai_donations');

    if (false === $count) {
        $count = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(id) FROM %i WHERE campaign_id = %d AND status = 'complete'",
            $table,
            $campaign_id
        ));
        wp_cache_set($cache_key, $count, 'donasai_donations', 300);
    }

    return $count;
}

/**
 * Check if a gateway is active
 */
function donasai_is_gateway_active($gateway_id)
{
    if ($gateway_id === 'midtrans') {
        $settings = get_option('donasai_settings_midtrans', []);
        return !empty($settings['enabled']);
    }
    return false;
}

/**
 * Get Donation Form HTML
 */
function donasai_get_donation_form_html($campaign_id)
{
    ob_start();
    include DONASAI_PLUGIN_PATH . 'frontend/templates/donation-form.php';
    return ob_get_clean();
}

/**
 * Donor Dashboard Shortcode [donasai_my_donations]
 */
function donasai_shortcode_my_donations()
{
    wp_enqueue_style('donasai-frontend', DONASAI_PLUGIN_URL . 'frontend/assets/frontend.css', array(), DONASAI_VERSION);
    wp_enqueue_style('donasai-dashboard', DONASAI_PLUGIN_URL . 'frontend/assets/dashboard.css', array('donasai-frontend'), DONASAI_VERSION);
    wp_enqueue_script('donasai-dashboard', DONASAI_PLUGIN_URL . 'frontend/assets/dashboard.js', array(), DONASAI_VERSION, true);

    // Localize Dashboard Script
    wp_localize_script('donasai-dashboard', 'donasaiSettings', array(
        'root' => esc_url_raw(rest_url()),
        'nonce' => wp_create_nonce('wp_rest')
    ));

    ob_start();
    include DONASAI_PLUGIN_PATH . 'frontend/templates/donor-dashboard.php';
    return ob_get_clean();
}
add_shortcode('donasai_my_donations', 'donasai_shortcode_my_donations');



/**
 * Output WhatsApp Flying Button (Footer)
 */
function donasai_footer_whatsapp()
{
    if (is_singular('donasai_campaign')) {
        $whatsapp = get_post_meta(get_the_ID(), '_donasai_whatsapp_settings', true);

        if (!empty($whatsapp['number'])) {
            $number = preg_replace('/\D/', '', $whatsapp['number']);
            $message = isset($whatsapp['message']) ? rawurlencode($whatsapp['message']) : '';
            $link = "https://wa.me/{$number}?text={$message}";
            ?>
            <a href="<?php echo esc_url($link); ?>" target="_blank"
                style="position:fixed; bottom:20px; right:20px; z-index:9999; background:#25D366; color:white; width:60px; height:60px; border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 10px rgba(0,0,0,0.3);">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                </svg>
            </a>
            <?php
        }
    }
}
add_action('wp_footer', 'donasai_footer_whatsapp');

/**
 * Handle Referral Tracking
 */
function donasai_track_referral()
{
    if (is_admin())
        return;

    $ref_code = get_query_var('ref');
    if ($ref_code) {
        $ref_code = sanitize_text_field($ref_code);
        $service = new DONASAI_Fundraiser_Service();
        $fundraiser = $service->get_by_code($ref_code);

        if ($fundraiser) {
            // Set Cookie for 30 days
            setcookie('donasai_ref', $fundraiser->id, time() + (30 * DAY_IN_SECONDS), COOKIEPATH, COOKIE_DOMAIN);

            // Log visit if we are on a campaign page (or maybe log all?)
            // If the ref link points to a single campaign, log it.
            if (is_singular('donasai_campaign')) {
                $service->track_visit($fundraiser->id, get_the_ID());
            }
        }
    }
}
add_action('template_redirect', 'donasai_track_referral');

/**
 * Fundraiser Stats Shortcode [donasai_fundraiser_stats]
 */
function donasai_shortcode_fundraiser_stats()
{
    if (!is_user_logged_in()) {
        return '<p>' . __('Silakan login untuk melihat statistik fundraiser Anda.', 'donasai') . '</p>';
    }

    global $wpdb;
    $user_id = get_current_user_id();
    $table_fundraisers = $wpdb->prefix . 'donasai_fundraisers';

    // Get all campaigns user is fundraising for
    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT f.*, p.post_title 
         FROM %i f
         JOIN %i p ON f.campaign_id = p.ID
         WHERE f.user_id = %d
         ORDER BY f.created_at DESC",
        $table_fundraisers,
        $wpdb->posts,
        $user_id
    ));

    if (empty($results)) {
        return '<p>' . __('Anda belum mendaftar sebagai fundraiser untuk campaign apapun.', 'donasai') . '</p>';
    }

    ob_start();
    ?>
    <div class="donasai-fundraiser-dashboard">
        <h3><?php esc_attr_e('Statistik Kampanye Anda', 'donasai'); ?></h3>
        <table class="donasai-table" style="width:100%; border-collapse:collapse; margin-top:15px;">
            <thead>
                <tr style="background:#f9fafb; text-align:left; border-bottom:1px solid #ddd;">
                    <th style="padding:10px;">Campaign</th>
                    <th style="padding:10px;">Link Referral</th>
                    <th style="padding:10px;">Visit</th>
                    <th style="padding:10px;">Donasi</th>
                    <th style="padding:10px;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row):
                    // Get visit count (lazy query, ideally should act stored count or cached)
                    $table_logs = $wpdb->prefix . 'donasai_referral_logs';
                    $visit_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(id) FROM %i WHERE fundraiser_id = %d", $table_logs, $row->id));
                    $link = add_query_arg('ref', $row->referral_code, get_permalink($row->campaign_id));
                    ?>
                    <tr style="border-bottom:1px solid #eee;">
                        <td style="padding:10px;"><strong><?php echo esc_html($row->post_title); ?></strong></td>
                        <td style="padding:10px;"><input type="text" value="<?php echo esc_url($link); ?>" readonly
                                style="width:100%; font-size:12px; padding:5px; background:#f9f9f9; border:1px solid #ddd;"
                                onclick="this.select()"></td>
                        <td style="padding:10px;"><?php echo intval($visit_count); ?></td>
                        <td style="padding:10px;"><?php echo intval($row->donation_count); ?></td>
                        <td style="padding:10px; color:#059669; font-weight:bold;">Rp
                            <?php echo esc_html(number_format($row->total_donations, 0, ',', '.')); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('donasai_fundraiser_stats', 'donasai_shortcode_fundraiser_stats');

/**
 * User Profile Shortcode [donasai_profile]
 */
function donasai_shortcode_profile()
{
    if (!is_user_logged_in()) {
        return sprintf(
            '<p>%s</p>',
            sprintf(
                /* translators: %s: Login URL */
                __('Silakan <a href="%s">login</a> untuk mengedit profil Anda.', 'donasai'),
                esc_url(wp_login_url(get_permalink()))
            )
        );
    }

    // Handle Form Submission
    $error_message = '';
    
    if (isset($_POST['donasai_profile_submit']) && isset($_POST['donasai_profile_nonce'])) {
        if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['donasai_profile_nonce'])), 'donasai_profile_update')) {
            wp_die('Security check failed');
        }

        $user_id = get_current_user_id();
        $name = isset($_POST['display_name']) ? sanitize_text_field(wp_unslash($_POST['display_name'])) : '';
        $phone = isset($_POST['phone']) ? sanitize_text_field(wp_unslash($_POST['phone'])) : '';
        $pass1 = isset($_POST['pass1']) ? sanitize_text_field(wp_unslash($_POST['pass1'])) : '';
        $pass2 = isset($_POST['pass2']) ? sanitize_text_field(wp_unslash($_POST['pass2'])) : '';

        // Update User
        $user_data = array(
            'ID' => $user_id,
            'display_name' => $name,
        );

        if (!empty($pass1)) {
            if ($pass1 === $pass2) {
                $user_data['user_pass'] = $pass1;
            } else {
                $error_message = 'Password tidak cocok.';
            }
        }

        if (empty($error_message)) {
            wp_update_user($user_data);
            update_user_meta($user_id, '_donasai_phone', $phone);

            // Redirect to avoid resubmission
            wp_safe_redirect(add_query_arg('updated', 'true'));
            exit;
        }
    }

    // Pass error to template if exists
    if (!empty($error_message)) {
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">' . esc_html($error_message) . '</div>';
    }

    ob_start();
    include DONASAI_PLUGIN_PATH . 'frontend/templates/profile.php';
    return ob_get_clean();
}
add_shortcode('donasai_profile', 'donasai_shortcode_profile');

/**
 * Donation Confirmation Shortcode [donasai_confirmation_form]
 */
function donasai_shortcode_confirmation_form()
{
    $success = false;
    $error = '';
    $donation_id_val = '';
    $amount_val = '';

    // Pre-fill from URL
    if (isset($_GET['donation_id'])) {
        global $wpdb;
        $d_id = intval($_GET['donation_id']);
        $table_donations = $wpdb->prefix . 'donasai_donations';
        $donation_row = $wpdb->get_row($wpdb->prepare("SELECT amount FROM %i WHERE id = %d", $table_donations, $d_id));
        if ($donation_row) {
            $donation_id_val = $d_id;
            $amount_val = $donation_row->amount;
        }
    }

    if (isset($_POST['donasai_confirm_submit']) && isset($_POST['_wpnonce'])) {
        if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'donasai_confirm_payment')) {
            $error = 'Security check failed.';
        } else {
            global $wpdb;
            $donation_id = isset($_POST['donation_id']) ? intval($_POST['donation_id']) : 0;
            $amount_post = isset($_POST['amount']) ? sanitize_text_field(wp_unslash($_POST['amount'])) : '0';
            $amount = intval(str_replace('.', '', $amount_post)); // Remove dots

            // Verify Donation Exists
            if ($donation_id > 0) {
                $table_donations = $wpdb->prefix . 'donasai_donations';
                $donation = $wpdb->get_row($wpdb->prepare("SELECT * FROM %i WHERE id = %d", $table_donations, $donation_id));

                if (!$donation) {
                    $error = 'ID Donasi tidak ditemukan.';
                } else {
                    // Handle File Upload
                    if (!function_exists('wp_handle_upload')) {
                        require_once(ABSPATH . 'wp-admin/includes/file.php');
                    }

                    if ( isset( $_FILES['proof_file'] ) && !empty($_FILES['proof_file']['name']) ) {
                        $file = $_FILES['proof_file'];
                        $uploadedfile = array(
                            'name'     => isset($file['name']) ? sanitize_file_name( wp_unslash( (string) $file['name'] ) ) : '',
                            'type'     => isset($file['type']) ? sanitize_mime_type( wp_unslash( (string) $file['type'] ) ) : '',
                            'tmp_name' => isset($file['tmp_name']) ? sanitize_text_field( wp_unslash( (string) $file['tmp_name'] ) ) : '',
                            'error'    => isset($file['error']) ? intval( $file['error'] ) : 0,
                            'size'     => isset($file['size']) ? intval( $file['size'] ) : 0,
                        );
                    } else {
                        $uploadedfile = null;
                    }

                    if ($uploadedfile) {
                        $upload_overrides = array('test_form' => false);
                        $movefile = wp_handle_upload($uploadedfile, $upload_overrides);

                        if ($movefile && !isset($movefile['error'])) {
                            $proof_url = esc_url_raw($movefile['url']);

                            // Sanitize New Fields
                            $sender_bank = isset($_POST['sender_bank']) ? sanitize_text_field(wp_unslash($_POST['sender_bank'])) : '';
                            $sender_name = isset($_POST['sender_name']) ? sanitize_text_field(wp_unslash($_POST['sender_name'])) : '';

                            // Update Donation Meta
                            $metadata = json_decode($donation->metadata, true);
                            if (!is_array($metadata))
                                $metadata = array();

                            $metadata['proof_url'] = $proof_url;
                            $metadata['sender_bank'] = $sender_bank; // New Field
                            $metadata['sender_name'] = $sender_name; // New Field
                            $metadata['confirmed_at'] = current_time('mysql');
                            $metadata['confirmed_amount'] = $amount; // For verification

                            $wpdb->update(
                                $table_donations,
                                array(
                                    'metadata' => json_encode($metadata),
                                    'status' => 'processing' // Mark as Processing (Enum matches DB)
                                ),
                                array('id' => $donation_id)
                            );

                            $success = true;
                        } else {
                            $error = 'Gagal upload file: ' . $movefile['error'];
                        }
                    } else {
                        $error = 'Bukti transfer wajib diupload.';
                    }
                }
            } else {
                $error = 'ID Donasi tidak valid.';
            }
        }
    }


    ob_start();
    include DONASAI_PLUGIN_PATH . 'frontend/templates/confirmation-form.php';
    return ob_get_clean();
}
add_shortcode('donasai_confirmation_form', 'donasai_shortcode_confirmation_form');

/**
 * Campaign List Shortcode [donasai_campaign_list]
 */
function donasai_shortcode_campaign_list($atts)
{
    wp_enqueue_style('donasai-frontend', DONASAI_PLUGIN_URL . 'frontend/assets/frontend.css', array(), DONASAI_VERSION);
    wp_enqueue_style('donasai-campaign-list', DONASAI_PLUGIN_URL . 'frontend/assets/campaign-list.css', array('donasai-frontend'), DONASAI_VERSION);

    ob_start();
    include DONASAI_PLUGIN_PATH . 'frontend/templates/campaign-list.php';
    return ob_get_clean();
}
add_shortcode('donasai_campaign_list', 'donasai_shortcode_campaign_list');

function donasai_enqueue_receipt_assets() {
    $receipt_id = get_query_var('donasai_receipt');
    if ($receipt_id) {
        $donasai_donation_id = intval($receipt_id);
        
        // Dynamic Receipt Data for styling
        $donasai_appearance = get_option('donasai_settings_appearance', []);
        $donasai_brand_color = !empty($donasai_appearance['brand_color']) ? $donasai_appearance['brand_color'] : '#0ea5e9';
        
        // Helper to adjust brightness for shades
        if (!function_exists('donasai_get_shade')) {
            function donasai_get_shade($hex, $steps) {
                $steps = max(-255, min(255, $steps));
                $hex = str_replace('#', '', $hex);
                if (strlen($hex) == 3) {
                    $hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat(substr($hex, 1, 1), 2) . str_repeat(substr($hex, 2, 1), 2);
                }
                $color_parts = str_split($hex, 2);
                $return = '#';
                foreach ($color_parts as $color) {
                    $color   = hexdec($color);
                    $color   = max(0, min(255, $color + $steps));
                    $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT);
                }
                return $return;
            }
        }

        $donasai_brand_50  = donasai_get_shade($donasai_brand_color, 180);
        $donasai_brand_100 = donasai_get_shade($donasai_brand_color, 150);
        $donasai_brand_900 = donasai_get_shade($donasai_brand_color, -80);

        // Core Receipt Styles - Replacing Tailwind CDN with native CSS
        $donasai_receipt_css = "
            :root {
                --donasai-brand-50: " . esc_attr($donasai_brand_50) . ";
                --donasai-brand-100: " . esc_attr($donasai_brand_100) . ";
                --donasai-brand-500: " . esc_attr($donasai_brand_color) . ";
                --donasai-brand-900: " . esc_attr($donasai_brand_900) . ";
            }
            body { 
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif;
                margin: 0; padding: 0; background-color: #f3f4f6;
            }
            .print-container { background: white; max-width: 800px; margin: 40px auto; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden; position: relative; }
            .bg-brand-600 { background-color: var(--donasai-brand-500); }
            .hover\:bg-brand-700:hover { background-color: var(--donasai-brand-900); }
            .text-brand-600 { color: var(--donasai-brand-500); }
            .border-brand-500 { border-color: var(--donasai-brand-500); }
            
            /* Print Specifics */
            @media print {
                @page { margin: 0; size: auto; }
                body { background: white; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                .no-print { display: none !important; }
                .print-container { box-shadow: none !important; max-width: 100% !important; width: 100% !important; margin: 0 !important; padding: 0 !important; border: none !important; }
                .wave-decoration, .header-curve { z-index: -1; print-color-adjust: exact; -webkit-print-color-adjust: exact; }
                canvas { display: none !important; }
            }
            .header-curve {
                position: absolute; top: 0; left: 0; width: 100%; height: 120px;
                background: linear-gradient(135deg, var(--donasai-brand-50) 0%, #ffffff 100%);
                border-bottom-right-radius: 50% 20px; border-bottom-left-radius: 50% 20px; z-index: 0;
            }
            .wave-decoration {
                position: absolute; top: -50px; left: -50px; width: 200px; height: 200px;
                background: radial-gradient(circle, var(--donasai-brand-100) 0%, rgba(255, 255, 255, 0) 70%);
                border-radius: 50%; z-index: 0;
            }
        ";
        wp_add_inline_style('donasai-frontend', $donasai_receipt_css);

        // Confetti Script
        $donation_status = 'pending';
        global $wpdb;
        $table = $wpdb->prefix . 'donasai_donations';
        $status = $wpdb->get_var($wpdb->prepare("SELECT status FROM %i WHERE id = %d", $table, $donasai_donation_id));
        
        if ($status === 'complete') {
            $confetti_script = "
                (function () {
                    const canvas = document.getElementById('confetti');
                    if (!canvas) return;
                    const ctx = canvas.getContext('2d');
                    canvas.width = window.innerWidth;
                    canvas.height = window.innerHeight;
                    const pieces = [];
                    const colors = ['#0ea5e9', '#0284c7', '#38bdf8', '#f0f9ff'];
                    for (let i = 0; i < 80; i++) pieces.push({
                        x: Math.random() * canvas.width, y: Math.random() * canvas.height - canvas.height,
                        color: colors[Math.floor(Math.random() * colors.length)],
                        size: Math.random() * 6 + 4, speed: Math.random() * 4 + 2
                    });
                    function draw() {
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                        for (let i = 0; i < pieces.length; i++) {
                            const p = pieces[i];
                            ctx.fillStyle = p.color;
                            ctx.fillRect(p.x, p.y, p.size, p.size);
                            p.y += p.speed;
                            if (p.y > canvas.height) p.y = -20;
                        }
                        requestAnimationFrame(draw);
                    }
                    draw();
                    setTimeout(() => { canvas.style.display = 'none'; }, 4000);
                })();
            ";
            wp_add_inline_script('donasai-frontend', $confetti_script);
        }
    }
}
add_action('wp_enqueue_scripts', 'donasai_enqueue_receipt_assets');
