<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package goncalovf-theme
 */

get_header();

$extended_post = gvf()->get_post( get_the_ID() );

/**
 * Maybe output the nav for smaller screens if the post is multi-page.
 */
if ( gvf_is_multi_page_post_with_index( $extended_post ) ) {

    $index = gvf_parse_content_file( 'post-indexes/' . $extended_post->get_index() . '.json' );

    gvf_get_template( 'nav.php', array( 'index' => $index ) );
}

?>


	<main id="primary" class="site-main">

		<?php
		while ( have_posts() ) :
			the_post();

            /**
             * Generate structured data
             */
            gvf()->structured_data->generate_website_data();
            gvf()->structured_data->generate_post_data( $extended_post );

            if ( $extended_post ) {

                $type = $extended_post->get_type();

                gvf_get_template( 'post-' . $type . '.php', array( 'extended_post' => $extended_post ), 'template-parts/single-post' );
            }

			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile;
		?>
	</main>
    <?php if ( gvf_is_multi_page_post_with_index( $extended_post ) ) : ?>
    <div class="site-secondary">
        <?php gvf_get_template( 'sidebar-post-index.php', array( 'index' => $index ), '' ); ?>
    </div>
    <?php endif; ?>
    <?php if ( ! gvf_is_multi_page_post_with_index( $extended_post ) ) : ?>
    <div class="adjacent-posts">
        <div class="adjacent-posts-wrapper">
            <?php
            $adjacents_position = array( 'previous', 'next' );

            foreach( $adjacents_position as $position ) : ?>
                <div class="<?php echo $position ?> cols">
                    <?php
                    $adjacent = get_adjacent_post( false, '', $position === 'previous' );
                    if ( $adjacent ) : ?>
                        <div class="col icon">
                            <?php gvf_print_icon( $position === 'previous' ? 'chevron-left' : 'chevron-right' ); ?>
                        </div>
                        <div class="col post">
                            <?php
                            $extended_adjacent = gvf()->get_post( $adjacent );
                            if ( $extended_adjacent ) {
                                gvf_get_template( 'template-parts/loop/post-simplified.php', array( 'extended_post' => $extended_adjacent ) );
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif;
get_footer();
