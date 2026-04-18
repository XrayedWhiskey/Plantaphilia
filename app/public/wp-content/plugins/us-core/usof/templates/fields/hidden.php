<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Hidden
 * Simple hidden field.
 *
 * @var string $name Field name
 * @var string $id Field ID
 * @var array $field Field options
 * @var string $field['auto_generate_value_by_switch_on'] Generate unique value when the switch is on
 * @var string $value Current value
 */

$input_atts = array(
	'name' => $name,
	'type' => 'hidden',
	'value' => $value,
);

// Field for editing in WPBakery
// Via the `wpb_vc_param_value` class WPBakery receives the final value
if ( isset( $field['us_vc_field'] ) ) {
	$input_atts['class'] = 'wpb_vc_param_value';
}

echo '<input ' . us_implode_atts( $input_atts ) . ' >';
