<?php
/**
 * GVF Meta Boxes
 *
 * Sets up the write panels.
 */

defined( 'ABSPATH' ) || exit;

class GVF_Admin_Meta_Boxes {

    /**
     * Was meta box already saved?
     *
     * @var boolean
     */
    private static $saved_meta_boxes = false;

    /**
     * Meta box error messages.
     *
     * @var array
     */
    public static $meta_box_errors = array();


    /**
     * Constructor.
     */
    public function __construct() {

        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );
        add_action( 'save_post',      array( $this, 'save_meta_boxes' ), 1, 2 );

        // Error handling (for showing errors from meta boxes on next page load).
        add_action( 'admin_notices',    array( $this, 'output_errors' ) );
        add_action( 'shutdown',         array( $this, 'save_errors' ) );
    }

    /**
     * Add an error message.
     *
     * @param string $text Error to add.
     */
    public static function add_error( $text ) {
        self::$meta_box_errors[] = $text;
    }

    /**
     * Save errors to an option.
     */
    public function save_errors() {
        update_option( 'gvf_meta_box_errors', self::$meta_box_errors );
    }

    /**
     * Show any stored error messages.
     */
    public function output_errors() {
        $errors = array_filter( (array) get_option( 'gvf_meta_box_errors' ) );

        if ( ! empty( $errors ) ) {

            echo '<div id="gvf_errors" class="error notice is-dismissible">';

            foreach ( $errors as $error ) {
                echo '<p>' . wp_kses_post( $error ) . '</p>';
            }

            echo '</div>';

            // Clear.
            delete_option( 'gvf_meta_box_errors' );
        }
    }

    /**
     * Add WC Meta boxes.
     */
    public function add_meta_boxes() {

        // Posts.
        add_meta_box( 'post_is_multi_page', __( 'Multi-Page', 'gvf' ), 'GVF_Meta_Box_Post_Multi_Page::output', 'post', 'side', 'default' );
        add_meta_box( 'post_is_pinned', __( 'Pin', 'gvf' ), 'GVF_Meta_Box_Post_Pinned::output', 'post', 'side', 'default' );
        add_meta_box( 'post_reading_time', __( 'Reading Time', 'gvf' ), 'GVF_Meta_Box_Post_Reading_Time::output', 'post', 'side', 'default' );
    }

    /**
     * Check if we're saving, then trigger an action based on the post type.
     *
     * @param  int      $post_id  Post ID.
     * @param  WP_Post  $post     Post object.
     */
    public function save_meta_boxes( $post_id, $post ) {
        $post_id = absint( $post_id );

        // $post_id and $post are required
        if ( empty( $post_id ) || empty( $post ) || self::$saved_meta_boxes ) {
            return;
        }

        // Dont' save meta boxes for revisions or autosaves
        if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
            return;
        }

        // Check the nonce.
        if ( empty( $_POST['gvf_meta_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['gvf_meta_nonce'] ), 'gvf_save_data' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            return;
        }

        // Check the post being saved == the $post_id to prevent triggering this call for other save_post events.
        if ( empty( $_POST['post_ID'] ) || absint( $_POST['post_ID'] ) !== $post_id ) {
            return;
        }

        // Check user has permission to edit.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // We need this save event to run once to avoid potential endless loops. This would have been perfect:
        // remove_action( current_filter(), __METHOD__ );
        // But cannot be used due to https://github.com/woocommerce/woocommerce/issues/6485
        // When that is patched in core we can use the above.
        self::$saved_meta_boxes = true;

        // Save meta boxes
        if ( $post->post_type === 'post' ) {
            GVF_Meta_Box_Post_Multi_Page::save( $post_id );
            GVF_Meta_Box_Post_Pinned::save( $post_id );
            GVF_Meta_Box_Post_Reading_Time::save( $post_id );

        }
    }
}

new GVF_Admin_Meta_Boxes();
