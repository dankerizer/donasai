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
    $appearance = get_option( 'wpd_settings_appearance', array( 'brand_color' => '#059669', 'button_color' => '#ec4899' ) );
    
    $brand_color = $appearance['brand_color'] ?: '#059669';
    $btn_color   = $appearance['button_color'] ?: '#ec4899';
    ?>
    <style>
        :root {
            --wpd-primary: <?php echo esc_attr( $brand_color ); ?>;
            --wpd-btn: <?php echo esc_attr( $btn_color ); ?>;
            --wpd-btn-hover: <?php echo esc_attr( wpd_adjust_brightness( $btn_color, -10 ) ); ?>;
        }

        /* Helper to override specific components dynamically */
        .wpd-text-primary { color: var(--wpd-primary) !important; }
        .wpd-bg-primary { background-color: var(--wpd-primary) !important; }
        .wpd-btn-action { background-color: var(--wpd-btn) !important; color: white !important; }
        .wpd-btn-action:hover { background-color: var(--wpd-btn-hover) !important; }
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
