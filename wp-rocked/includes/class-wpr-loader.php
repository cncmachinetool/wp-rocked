<?php
/**
 * Main loader for WP Rocked.
 */
class WPR_Loader {
    /**
     * Cache manager instance.
     *
     * @var WPR_Cache_Manager
     */
    protected $cache_manager;

    /**
     * HTML optimizer instance.
     *
     * @var WPR_HTML_Optimizer
     */
    protected $html_optimizer;

    /**
     * Lazyload instance.
     *
     * @var WPR_Lazyload
     */
    protected $lazyload;

    /**
     * Admin instance.
     *
     * @var WPR_Admin
     */
    protected $admin;

    /**
     * Option key used by the plugin.
     *
     * @var string
     */
    protected $option_key = 'wp_rocked_settings';

    public function __construct( WPR_Cache_Manager $cache_manager, WPR_HTML_Optimizer $html_optimizer, WPR_Lazyload $lazyload, WPR_Admin $admin ) {
        $this->cache_manager  = $cache_manager;
        $this->html_optimizer = $html_optimizer;
        $this->lazyload       = $lazyload;
        $this->admin          = $admin;

        add_action( 'plugins_loaded', array( $this, 'bootstrap' ) );
    }

    /**
     * Set up plugin pieces.
     */
    public function bootstrap() {
        $settings = get_option( $this->option_key, $this->get_default_settings() );
        $settings = wp_parse_args( $settings, $this->get_default_settings() );

        $this->cache_manager->setup_hooks( $settings );
        $this->html_optimizer->setup_hooks( $settings );
        $this->lazyload->setup_hooks( $settings );
        $this->admin->setup_hooks( $settings, $this->option_key, $this->get_default_settings(), $this->cache_manager );
    }

    /**
     * Default settings for the plugin.
     *
     * @return array
     */
    protected function get_default_settings() {
        return array(
            'enable_cache'     => true,
            'enable_optimizer' => true,
            'enable_browser'   => true,
            'enable_lazyload'  => true,
        );
    }
}
