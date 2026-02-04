<?php
/**
 * Dynamic CSS Loader
 * Injects CSS variables for theme customization.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_head', 'donasai_inject_custom_styles' );

function donasai_inject_custom_styles() {
    $appearance = get_option( 'donasai_settings_appearance', array() );
    
    // Defaults
    $brand_color   = !empty($appearance['brand_color']) ? $appearance['brand_color'] : '#059669';
    $button_color  = !empty($appearance['button_color']) ? $appearance['button_color'] : '#ec4899';
    $border_radius = !empty($appearance['border_radius']) ? $appearance['border_radius'] : '12px';
    $font_family   = !empty($appearance['font_family']) ? $appearance['font_family'] : 'Inter';
    $font_size     = !empty($appearance['font_size']) ? $appearance['font_size'] : '16px';
    $dark_mode     = !empty($appearance['dark_mode']) && defined('DONASAI_PRO_VERSION');

    $font_family_stack = "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif";
    ?>
    <style id="donasai-global-styles">
        /* Third-party fonts (like Google Fonts) are removed to comply with WordPress.org guidelines. */
        :root {
            /* Branding */
            --donasai-primary:   <?php echo esc_attr( $brand_color ); ?>;
            --donasai-btn:       <?php echo esc_attr( $button_color ); ?>;
            --donasai-btn-hover: <?php echo esc_attr( donasai_adjust_brightness( $button_color, -10 ) ); ?>;
            
            /* UI Config */
            --donasai-radius:    <?php echo esc_attr( $border_radius ); ?>;
            --donasai-font-family: <?php echo esc_attr( $font_family_stack ); ?>;
            --donasai-font-size: <?php echo esc_attr( $font_size ); ?>;

            /* Light Theme Defaults */
            --donasai-bg:        #f3f4f6;
            --donasai-card-bg:   #ffffff;
            --donasai-text-main: #1f2937; /* Gray-900 approx */
            --donasai-text-muted: #6b7280; /* Gray-500 */
            --donasai-border:    #e5e7eb; /* Gray-200 */
            --donasai-bg-soft:   #eff6ff; /* Blue-50 approx for active states */
            
            /* Component Specific Helpers */
            --donasai-input-bg:  #fdfdfd;
            --donasai-input-border: #d1d5db;
        }

        <?php if ( $dark_mode ): ?>
        /* Dark Mode Overrides - Applied to body.dark or standard dark preference media query if desired, 
           but usually controlled by class on body for standardized toggle */
        :root.dark, body.dark {
            --donasai-bg:        #111827; /* Gray-900 */
            --donasai-card-bg:   #1f2937; /* Gray-800 */
            --donasai-text-main: #f3f4f6; /* Gray-100 */
            --donasai-text-muted: #9ca3af; /* Gray-400 */
            --donasai-border:    #374151; /* Gray-700 */
            --donasai-bg-soft:   #374151; /* Gray-700 match for soft active */
            
            --donasai-input-bg:  #374151;
            --donasai-input-border: #4b5563;
        }
        <?php endif; ?>

        /* Global Helpers */
        body {
            font-family: var(--donasai-font-family);
            font-size: var(--donasai-font-size);
            color: var(--donasai-text-main);
        }

        /* Helper to override specific components dynamically */
        .donasai-text-primary { color: var(--donasai-primary) !important; }
        .donasai-bg-primary { background-color: var(--donasai-primary) !important; }
        .donasai-btn-action { background-color: var(--donasai-btn) !important; color: white !important; }
        .donasai-btn-action:hover { background-color: var(--donasai-btn-hover) !important; }
        
        /* Utility Classes for usage in templates */
        .donasai-rounded { border-radius: var(--donasai-radius) !important; }
    </style>
    <?php
}

/**
 * Utility to darken/lighten color for hover states
 */
function donasai_adjust_brightness($hex, $steps) {
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
