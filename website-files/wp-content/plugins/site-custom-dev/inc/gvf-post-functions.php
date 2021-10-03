<?php

/**
 * Get all pinned posts' IDs
 *
 * @return int[]    $pinned_posts_ids
 */
function gvf_get_pinned_posts_ids() {

    $query = new WP_Query(
        array(
            'post_type'             => 'post',
            'posts_per_page'        => -1,
            'ignore_sticky_posts'   => 1,
            'order'                 => 'DESC',
            'orderby'               => 'date',
            'meta_query'            => array(
                array (
                    'key'   => '_is_pinned',
                    'value' => 'yes'
                )
            ),
            'fields'                => 'ids'
        ));

    return $query->get_posts();
}
