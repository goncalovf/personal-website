<?php
/**
 * GVF post base class
 */

defined( 'ABSPATH' ) || exit;

class GVF_Post extends GVF_Data {

    /**
     * Stores post data.
     *
     * @var array
     */
    protected $data = array(
        'title'             => '',
        'slug'              => '',
        'status'            => 'publish',
        'content'           => '',
        'excerpt'           => '',
        'parent_id'         => 0,
        'author'            => 0,
        'date_created'      => '0000-00-00 00:00:00',
        'date_modified'     => '0000-00-00 00:00:00',
        'guid'              => '',
        'menu_order'        => 0,
        'post_mime_type'    => '',
        'password'          => '',
        'ping_status'       => 'open',
        'to_ping'           => '',
        'pinged'            => '',
        'category_ids'      => array(),
        'tag_ids'           => array(),
        'is_multi_page'     => false,
        'is_pinned'         => false,
        'reading_time'      => 0
    );

    /**
     * Get the post if ID is passed, otherwise the post is new and empty.
     * This class should NOT be instantiated. Use gvf()->get_post() function.
     *
     * @param   int|GVF_Post|object   $post     Post to init.
     */
    public function __construct( $post = 0 ) {
        parent::__construct( $post );
        if ( is_numeric( $post ) && $post > 0 ) {
            $this->set_id( $post );
        } elseif ( $post instanceof self ) {
            $this->set_id( absint( $post->get_id() ) );
        } elseif ( ! empty( $post->ID ) ) {
            $this->set_id( absint( $post->ID ) );
        } else {
            $this->set_object_read( true );
        }

        $this->data_store = GVF_Data_Store::load( 'post-' . $this->get_type() );
        if ( $this->get_id() > 0 ) {
            $this->data_store->read( $this );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Getter Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get internal type. Should return string and *should be overridden* by child classes.
     *
     * @return string
     */
    public function get_type() {
        return 'simple';
    }

    /**
     * Get post author.
     *
     * @return int
     */
    public function get_author() {
        return $this->get_prop( 'author' );
    }

    /**
     * Get post date.
     *
     * @return GVF_DateTime
     */
    public function get_date_created() {
        return $this->get_prop( 'date_created' );
    }

    /**
     * Get post content.
     *
     * @return string
     */
    public function get_content() {
        return $this->get_prop( 'content' );
    }

    /**
     * Get post name.
     *
     * @return string
     */
    public function get_title() {
        return $this->get_prop( 'title' );
    }

    /**
     * Get post excerpt.
     *
     * @return string
     */
    public function get_excerpt() {
        return $this->get_prop( 'excerpt' );
    }

    /**
     * Get post status.
     *
     * @return string
     */
    public function get_status() {
        return $this->get_prop( 'status' );
    }

    /**
     * Get post ping status.
     *
     * @return string
     */
    public function get_ping_status() {
        return $this->get_prop( 'ping_status' );
    }

    /**
     * Get post password.
     *
     * @return string
     */
    public function get_post_password() {
        return $this->get_prop( 'password' );
    }

    /**
     * Get post slug.
     *
     * @return string
     */
    public function get_slug() {
        return $this->get_prop( 'slug' );
    }

    /**
     * Get post to ping.
     *
     * @return string
     */
    public function get_to_ping() {
        return $this->get_prop( 'to_ping' );
    }

    /**
     * Get post pinged.
     *
     * @return string
     */
    public function get_pinged() {
        return $this->get_prop( 'pinged' );
    }

    /**
     * Get post modified date.
     *
     * @return GVF_DateTime
     */
    public function get_date_modified() {
        return $this->get_prop( 'date_modified' );
    }

    /**
     * Get post parent.
     *
     * @return int
     */
    public function get_parent_id() {
        return $this->get_prop( 'parent_id' );
    }

    /**
     * Get post guid.
     *
     * @return string
     */
    public function get_guid() {
        return $this->get_prop( 'guid' );
    }

    /**
     * Get post menu order.
     *
     * @return int
     */
    public function get_menu_order() {
        return $this->get_prop( 'menu_order' );
    }

    /**
     * Get post mime type.
     *
     * @return string
     */
    public function get_mime_type() {
        return $this->get_prop( 'post_mime_type' );
    }

    /**
     * Get category ids.
     *
     * @return array
     */
    public function get_category_ids() {
        return $this->get_prop( 'category_ids' );
    }

    /**
     * Get tag ids.
     *
     * @return array
     */
    public function get_tag_ids() {
        return $this->get_prop( 'tag_ids' );
    }

    /**
     * Get whether the post is multi page.
     *
     * @return bool
     */
    public function get_is_multi_page() {
        return $this->get_prop( 'is_multi_page' );
    }

    /**
     * Get whether the post is pinned.
     *
     * @return bool
     */
    public function get_is_pinned() {
        return $this->get_prop( 'is_pinned' );
    }

    /**
     * Get the estimated time of reading (in minutes).
     *
     * @return int
     */
    public function get_reading_time() {
        return $this->get_prop( 'reading_time' );
    }

    /**
     * Get index.
     *
     * @return string
     */
    public function get_index() {
        return '';
    }

    /*
    |--------------------------------------------------------------------------
    | Setter Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Set post author.
     *
     * @param int $author_id
     */
    public function set_author( $author_id ) {
        $this->set_prop( 'author', $author_id );
    }

    /**
     * Set post date.
     *
     * @param string|integer|null   $date   UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone
     *                                      or offset, WordPress site timezone will be assumed. Null if their is no date.
     */
    public function set_date_created( $date ) {
        $this->set_date_prop( 'date_created', $date );
    }

    /**
     * Set post content.
     *
     * @param string $content
     */
    public function set_content( $content ) {
        $this->set_prop( 'content', $content );
    }

    /**
     * Set post name.
     *
     * @param string $title
     */
    public function set_title( $title ) {
        $this->set_prop( 'title', $title );
    }

    /**
     * Set post excerpt.
     *
     * @param string $excerpt
     */
    public function set_excerpt( $excerpt ) {
        $this->set_prop( 'excerpt', $excerpt );
    }

    /**
     * Set post status.
     *
     * @param string $status
     */
    public function set_status( $status ) {
        $this->set_prop( 'status', $status );
    }

    /**
     * Set post ping status.
     *
     * @param string $ping_status
     */
    public function set_ping_status( $ping_status ) {
        $this->set_prop( 'ping_status', $ping_status );
    }

    /**
     * Set post password.
     *
     * @param string $post_password
     */
    public function set_post_password( $post_password ) {
        $this->set_prop( 'password', $post_password );
    }

    /**
     * Set post name.
     *
     * @param string $name
     */
    public function set_slug( $name ) {
        $this->set_prop( 'slug', $name );
    }

    /**
     * Set post to ping.
     *
     * @param string $to_ping
     */
    public function set_to_ping( $to_ping ) {
        $this->set_prop( 'to_ping', $to_ping );
    }

    /**
     * Set post pinged.
     *
     * @param string $pinged
     */
    public function set_pinged( $pinged ) {
        $this->set_prop( 'pinged', $pinged );
    }

    /**
     * Set post modified date.
     *
     * @param string|integer|null   $date   UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone
     *                                      or offset, WordPress site timezone will be assumed. Null if their is no date.
     */
    public function set_date_modified( $date ) {
        $this->set_date_prop( 'date_modified', $date );
    }

    /**
     * Set post parent.
     *
     * @param int $parent_id
     */
    public function set_parent_id( $parent_id ) {
        $this->set_prop( 'parent_id', $parent_id );
    }

    /**
     * Set post guid.
     *
     * @param string $guid
     */
    public function set_guid( $guid ) {
        $this->set_prop( 'guid', $guid );
    }

    /**
     * Set post menu order.
     *
     * @param int $menu_order
     */
    public function set_menu_order( $menu_order ) {
        $this->set_prop( 'menu_order', $menu_order );
    }

    /**
     * Set post mime type.
     *
     * @param string $mime_type
     */
    public function set_mime_type( $mime_type ) {
        $this->set_prop( 'post_mime_type', $mime_type );
    }

    /**
     * Set the post categories.
     *
     * @param array $term_ids List of terms IDs.
     */
    public function set_category_ids( $term_ids ) {
        $this->set_prop( 'category_ids', array_unique( array_map( 'intval', $term_ids ) ) );
    }

    /**
     * Set the post tags.
     *
     * @param array $term_ids List of terms IDs.
     */
    public function set_tag_ids( $term_ids ) {
        $this->set_prop( 'tag_ids', array_unique( array_map( 'intval', $term_ids ) ) );
    }

    /**
     * Set whether the post is multi page.
     *
     * @param bool $is_multi_page
     */
    public function set_is_multi_page( $is_multi_page ) {
        $this->set_prop( 'is_multi_page', gvf_string_to_bool( $is_multi_page ) );
    }

    /**
     * Set whether the post is pinned.
     *
     * @param bool  $is_pinned
     */
    public function set_is_pinned( $is_pinned ) {
        $this->set_prop( 'is_pinned', gvf_string_to_bool( $is_pinned ) );
    }

    /**
     * Set the estimated time of reading (in minutes).
     *
     * @param int  $reading_time
     */
    public function set_reading_time( $reading_time ) {
        $this->set_prop( 'reading_time', intval( $reading_time ) );
    }

    /*
    |--------------------------------------------------------------------------
    | Other methods.
    |--------------------------------------------------------------------------
    */

    /**
     * Get the reading time to display on the site.
     *
     * @return string
     */
    public function get_reading_time_html() {

        return $this->get_reading_time() . " " . __( "min read", 'gvf' );
    }
}
