<?php
/**
 * Post Pinned
 *
 * Is the post pinned?
 */

defined( 'ABSPATH' ) || exit;

class GVF_Meta_Box_Post_Pinned {

    /**
     * Output the metabox.
     *
     * @param WP_Post $post
     */
    public static function output( $post ) {

        wp_nonce_field( 'gvf_save_data', 'gvf_meta_nonce' );

        $post_id = $post->ID;

        $extended_post = $post_id ? gvf()->get_post( $post_id ) : new GVF_Post();

        gvf_wp_checkbox( array(
            'id'      => '_is_pinned',
            'label'   => __( "Is pinned", 'gvf' ),
            'value'   => $extended_post->get_is_pinned(),
            'cbvalue' => true
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
                'is_pinned' => ! empty( $_POST['_is_pinned'] )
            )
        );

        if ( is_wp_error( $errors ) ) {
            GVF_Admin_Meta_Boxes::add_error( $errors->get_error_message() );
        }

        $extended_post->save();
    }
}
