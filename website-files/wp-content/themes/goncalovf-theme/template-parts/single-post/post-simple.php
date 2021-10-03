<?php
/**
 * Template part for displaying simple posts
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
        <div class="entry-meta">
            <span class="posted-on"><time class="entry-date published updated" datetime="<?php echo $extended_post->get_date_created()->date_i18n() ?>"><?php echo gvf_format_datetime( $extended_post->get_date_created() ) ?></time></span>
            <?php if( $extended_post->get_reading_time() ) : ?>
                <span> · </span>
                <span class="minutes-to-read"><?php echo $extended_post->get_reading_time_html() ?></span>
            <?php endif; ?>
        </div>
    </header>
    <div class="entry-content">
        <?php
        the_content();

        wp_link_pages(
            array(
                'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'gvf' ),
                'after'  => '</div>',
            )
        );
        ?>
    </div>

    <footer class="entry-footer">
        <div class="separator"><span>·</span><span>·</span><span>·</span></div>
        <?php
        $categories_list = get_the_category_list(", " );
        if ( $categories_list ) : ?>
        <div class="cats-container">
            <p class="cats"><?php printf( __( 'Read more %1$s.', 'gvf' ), $categories_list ); ?></p>
        </div>
        <?php
        endif;
        gvf_print_tags();
        ?>
    </footer>
</article>
