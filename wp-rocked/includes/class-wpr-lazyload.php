<?php
/**
 * Provides lazy loading for images and iframes.
 */
class WPR_Lazyload {
    /**
     * Register hooks.
     *
     * @param array $settings Plugin settings.
     */
    public function setup_hooks( array $settings ) {
        if ( empty( $settings['enable_lazyload'] ) ) {
            return;
        }

        add_filter( 'the_content', array( $this, 'apply_lazyload' ), 20 );
    }

    /**
     * Swap img and iframe tags to use loading="lazy" and replace src.
     *
     * @param string $content
     * @return string
     */
    public function apply_lazyload( $content ) {
        if ( empty( $content ) ) {
            return $content;
        }

        $content = preg_replace_callback( '/<(img|iframe)([^>]+?)>/', array( $this, 'add_lazy_attribute' ), $content );

        return $content;
    }

    /**
     * Add lazy attributes to tag.
     *
     * @param array $matches
     * @return string
     */
    protected function add_lazy_attribute( $matches ) {
        $tag   = $matches[1];
        $attrs = $matches[2];

        if ( false !== stripos( $attrs, 'loading=' ) ) {
            return $matches[0];
        }

        $attrs = preg_replace( '/\\s+src(=(\"|\')(.*?)\\2)/i', ' data-src$1', $attrs );
        $attrs .= ' loading="lazy" class="wpr-lazyload"';

        return sprintf( '<%1$s%2$s>', $tag, $attrs );
    }
}
