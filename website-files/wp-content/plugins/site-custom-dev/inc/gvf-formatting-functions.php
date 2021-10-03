<?php

/**
 * Convert mysql datetime to PHP timestamp, forcing UTC. Wrapper for strtotime.
 *
 * @param  string   $time_string    Time string.
 * @param  int|null $from_timestamp Timestamp to convert from.
 * @return int
 */
function gvf_string_to_timestamp( $time_string, $from_timestamp = null ) {
    $original_timezone = date_default_timezone_get();

    // @codingStandardsIgnoreStart
    date_default_timezone_set( 'UTC' );

    if ( null === $from_timestamp ) {
        $next_timestamp = strtotime( $time_string );
    } else {
        $next_timestamp = strtotime( $time_string, $from_timestamp );
    }

    date_default_timezone_set( $original_timezone );
    // @codingStandardsIgnoreEnd

    return $next_timestamp;
}

/**
 * Get timezone offset in seconds.
 *
 * @return float
 */
function gvf_timezone_offset() {
    $timezone = get_option( 'timezone_string' );

    if ( $timezone ) {
        $timezone_object = new DateTimeZone( $timezone );
        return $timezone_object->getOffset( new DateTime( 'now' ) );
    } else {
        return floatval( get_option( 'gmt_offset', 0 ) ) * HOUR_IN_SECONDS;
    }
}

/**
 * GVF Timezone - helper to retrieve the timezone string for a site until
 * a WP core method exists (see https://core.trac.wordpress.org/ticket/24730 ).
 *
 * Adapted from https://secure.php.net/manual/en/function.timezone-name-from-abbr.php#89155.
 *
 * @return string PHP timezone string for the site
 */
function gvf_timezone_string() {
    // If site timezone string exists, return it.
    $timezone = get_option( 'timezone_string' );
    if ( $timezone ) {
        return $timezone;
    }

    // Get UTC offset, if it isn't set then return UTC.
    $utc_offset = intval( get_option( 'gmt_offset', 0 ) );
    if ( 0 === $utc_offset ) {
        return 'UTC';
    }

    // Adjust UTC offset from hours to seconds.
    $utc_offset *= 3600;

    // Attempt to guess the timezone string from the UTC offset.
    $timezone = timezone_name_from_abbr( '', $utc_offset );
    if ( $timezone ) {
        return $timezone;
    }

    // Last try, guess timezone string manually.
    foreach ( timezone_abbreviations_list() as $abbr ) {
        foreach ( $abbr as $city ) {
            // WordPress restrict the use of date(), since it's affected by timezone settings, but in this case is just what we need to guess the correct timezone.
            if ( (bool) date( 'I' ) === (bool) $city['dst'] && $city['timezone_id'] && intval( $city['offset'] ) === $utc_offset ) { // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
                return $city['timezone_id'];
            }
        }
    }

    // Fallback to UTC.
    return 'UTC';
}

/**
 * Converts a bool to a 'yes' or 'no'.
 *
 * @param   bool    $bool   String to convert.
 * @return  string
 */
function gvf_bool_to_string( $bool ) {
    if ( ! is_bool( $bool ) ) {
        $bool = gvf_string_to_bool( $bool );
    }
    return true === $bool ? 'yes' : 'no';
}

/**
 * Converts a string (e.g. 'yes' or 'no') to a bool.
 *
 * @param   string  $string     String to convert.
 * @return  bool
 */
function gvf_string_to_bool( $string ) {
    return is_bool( $string ) ? $string : ( 'yes' === strtolower( $string ) || 1 === $string || 'true' === strtolower( $string ) || '1' === $string );
}

/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 * Non-scalar values are ignored.
 *
 * @param   string|array $var Data to sanitize.
 * @return  string|array
 */
function gvf_clean( $var ) {
    if ( is_array( $var ) ) {
        return array_map( 'gvf_clean', $var );
    } else {
        return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
    }
}

/**
 * Sanitize a string destined to be a tooltip.
 * Tooltips are encoded with htmlspecialchars to prevent XSS. Should not be used in conjunction with esc_attr()
 *
 * @param  string $var Data to sanitize.
 * @return string
 */
function gvf_sanitize_tooltip( $var ) {
    return htmlspecialchars(
        wp_kses(
            html_entity_decode( $var ),
            array(
                'br'     => array(),
                'em'     => array(),
                'strong' => array(),
                'small'  => array(),
                'span'   => array(),
                'ul'     => array(),
                'li'     => array(),
                'ol'     => array(),
                'p'      => array(),
            )
        )
    );
}

/**
 * Format a date for output.
 * To be used for dates showed to the user. Ensures that dates are consistent, because we set the date format
 * here. Changes in the date format here are reflected throughout the site.
 *
 * @param  GVF_DateTime $date
 * @param  string       $format Data format.
 * @return string
 */
function gvf_format_datetime( $date, $format = 'M d, Y' ) {
    if ( ! is_a( $date, 'GVF_DateTime' ) ) {
        return '';
    }
    return $date->date_i18n( $format );
}

/**
 * Implode and escape HTML attributes for output.
 *
 * @param   array   $raw_attributes     Attribute name value pairs.
 * @return  string
 */
function gvf_implode_html_attributes( $raw_attributes ) {
    $attributes = array();
    foreach ( $raw_attributes as $name => $value ) {
        $attributes[] = esc_attr( $name ) . '="' . esc_attr( $value ) . '"';
    }
    return implode( ' ', $attributes );
}

/**
 * Escape JSON for use on HTML or attribute text nodes.
 *
 * @param string $json JSON to escape.
 * @param bool   $html True if escaping for HTML text node, false for attributes. Determines how quotes are handled.
 * @return string Escaped JSON.
 */
function gvf_esc_json( $json, $html = false ) {
    return _wp_specialchars(
        $json,
        $html ? ENT_NOQUOTES : ENT_QUOTES, // Escape quotes in attribute nodes only.
        'UTF-8',                           // json_encode() outputs UTF-8 (really just ASCII), not the blog's charset.
        true                               // Double escape entities: `&amp;` -> `&amp;amp;`.
    );
}
