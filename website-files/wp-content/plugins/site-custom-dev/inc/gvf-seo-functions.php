<?php
/**
 * GVF SEO Functions
 *
 * Functions that improve SEO.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Add the gtag.js to every page.
 */
function gvf_add_gtag() { ?>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-111453149-3"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-111453149-3');
    </script>
<?php }
add_action( 'wp_head', 'gvf_add_gtag' );

/**
 * Add the gtag.js to every page.
 */
function gvf_add_description_meta() {

    if ( is_single() && $excerpt = get_the_excerpt( get_the_ID() ) ) : ?>
        <meta name="description" content="<?php echo $excerpt ?>">
    <?php endif;
}
add_action( 'wp_head', 'gvf_add_description_meta' );
