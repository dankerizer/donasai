<?php
// Mock WordPress Environment for Testing
define('ABSPATH', '/temp/');
define('WPD_PRO_VERSION', true);

if (!function_exists('get_option')) {
    function get_option($name, $default = false) {
        if ($name === 'wpd_settings_appearance') {
            return [
                'brand_color' => '#10b981',
                'button_color' => '#f59e0b',
                'border_radius' => '8px',
                'font_family' => 'Inter',
                'font_size' => '16px',
                'dark_mode' => true
            ];
        }
        return $default;
    }
}

if (!function_exists('esc_attr')) {
    function esc_attr($s) { return htmlspecialchars($s, ENT_QUOTES); }
}

if (!function_exists('esc_html')) {
    function esc_html($s) { return htmlspecialchars($s, ENT_QUOTES); }
}

if (!function_exists('wp_enqueue_style')) {
    function wp_enqueue_style($handle, $src) { echo "Enqueued: $handle\n"; }
}

if (!function_exists('add_action')) {
    function add_action($hook, $function_to_add) {}
}

// Include the file to test
require_once __DIR__ . '/../../includes/frontend/css-loader.php';

// Capture Output
ob_start();
wpd_inject_custom_styles();
$output = ob_get_clean();

// Assertions
$errors = [];
$normalized_output = preg_replace('/\s+/', ' ', $output);

if (strpos($normalized_output, '--wpd-primary: #10b981') === false) {
    $errors[] = "Missing or incorrect --wpd-primary";
}

if (strpos($normalized_output, '--wpd-btn: #f59e0b') === false) {
    $errors[] = "Missing or incorrect --wpd-btn";
}

if (strpos($normalized_output, '--wpd-radius: 8px') === false) {
    $errors[] = "Missing or incorrect --wpd-radius";
}

if (strpos($normalized_output, 'body.dark') === false) {
    $errors[] = "Missing dark mode block";
}

if (empty($errors)) {
    echo "PASS: CSS Loader outputs correct variables.\n";
    exit(0);
} else {
    echo "FAIL:\n";
    foreach ($errors as $err) {
        echo "- $err\n";
    }
    echo "\nOutput Preview:\n" . substr($output, 0, 500) . "...\n";
    exit(1);
}
