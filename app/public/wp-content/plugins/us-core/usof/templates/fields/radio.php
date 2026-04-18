<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Radio
 *
 * Radio buttons selector
 *
 * @var   $name  string Field name
 * @var   $id    string Field ID
 * @var   $field array Field options
 *
 * @param $field ['title'] string Field title
 * @param $field ['description'] string Field title
 * @param $field ['options'] array List of key => title pairs
 *
 * @var   $value array List of checked keys
 */

$output = '';

// Add to the output of radio buttons
foreach ( us_arr_path( $field, 'options', array() ) as $key => $label ) {
	$radio_atts = us_implode_atts( array(
		// Note: If a name is given in this place, the browser will look at the context, if the buttons
		// are in `<form>...</form>` it will toggle selection by name within the form, if there is no form
		// it will toggle selection within the whole page. This leads to incorrect selection because there
		// may be the same radio button names for different elements on the page. The value is in a hidden field,
		// and radio buttons are needed only for UI.
		'name' => '', // don't set a name!
		'type' => 'radio',
		'value' => $key,
	) );
	$output .= '<label title="' . esc_attr( $label ) . '">';
	$output .= '<input' . $radio_atts . checked( $value, $key, /* Default */FALSE ) . '>';

	// Output icons instead of labels if set
	$output .= '<span class="usof-radio-value">';
	$output .= ! empty( $field['labels_as_icons'] )
		? '<i class="' . esc_attr( str_replace( '*', $key, $field['labels_as_icons'] ) ) . '"></i>'
		: strip_tags( $label );
	$output .= '</span>';

	$output .= '</label>';
}

// Hidden field for correct data transfer via POST and uniqueness of buttons outside the form
$input_atts = array(
	'name' => $name, // Name to define in GET/POST/REQUEST
	'type' => 'hidden',
	'value' => $value,
);

// Field for editing in WPBakery
// Via the `wpb_vc_param_value` class WPBakery receives the final value
if ( isset( $field['us_vc_field'] ) ) {
	$input_atts['class'] = 'wpb_vc_param_value';
}

$output .= '<input' . us_implode_atts( $input_atts ) . '>';

echo '<div class="usof-radio">' . $output . '</div>';
