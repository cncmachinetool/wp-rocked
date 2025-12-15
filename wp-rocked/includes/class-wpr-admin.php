<?php
/**
 * Admin UI for WP Rocked.
 */
class WPR_Admin {
    /**
     * Register hooks.
     *
     * @param array            $settings        Current settings.
     * @param string           $option_key      Option key.
     * @param array            $defaults        Default settings.
     * @param WPR_Cache_Manager $cache_manager Cache manager instance.
     */
    public function setup_hooks( array $settings, $option_key, array $defaults, WPR_Cache_Manager $cache_manager ) {
        add_action( 'admin_menu', function () {
            add_options_page( __( 'WP Rocked', 'wp-rocked' ), __( 'WP Rocked', 'wp-rocked' ), 'manage_options', 'wp-rocked', array( $this, 'render_page' ) );
        } );

        add_action( 'admin_init', function () use ( $option_key, $defaults, $cache_manager ) {
            register_setting( 'wp-rocked', $option_key );

            add_settings_section( 'wpr_general', __( 'General', 'wp-rocked' ), '__return_false', 'wp-rocked' );

            $fields = array(
                'enable_cache'     => __( 'Enable page cache', 'wp-rocked' ),
                'enable_optimizer' => __( 'Optimize HTML output', 'wp-rocked' ),
                'enable_browser'   => __( 'Send browser cache headers', 'wp-rocked' ),
                'enable_lazyload'  => __( 'Lazy load images and iframes', 'wp-rocked' ),
            );

            foreach ( $fields as $key => $label ) {
                add_settings_field(
                    $key,
                    $label,
                    function () use ( $key, $option_key, $defaults ) {
                        $settings = get_option( $option_key, $defaults );
                        $checked  = ! empty( $settings[ $key ] ) ? 'checked' : '';
                        printf( '<label><input type="checkbox" name="%1$s[%2$s]" value="1" %3$s/> %4$s</label>', esc_attr( $option_key ), esc_attr( $key ), $checked, esc_html__( 'Enabled', 'wp-rocked' ) );
                    },
                    'wp-rocked',
                    'wpr_general'
                );
            }

            add_settings_section( 'wpr_tools', __( 'Tools', 'wp-rocked' ), '__return_false', 'wp-rocked' );
            add_settings_field( 'clear_cache', __( 'Cache', 'wp-rocked' ), function () use ( $cache_manager ) {
                submit_button( __( 'Clear Cache', 'wp-rocked' ), 'secondary', 'wpr_clear_cache' );
                if ( isset( $_POST['wpr_clear_cache'] ) ) {
                    check_admin_referer( 'wp-rocked-options' );
                    $cache_manager->purge_cache();
                    echo '<p>' . esc_html__( 'Cache purged.', 'wp-rocked' ) . '</p>';
                }
            }, 'wp-rocked', 'wpr_tools' );
        } );

        add_action( 'admin_notices', function () {
            if ( isset( $_GET['settings-updated'] ) ) {
                echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'WP Rocked settings saved.', 'wp-rocked' ) . '</p></div>';
            }
        } );

        add_action( 'send_headers', function () use ( $settings ) {
            if ( empty( $settings['enable_browser'] ) ) {
                return;
            }

            header( 'Cache-Control: public, max-age=604800' );
            header( 'Pragma: public' );
            header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', time() + WEEK_IN_SECONDS ) . ' GMT' );
        } );

        add_filter( 'plugin_action_links_' . plugin_basename( WP_ROCKED_PATH . 'wp-rocked.php' ), function ( $links ) {
            $links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=wp-rocked' ) ) . '">' . esc_html__( 'Settings', 'wp-rocked' ) . '</a>';
            return $links;
        } );
    }

    /**
     * Render settings page.
     */
    public function render_page() {
        echo '<div class="wrap">';
        echo '<h1>' . esc_html__( 'WP Rocked', 'wp-rocked' ) . '</h1>';
        echo '<form method="post" action="options.php">';
        settings_fields( 'wp-rocked' );
        do_settings_sections( 'wp-rocked' );
        wp_nonce_field( 'wp-rocked-options' );
        submit_button();
        echo '</form>';
        echo '</div>';
    }
}
