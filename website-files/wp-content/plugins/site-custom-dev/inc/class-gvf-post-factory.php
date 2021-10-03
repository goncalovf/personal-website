<?php
/**
 * Post Factory
 *
 * The GVF post factory creating the right post object.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Post factory class.
 */
class GVF_Post_Factory {

    /**
     * Get a post.
     *
     * @param   GVF_Post|WP_Post|int|bool     $post_id      GVF_Post instance, post instance, numeric or false to use global $post.
     * @return  GVF_Post|bool                               GVF_Post object or false if the post cannot be loaded.
     */
    public function get_post( $post_id = false) {
        $post_id = $this->get_post_id( $post_id );

        if ( ! $post_id ) {
            return false;
        }

        $post_type = $this->get_post_type( $post_id );

        $classname = $this->get_post_classname( $post_type );

        try {
            return new $classname( $post_id );
        } catch ( Exception $e ) {
            return false;
        }
    }

    /**
     * Gets a post classname and allows filtering. Returns GVF_Post_Simple if the class does not exist.
     *
     * @param  string   $post_type
     * @return string
     */
    public static function get_post_classname( $post_type ) {
        $classname = self::get_classname_from_post_type( $post_type );

        if ( ! $classname || ! class_exists( $classname ) ) {
            $classname = 'GVF_Post_Simple';
        }

        return $classname;
    }

    /**
     * Get the post type.
     *
     * @param  int              $post_id
     * @return string|false
     */
    public static function get_post_type( $post_id ) {
        return GVF_Data_Store::load( 'post' )->get_post_type( $post_id );
    }

    /**
     * Create a GVF coding standards compliant class name e.g. GVF_Post_Type_Class instead of GVF_Post_type-class.
     *
     * @param  string           $post_type
     * @return string|false
     */
    public static function get_classname_from_post_type( $post_type ) {
        return $post_type ? 'GVF_Post_' . implode( '_', array_map( 'ucfirst', explode( '-', $post_type ) ) ) : false;
    }

    /**
     * Get the post ID depending on what was passed.
     *
     * @param  GVF_Post|WP_Post|int|bool    $passed_post    CTP_Post instance, post instance, numeric or false to use global $post.
     * @return int|bool                                     false on failure
     */
    private function get_post_id( $passed_post ) {
        global $post;

        if ( false === $passed_post && isset( $post, $post->ID ) && 'post' === get_post_type( $post->ID ) ) {
            return absint( $post->ID );
        } elseif ( is_numeric( $passed_post ) ) {
            return $passed_post;
        } elseif ( $passed_post instanceof GVF_Post ) {
            return $passed_post->get_id();
        } elseif ( ! empty( $passed_post->ID ) ) {
            return $passed_post->ID;
        } else {
            return false;
        }
    }
}
