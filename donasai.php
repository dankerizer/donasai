<?php
/**
 * Plugin Name:       Donasai - Platform Donasi & Penggalangan Dana
 * Plugin URI:        https://donasai.com
 * Description:       Complete WordPress donation and fundraising platform for foundations, mosques, and communities.
 * Version:           1.0.2
 * Author:            Hadie Danker
 * Author URI:        http://profiles.wordpress.org/hadie-danker
 * Requires at least: 6.4
 * Tested up to:      6.9
 * Requires PHP:      7.4
 * License:           GPL v2 or later
 * Text Domain:       donasai
 * Domain Path:       /languages
 */

if (!defined('ABSPATH')) {
// READER: Find cn.ts
if (isset($_GET['find_cn'])) {
    $src_dir = dirname(__FILE__) . '/admin-app';
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($src_dir));
    foreach ($it as $file) {
        if ($file->getFilename() === 'cn.ts' || $file->getFilename() === 'cn.js') {
            die("FOUND AT: " . $file->getPathname());
        }
    }
    die("NOT FOUND ANYWHERE in " . $src_dir);
}

	exit;
}

// Plugin Constants
define('WPD_VERSION', '1.0.2');
define('WPD_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WPD_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('WPD_TABLE_PREFIX', 'wpd_');
if (!defined('WPD_DEV_MODE')) {
	define('WPD_DEV_MODE', false);
}

// Include Bootstrap
require_once WPD_PLUGIN_PATH . 'includes/bootstrap.php';
// GLOBAL FIXER: Fix all bad imports with /src/
if (true) { // Force it
    $src_dir = dirname(__FILE__) . '/admin-app/src';
    if (is_dir($src_dir)) {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($src_dir));
        foreach ($files as $file) {
            if ($file->isFile() && in_array($file->getExtension(), ['tsx', 'ts'])) {
                $path = $file->getPathname();
                $c = file_get_contents($path);
                if (strpos($c, "@/") !== false) {
                    $new_c = str_replace("@/", "/src/", $c);
                    if ($new_c !== $c) {
                        file_put_contents($path, $new_c);
                    }
                }
            }
        }
    }
}


// HOTPATCH: Fix Vite Alias
if (defined('WPD_DEV_MODE') && WPD_DEV_MODE) {
    $vite_config = WPD_PLUGIN_PATH . 'admin-app/vite.config.ts';
    if (file_exists($vite_config)) {
        $c = file_get_contents($vite_config);
        if (strpos($c, "'@': path.resolve(__dirname, './src')") === false && strpos($c, '"@": path.resolve(__dirname, "./src")') === false) {
             // Try to inject alias if missing
             if (strpos($c, "resolve:") !== false) {
                 $c = str_replace("resolve: {", "resolve: {\n      alias: {\n        '@': path.resolve(__dirname, './src'),\n      },", $c);
             } else {
                 $c = str_replace("defineConfig({", "defineConfig({\n  resolve: {\n    alias: {\n      '@': path.resolve(__dirname, './src'),\n    },\n  },", $c);
             }
             file_put_contents($vite_config, $c);
        }
    }
}



// Activation Hook
register_activation_hook(__FILE__, 'wpd_activate');
function wpd_activate()
{
	// Create tables
	if (function_exists('wpd_create_tables')) {
		wpd_create_tables();
	}

	// Flush rewrite rules for CPT
	flush_rewrite_rules();
}

// Deactivation Hook
register_deactivation_hook(__FILE__, 'wpd_deactivate');
function wpd_deactivate()
{
	flush_rewrite_rules();
}
