<?php

defined( 'ABSPATH' ) || exit;

/**
 * Writes full Call Trace until this debugging function is called.
 */
if( ! function_exists('write_calltrace') ) {

    function write_calltrace() {

        if (true === WP_DEBUG) {

            $e = new Exception();
            $trace = explode("\n", $e->getTraceAsString());
            // reverse array to make steps line up chronologically
            $trace = array_reverse($trace);
            array_shift($trace); // remove {main}
            array_pop($trace); // remove call to this method
            $length = count($trace);
            $result = array();

            for ($i = 0; $i < $length; $i++) {
                $result[] = ($i + 1) . ')' . substr($trace[$i], strpos($trace[$i], ' ')); // replace '#someNum' with '$i)', set the right ordering
            }

            error_log("\n\t" . implode("\n\t", $result));
        }
    }

}


/**
 * Writes message passed into the function.
 * By default, includes calling function and class names.
 */
if( ! function_exists('write_log') ) {

    function write_log($log, $locator = false ) {

        if (true === WP_DEBUG) {

            if( $locator === true ) write_log_locator();

            if (is_array($log) || is_object($log)) {
                error_log(print_r($log, true));
            } else {
                error_log($log);
            }
        }
    }
}


/**
 * Writes the requester of a function call. Important to trace what is determining the execution of a block of code.
 * Writes calling function and class name.
 */
if( ! function_exists('write_requester') ) {

    function write_requester( $locator = false ) {

        if (true === WP_DEBUG) {

            if( $locator === true ) write_log_locator();

            error_log(time() . "\t" . $_SERVER['REQUEST_URI']);
        }
    }
}


/**
 * Writes calling function and class name.
 */
if( ! function_exists('write_log_locator') ) {

    function write_log_locator() {

        $trace = debug_backtrace();
        $caller = $trace[2];            // 1 is the functions in this Debug pugin

        $log_intro = (isset($caller['class'])) ? "Called by {$caller['function']} in {$caller['class']}" : "Called by {$caller['function']}";

        error_log($log_intro);
    }
}
