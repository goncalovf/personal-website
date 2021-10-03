<?php
/**
 * Shared logic for WP based data.
 * Contains functions like meta handling for all default data stores.
 */

defined( 'ABSPATH' ) || exit;

/**
 * GVF_Data_Store_WP class.
 */
class GVF_Data_Store_WP {

    /**
     * Meta data which should exist in the DB, even if empty.
     *
     * @since 3.6.0
     *
     * @var array
     */
    protected $must_exist_meta_keys = array();

    /**
     * Get and store terms from a taxonomy.
     *
     * @param  GVF_Data|integer $object     GVF_Data object or object ID.
     * @param  string           $taxonomy   Taxonomy name.
     * @return array of terms
     */
    protected function get_term_ids( $object, $taxonomy ) {
        if ( is_numeric( $object ) ) {
            $object_id = $object;
        } else {
            $object_id = $object->get_id();
        }
        $terms = get_the_terms( $object_id, $taxonomy );
        if ( false === $terms || is_wp_error( $terms ) ) {
            return array();
        }
        return wp_list_pluck( $terms, 'term_id' );
    }

    /**
     * Gets a list of props and meta keys that need updated based on change state or if they are present in the
     * database or not.
     *
     * @param  GVF_Data $object
     * @param  array    $meta_key_to_props   A mapping of meta keys => prop names.
     * @param  string   $meta_type           The internal WP meta type (post, user, etc).
     * @return array                         A mapping of meta keys => prop names, filtered by ones that should be updated.
     */
    protected function get_props_to_update( $object, $meta_key_to_props, $meta_type = 'post' ) {
        $props_to_update = array();
        $changed_props   = $object->get_changes();

        // Props should be updated if they are a part of the $changed array or don't exist yet.
        foreach ( $meta_key_to_props as $meta_key => $prop ) {
            if ( array_key_exists( $prop, $changed_props ) || ! metadata_exists( $meta_type, $object->get_id(), $meta_key ) ) {
                $props_to_update[ $meta_key ] = $prop;
            }
        }

        return $props_to_update;
    }

    /**
     * Update meta data in, or delete it from, the database.
     *
     * Avoids storing meta when it's either an empty string or empty array.
     * Other empty values such as numeric 0 and null should still be stored.
     * Data-stores can force meta to exist using `must_exist_meta_keys`.
     *
     * Note: WordPress `get_metadata` function returns an empty string when meta data does not exist.
     *
     * @param GVF_Data  $object
     * @param string    $meta_key       Meta key to update.
     * @param mixed     $meta_value     Value to save.
     *
     * @return bool True if updated/deleted.
     */
    protected function update_or_delete_post_meta( $object, $meta_key, $meta_value ) {
        if ( in_array( $meta_value, array( array(), '' ), true ) && ! in_array( $meta_key, $this->must_exist_meta_keys, true ) ) {
            $updated = delete_post_meta( $object->get_id(), $meta_key );
        } else {
            $updated = update_post_meta( $object->get_id(), $meta_key, $meta_value );
        }

        return (bool) $updated;
    }
}
