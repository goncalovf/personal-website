    <footer id="colophon" class="site-footer">
        <hr class="footer-hr">
        <p><?php _e( "A site for my experimentation and sharing.", 'gvf' ) ?></p>
        <p><?php _e( "Reach me at", 'gvf' ) ?> <a href="mailto:<?php echo get_bloginfo( 'admin_email' ) ?>"><?php echo get_bloginfo( 'admin_email' ) ?></a></p>
        <div class="social-links">
            <a class="social-link" href="https://twitter.com/goncalovf"><?php gvf_print_icon( 'social-twitter' ) ?></a>
            <a class="social-link" href="<?php bloginfo('rss2_url') ?>"><?php gvf_print_icon( 'rss-feed' ) ?></a>
        </div>
    </footer>
    </div><!-- .site-secondary -->
    <?php wp_footer(); ?>
</body>
</html>
