<?php
/**
 * GVF_Post_Data_Store_CPT class file.
 */

defined( 'ABSPATH' ) || exit;

/**
 * GVF Post Data Store.
 */
class GVF_Post_Data_Store_CPT extends GVF_Data_Store_WP implements GVF_Object_Data_Store_Interface {

    /**
     * Data stored in meta keys, but not considered "meta".
     *
     * @var array
     */
    protected $internal_meta_keys = array(
        '_is_multi_page',
    );

    /**
     * If we have already saved our extra data, don't do automatic / default handling.
     *
     * @var bool
     */
    protected $extra_data_saved = false;

    /**
     * Stores updated props.
     *
     * @var array
     */
    protected $updated_props = array();

    /*
    |--------------------------------------------------------------------------
    | CRUD Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Method to create a new post in the database.
     *
     * @param GVF_Post $post
     */
    public function create( &$post ) {
        if ( ! $post->get_date_created() ) {
            $post->set_date_created( time() );
        }

        $id = wp_insert_post(
            array(
                'post_type'         => 'post',
                'post_author'       => get_current_user_id(),
                'post_date'         => gmdate( 'Y-m-d H:i:s', $post->get_date_created()->getOffsetTimestamp() ),
                'post_date_gmt'     => gmdate( 'Y-m-d H:i:s', $post->get_date_created()->getTimestamp() ),
                'post_content'      => $post->get_content(),
                'post_title'        => $post->get_title() ? $post->get_title() : __( 'Post', 'gvf' ),
                'post_excerpt'      => $post->get_excerpt(),
                'post_status'       => $post->get_status() ? $post->get_status() : 'publish',
                'ping_status'       => 'closed',
                'post_password'     => $post->get_post_password(),
                'post_name'         => $post->get_slug(),
                'to_ping'           => $post->get_to_ping(),
                'pinged'            => $post->get_pinged(),
                'post_parent'       => $post->get_parent_id(),
                'guid'              => $post->get_guid(),
                'menu_order'        => $post->get_menu_order(),
                'post_mime_type'    => $post->get_mime_type()
            ),
        true
        );

        if ( $id && ! is_wp_error( $id ) ) {
            $post->set_id( $id );

            $this->update_post_meta( $post, true );
            $this->update_terms( $post, true );

            $post->apply_changes();
        }
    }

    /**
     * Method to read a post from the database.
     *
     * @param   GVF_Post    $post
     * @throws  Exception           If invalid post.
     */
    public function read( &$post ) {
        $post->set_defaults();
        $post_object = get_post( $post->get_id() );

        if ( ! $post->get_id() || ! $post_object || 'post' !== $post_object->post_type ) {
            throw new Exception( __( 'Invalid post.', 'gvf' ) );
        }

        $post->set_props(
            array(
                'title'          => $post_object->post_title,
                'slug'           => $post_object->post_name,
                'status'         => $post_object->post_status,
                'content'        => $post_object->post_content,
                'excerpt'        => $post_object->post_excerpt,
                'parent_id'      => $post_object->post_parent,
                'author'         => $post_object->post_author,
                'date_created'   => 0 < $post_object->post_date_gmt ? gvf_string_to_timestamp( $post_object->post_date_gmt ) : null,
                'date_modified'  => 0 < $post_object->post_date_gmt ? gvf_string_to_timestamp( $post_object->post_date_gmt ) : null,
                'guid'           => $post_object->guid,
                'menu_order'     => $post_object->menu_order,
                'post_mime_type' => $post_object->post_mime_type,
                'password'       => $post_object->post_password,
                'ping_status'    => $post_object->ping_status,
                'to_ping'        => $post_object->to_ping,
                'pinged'         => $post_object->pinged,
            )
        );

        $this->read_post_data( $post );
        $this->read_extra_data( $post );

        $post->set_object_read( true );
    }

    /**
     * Method to update a post in the database.
     *
     * @param GVF_Post $post
     */
    public function update( &$post ) {
        $changes = $post->get_changes();

        // Only update the post when the post data changes.
        if ( array_intersect( array( 'title', 'slug', 'status', 'content', 'excerpt', 'parent_id', 'author', 'date_created', 'date_modified', 'guid', 'menu_order', 'post_mime_type', 'password', 'ping_status', 'to_ping', 'pinged' ), array_keys( $changes ) ) ) {
            $post_data = array(
                'post_type'         => 'post',
                'post_author'       => get_current_user_id(),
                'post_content'      => $post->get_content(),
                'post_title'        => $post->get_title() ? $post->get_title() : __( 'Post', 'gvf' ),
                'post_excerpt'      => $post->get_excerpt(),
                'post_status'       => $post->get_status() ? $post->get_status() : 'publish',
                'ping_status'       => 'closed',
                'post_password'     => $post->get_post_password(),
                'post_name'         => $post->get_slug(),
                'to_ping'           => $post->get_to_ping(),
                'pinged'            => $post->get_pinged(),
                'post_parent'       => $post->get_parent_id(),
                'guid'              => $post->get_guid(),
                'menu_order'        => $post->get_menu_order(),
                'post_mime_type'    => $post->get_mime_type()
            );
            if ( $post->get_date_created() ) {
                $post_data['post_date']     = gmdate( 'Y-m-d H:i:s', $post->get_date_created()->getOffsetTimestamp() );
                $post_data['post_date_gmt'] = gmdate( 'Y-m-d H:i:s', $post->get_date_created()->getTimestamp() );
            }
            if ( isset( $changes['date_modified'] ) && $post->get_date_modified() ) {
                $post_data['post_modified']     = gmdate( 'Y-m-d H:i:s', $post->get_date_modified()->getOffsetTimestamp() );
                $post_data['post_modified_gmt'] = gmdate( 'Y-m-d H:i:s', $post->get_date_modified()->getTimestamp() );
            } else {
                $post_data['post_modified']     = current_time( 'mysql' );
                $post_data['post_modified_gmt'] = current_time( 'mysql', 1 );
            }

            /**
             * When updating this object, to prevent infinite loops, use $wpdb to update data, since wp_update_post
             * spawns more calls to the save_post action.
             *
             * This ensures hooks are fired by either WP itself (admin screen save), or an update purely from CRUD.
             */
            if ( doing_action( 'save_post' ) ) {
                $GLOBALS['wpdb']->update( $GLOBALS['wpdb']->posts, $post_data, array( 'ID' => $post->get_id() ) );
                clean_post_cache( $post->get_id() );
            } else {
                wp_update_post( array_merge( array( 'ID' => $post->get_id() ), $post_data ) );
            }

        } else { // Only update post modified time to record this save event.
            $GLOBALS['wpdb']->update(
                $GLOBALS['wpdb']->posts,
                array(
                    'post_modified'     => current_time( 'mysql' ),
                    'post_modified_gmt' => current_time( 'mysql', 1 ),
                ),
                array(
                    'ID' => $post->get_id(),
                )
            );
            clean_post_cache( $post->get_id() );
        }

        $this->update_post_meta( $post );
        $this->update_terms( $post );

        $post->apply_changes();
    }

    /**
     * Method to delete a post from the database.
     *
     * @param GVF_Post  $post
     * @param array     $args
     */
    public function delete( &$post, $args = array() ) {
        $id = $post->get_id();

        $args = wp_parse_args(
            $args,
            array(
                'force_delete' => false,
            )
        );

        if ( ! $id ) {
            return;
        }

        if ( $args['force_delete'] ) {
            wp_delete_post( $id );
            $post->set_id( 0 );
        } else {
            wp_trash_post( $id );
            $post->set_status( 'trash' );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Additional Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Read post data. Can be overridden by child classes to load other props.
     *
     * @param GVF_Post $post
     */
    protected function read_post_data( &$post ) {
        $id                = $post->get_id();
        $post_meta_values  = get_post_meta( $id );
        $meta_key_to_props = array(
            '_is_multi_page' => 'is_multi_page',
            '_is_pinned'     => 'is_pinned',
            '_reading_time'  => 'reading_time'
        );

        $set_props = array();

        foreach ( $meta_key_to_props as $meta_key => $prop ) {
            $meta_value         = isset( $post_meta_values[ $meta_key ][0] ) ? $post_meta_values[ $meta_key ][0] : null;
            $set_props[ $prop ] = maybe_unserialize( $meta_value ); // get_post_meta only unserializes single values.
        }

        $set_props['category_ids']      = $this->get_term_ids( $post, 'category' );
        $set_props['tag_ids']           = $this->get_term_ids( $post, 'post_tag' );

        $post->set_props( $set_props );
    }

    /**
     * Read extra data associated with the post.
     *
     * @param GVF_Post $post
     */
    protected function read_extra_data( &$post ) {
        foreach ( $post->get_extra_data_keys() as $key ) {
            $function = 'set_' . $key;
            if ( is_callable( array( $post, $function ) ) ) {
                $post->{$function}( get_post_meta( $post->get_id(), '_' . $key, true ) );
            }
        }
    }

    /**
     * Helper method that updates all the post meta for a post based on it's settings in the GVF_Post class.
     *
     * @param GVF_Post  $post
     * @param bool      $force Force update. Used during create.
     */
    protected function update_post_meta( &$post, $force = false ) {
        $meta_key_to_props = array(
            '_is_multi_page' => 'is_multi_page',
            '_is_pinned'     => 'is_pinned',
            '_reading_time'  => 'reading_time'
        );

        // Make sure to take extra data (like index for multi-page posts) into account.
        $extra_data_keys = $post->get_extra_data_keys();

        foreach ( $extra_data_keys as $key ) {
            $meta_key_to_props[ '_' . $key ] = $key;
        }

        $props_to_update = $force ? $meta_key_to_props : $this->get_props_to_update( $post, $meta_key_to_props );

        foreach ( $props_to_update as $meta_key => $prop ) {
            $value = $post->{"get_$prop"}();
            $value = is_string( $value ) ? wp_slash( $value ) : $value;
            $value = in_array( $prop, array( 'is_multi_page', 'is_pinned' ) ) ? gvf_bool_to_string( $value ) : $value;

            $updated = $this->update_or_delete_post_meta( $post, $meta_key, $value );

            if ( $updated ) {
                $this->updated_props[] = $prop;
            }
        }

        // Update extra data associated with the post.
        if ( ! $this->extra_data_saved ) {
            foreach ( $extra_data_keys as $key ) {
                $meta_key = '_' . $key;
                $function = 'get_' . $key;
                if ( ! array_key_exists( $meta_key, $props_to_update ) ) {
                    continue;
                }
                if ( is_callable( array( $post, $function ) ) ) {
                    $value   = $post->{$function}();
                    $value   = is_string( $value ) ? wp_slash( $value ) : $value;
                    $updated = $this->update_or_delete_post_meta( $post, $meta_key, $value );

                    if ( $updated ) {
                        $this->updated_props[] = $key;
                    }
                }
            }
        }
    }


    /**
     * For all stored terms in all taxonomies, save them to the DB.
     *
     * @param GVF_Post  $post   Post object.
     * @param bool      $force  Force update. Used during create.
     */
    protected function update_terms( &$post, $force = false ) {
        $changes = $post->get_changes();

        if ( $force || array_key_exists( 'category_ids', $changes ) ) {
            wp_set_post_terms( $post->get_id(), $post->get_category_ids(), 'category', false );
        }
        if ( $force || array_key_exists( 'tag_ids', $changes ) ) {
            wp_set_post_terms( $post->get_id(), $post->get_tag_ids(), 'post_tag', false );
        }
    }


    /**
     * Get the post type based on post ID.
     *
     * @param   int             $post_id
     * @return  bool|string
     */
    public function get_post_type( $post_id ) {

        $is_multi_page = gvf_string_to_bool( get_post_meta( $post_id, '_is_multi_page', true ) );

        if ( $is_multi_page ) {
            return 'multi-page';

        } elseif ( ! $is_multi_page || $is_multi_page === "" ) {
            return 'simple';

        } else {
            return false;
        }
    }
}
