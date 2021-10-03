<?php
/**
 * The sidebar containing the post's index
 */
?>

<aside class="site-sidebar">
    <section>
        <h3><?php _e( "Index", 'gvf' ) ?></h3>
        <div class="index-container">
            <?php
            global $page;

            gvf_print_index( get_permalink(), $page, $index );
            ?>
        </div>
    </section>
</aside>
