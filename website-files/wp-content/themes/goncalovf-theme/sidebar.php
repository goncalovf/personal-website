<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package goncalovf-theme
 */
?>

<aside class="site-sidebar">
    <?php if ( is_category() || is_tag() ) : ?>
    <section>
        <h3><?php echo single_term_title( '', true ) ?></h3>
        <?php echo term_description() ?>
    </section>
    <hr>
    <?php endif; ?>
    <?php
    $pinned_ids = gvf_get_pinned_posts_ids();
    if ( ! empty( $pinned_ids ) ) : ?>
    <section>
        <div class="pinned-post-container">
            <h3><?php _e( "Pinned Posts", 'gvf' ) ?></h3>
        </div>
        <div class="pinned-posts">
            <?php
            foreach( $pinned_ids as $id ) {

                $extended_post = gvf()->get_post( $id );

                if ( $extended_post ) {

                    gvf_get_template( 'post-simplified.php', array( 'extended_post' => $extended_post ), 'template-parts/loop' );
                }
            }
            ?>
        </div>
    </section>
    <hr>
    <?php endif; ?>
</aside>
