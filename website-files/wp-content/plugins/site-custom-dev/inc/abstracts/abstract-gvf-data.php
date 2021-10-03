<?php
/**
 * Abstract Data.
 *
 * Handles generic data interaction which is implemented by
 * the different data store classes.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Abstract CVF Data Class
 *
 * Implemented by classes using the same CRUD(s) pattern.
 */
abstract class GVF_Data {

    /**
     * ID for this object.
     *
     * @var int
     */
    protected $id = 0;

    /**
     * Core data for this object. Name value pairs (name + default value).
     *
     * @var array
     */
    protected $data = array();

    /**
     * Core data changes for this object.
     *
     * @var array
     */
    protected $changes = array();

    /**
     * Set to _data on construct so we can track and reset data if needed.
     *
     * @var array
     */
    protected $default_data = array();

    /**
     * This is false until the object is read from the DB.
     *
     * @var bool
     */
    protected $object_read = false;

    /**
     * Extra data for this object. Name value pairs (name + default value).
     * Used as a standard way for sub classes (like multi-page posts) to add additional information
     * to an inherited class.
     *
     * @var array
     */
    protected $extra_data = array();

    /**
     * Contains a reference to the data store for this class.
     *
     * @var object
     */
    protected $data_store;

    /**
     * Default constructor.
     *
     * @param int|object|array $read ID to load from the DB (optional) or already queried data.
     */
    public function __construct( $read = 0 ) {
        $this->data         = array_merge( $this->data, $this->extra_data );
        $this->default_data = $this->data;
    }

    /**
     * Get the data store.
     *
     * @return object
     */
    public function get_data_store() {
        return $this->data_store;
    }

    /**
     * Returns the unique ID for this object.
     *
     * @return int
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * Delete an object, set the ID to 0, and return result.
     *
     * @param  bool $force_delete   Delete permanently.
     * @return bool result
     */
    public function delete( $force_delete = false ) {
        if ( $this->data_store ) {
            $this->data_store->delete( $this, array( 'force_delete' => $force_delete ) );
            $this->set_id( 0 );
            return true;
        }
        return false;
    }

    /**
     * Save should create or update based on object existence.
     *
     * @return int
     */
    public function save() {
        if ( ! $this->data_store ) {
            return $this->get_id();
        }

        if ( $this->get_id() ) {
            $this->data_store->update( $this );
        } else {
            $this->data_store->create( $this );
        }

        return $this->get_id();
    }

    /**
     * Returns all data for this object.
     *
     * @return array
     */
    public function get_data() {
        return array_merge( array( 'id' => $this->get_id() ), $this->data );
    }

    /**
     * Returns array of expected data keys for this object.
     *
     * @return  array
     */
    public function get_data_keys() {
        return array_keys( $this->data );
    }

    /**
     * Returns all "extra" data keys for an object (for sub objects like multi-page post).
     *
     * @return array
     */
    public function get_extra_data_keys() {
        return array_keys( $this->extra_data );
    }

    /**
     * Set ID.
     *
     * @param int $id ID.
     */
    public function set_id( $id ) {
        $this->id = absint( $id );
    }

    /**
     * Set all props to default values.
     */
    public function set_defaults() {
        $this->data    = $this->default_data;
        $this->changes = array();
        $this->set_object_read( false );
    }

    /**
     * Set object read property.
     *
     * @param boolean $read Should read?
     */
    public function set_object_read( $read = true ) {
        $this->object_read = (bool) $read;
    }

    /**
     * Get object read property.
     *
     * @return boolean
     */
    public function get_object_read() {
        return (bool) $this->object_read;
    }

    /**
     * Set a collection of props in one go, collect any errors, and return the result.
     * Only sets using public methods.
     *
     * @param array  $props     Key value pairs to set. Key is the prop and should map to a setter function name.
     * @param string $context   In what context to run this.
     *
     * @return bool|WP_Error
     */
    public function set_props( $props, $context = 'set' ) {
        $errors = false;

        foreach ( $props as $prop => $value ) {
            try {
                /**
                 * Checks if the prop being set is allowed, and the value is not null.
                 */
                if ( is_null( $value ) || in_array( $prop, array( 'prop', 'date_prop', 'meta_data' ), true ) ) {
                    continue;
                }
                $setter = "set_$prop";

                if ( is_callable( array( $this, $setter ) ) ) {
                    $this->{$setter}( $value );
                }
            } catch ( GVF_Data_Exception $e ) {
                if ( ! $errors ) {
                    $errors = new WP_Error();
                }
                $errors->add( $e->getErrorCode(), $e->getMessage() );
            }
        }

        return $errors && count( $errors->get_error_codes() ) ? $errors : true;
    }

    /**
     * Sets a prop for a setter method.
     *
     * This stores changes in a special array so we can track what needs saving the the DB later.
     *
     * @param string $prop      Name of prop to set.
     * @param mixed  $value     Value of the prop.
     */
    protected function set_prop( $prop, $value ) {
        if ( array_key_exists( $prop, $this->data ) ) {
            if ( true === $this->object_read ) {
                if ( $value !== $this->data[ $prop ] || array_key_exists( $prop, $this->changes ) ) {
                    $this->changes[ $prop ] = $value;
                }
            } else {
                $this->data[ $prop ] = $value;
            }
        }
    }

    /**
     * Return data changes only.
     *
     * @return array
     */
    public function get_changes() {
        return $this->changes;
    }

    /**
     * Merge changes with data and clear.
     */
    public function apply_changes() {
        $this->data    = array_replace_recursive( $this->data, $this->changes ); // @codingStandardsIgnoreLine
        $this->changes = array();
    }

    /**
     * Gets a prop for a getter method.
     *
     * Gets the value from either current pending changes, or the data itself.
     * Context controls what happens to the value before it's returned.
     *
     * @param  string $prop Name of prop to get.
     * @param  string $context What the value is for. Valid values are view and edit.
     * @return mixed
     */
    protected function get_prop( $prop ) {
        $value = null;

        if ( array_key_exists( $prop, $this->data ) ) {
            $value = array_key_exists( $prop, $this->changes ) ? $this->changes[ $prop ] : $this->data[ $prop ];
        }

        return $value;
    }

    /**
     * Sets a date prop whilst handling formatting and datetime objects.
     *
     * @param string         $prop Name of prop to set.
     * @param string|integer $value Value of the prop.
     */
    protected function set_date_prop( $prop, $value ) {
        try {
            if ( empty( $value ) ) {
                $this->set_prop( $prop, null );
                return;
            }

            if ( is_a( $value, 'GVF_DateTime' ) ) {
                $datetime = $value;
            } elseif ( is_numeric( $value ) ) {
                // Timestamps are handled as UTC timestamps in all cases.
                $datetime = new GVF_DateTime( "@{$value}", new DateTimeZone( 'UTC' ) );
            } else {
                // Strings are defined in local WP timezone. Convert to UTC.
                if ( 1 === preg_match( '/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(Z|((-|\+)\d{2}:\d{2}))$/', $value, $date_bits ) ) {
                    $offset    = ! empty( $date_bits[7] ) ? iso8601_timezone_to_offset( $date_bits[7] ) : gvf_timezone_offset();
                    $timestamp = gmmktime( $date_bits[4], $date_bits[5], $date_bits[6], $date_bits[2], $date_bits[3], $date_bits[1] ) - $offset;
                } else {
                    $timestamp = gvf_string_to_timestamp( get_gmt_from_date( gmdate( 'Y-m-d H:i:s', gvf_string_to_timestamp( $value ) ) ) );
                }
                $datetime = new GVF_DateTime( "@{$timestamp}", new DateTimeZone( 'UTC' ) );
            }

            // Set local timezone or offset.
            if ( get_option( 'timezone_string' ) ) {
                $datetime->setTimezone( new DateTimeZone( gvf_timezone_string() ) );
            } else {
                $datetime->set_utc_offset( gvf_timezone_offset() );
            }

            $this->set_prop( $prop, $datetime );
        } catch ( Exception $e ) {} // @codingStandardsIgnoreLine.
    }

    /**
     * When invalid data is found, throw an exception unless reading from the DB.
     *
     * @throws  GVF_Data_Exception Data     Exception.
     * @param   string $code                Error code.
     * @param   string $message             Error message.
     * @param   int    $http_status_code    HTTP status code.
     * @param   array  $data                Extra error data.
     */
    protected function error( $code, $message, $http_status_code = 400, $data = array() ) {
        throw new GVF_Data_Exception( $code, $message, $http_status_code, $data );
    }
}
