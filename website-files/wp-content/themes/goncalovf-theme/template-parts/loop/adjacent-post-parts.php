<?php
/**
 * Template part for listing adjacent post parts in the part single
 */
?>

<article>
    <header class="entry-header">
        <h4 class="entry-title">
            <a href="<?php echo get_permalink( $part_post_id ) ?>"><?php echo get_the_title( $part_post_id ) ?></a>
        </h4>
    </header>
</article>
