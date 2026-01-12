<?php
/**
 * Dynamic CSS Loader
 * Injects CSS variables for theme customization.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_head', 'wpd_inject_custom_styles' );

function wpd_inject_custom_styles() {
    $appearance = get_option( 'wpd_settings_appearance', array() );
    
    // Defaults
    $brand_color   = !empty($appearance['brand_color']) ? $appearance['brand_color'] : '#059669';
    $button_color  = !empty($appearance['button_color']) ? $appearance['button_color'] : '#ec4899';
    $border_radius = !empty($appearance['border_radius']) ? $appearance['border_radius'] : '12px';
    $font_family   = !empty($appearance['font_family']) ? $appearance['font_family'] : 'Inter';
    $font_size     = !empty($appearance['font_size']) ? $appearance['font_size'] : '16px';
    $dark_mode     = !empty($appearance['dark_mode']) && defined('WPD_PRO_VERSION');

    // Font Loading (if not 'Inter' which might be default or cached, but best to load if specific)
    $fonts_map = [
        'Inter'     => 'Inter:wght@400;500;600;700',
        'Roboto'    => 'Roboto:wght@400;500;700',
        'Open Sans' => 'Open+Sans:wght@400;600;700',
        'Poppins'   => 'Poppins:wght@400;500;600;700',
        'Lato'      => 'Lato:wght@400;700'
    ];
    
    if ( isset($fonts_map[$font_family]) ) {
        echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
        echo '<link href="https://fonts.googleapis.com/css2?family=' . esc_attr($fonts_map[$font_family]) . '&display=swap" rel="stylesheet">';
    }
    ?>
    <style id="wpd-global-styles">
        :root {
            /* Branding */
            --wpd-primary:   <?php echo esc_attr( $brand_color ); ?>;
            --wpd-btn:       <?php echo esc_attr( $button_color ); ?>;
            --wpd-btn-hover: <?php echo esc_attr( wpd_adjust_brightness( $button_color, -10 ) ); ?>;
            
            /* UI Config */
            --wpd-radius:    <?php echo esc_attr( $border_radius ); ?>;
            --wpd-font-family: '<?php echo esc_attr( $font_family ); ?>', sans-serif;
            --wpd-font-size: <?php echo esc_attr( $font_size ); ?>;

            /* Light Theme Defaults */
            --wpd-bg:        #f3f4f6;
            --wpd-card-bg:   #ffffff;
            --wpd-text-main: #1f2937; /* Gray-900 approx */
            --wpd-text-muted: #6b7280; /* Gray-500 */
            --wpd-border:    #e5e7eb; /* Gray-200 */
            --wpd-bg-soft:   #eff6ff; /* Blue-50 approx for active states */
            
            /* Component Specific Helpers */
            --wpd-input-bg:  #fdfdfd;
            --wpd-input-border: #d1d5db;
        }

        <?php if ( $dark_mode ): ?>
        /* Dark Mode Overrides - Applied to body.dark or standard dark preference media query if desired, 
           but usually controlled by class on body for standardized toggle */
        :root.dark, body.dark {
            --wpd-bg:        #111827; /* Gray-900 */
            --wpd-card-bg:   #1f2937; /* Gray-800 */
            --wpd-text-main: #f3f4f6; /* Gray-100 */
            --wpd-text-muted: #9ca3af; /* Gray-400 */
            --wpd-border:    #374151; /* Gray-700 */
            --wpd-bg-soft:   #374151; /* Gray-700 match for soft active */
            
            --wpd-input-bg:  #374151;
            --wpd-input-border: #4b5563;
        }
        <?php endif; ?>

        /* Global Helpers */
        body {
            font-family: var(--wpd-font-family);
            font-size: var(--wpd-font-size);
            color: var(--wpd-text-main);
        }

        /* Helper to override specific components dynamically */
        .wpd-text-primary { color: var(--wpd-primary) !important; }
        .wpd-bg-primary { background-color: var(--wpd-primary) !important; }
        .wpd-btn-action { background-color: var(--wpd-btn) !important; color: white !important; }
        .wpd-btn-action:hover { background-color: var(--wpd-btn-hover) !important; }
        
        /* Utility Classes for usage in templates */
        .wpd-rounded { border-radius: var(--wpd-radius) !important; }
    </style>
    <?php
}

/**
 * Utility to darken/lighten color for hover states
 */
function wpd_adjust_brightness($hex, $steps) {
    // Steps should be between -255 and 255. Negatives = darker, positives = lighter
    $steps = max(-255, min(255, $steps));

    // Normalize into a six character long hex string
    $hex = str_replace('#', '', $hex);
    if (strlen($hex) == 3) {
        $hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat(substr($hex, 1, 1), 2) . str_repeat(substr($hex, 2, 1), 2);
    }

    // Split into three parts: R, G and B
    $color_parts = str_split($hex, 2);
    $return = '#';

    foreach ($color_parts as $color) {
        $color   = hexdec($color); // Convert to decimal
        $color   = max(0, min(255, $color + $steps)); // Adjust color
        $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
    }

    return $return;
}
