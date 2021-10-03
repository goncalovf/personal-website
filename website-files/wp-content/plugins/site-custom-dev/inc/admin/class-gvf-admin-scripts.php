<?php
/**
 * Load admin scripts
 */

defined( 'ABSPATH' ) || exit;

class GVF_Admin_Scripts extends GVF_Scripts {

    /**
     * Hook in methods.
     */
    public function __construct() {

        add_action( 'admin_enqueue_scripts',        array( $this, 'load_scripts' ), 99999 );
        add_action( 'admin_print_scripts',          array( $this, 'localize_printed_scripts' ), 5 );
        add_action( 'admin_print_footer_scripts',   array( $this, 'localize_printed_scripts' ), 5 );
    }

    /**
     * Return asset URL.
     *
     * @param   string $path Assets path.
     * @return  string
     */
    private function get_asset_url( $path ) {
        return plugins_url( $path, GVF_PLUGIN_FILE );
    }

    /**
     * Return data for script handles.
     *
     * @param  string       $handle     Script handle the data will be attached to.
     * @return array|bool
     */
    protected function get_script_data( $handle ) {

        switch ( $handle ) {
            case 'post-part':

                $params = array(
                    'ajax_url' => admin_url( 'admin-ajax.php' ),
                    'i18n' => array(
                        'placeholder' => __( "Input a post title...", 'gvf' ),
                    ),
                );

                break;
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
            'selectWoo'   => array(
                'src'     => $this->get_asset_url('assets/js/selectWoo/selectWoo.full' . $suffix . '.js'),
                'deps'    => array( 'jquery' ),
                'version' => '1.0.0'
            ),
            'single-post'   => array(
                'src'     => $this->get_asset_url('assets/js/admin/edit-post' . $suffix . '.js'),
                'deps'    => array( 'jquery' ),
                'version' => '1.0.0'
            )
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
            'selectWoo'     => array(
                'src'     => $this->get_asset_url('assets/css/selectWoo/select2.css' ),
                'deps'    => array(),
                'version' => GVF_VERSION,
                'has_rtl' => false
            ),
            'single-post'   => array(
                'src'     => $this->get_asset_url('assets/css/admin/edit-post.css' ),
                'deps'    => array(),
                'version' => GVF_VERSION,
                'has_rtl' => false
            ),

            // Post editing blocks.
            'gutenberg-note' => array(

            )
        );

        foreach ( $register_styles as $name => $props ) {
            $this->register_style( $name, $props['src'], $props['deps'], $props['version'], 'all', $props['has_rtl'] );
        }
    }

    /**
     * Register/queue frontend scripts.
     *
     * Note: I could add the parameter $hook since admin_enqueue_scripts hook passes it.
     * I don't so that the declarations of load_scripts in CTP_Admin_Scripts is compatible
     * with CTP_Scripts::load_scripts()
     */
    public function load_scripts() {

        $this->register_scripts();
        $this->register_styles();

        if ( is_edit_page( 'post' ) ) {

            $this->enqueue_style('single-post' );       // Gutenberg style editing
            $this->enqueue_script('single-post' );

        }
    }
}

new GVF_Admin_Scripts();
