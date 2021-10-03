<?php

/**
 * Check if current page is post edit page
 *
 * @param  string   $post_type
 * @return boolean
 */
function is_edit_page( $post_type = '' ){

    //make sure we are on the backend
    if( !is_admin() ) return false;

    $screen = get_current_screen();

    $is_edit_page = $screen->base === 'post';

    if( !empty( $post_type ) ) {

        $is_post_type = $screen->post_type === $post_type;

        return $is_edit_page && $is_post_type;
    } else {

        return $is_edit_page;
    }
}
