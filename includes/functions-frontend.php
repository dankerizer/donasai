<?php
/**
 * Frontend Functions & Template Loader
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Template Loader for Custom Post Types
 */
function wpd_template_loader( $template ) {
	if ( is_singular( 'wpd_campaign' ) ) {
		$plugin_template = WPD_PLUGIN_PATH . 'frontend/templates/campaign-single.php';
		if ( file_exists( $plugin_template ) ) {
			return $plugin_template;
		}
	}
	return $template;
}
add_filter( 'template_include', 'wpd_template_loader' );

/**
 * Template Loader for Custom Post Types
 */
function wpd_get_template_part( $slug, $name = null ) {
    $template = '';

    // Look in yourtheme/slug-name.php
    if ( $name ) {
        $template = locate_template( array( "wp-donasi/{$slug}-{$name}.php", "{$slug}-{$name}.php" ) );
    }

    // Get default slug-name.php
    if ( ! $template && $name && file_exists( WPD_PLUGIN_PATH . "frontend/templates/{$slug}-{$name}.php" ) ) {
        $template = WPD_PLUGIN_PATH . "frontend/templates/{$slug}-{$name}.php";
    }

    // If not found, look for slug.php
    if ( ! $template ) {
        $template = locate_template( array( "wp-donasi/{$slug}.php", "{$slug}.php" ) );
    }
    
    // Default slug.php
    if ( ! $template && file_exists( WPD_PLUGIN_PATH . "frontend/templates/{$slug}.php" ) ) {
        $template = WPD_PLUGIN_PATH . "frontend/templates/{$slug}.php";
    }

    if ( $template ) {
        load_template( $template, false );
    }
}

/**
 * Enqueue Frontend Assets
 */
function wpd_enqueue_frontend_assets() {
	if ( is_singular( 'wpd_campaign' ) ) {
		wp_enqueue_style( 'wp-donasi-frontend', WPD_PLUGIN_URL . 'frontend/assets/frontend.css', array(), WPD_VERSION );
		// Ensure Tailwind/Utility classes are handled. For MVP without build step, we might write raw CSS or use CDN for quick demo (not recommended for production plugin, but okay for MVP dev).
		// Better: write custom CSS in frontend.css that mimics Tailwind utilities needed.
	}
}
add_action( 'wp_enqueue_scripts', 'wpd_enqueue_frontend_assets' );

/**
 * Get Donation Form HTML
 */
function wpd_get_donation_form_html( $campaign_id ) {
	ob_start();
	include WPD_PLUGIN_PATH . 'frontend/templates/donation-form.php';
	return ob_get_clean();
}

/**
 * Donor Dashboard Shortcode [wpd_my_donations]
 */
function wpd_shortcode_my_donations() {
	ob_start();
	include WPD_PLUGIN_PATH . 'frontend/templates/donor-dashboard.php';
	return ob_get_clean();
}
add_shortcode( 'wpd_my_donations', 'wpd_shortcode_my_donations' );

/**
 * Output Marketing Pixels (Head)
 */
function wpd_head_pixels() {
    if ( is_singular( 'wpd_campaign' ) ) {
        $pixels = get_post_meta( get_the_ID(), '_wpd_pixel_ids', true );
        
        if ( ! empty( $pixels['fb'] ) ) {
            // Echo generic FB Pixel Code with ID
            echo "<!-- Facebook Pixel Code -->
            <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '" . esc_js( $pixels['fb'] ) . "');
            fbq('track', 'PageView');
            </script>
            <!-- End Facebook Pixel Code -->\n";
        }
        
        if ( ! empty( $pixels['tiktok'] ) ) {
            // Echo TikTok Pixel placeholder
            echo "<!-- TikTok Pixel Code -->\n";
            echo "<script>!function (w, d, t) { w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=[\"page\",\"track\",\"identify\",\"instances\",\"debug\",\"on\",\"off\",\"once\",\"ready\",\"alias\",\"group\",\"enableCookie\",\"disableCookie\"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq.methods[i],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(t,ttq.methods[n]);return t},ttq.load=function(e,n){var i=\"https://analytics.tiktok.com/i18n/pixel/events.js\";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement(\"script\");o.type=\"text/javascript\",o.async=!0,o.src=i+\"?sdkid=\"+e+\"&lib=\"+t;var a=document.getElementsByTagName(\"script\")[0];a.parentNode.insertBefore(o,a)}; ttq.load('" . esc_js( $pixels['tiktok'] ) . "'); ttq.page(); }(window, document, 'ttq');</script>\n";
        }
    }
}
add_action( 'wp_head', 'wpd_head_pixels' );

/**
 * Output WhatsApp Flying Button (Footer)
 */
function wpd_footer_whatsapp() {
    if ( is_singular( 'wpd_campaign' ) ) {
        $whatsapp = get_post_meta( get_the_ID(), '_wpd_whatsapp_settings', true );
        
        if ( ! empty( $whatsapp['number'] ) ) {
            $number = preg_replace( '/\D/', '', $whatsapp['number'] );
            $message = isset( $whatsapp['message'] ) ? rawurlencode( $whatsapp['message'] ) : '';
            $link = "https://wa.me/{$number}?text={$message}";
            ?>
            <a href="<?php echo esc_url( $link ); ?>" target="_blank" style="position:fixed; bottom:20px; right:20px; z-index:9999; background:#25D366; color:white; width:60px; height:60px; border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 10px rgba(0,0,0,0.3);">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
            </a>
            <?php
        }
    }
}
add_action( 'wp_footer', 'wpd_footer_whatsapp' );

/**
 * Handle Referral Tracking
 */
function wpd_track_referral() {
    if ( is_admin() ) return;
    
    if ( isset( $_GET['ref'] ) ) {
        $ref_code = sanitize_text_field( $_GET['ref'] );
        $service = new WPD_Fundraiser_Service();
        $fundraiser = $service->get_by_code( $ref_code );
        
        if ( $fundraiser ) {
            // Set Cookie for 30 days
            setcookie( 'wpd_ref', $fundraiser->id, time() + ( 30 * DAY_IN_SECONDS ), COOKIEPATH, COOKIE_DOMAIN );
            
            // Log visit if we are on a campaign page (or maybe log all?)
            // If the ref link points to a single campaign, log it.
            if ( is_singular( 'wpd_campaign' ) ) {
                $service->track_visit( $fundraiser->id, get_the_ID() );
            }
        }
    }
}
add_action( 'template_redirect', 'wpd_track_referral' );
