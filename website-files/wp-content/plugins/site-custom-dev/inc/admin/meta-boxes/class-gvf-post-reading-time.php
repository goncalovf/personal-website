<?php
/**
 * Post Reading Time
 *
 * How much time does reading the post require (in minutes)?
 */

defined( 'ABSPATH' ) || exit;

class GVF_Meta_Box_Post_Reading_Time {

    /**
     * Output the metabox.
     *
     * @param WP_Post $post
     */
    public static function output( $post ) {

        wp_nonce_field( 'gvf_save_data', 'gvf_meta_nonce' );

        $post_id = $post->ID;

        $extended_post = $post_id ? gvf()->get_post( $post_id ) : new GVF_Post();

        gvf_wp_text_input( array(
            'id'    => '_reading_time',
            'label' => __( "Reading time (in minutes)", 'gvf' ),
            'value' => $extended_post->get_reading_time(),
            'type'  => 'number'
        ));
    }

    /**
     * Save meta box data.
     *
     * @param int     $post_id
     */
    public static function save( $post_id ) {

        $extended_post = $post_id ? gvf()->get_post( $post_id ) : new GVF_Post();

        $errors = $extended_post->set_props(
            array(
                'reading_time' => isset( $_POST['_reading_time'] ) ? intval( wp_unslash( $_POST['_reading_time'] ) ) : null,   // Does not exist in DB if not set
            )
        );

        if ( is_wp_error( $errors ) ) {
            GVF_Admin_Meta_Boxes::add_error( $errors->get_error_message() );
        }

        $extended_post->save();
    }
}
