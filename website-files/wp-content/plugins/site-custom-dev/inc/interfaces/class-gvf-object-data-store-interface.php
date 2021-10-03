<?php
/**
 * Object Data Store Interface
 */

defined( 'ABSPATH' ) || exit;

/**
 * GVF Data Store Interface
 */
interface GVF_Object_Data_Store_Interface {

    /**
     * Method to create a new record of a GVF_Data based object.
     *
     * @param GVF_Data $data Data object.
     */
    public function create( &$data );

    /**
     * Method to read a record. Creates a new GVF_Data based object.
     *
     * @param GVF_Data $data Data object.
     */
    public function read( &$data );

    /**
     * Updates a record in the database.
     *
     * @param GVF_Data $data Data object.
     */
    public function update( &$data );

    /**
     * Deletes a record from the database.
     *
     * @param  GVF_Data $data Data object.
     * @param  array    $args Array of args to pass to the delete method.
     * @return bool result
     */
    public function delete( &$data, $args = array() );
}
