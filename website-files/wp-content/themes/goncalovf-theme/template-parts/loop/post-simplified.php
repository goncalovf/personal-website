<?php
/**
 * Template part for listing posts but with less information
 */
?>

<article>
    <header class="entry-header">
        <h4 class="entry-title">
            <a href="<?php echo get_permalink( $extended_post->get_id() ) ?>"><?php echo $extended_post->get_title() ?></a>
        </h4>
    </header>
    <footer class="entry-footer">
        <div class="row entry-meta">
            <span class="posted-on"><time class="entry-date published updated" datetime="<?php echo $extended_post->get_date_created()->date_i18n() ?>"><?php echo gvf_format_datetime( $extended_post->get_date_created() ) ?></time></span>
            <?php if( $extended_post->get_reading_time() ) : ?>
                <span> · </span>
                <span class="minutes-to-read"><?php echo $extended_post->get_reading_time_html() ?></span>
            <?php endif; ?>
        </div>
    </footer>
</article>
