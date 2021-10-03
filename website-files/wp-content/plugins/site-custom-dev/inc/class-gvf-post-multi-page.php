<?php
/**
 * GVF multi-page post class
 */

defined( 'ABSPATH' ) || exit;

class GVF_Post_Multi_Page extends GVF_Post {

    /**
     * Stores post data.
     *
     * @var array
     */
    protected $extra_data = array(
        'index' => ''
    );

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
        return 'multi-page';
    }

    /**
     * Get index.
     * The index prop is only the name of the file containing the index json, without the extension.
     *
     * @return int
     */
    public function get_index() {
        return $this->get_prop( 'index' );
    }

    /*
    |--------------------------------------------------------------------------
    | Setter Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Set index.
     *
     * @param string    $index
     */
    public function set_index( $index ) {
        $this->set_prop( 'index', $index );
    }
}
