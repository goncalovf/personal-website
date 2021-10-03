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
        </div>
    </header>
    <div class="entry-content">
        <?php the_content(); ?>
    </div>
    <div class="adjacent-posts">
        <div class="adjacent-posts-wrapper">
            <?php

            global $page, $numpages;

            $adjacents_position = array(
                'previous'  => array(
                    'page'      => $page - 1,
                    'link_name' => __( "Previous", 'gvf' )
                ),
                'next'      => array(
                    'page'      => $page + 1,
                    'link_name' => __( "Next", 'gvf' )
                ),
            );

            foreach ( $adjacents_position as $position => $attr ) : ?>
                <div class="<?php echo $position ?> cols">
                <?php if ( ( $position === 'previous' && $attr['page'] > 0 ) || ( $position === 'next' && $attr['page'] <= $numpages )  ) : ?>

                        <div class="col icon">
                            <?php gvf_print_icon( $position === 'previous' ? 'chevron-left' : 'chevron-right' ); ?>
                        </div>
                        <div class="col post">
                            <article>
                                <header class="entry-header">
                                    <h4 class="entry-title">
                                        <?php echo _wp_link_page( $attr['page'] ) . $attr['link_name'] ?></a>
                                    </h4>
                                </header>
                            </article>
                        </div>
                <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</article>
