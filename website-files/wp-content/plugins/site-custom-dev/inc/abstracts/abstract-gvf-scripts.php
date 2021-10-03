<?php
/**
 * Abstract for scripts handling
 */

defined( 'ABSPATH' ) || exit;

abstract class GVF_Scripts {


    /**
     * Contains an array of script handles registered by this plugin.
     *
     * @var array
     */
    protected $scripts = array();


    /**
     * Contains an array of script handles registered by this plugin.
     *
     * @var array
     */
    protected $styles = array();


    /**
     * Contains an array of script handles localized by this plugin.
     *
     * @var array
     */
    protected $wp_localize_scripts = array();


    /**
     * Register a script for use.
     *
     * @uses   wp_register_script()
     *
     * @param  string       $handle         Name of the script. Should be unique.
     * @param  string       $path           Full URL of the script, or path of the script relative to the WordPress root directory.
     * @param  string[]     $deps           An array of registered script handles this script depends on.
     * @param  string       $version        String specifying script version number, if it has one, which is added to the URL as a query string for cache busting purposes. If version is set to false, a version number is automatically added equal to current installed WordPress version. If set to null, no version is added.
     * @param  boolean      $in_footer      Whether to enqueue the script before </body> instead of in the <head>. Default 'false'.
     */
    protected function register_script( $handle, $path, $deps = array( 'jquery' ), $version = GVF_VERSION, $in_footer = true ) {
        $this->scripts[] = $handle;
        wp_register_script( $handle, $path, $deps, $version, $in_footer );
    }


    /**
     * Register a style for use.
     *
     * @uses   wp_register_style()
     *
     * @param  string       $handle         Name of the stylesheet. Should be unique.
     * @param  string       $path           Full URL of the stylesheet, or path of the stylesheet relative to the WordPress root directory.
     * @param  string[]     $deps           An array of registered stylesheet handles this stylesheet depends on.
     * @param  string       $version        String specifying stylesheet version number, if it has one, which is added to the URL as a query string for cache busting purposes. If version is set to false, a version number is automatically added equal to current installed WordPress version. If set to null, no version is added.
     * @param  string       $media          The media for which this stylesheet has been defined. Accepts media types like 'all', 'print' and 'screen', or media queries like '(orientation: portrait)' and '(max-width: 640px)'.
     * @param  boolean      $has_rtl        If has RTL version to load too.
     */
    protected function register_style( $handle, $path, $deps = array(), $version = GVF_VERSION, $media = 'all', $has_rtl = false ) {
        $this->styles[] = $handle;
        wp_register_style( $handle, $path, $deps, $version, $media );

        if ( $has_rtl ) {
            wp_style_add_data( $handle, 'rtl', 'replace' );
        }
    }


    /**
     * Register and enqueue a script for use.
     *
     * @uses   wp_enqueue_script()
     *
     * @param  string       $handle         Name of the script. Should be unique.
     * @param  string       $path           Full URL of the script, or path of the script relative to the WordPress root directory.
     * @param  string[]     $deps           An array of registered script handles this script depends on.
     * @param  string       $version        String specifying script version number, if it has one, which is added to the URL as a query string for cache busting purposes. If version is set to false, a version number is automatically added equal to current installed WordPress version. If set to null, no version is added.
     * @param  boolean      $in_footer      Whether to enqueue the script before </body> instead of in the <head>. Default 'false'.
     */
    protected function enqueue_script( $handle, $path = '', $deps = array( 'jquery' ), $version = GVF_VERSION, $in_footer = true ) {
        if ( ! in_array( $handle, $this->scripts, true ) && $path ) {
            $this->register_script( $handle, $path, $deps, $version, $in_footer );
        }
        wp_enqueue_script( $handle );
    }


    /**
     * Register and enqueue a styles for use.
     *
     * @uses   wp_enqueue_style()
     * @param  string   $handle  Name of the stylesheet. Should be unique.
     * @param  string   $path    Full URL of the stylesheet, or path of the stylesheet relative to the WordPress root directory.
     * @param  string[] $deps    An array of registered stylesheet handles this stylesheet depends on.
     * @param  string   $version String specifying stylesheet version number, if it has one, which is added to the URL as a query string for cache busting purposes. If version is set to false, a version number is automatically added equal to current installed WordPress version. If set to null, no version is added.
     * @param  string   $media   The media for which this stylesheet has been defined. Accepts media types like 'all', 'print' and 'screen', or media queries like '(orientation: portrait)' and '(max-width: 640px)'.
     * @param  boolean  $has_rtl If has RTL version to load too.
     */
    protected function enqueue_style( $handle, $path = '', $deps = array(), $version = GVF_VERSION, $media = 'all', $has_rtl = false ) {
        if ( ! in_array( $handle, $this->styles, true ) && $path ) {
            $this->register_style( $handle, $path, $deps, $version, $media, $has_rtl );
        }
        wp_enqueue_style( $handle );
    }


    /**
     * Localize a script once.
     *
     * @param   string      $handle         Script handle the data will be attached to.
     */
    protected function localize_script( $handle ) {
        if ( ! in_array( $handle, $this->wp_localize_scripts, true ) && wp_script_is( $handle ) ) {
            $data = $this->get_script_data( $handle );

            if ( ! $data ) return;

            $name                        = str_replace( '-', '_', $handle ) . '_params';
            $this->wp_localize_scripts[] = $handle;
            wp_localize_script( $handle, $name, $data );
        }
    }


    /**
     * Return data for script handles.
     *
     * @param  string       $handle         Script handle the data will be attached to.
     * @return array|bool   $params
     */
    protected function get_script_data( $handle ) {
        return false;
    }


    /**
     * Register all scripts.
     */
    protected function register_scripts() {}


    /**
     * Register all styles.
     */
    protected function register_styles() {}


    /**
     * Register/queue frontend scripts.
     */
    public function load_scripts() {}


    /**
     * Localize scripts only when enqueued.
     */
    public function localize_printed_scripts() {
        foreach ( $this->scripts as $handle ) {
            $this->localize_script( $handle );
        }
    }
}
