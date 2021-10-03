<?php
/**
 * Handle AJAX
 */

defined( 'ABSPATH' ) || exit;

class GVF_Ajax {

    public function __construct() {

        add_action( 'wp_ajax_get_multi_page_posts',         array( $this, 'get_multi_page_posts' ) );
    }

    /**
     * Get posts that are multi-page. Called in the post parts' editing page so that we can associate the part to the
     * multi-page parent.
     */
    public function get_multi_page_posts() {

        if ( ! isset( $_GET['q'] ) || empty( $_GET['q'] ) ) {

            echo json_encode( array() );
            wp_die();
        }

        $query = new WP_Query(
            array(
                's'                   => $_GET['q'],
                'post_type'           => 'post',
                'posts_per_page'      => -1,
                'ignore_sticky_posts' => 1,
                'order'               => 'ASC',
                'orderby'             => 'title',
                'meta_query'          => array(
                    array(
                        'key'   => '_is_multi_page',
                        'value' => 'yes'
                    )
                )
            ));

        if ( ! $query->have_posts() ) {

            echo json_encode( array() );
            wp_die();
        }

        $return = array();

        while ( $query->have_posts() ) : $query->the_post();

            // shorten the title a little
            $title = ( strlen( $query->post->post_title ) > 50 ) ? substr( $query->post->post_title, 0, 49 ) . '...' : $query->post->post_title;
            $return[] = array( $query->post->ID, $title . " (#" . $query->post->ID . ")"  );
        endwhile;

        echo json_encode( $return );
        wp_die();
    }
}

new GVF_Ajax();
