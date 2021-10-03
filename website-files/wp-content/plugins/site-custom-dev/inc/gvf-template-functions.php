<?php

/**
 * Print an icon.
 * Icon has to be a svg file.
 *
 * @param $icon
 */
function gvf_print_icon( $icon, $args = array() ) {

    gvf_get_template( 'icon-'.$icon.'.php', $args, 'images/icons' );
}

/**
 * Print the post's tags.
 *
 * @param string $wrapper_class
 */
function gvf_print_tags( $wrapper_class = '' ) {

    $tags_list = get_the_tag_list( '<span class="tag">', '</span><span class="tag">', '</span>' );

    if ( $tags_list ) : ?>
    <div class="tags <?php esc_attr_e( $wrapper_class ) ?>">
        <?php echo $tags_list ?>
    </div>
    <?php endif;
}

/**
 * Parse json stored in a file.
 *
 * @param   string      $file_name
 * @param   string      $file_path
 * @return  mixed|false $json
 */
function gvf_parse_content_file( $file_name, $file_path = '' ) {

    $file = gvf_locate_content_file( $file_name, $file_path );

    $string = file_get_contents( $file );

    if ( $string === false ) {
        return false;
    }

    $index = json_decode($string, true);
    if ( $index === null ) {
        return false;
    }

    return $index;
}


function gvf_index_item_should_be_open( $page, $item ) {

    if ( (int) $item['Page'] === $page ) return true;

    if ( ! isset( $item['Children'] ) ) return false;

    $return = false;

    foreach ( $item['Children'] as $child ) {

        if ( $child['Page'] === $page ) {

            $return = true;
            break;

        } else {

            $return = gvf_index_item_should_be_open( $page, $child );

            if ( $return ) {
                break;
            }
        }
    }

    return $return;
}

/**
 * Recursive function to print every index item inside an item.
 *
 * @param string    $permalink      The post's permalink, without the page nor any heading ID.
 * @param int       $page           The current post page.
 * @param array     $item           A index item / heading.
 */
function gvf_get_index_items_recursive( $permalink, $page, $item ) {

    if ( ! isset( $item['Heading'] ) || ! isset( $item['Page'] ) ) return;

    $link  = $permalink;
    $link .= $item['Page'] !== 1         ? $item['Page'] . '/' : '';
    $link .= isset( $item['HeadingID'] ) ? '#' . $item['HeadingID'] : '';

    $opened = gvf_index_item_should_be_open( $page, $item );
    ?>
    <li>
        <?php if ( isset( $item['Children'] ) ) :
        gvf_print_icon( 'chevron-right', $opened ? array( 'class' => 'active' ) : array() );
        endif; ?>
        <a href="<?php echo $link ?>">
            <div class="<?php echo (int) $item['Page'] === $page ? 'active' : '' ?>"><?php echo $item['Heading'] ?></div>
        </a>
        <?php if ( isset( $item['Children'] ) ) : ?>
        <ul class="<?php echo $opened ? '' : 'closed' ?>">
            <?php foreach ( $item['Children'] as $child ) :
                gvf_get_index_items_recursive( $permalink, $page, $child );
            endforeach; ?>
        </ul>
        <?php endif; ?>
    </li>
    <?php
}

/**
 * Output the index for multi-page posts.
 *
 * @see gvf_parse_content_file()
 *
 * @param string    $permalink      The post's permalink, without the page nor any heading ID.
 * @param int       $page           The current post page.
 * @param array     $index          The object obtained from parsing the index json.
 */
function gvf_print_index( $permalink, $page, $index ) {

    if ( ! is_array( $index ) ) return; ?>

    <ul class="index">
        <?php foreach ( $index as $item ):
            gvf_get_index_items_recursive( $permalink, $page, $item );
        endforeach; ?>
    </ul>
    <?php
}

/**
 * Recursive function to print every index item inside an item.
 * To be used in the hamburger menu for small screens.
 *
 * @param string    $permalink      The post's permalink, without the page nor any heading ID.
 * @param int       $page           The current post page.
 * @param array     $item           A index item / heading.
 */
function gvf_get_index_items_recursive_ham_menu( $permalink, $page, $item ) {

    if ( ! isset( $item['Heading'] ) || ! isset( $item['Page'] ) ) return;

    $link  = $permalink;
    $link .= $item['Page'] !== 1         ? $item['Page'] . '/' : '';
    $link .= isset( $item['HeadingID'] ) ? '#' . $item['HeadingID'] : '';
    ?>
    <li class="nav-item <?php echo isset( $item['Children'] ) ? 'nav-expand' : '' ?>">
        <a class="nav-link-final" href="<?php echo $link ?>">
            <div class="<?php echo (int) $item['Page'] === $page ? 'active' : '' ?>"><?php echo $item['Heading'] ?></div>
        </a>
        <?php if ( isset( $item['Children'] ) ) : ?>
        <div class="nav-link nav-expand-link">
            <div class="nav-expand-link-icon-container">
                <?php if ( isset( $item['Children'] ) ) :
                    gvf_print_icon( 'chevron-right' );
                endif; ?>
            </div>
        </div>
        <?php endif; ?>
        <?php if ( isset( $item['Children'] ) ) : ?>
            <ul class="nav-items nav-expand-content">
                <li class="nav-item nav-back">
                    <div class="nav-link nav-back-link">
                        <div class="nav-back-link-icon-container">
                            <?php if ( isset( $item['Children'] ) ) :
                                gvf_print_icon( 'chevron-left' );
                            endif; ?>
                        </div>
                        <div class="nav-back-text-container">
                            <?php _e( "Back", 'gvf' ) ?>
                        </div>
                    </div>
                </li>
                <?php foreach ( $item['Children'] as $child ) :
                    gvf_get_index_items_recursive_ham_menu( $permalink, $page, $child );
                endforeach; ?>
            </ul>
        <?php endif; ?>
    </li>
    <?php
}

/**
 * Output the index for multi-page posts.
 * To be used in the hamburger menu for small screens.
 *
 * @see gvf_parse_content_file()
 *
 * @param string    $permalink      The post's permalink, without the page nor any heading ID.
 * @param int       $page           The current post page.
 * @param array     $index          The object obtained from parsing the index json.
 */
function gvf_print_index_for_ham_menu( $permalink, $page, $index ) {

    if ( ! is_array( $index ) ) return; ?>

    <ul class="nav-items nav-items-first">
        <?php foreach ( $index as $item ):
            gvf_get_index_items_recursive_ham_menu( $permalink, $page, $item );
        endforeach; ?>
    </ul>
    <?php
}

/**
 * Get the permalink of a post with the page query.
 * E.g. goncalovf.com/post-slug/page/2
 *
 * @param  int      $i  Page number
 * @return string
 */
function gvf_get_permalink_in_page( $i ) {

    return add_query_arg( 'page', $i, get_permalink() );
}
