<?php
/**
 * Plugin Name: Site Custom Dev
 * Version: 1.0
 * Author: Gonçalo Figueiredo
 * License: GPL2
*/

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'GVF_PLUGIN_FILE' ) ) {
    define( 'GVF_PLUGIN_FILE', __FILE__ );
}

// Include the main WooCommerce class.
if ( ! class_exists( 'GVF', false ) ) {
    include_once dirname( GVF_PLUGIN_FILE ) . '/inc/class-gvf.php';
}

/**
 * Returns the main instance of WC.
 *
 * @since  2.1
 * @return GVF
 */
function gvf() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
    return GVF::instance();
}

gvf();
