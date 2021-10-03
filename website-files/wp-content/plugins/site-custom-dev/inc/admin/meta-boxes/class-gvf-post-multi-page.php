<?php
/**
 * Post Multi-page
 *
 * Is the post multi-page?
 */

defined( 'ABSPATH' ) || exit;

class GVF_Meta_Box_Post_Multi_Page {

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
            'id'      => '_is_multi_page',
            'label'   => __( "Is multi-page", 'gvf' ),
            'value'   => $extended_post->get_is_multi_page(),
            'cbvalue' => true
        ));

        gvf_wp_text_input( array(
            'id'            => '_index',
            'label'         => __( "Index file (without extension)", 'gvf' ),
            'value'         => $extended_post->get_index(),
            'type'          => 'text',
            'placeholder'   => __( "E.g: personal-finances-guide-index", 'gvf' )
        ));
    }

    /**
     * Save meta box data.
     *
     * @param int     $post_id
     */
    public static function save( $post_id ) {

        $extended_post = $post_id ? gvf()->get_post( $post_id ) : new GVF_Post();

        // Only save index if the post is multi-page.
        $errors = $extended_post->set_props(
            array(
                'is_multi_page' => ! empty( $_POST['_is_multi_page'] ),
                'index'         => isset( $_POST['_is_multi_page'] ) && $_POST['_is_multi_page'] && isset( $_POST['_index'] ) ? gvf_clean( wp_unslash( $_POST['_index'] ) ) : ''
            )
        );

        if ( is_wp_error( $errors ) ) {
            GVF_Admin_Meta_Boxes::add_error( $errors->get_error_message() );
        }

        $extended_post->save();
    }
}
