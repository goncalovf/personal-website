<?php
/**
 * Load theme scripts
 */

defined( 'ABSPATH' ) || exit;

class GVF_Theme_Scripts extends GVF_Scripts {

    /**
     * Hook in methods.
     */
    public function __construct() {

        add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ), 99999 );
        add_action( 'wp_footer', array( $this, 'output_footer' ), 10 );
    }

    /**
     * Return asset URL.
     *
     * @param   string $path Assets path.
     * @return  string
     */
    private function get_asset_url( $path ) {

        return get_template_directory_uri() . "/" . $path;
    }

    /**
     * Return data for script handles.
     *
     * @param  string       $handle     Script handle the data will be attached to.
     * @return array|bool
     */
    protected function get_script_data( $handle ) {

        switch ( $handle ) {
            default:
                $params = false;
        }

        return $params;
    }

    /**
     * Register all scripts.
     */
    protected function register_scripts() {

        $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

        $register_scripts = array(
            'nav' => array(
                'src'     => $this->get_asset_url('/js/nav' . $suffix . '.js'),
                'deps'    => array( 'jquery' ),
                'version' => '1.0.0'
            ),
            'single-post-multi-page' => array(
                'src'     => $this->get_asset_url('/js/post-multi-page' . $suffix . '.js'),
                'deps'    => array( 'jquery' ),
                'version' => '1.0.0'
            ),
            'd3-v3' => array(
                'src'     => 'https://d3js.org/d3.v3.min.js',
                'deps'    => array(),
                'version' => '3.0.0'
            ),
            'd3-v5' => array(
                'src'     => 'https://d3js.org/d3.v5.min.js',
                'deps'    => array(),
                'version' => '5.0.0'
            ),            
            'post-courses-college-admission-grade' => array(
                'src'     => $this->get_asset_url('/js/posts/courses-college-admission-grade' . $suffix . '.js'),
                'deps'    => array( 'jquery' ),
                'version' => '1.0.0'
            ),
            'post-courses-preference-association' => array(
                'src'     => $this->get_asset_url('/js/posts/courses-preference-association' . $suffix . '.js'),
                'deps'    => array( 'jquery' ),
                'version' => '1.0.0'
            ),
        );
        foreach ( $register_scripts as $name => $props ) {
            $this->register_script( $name, $props['src'], $props['deps'], $props['version'] );
        }
    }


    /**
     * Register all styles.
     */
    protected function register_styles() {

        $register_styles = array(
            'archive-css'           => array(
                'src'     => $this->get_asset_url('sass/archive.css' ),
                'deps'    => array(),
                'version' => '1.3',
                'has_rtl' => false
            ),
            'single-post-simple'    => array(
                'src'     => $this->get_asset_url('sass/single-post-simple.css' ),
                'deps'    => array(),
                'version' => '1.4.1',
                'has_rtl' => false
            ),
            'single-post-multi-page' => array(
                'src'     => $this->get_asset_url('sass/single-post-multi-page.css' ),
                'deps'    => array(),
                'version' => '1.4.1',
                'has_rtl' => false
            ),
            'post-courses-college-admission-grade' => array(
                'src'     => $this->get_asset_url('sass/posts/courses-college-admission-grade.css' ),
                'deps'    => array(),
                'version' => '1.3.1',
                'has_rtl' => false
            ),
            'post-courses-preference-association' => array(
                'src'     => $this->get_asset_url('sass/posts/courses-preference-association.css' ),
                'deps'    => array(),
                'version' => '1.3.1',
                'has_rtl' => false
            ),
        );

        foreach ( $register_styles as $name => $props ) {
            $this->register_style( $name, $props['src'], $props['deps'], $props['version'], 'all', $props['has_rtl'] );
        }
    }

    /**
     * Register/queue frontend scripts.
     */
    public function load_scripts() {

        $this->register_scripts();
        $this->register_styles();

        $this->enqueue_script( 'nav' );

        if ( is_front_page() || is_archive() || is_404() ) {
            $this->enqueue_style('archive-css' );

        } elseif ( is_singular( 'post' ) ) {

            if ( gvf_is_multi_page_post_with_index( get_the_ID() ) ) {

                $this->enqueue_style('single-post-multi-page' );
                $this->enqueue_script('single-post-multi-page' );
            } else {
                $this->enqueue_style('single-post-simple' );
            }
        } 
        
        if ( is_single( 57 ) ) {
            $this->enqueue_style('post-courses-college-admission-grade' );
            $this->enqueue_script('post-courses-college-admission-grade' );
            $this->enqueue_script('d3-v3' );
        }
        if ( is_single( 62 ) ) {
            $this->enqueue_style('post-courses-preference-association' );
            $this->enqueue_script('post-courses-preference-association' );
            $this->enqueue_script('d3-v5' );
        }
    }

    public function output_footer() {
        echo '<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />';
        echo '<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>';   
    }
}

new GVF_Theme_Scripts();
