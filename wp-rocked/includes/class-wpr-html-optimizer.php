<?php
/**
 * Provides HTML optimization utilities.
 */
class WPR_HTML_Optimizer {
    /**
     * Setup optimization hooks.
     *
     * @param array $settings Plugin settings.
     */
    public function setup_hooks( array $settings ) {
        if ( empty( $settings['enable_optimizer'] ) ) {
            return;
        }

        add_filter( 'template_include', array( $this, 'start_buffering' ), PHP_INT_MAX );
    }

    /**
     * Start buffering during template rendering so we can minify output.
     */
    public function start_buffering( $template ) {
        ob_start( array( $this, 'optimize_html' ) );
        return $template;
    }

    /**
     * Perform very small HTML minification to remove whitespace and comments.
     *
     * @param string $html Rendered HTML.
     * @return string
     */
    public function optimize_html( $html ) {
        if ( empty( $html ) ) {
            return $html;
        }

        $search  = array( '/<!--(?!\s*\[if).*?-->/', '/\s{2,}/' );
        $replace = array( '', ' ' );

        $html = preg_replace( $search, $replace, $html );

        return trim( $html );
    }
}
