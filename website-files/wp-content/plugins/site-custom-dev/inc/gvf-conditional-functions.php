<?php

/**
 * Returns true when the current post is multi-page.
 *
 * @param  GVF_Post|int    $post     GVF Post object or post ID.
 * @return bool
 */
function gvf_is_multi_page_post_with_index( $post ) {

    if ( $post instanceof GVF_Post ) {
        $extended_post = $post;
    } else {
        $extended_post = gvf()->get_post( $post );
    }

    if ( $extended_post && $extended_post->get_type() === 'multi-page' && $extended_post->get_index() ) {
        return true;
    } else {
        return false;
    }
}
