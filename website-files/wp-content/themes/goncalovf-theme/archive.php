<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package goncalovf-theme
 */

get_header();

/**
 * Generate structured data
 */
gvf()->structured_data->generate_website_data();
?>
    <main id="primary" class="site-main">

        <?php if ( have_posts() ) :

            /* Start the Loop */
            while ( have_posts() ) :
                the_post();

                $extended_post = gvf()->get_post( get_the_ID() );

                if ( $extended_post ) {

                    gvf_get_template( 'post.php', array( 'extended_post' => $extended_post ), 'template-parts/loop' );
                }

            endwhile;

            the_posts_navigation();

        else :

            get_template_part( 'template-parts/content', 'none' );

        endif;
        ?>

    </main>

    <div class="site-secondary">
        <?php get_sidebar(); ?>
        <?php get_footer( "archive" ); ?>
