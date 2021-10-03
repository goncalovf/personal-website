<?php
/**
 * GVF Data Store.
 *
 */

defined( 'ABSPATH' ) || exit;

/**
 * Data store class.
 */
class GVF_Data_Store {

    /**
     * Contains an instance of the data store class that we are working with.
     *
     * @var GVF_Data_Store
     */
    private $instance = null;

    /**
     * Contains an array of default GVF supported data stores.
     * Format of object name => class name.
     * You can also pass something like post_<type> for post stores and that type will be used first when available, if
     * a store is requested like this and doesn't exist, then the store would fall back to 'post'.
     *
     * @var array
     */
    private $stores = array(
        'post'              => 'GVF_Post_Simple_Data_Store_CPT',
        'post-simple'       => 'GVF_Post_Simple_Data_Store_CPT',
        'post-multi-page'   => 'GVF_Post_Multi_Page_Data_Store_CPT'
    );

    /**
     * Contains the name of the current data store's class name.
     *
     * @var string
     */
    private $current_class_name = '';

    /**
     * The object type this store works with.
     *
     * @var string
     */
    private $object_type = '';

    /**
     * Tells GVF_Data_Store which object store we want to work with.
     *
     * @throws  Exception                   When validation fails.
     * @param   string      $object_type    Name of object.
     */
    public function __construct( $object_type ) {
        $this->object_type = $object_type;

        // If this object type can't be found, check to see if we can load one level up (so if post-type isn't found,
        // we try post).
        if ( ! array_key_exists( $object_type, $this->stores ) ) {
            $pieces      = explode( '-', $object_type );
            $object_type = $pieces[0];
        }

        if ( array_key_exists( $object_type, $this->stores ) ) {
            $store = $this->stores[ $object_type ];
            if ( is_object( $store ) ) {
                if ( ! $store instanceof GVF_Object_Data_Store_Interface ) {
                    throw new Exception( __( 'Invalid data store.', 'gvf' ) );
                }
                $this->current_class_name = get_class( $store );
                $this->instance           = $store;
            } else {
                if ( ! class_exists( $store ) ) {
                    throw new Exception( __( 'Invalid data store.', 'gvf' ) );
                }
                $this->current_class_name = $store;
                $this->instance           = new $store();
            }
        } else {
            throw new Exception( __( 'Invalid data store.', 'gvf' ) );
        }
    }

    /**
     * Only store the object type to avoid serializing the data store instance.
     *
     * @return array
     */
    public function __sleep() {
        return array( 'object_type' );
    }

    /**
     * Re-run the constructor with the object type.
     *
     * @throws Exception When validation fails.
     */
    public function __wakeup() {
        $this->__construct( $this->object_type );
    }

    /**
     * Loads a data store.
     *
     * @param string $object_type Name of object.
     *
     * @throws Exception When validation fails.
     * @return GVF_Data_Store
     */
    public static function load( $object_type ) {
        return new GVF_Data_Store( $object_type );
    }

    /**
     * Returns the class name of the current data store.
     *
     * @return string
     */
    public function get_current_class_name() {
        return $this->current_class_name;
    }

    /**
     * Reads an object from the data store.
     *
     * @param GVF_Data  $data   GVF data instance.
     */
    public function read( &$data ) {
        $this->instance->read( $data );
    }

    /**
     * Create an object in the data store.
     *
     * @param GVF_Data  $data   GVF data instance.
     */
    public function create( &$data ) {
        $this->instance->create( $data );
    }

    /**
     * Update an object in the data store.
     *
     * @param GVF_Data  $data   GVF data instance.
     */
    public function update( &$data ) {
        $this->instance->update( $data );
    }

    /**
     * Delete an object from the data store.
     *
     * @param GVF_Data  $data   GVF data instance.
     * @param array     $args   Array of args to pass to the delete method.
     */
    public function delete( &$data, $args = array() ) {
        $this->instance->delete( $data, $args );
    }

    /**
     * Data stores can define additional functions. This passes through to the instance if that function exists.
     *
     * @param   string $method
     * @param   mixed  $parameters
     * @return  mixed
     */
    public function __call( $method, $parameters ) {
        if ( is_callable( array( $this->instance, $method ) ) ) {
            $object     = array_shift( $parameters );
            $parameters = array_merge( array( &$object ), $parameters );
            return $this->instance->$method( ...$parameters );
        }
    }
}
