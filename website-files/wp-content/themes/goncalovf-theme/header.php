<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package goncalovf-theme
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'gvf' ); ?></a>
	<header id="masthead" class="site-header">
		<div class="site-header-container">
			<?php the_custom_logo(); ?>
            <p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
            <?php if ( is_category() || is_tag() ) : ?>
            <p class="header-separator">|</p>
            <h1 class="archive-title"><?php single_term_title( '', true ) ?></h1>
            <?php endif; ?>
            <?php if ( is_single() && gvf_is_multi_page_post_with_index( get_the_id() ) ) : ?>
            <div class="h-menu-container">
                <?php gvf_print_icon( 'menu-hamburger' ) ?>
            </div>
            <?php endif; ?>
		</div>
	</header>
