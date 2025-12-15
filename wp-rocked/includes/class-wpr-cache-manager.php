<?php
/**
 * Handles page caching and cache invalidation.
 */
class WPR_Cache_Manager {
    const CACHE_GROUP = 'wp-rocked';

    /**
     * Base directory for the cache.
     *
     * @var string
     */
    protected $cache_dir;

    public function __construct() {
        $this->cache_dir = WP_CONTENT_DIR . '/cache/wp-rocked';
    }

    /**
     * Registers hooks for caching.
     *
     * @param array $settings Plugin settings.
     */
    public function setup_hooks( array $settings ) {
        if ( ! empty( $settings['enable_cache'] ) ) {
            add_action( 'init', array( $this, 'maybe_serve_cache' ), 0 );
            add_action( 'template_redirect', array( $this, 'start_buffering' ), 0 );
        }

        add_action( 'save_post', array( $this, 'purge_cache' ) );
        add_action( 'deleted_post', array( $this, 'purge_cache' ) );
        add_action( 'trashed_post', array( $this, 'purge_cache' ) );
        add_action( 'switch_theme', array( $this, 'purge_cache' ) );
    }

    /**
     * Deliver cached content when available.
     */
    public function maybe_serve_cache() {
        if ( ! $this->is_cacheable_request() ) {
            return;
        }

        $cache_file = $this->get_cache_path();

        if ( is_readable( $cache_file ) ) {
            readfile( $cache_file );
            exit;
        }
    }

    /**
     * Start output buffering to store the generated page.
     */
    public function start_buffering() {
        if ( ! $this->is_cacheable_request() ) {
            return;
        }

        ob_start( array( $this, 'cache_buffer' ) );
    }

    /**
     * Save buffered HTML to disk.
     *
     * @param string $html Output HTML.
     * @return string
     */
    public function cache_buffer( $html ) {
        if ( empty( $html ) ) {
            return $html;
        }

        if ( ! wp_mkdir_p( $this->cache_dir ) ) {
            return $html;
        }

        $cache_file = $this->get_cache_path();
        file_put_contents( $cache_file, $html );

        return $html;
    }

    /**
     * Purge cache directory.
     */
    public function purge_cache() {
        if ( ! is_dir( $this->cache_dir ) ) {
            return;
        }

        $files = glob( trailingslashit( $this->cache_dir ) . '*.html' );

        if ( empty( $files ) ) {
            return;
        }

        foreach ( $files as $file ) {
            if ( is_file( $file ) ) {
                unlink( $file );
            }
        }
    }

    /**
     * Whether the current request can be cached.
     *
     * @return bool
     */
    protected function is_cacheable_request() {
        if ( is_admin() || is_user_logged_in() ) {
            return false;
        }

        if ( is_feed() || is_trackback() || is_preview() || is_robots() ) {
            return false;
        }

        if ( is_search() || is_404() ) {
            return false;
        }

        if ( ! is_main_query() || ( ! is_singular() && ! is_home() && ! is_front_page() ) ) {
            return false;
        }

        return 'GET' === $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Cache file path for the current request.
     *
     * @return string
     */
    protected function get_cache_path() {
        $key = md5( home_url( add_query_arg( array(), $_SERVER['REQUEST_URI'] ) ) );

        return trailingslashit( $this->cache_dir ) . $key . '.html';
    }
}
