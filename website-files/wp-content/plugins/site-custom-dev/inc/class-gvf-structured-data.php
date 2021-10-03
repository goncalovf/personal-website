<?php
/**
 * Structured data's handler and generator.
 */

defined( 'ABSPATH' ) || exit;

class GVF_Structured_Data {

    /**
     * Stores the structured data.
     *
     * @var array $_data Array of structured data.
     */
    private $_data = array();

    /**
     * Constructor.
     */
    public function __construct() {

        add_action( 'wp_footer', array( $this, 'output_structured_data' ), 10 );
    }

    /**
     * Sets data.
     *
     * @param  array $data  Structured data.
     * @param  bool  $reset Unset data (default: false).
     * @return bool
     */
    public function set_data( $data, $reset = false ) {
        if ( ! isset( $data['@type'] ) || ! preg_match( '|^[a-zA-Z]{1,20}$|', $data['@type'] ) ) {
            return false;
        }

        if ( $reset && isset( $this->_data ) ) {
            unset( $this->_data );
        }

        $this->_data[] = $data;

        return true;
    }

    /**
     * Gets data.
     *
     * @return array
     */
    public function get_data() {
        return $this->_data;
    }

    /**
     * Structures and returns data.
     *
     * List of types available by default for specific request:
     *
     * 'blog',
     * 'blogposting'
     *
     * @param  array $types Structured data types.
     * @return array
     */
    public function get_structured_data( $types ) {
        $data = array();

        // Put together the values of same type of structured data.
        foreach ( $this->get_data() as $value ) {
            $data[ strtolower( $value['@type'] ) ][] = $value;
        }

        // Wrap the multiple values of each type inside a graph... Then add context to each type.
        foreach ( $data as $type => $value ) {
            $data[ $type ] = count( $value ) > 1 ? array( '@graph' => $value ) : $value[0];
            $data[ $type ] = array( '@context' => 'https://schema.org/' ) + $data[ $type ];
        }

        // If requested types, pick them up... Finally change the associative array to an indexed one.
        $data = $types ? array_values( array_intersect_key( $data, array_flip( $types ) ) ) : array_values( $data );

        if ( ! empty( $data ) ) {
            if ( 1 < count( $data ) ) {
                $data = array( '@context' => 'https://schema.org/' ) + array( '@graph' => $data );
            } else {
                $data = $data[0];
            }
        }
        return $data;
    }

    /**
     * Generate data of type Person for the site's admin to be used for other markups
     *
     * @return array
     */
    private function get_admin_data() {

        $admin_email = get_bloginfo( 'admin_email' );
        $admin = get_user_by( 'email', $admin_email );

        return array(
            '@type'         => 'Person',
            'email'         => $admin_email,
            'name'          => $admin->get( 'display_name' ),
            'givenName'     => $admin->get( 'first_name' ),
            'familyName'    => $admin->get( 'last_name' ),
            'sameAs'        => 'https://twitter.com/goncalovf'
        );
    }

    /**
     * Generate data of type Language to be used for other markups
     *
     * @return array
     */
    private function get_language_data() {

        return array(
            '@type'         => 'Language',
            'name'          => 'English',
            'alternateName' => 'en-US'
        );
    }

    /**
     * Generates WebSite structured data.
     */
    public function generate_website_data() {

        $markup_blog = array(
            '@type'         => 'Blog',
            '@id'           => home_url(),
            'name'          => get_bloginfo( 'name' ),
            'url'           => home_url(),
            'author'        => $this->get_admin_data(),
            'inLanguage'    => $this->get_language_data()
        );

        $this->set_data( $markup_blog );
    }

    /**
     * Generates Post structured data.
     *
     * @param GVF_Post $post
     */
    public function generate_post_data( $post ) {

        if ( ! is_a( $post, 'GVF_Post' ) ) {
            return;
        }

        $post_permalink = get_permalink( $post->get_id() );

        $markup_person   = $this->get_admin_data();
        $markup_language = $this->get_language_data();

        $markup_post = array(
            '@type'             => 'BlogPosting',
            '@id'               => $post_permalink,
            'mainEntityOfPage'  => $post_permalink,
            'url'               => $post_permalink,
            'headline'          => $post->get_title(),
            'author'            => $markup_person,
            'publisher'         => $markup_person,
            'inLanguage'        => $markup_language,
            'datePublished'     => date( 'Y-m-d', $post->get_date_created()->getTimestamp() ),
            'dateModified'      => date( 'Y-m-d', $post->get_date_modified()->getTimestamp() ),
            'articleSection'    => array_column( get_the_category( $post->get_id() ), 'name' ),
            'keywords'          => array_column( get_the_tags( $post->get_id() ), 'name' ),
            'abstract'          => wp_strip_all_tags( get_the_excerpt() ),
            // articleBody set afterwards.
        );

        if ( $post->get_reading_time() ) {
            $markup_post['timeRequired'] = 'PT' . $post->get_reading_time() . 'M';
        }

        global $multipage;

        if ( $multipage ) {

            global $page;

            $permalink_in_page = gvf_get_permalink_in_page( $page );

            $markup_post = array(
                '@type'             => 'BlogPosting',
                '@id'               => $permalink_in_page,
                'mainEntityOfPage'  => $permalink_in_page,
                'url'               => $permalink_in_page,
                'headline'          => $post->get_title(),
                'author'            => $markup_person,
                'publisher'         => $markup_person,
                'inLanguage'        => $markup_language,
                'datePublished'     => date( 'Y-m-d', $post->get_date_created()->getTimestamp() ),
                'dateModified'      => date( 'Y-m-d', $post->get_date_modified()->getTimestamp() ),
                'articleBody'       => wp_strip_all_tags( get_the_content() ),
                'isPartOf'          => $markup_post
            );

        } else {

            $markup_post['articleBody'] = wp_strip_all_tags( $post->get_content() );
        }

        $this->set_data( $markup_post );
    }

    /**
     * Get data types for pages.
     *
     * @return array
     */
    protected function get_data_type_for_page() {
        $types   = array( 'blog' );
        $types[] = is_single() ? 'blogposting' : '';

        return array_filter( $types );
    }

    /**
     * Sanitizes, encodes and outputs structured data.
     */
    public function output_structured_data() {
        $types = $this->get_data_type_for_page();
        $data  = $this->get_structured_data( $types );

        if ( $data ) {
            echo '<script type="application/ld+json">' . gvf_esc_json( wp_json_encode( $data ), true ) . '</script>';
        }
    }
}
