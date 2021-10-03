<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package goncalovf-theme
 */

get_header();
?>

	<main id="primary" class="site-main">

		<section class="error-404 not-found">
			<header class="page-header">
				<h1 class="page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'gvf' ); ?></h1>
			</header>
			<div class="page-content">
				<p><?php esc_html_e( "Nothing was found at this location, but I&rsquo;m pleased you came! Feel free to explore my blog :)", 'gvf' ); ?></p>
			</div>
		</section>

	</main>
    <div class="site-secondary">
        <?php get_sidebar(); ?>
        <?php get_footer( "archive" ); ?>
    </div>
