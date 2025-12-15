<?php
/**
 * Plugin Name: WP Rocked
 * Description: Performance optimization plugin offering page caching, HTML optimization, browser caching headers, and lazy loading for media.
 * Version: 1.0.0
 * Author: Your Name
 * License: GPL-2.0+
 * Text Domain: wp-rocked
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'WP_ROCKED_PATH' ) ) {
    define( 'WP_ROCKED_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'WP_ROCKED_URL' ) ) {
    define( 'WP_ROCKED_URL', plugin_dir_url( __FILE__ ) );
}

require_once WP_ROCKED_PATH . 'includes/class-wpr-loader.php';
require_once WP_ROCKED_PATH . 'includes/class-wpr-cache-manager.php';
require_once WP_ROCKED_PATH . 'includes/class-wpr-html-optimizer.php';
require_once WP_ROCKED_PATH . 'includes/class-wpr-lazyload.php';
require_once WP_ROCKED_PATH . 'includes/class-wpr-admin.php';

function wp_rocked() {
    static $plugin;

    if ( null === $plugin ) {
        $plugin = new WPR_Loader(
            new WPR_Cache_Manager(),
            new WPR_HTML_Optimizer(),
            new WPR_Lazyload(),
            new WPR_Admin()
        );
    }

    return $plugin;
}

wp_rocked();
