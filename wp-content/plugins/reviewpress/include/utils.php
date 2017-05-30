<?php

/**
 * Get the value of a settings field
 *
 * @param string  $option  settings field name
 * @param string  $section the section name this field belongs to
 * @param string  $default default text if it's not found
 * @return string
 */
function review_get_option( $option, $section, $default = '' ) {

    $options = get_option( $section );

    if ( isset( $options[$option] ) ) {
        return $options[$option];
    }

    return $default;
}

?>
