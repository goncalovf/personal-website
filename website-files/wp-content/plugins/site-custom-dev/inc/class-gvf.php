<?php
/**
 * Site plugin setup
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main Plugin Class.
 *
 * @class GVF
 */
final class GVF {

    /**
     * Plugin version.
     *
     * @var string
     */
    public $version = '1.0.0';

    /**
     * The single instance of the class.
     *
     * @var GVF
     */
    protected static $_instance = null;

    /**
     * Post factory instance.
     *
     * @var GVF_Post_Factory
     */
    public $post_factory = null;

    /**
     * Structured data instance.
     *
     * @var GVF_Structured_Data
     */
    public $structured_data = null;

    /**
     * Main Site Plugin Instance.
     *
     * Ensures only one instance of the Site Plugin is loaded or can be loaded.
     *
     * @static
     * @see gvf()
     * @return GVF - Main instance.
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * WooCommerce Constructor.
     */
    public function __construct() {
        $this->define_constants();
        $this->includes();
        $this->init();
        $this->init_hooks();
    }

    /**
     * Hook into actions and filters.
     *
     * @since 2.3
     */
    private function init_hooks() {

    }

    /**
     * Define WC Constants.
     */
    private function define_constants() {

        $this->define( 'GVF_ABSPATH', dirname( GVF_PLUGIN_FILE ) . '/' );
        $this->define( 'GVF_VERSION', $this->version );
    }

    /**
     * Define constant if not already set.
     *
     * @param string      $name  Constant name.
     * @param string|bool $value Constant value.
     */
    private function define( $name, $value ) {
        if ( ! defined( $name ) ) {
            define( $name, $value );
        }
    }

    /**
     * What type of request is this?
     *
     * @param  string $type admin, ajax, cron or frontend.
     * @return bool
     */
    private function is_request( $type ) {
        switch ( $type ) {
            case 'admin':
                return is_admin();
            case 'ajax':
                return defined( 'DOING_AJAX' );
            case 'cron':
                return defined( 'DOING_CRON' );
            case 'frontend':
                return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
        }
    }

    /**
     * Include required core files used in admin and on the frontend.
     */
    public function includes() {

        /**
         * Interfaces.
         */
        include_once GVF_ABSPATH . 'inc/interfaces/class-gvf-object-data-store-interface.php';

        /**
         * Abstract classes.
         */
        include_once GVF_ABSPATH . 'inc/abstracts/abstract-gvf-data.php';
        include_once GVF_ABSPATH . 'inc/abstracts/abstract-gvf-post.php';
        include_once GVF_ABSPATH . 'inc/abstracts/abstract-gvf-scripts.php';

        /**
         * Core classes.
         */
        include_once GVF_ABSPATH . 'inc/gvf-core-functions.php';
        include_once GVF_ABSPATH . 'inc/gvf-formatting-functions.php'; // WC wraps this include in a function to make it pluggable by plugins and themes. @see include_template_functions()
        include_once GVF_ABSPATH . 'inc/gvf-conditional-functions.php';
        include_once GVF_ABSPATH . 'inc/gvf-logging-functions.php';
        include_once GVF_ABSPATH . 'inc/class-gvf-datetime.php';
        include_once GVF_ABSPATH . 'inc/class-gvf-ajax.php';
        include_once GVF_ABSPATH . 'inc/class-gvf-data-exception.php';
        include_once GVF_ABSPATH . 'inc/class-gvf-post-factory.php';
        include_once GVF_ABSPATH . 'inc/gvf-post-functions.php';
        include_once GVF_ABSPATH . 'inc/gvf-template-hooks.php';
        include_once GVF_ABSPATH . 'inc/gvf-template-functions.php';

        /**
         * Data stores - used to store and retrieve CRUD object data from the database.
         */
        include_once GVF_ABSPATH . 'inc/class-gvf-data-store.php';
        include_once GVF_ABSPATH . 'inc/data-stores/class-gvf-data-store-wp.php';
        include_once GVF_ABSPATH . 'inc/data-stores/class-gvf-post-data-store-cpt.php';
        include_once GVF_ABSPATH . 'inc/data-stores/class-gvf-post-simple-data-store-cpt.php';
        include_once GVF_ABSPATH . 'inc/data-stores/class-gvf-post-multi-page-data-store-cpt.php';

        /**
         * Object classes
         * Note: I don't know where WC includes these
         */
        include_once GVF_ABSPATH . 'inc/class-gvf-post-simple.php';
        include_once GVF_ABSPATH . 'inc/class-gvf-post-multi-page.php';

        /**
         * SEO
         */
        include_once GVF_ABSPATH . 'inc/class-gvf-structured-data.php';
        include_once GVF_ABSPATH . 'inc/class-gvf-structured-data-ogp.php';
        include_once GVF_ABSPATH . 'inc/gvf-seo-functions.php';


        if ( $this->is_request( 'admin' ) ) {

            /**
             * Core classes.
             */
            include_once GVF_ABSPATH . 'inc/admin/gvf-admin-functions.php';
            include_once GVF_ABSPATH . 'inc/admin/gvf-meta-box-functions.php';
            include_once GVF_ABSPATH . 'inc/admin/class-gvf-admin-meta-boxes.php';
            include_once GVF_ABSPATH . 'inc/admin/class-gvf-admin-scripts.php';

            /**
             * Admin Meta Boxes
             * Note: I don't know where WC includes these
             */
            include_once GVF_ABSPATH . 'inc/admin/meta-boxes/class-gvf-post-multi-page.php';
            include_once GVF_ABSPATH . 'inc/admin/meta-boxes/class-gvf-post-pinned.php';
            include_once GVF_ABSPATH . 'inc/admin/meta-boxes/class-gvf-post-reading-time.php';
        }
    }

    /**
     * Init.
     */
    public function init() {

        // Set up localisation.
        $this->load_plugin_textdomain();

        // Load class instances.
        $this->post_factory     = new GVF_Post_Factory();
        $this->structured_data  = new GVF_Structured_Data();
    }

    /**
     * Load plugin text domain
     *
     * @since 1.0.0
     */
    function load_plugin_textdomain() {

        load_plugin_textdomain( 'gvf', FALSE, basename( dirname( __FILE__ ) ) . '/lang/' );
    }

    /**
     * Get the plugin url.
     *
     * @return string
     */
    public function plugin_url() {
        return untrailingslashit( plugins_url( '/', GVF_PLUGIN_FILE ) );
    }

    /**
     * Get the plugin path.
     *
     * @return string
     */
    public function plugin_path() {
        return untrailingslashit( plugin_dir_path( GVF_PLUGIN_FILE ) );
    }

    /**
     * Get the template path.
     *
     * @return string
     */
    public function template_path() {
        return 'template-parts/';
    }

    /**
     * Get the content path.
     *
     * @return string
     */
    public function content_path() {
        return 'content/';
    }

    /**
     * Get Ajax URL.
     *
     * @return string
     */
    public function ajax_url() {
        return admin_url( 'admin-ajax.php', 'relative' );
    }

    /**
     * Main function for returning posts, uses the GVF_Post_Factory class.
     *
     * @param   mixed                   $the_post   Post object or post ID.
     * @return  GVF_Post|null|false
     */
    function get_post( $the_post = false ) {

        return $this->post_factory->get_post( $the_post );
    }
}
