<?php
/**
 * Template part for listing posts
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class() ?>>
    <header class="entry-header">
        <h1 class="entry-title">
            <a href="<?php echo get_permalink() ?>">
                <?php the_title( '', '' ); ?>
            </a>
        </h1>
    </header>
    <footer class="entry-footer">
        <div class="row entry-meta">
            <span class="posted-on"><time class="entry-date published updated" datetime="<?php echo $extended_post->get_date_created()->date_i18n() ?>"><?php echo gvf_format_datetime( $extended_post->get_date_created() ) ?></time></span>
            <?php if( $extended_post->get_reading_time() ) : ?>
                <span> · </span>
                <span class="minutes-to-read"><?php echo $extended_post->get_reading_time_html() ?></span>
            <?php endif; ?>
            <span> · </span>
            <?php
            $categories_list = get_the_category_list(", " );
            if ( $categories_list ) {
                printf( '<span class="cat-links">' . esc_html_x( 'In %1$s', 'The indefinite article.', 'gvf' ) . '</span>', $categories_list );
            }
            ?>
        </div>
        <?php gvf_print_tags( 'row' ); ?>
    </footer>
</article>
