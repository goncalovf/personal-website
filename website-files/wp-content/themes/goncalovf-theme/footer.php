<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package goncalovf-theme
 */

?>

    <div class="pre-footer">
        <div class="pre-footer-wrapper">
            <h3><?php _e( "More", 'gvf' ) ?></h3>
            <div class="cols">
                <?php
                $pinned_ids = gvf_get_pinned_posts_ids();
                if ( ! empty( $pinned_ids ) ) : ?>
                <div class="col">
                    <h4><?php _e( "Pinned Posts", 'gvf' ) ?></h4>
                    <div>
                        <ul>
                            <?php foreach ( $pinned_ids as $id ) :
                                $extended_post = gvf()->get_post( $id ); ?>
                                <li><a href="<?php echo get_permalink( $extended_post->get_id() ) ?>"><?php echo $extended_post->get_title() ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>
                <div class="col">
                    <h4><?php _e( "Content Types", 'gvf' ) ?></h4>
                    <div>
                        <ul>
                            <?php
                            $categories = get_categories();

                            foreach ( $categories as $cat ) : ?>
                                <li><a href="<?php echo get_term_link( $cat ) ?>"><?php echo $cat->name ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <div class="col">
                    <h4><?php _e( "Tags", 'gvf' ) ?></h4>
                    <div>
                        <ul>
                            <?php
                            $tags = get_tags();

                            foreach ( $tags as $tag ) : ?>
                                <li><a href="<?php echo get_term_link( $tag ) ?>"><?php echo $tag->name ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer id="colophon" class="site-footer">
        <div class="footer-wrapper">
            <div class="footer-message">
                <p><?php _e( "A site for my experimentation and sharing.", 'gvf' ) ?></p>
                <p><?php _e( "Reach me at", 'gvf' ) ?> <a href="mailto:<?php echo get_bloginfo( 'admin_email' ) ?>"><?php echo get_bloginfo( 'admin_email' ) ?></a></p>
            </div>
            <div class="footer-site-title">
                <p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' )?></a></p>
            </div>
            <div class="social-links">
                <a class="social-link" href="https://twitter.com/goncalovf"><?php gvf_print_icon( 'social-twitter' ) ?></a>
                <a class="social-link" href="https://www.linkedin.com/in/goncalovf/"><?php gvf_print_icon( 'social-linkedin' ) ?></a>
                <a class="social-link" href="https://www.behance.net/goncalovf"><?php gvf_print_icon( 'social-behance' ) ?></a>
                <a class="social-link" href="<?php bloginfo('rss2_url') ?>"><?php gvf_print_icon( 'rss-feed' ) ?></a>
            </div>
        </div>
    </footer>
</div>

<?php wp_footer(); ?>

</body>
</html>
