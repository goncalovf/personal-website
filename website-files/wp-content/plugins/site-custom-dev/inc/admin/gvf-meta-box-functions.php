<?php

/**
 * Output a text input box.
 *
 * @param array $field
 */
function gvf_wp_text_input( $field ) {
    global $thepostid, $post;

    $thepostid              = empty( $thepostid ) ? $post->ID : $thepostid;
    $field['placeholder']   = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
    $field['class']         = isset( $field['class'] ) ? $field['class'] : 'short';
    $field['style']         = isset( $field['style'] ) ? $field['style'] : '';
    $field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
    $field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
    $field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
    $field['type']          = isset( $field['type'] ) ? $field['type'] : 'text';
    $field['desc_tip']      = isset( $field['desc_tip'] ) ? $field['desc_tip'] : false;
    $data_type              = empty( $field['data_type'] ) ? '' : $field['data_type'];

    switch ( $data_type ) {
        case 'url':
            $field['class'] .= ' wc_input_url';
            $field['value']  = esc_url( $field['value'] );
            break;

        default:
            break;
    }

    // Custom attribute handling
    $custom_attributes = array();

    if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {

        foreach ( $field['custom_attributes'] as $attribute => $value ) {
            $custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
        }
    }

    ?>
    <div class="field-row <?php esc_attr_e( $field['id'] ) ?>_field">
        <div class="field-wrapper">
            <label for="<?php esc_attr_e( $field['id'] ) ?>"><?php echo wp_kses_post( $field['label'] ) ?></label>
            <span class="text-input-container">
                <input type="<?php esc_attr_e( $field['type'] ) ?>" class="text-input <?php esc_attr_e( $field['class'] ) ?>" style="<?php esc_attr_e( $field['style'] ) ?>" name="<?php esc_attr_e( $field['name'] ) ?>" id="<?php esc_attr_e( $field['id'] ) ?>" value="<?php esc_attr_e( $field['value'] ) ?>" placeholder="<?php esc_attr_e( $field['placeholder'] ) ?>" <?php echo implode( ' ', $custom_attributes ) ?> />
            </span>
        </div>
    </div>
    <?php
}

/**
 * Output a checkbox input box.
 *
 * @param array $field
 */
function gvf_wp_checkbox( $field ) {
    global $thepostid, $post;

    $thepostid              = empty( $thepostid ) ? $post->ID : $thepostid;
    $field['class']         = isset( $field['class'] ) ? $field['class'] : 'checkbox';
    $field['style']         = isset( $field['style'] ) ? $field['style'] : '';
    $field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
    $field['cbvalue']       = isset( $field['cbvalue'] ) ? $field['cbvalue'] : 'yes';
    $field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
    $field['desc_tip']      = isset( $field['desc_tip'] ) ? $field['desc_tip'] : false;

    // Custom attribute handling
    $custom_attributes = array();

    if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {

        foreach ( $field['custom_attributes'] as $attribute => $value ) {
            $custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
        }
    }
    ?>
    <div class="field-row <?php esc_attr_e( $field['id'] ) ?>_field">
        <div class="field-wrapper">
            <span class="checkbox-input-container">
                <input type="checkbox" class="checkbox-input <?php esc_attr_e( $field['class'] ) ?>" style="<?php esc_attr_e( $field['style'] ) ?>" name="<?php esc_attr_e( $field['name'] ) ?>" id="<?php esc_attr_e( $field['id'] ) ?>" value="<?php esc_attr_e( $field['cbvalue'] ) ?>" <?php checked( $field['value'], $field['cbvalue'] ) ?> <?php echo implode( ' ', $custom_attributes ) ?> />
            </span>
            <label for="<?php esc_attr_e( $field['id'] ) ?>"><?php echo wp_kses_post( $field['label'] ) ?></label>
        </div>
    </div>
    <?php
}

/**
 * Output a select input box.
 *
 * @param array $field Data about the field to render.
 */
function gvf_wp_select( $field ) {
    global $thepostid, $post;

    $thepostid = empty( $thepostid ) ? $post->ID : $thepostid;
    $field     = wp_parse_args(
        $field, array(
            'class'             => 'select',
            'style'             => '',
            'wrapper_class'     => '',
            'value'             => get_post_meta( $thepostid, $field['id'], true ),
            'name'              => $field['id'],
            'desc_tip'          => false,
            'custom_attributes' => array(),
        )
    );

    $field_attributes          = (array) $field['custom_attributes'];
    $field_attributes['style'] = $field['style'];
    $field_attributes['id']    = $field['id'];
    $field_attributes['name']  = $field['name'];
    $field_attributes['class'] = $field['class'];
    ?>
    <div class="field-row <?php esc_attr_e( $field['id'] ) ?>_field">
        <div class="field-wrapper">
            <label for="<?php esc_attr_e( $field['id'] ) ?>"><?php echo wp_kses_post( $field['label'] ) ?></label>
            <div class="select-input-container">
                <select <?php echo gvf_implode_html_attributes( $field_attributes ); // WPCS: XSS ok. ?>>
                    <?php
                    if ( isset( $field['options'] ) && ! empty($field['options']) ) :
                        foreach ( $field['options'] as $key => $value ) : ?>
                            <option value="<?php esc_attr_e( $key ) ?>" <?php gvf_selected( $key, $field['value'] ) ?>><?php esc_html_e( $value ) ?></option>
                        <?php endforeach;
                    endif;
                    ?>
                </select>
            </div>
        </div>
    </div>
    <?php
}
