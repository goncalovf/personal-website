<?php

/**
 * Display a GVF help tip.
 *
 * @param  string $tip        Help tip text.
 * @param  bool   $allow_html Allow sanitized HTML if true or escape.
 * @return string
 */
function gvf_help_tip( $tip, $allow_html = false ) {
    if ( $allow_html ) {
        $tip = gvf_sanitize_tooltip( $tip );
    } else {
        $tip = esc_attr( $tip );
    }

    return '<span class="gvf-help-tip" data-tip="' . $tip . '"></span>';
}

/**
 * Return the html selected attribute if stringified $value is found in array of stringified $options
 * or if stringified $value is the same as scalar stringified $options.
 *
 * @param   string|int       $value   Value to find within options.
 * @param   string|int|array $options Options to go through when looking for value.
 * @return  string
 */
function gvf_selected( $value, $options ) {
    if ( is_array( $options ) ) {
        $options = array_map( 'strval', $options );
        return selected( in_array( (string) $value, $options, true ), true, false );
    }

    return selected( $value, $options, false );
}

/**
 * Get other templates passing attributes and including the file.
 *
 * @param string $template_name Template name.
 * @param array  $args          Arguments.
 * @param string $template_path Template path.
 * @param string $default_path  Default path.
 */
function gvf_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {

    $template = gvf_locate_template( $template_name, $template_path, $default_path );

    if ( ! empty( $args ) && is_array( $args ) ) {
        extract( $args );
    }

    include $template;
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 * yourtheme/$template_path/$template_name
 * yourtheme/$template_name
 * $default_path/$template_name
 *
 * @param   string  $template_name  Template name.
 * @param   string  $template_path  Template path.
 * @param   string  $default_path   Default path.
 * @return  string
 */
function gvf_locate_template( $template_name, $template_path = '', $default_path = '' ) {
    if ( ! $template_path ) {
        $template_path = gvf()->template_path();
    }

    if ( ! $default_path ) {
        $default_path = gvf()->plugin_path() . '/templates/';
    }

    // Look within passed path within the theme - this is priority.
    $template = locate_template(
        array(
            trailingslashit( $template_path ) . $template_name,
            $template_name,
        )
    );

    // Get default template/.
    if ( ! $template ) {
        $template = $default_path . $template_name;
    }

    // Return what we found.
    return $template;
}

/**
 * Locate a content file and return the path for inclusion.
 *
 * @param   string $file_name
 * @param   string $file_path
 * @return  string
 */
function gvf_locate_content_file( $file_name, $file_path = '' ) {

    if ( ! $file_path ) {
        $file_path = gvf()->content_path();
    }

    $file = '';

    if ( file_exists( STYLESHEETPATH . '/' . trailingslashit( $file_path ) . $file_name ) ) {
        $file = STYLESHEETPATH . '/' . trailingslashit( $file_path ) . $file_name;
    }

    return $file;
}
