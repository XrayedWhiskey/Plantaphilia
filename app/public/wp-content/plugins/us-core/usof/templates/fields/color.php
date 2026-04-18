<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Theme Options Field: Color
 *
 * Simple color picker
 *
 * @param $field ['title'] string Field title
 * @param $field ['description'] string Field title
 * @param $field ['text'] string Field additional text
 * @param $field ['exclude_dynamic_colors'] bool which type of dynamic colors to exclude from the list of color variables
 * @param $field ['us_vc_field'] bool Field used in Visual Composer
 *
 * @var   $name  string Field name
 * @var   $id    string Field ID
 * @var   $field array Field options
 *
 * @var   $value string Current value
 */

// Check the color value for gradient
if ( preg_match( '~^\#([\da-f])([\da-f])([\da-f])$~', $value, $matches ) ) {
	$value = '#' . $matches[1] . $matches[1] . $matches[2] . $matches[2] . $matches[3] . $matches[3];
}

$atts = array(
	'class' => 'usof-color',
);

if ( isset( $field['clear_pos'] ) ) {
	$atts['class'] .= ' clear_' . $field['clear_pos'];
}

// Disable gradient colorpicker
if ( ! isset( $field['with_gradient'] ) OR $field['with_gradient'] !== FALSE ) {
	$atts['class'] .= ' with_gradient';
}

$show_color_vars_list = us_arr_path( $field, 'exclude_dynamic_colors' ) !== 'all';

if ( $show_color_vars_list ) {
	$atts['class'] .= ' with_color_list';
	if ( strpos( us_arr_path( $field, 'exclude_dynamic_colors', '' ), 'scheme' ) !== FALSE ) {
		$atts['class'] .= ' hide_scheme_vars';
	}
	if ( strpos( us_arr_path( $field, 'exclude_dynamic_colors', '' ), 'custom_field' ) !== FALSE ) {
		$atts['class'] .= ' hide_cf_vars';
	}
}

$input_atts = array(
	'autocomplete' => 'off',
	'class' => 'usof-color-value',
	'name' => $name,
	'type' => 'text',
	'value' => $value,
);

$color_value = preg_match( "/{{([^}]+)}}/", $value )
	? 'white'
	: us_get_color( $value, /* gradient */ TRUE, /* cssvar */ FALSE );

if ( strpos( $value, '_' ) === 0 ) {
	$atts['data-value'] = $color_value;
}

// Output color input setting
$output = '<div' . us_implode_atts( $atts ) . '>';

$output .= '<div class="usof-color-preview" style="background: ' . $color_value . '"></div>';
$output .= '<input' . us_implode_atts( $input_atts ) . ' />';

// Output color variables list
if ( $show_color_vars_list ) {
	$output .= '<div class="usof-color-arrow" title="' . __( 'Select Dynamic Value', 'us' ) . '"></div>';
	$output .= '<div class="usof-color-list"></div>';
}

// Output 'Clear' button, if set
if ( isset( $field['clear_pos'] ) ) {
	$output .= '<div class="usof-color-clear" title="' . us_translate( 'Clear' ) . '"></div>';
}

$output .= '</div>'; // .usof-color

// Field for editing in Visual Composer
if ( isset( $field['us_vc_field'] ) ) {
	// The `js_hidden` class is required to exclude fields from the `$usof.field.$input` search.
	// These fields are required for static pages or other tasks. The `wpb_vc_param_value` class
	// is required to support this field in Visual Composer.
	$vc_input_atts = array(
		'class' => 'wpb_vc_param_value js_hidden',
		'name' => $name,
		'type' => 'hidden',
		'value' => $value,
	);
	$output .= '<input' . us_implode_atts( $vc_input_atts ) . '>';
}

// Output the list of dynamic colors once globally
global $_us_color_vars_included;
if ( empty( $_us_color_vars_included ) OR usb_is_site_settings() ) {
	$color_list = (array) usof_get_color_vars();

	$color_vars = array();
	foreach( $color_list as $colors ) {
		foreach ( $colors as $color ) {
			if ( ! isset( $color['value'] ) ) {
				$color['value'] = us_get_color( $color['name'], TRUE, TRUE );
			}
			$color_vars[ $color['name'] ] = $color['value'];
		}
	}

	$output .= '<script>
		window.$usof = window.$usof || { _$$data: {} };
		window.$usof._$$data.colorList = \'' . json_encode( $color_list, JSON_HEX_APOS ) . '\';
		window.$usof._$$data.colorVars = \'' . json_encode( $color_vars, JSON_HEX_APOS ) . '\';
	</script>';
	$_us_color_vars_included = TRUE;
}

if ( ! empty( $field['text'] ) ) {
	$output .= '<div class="usof-color-text">' . $field['text'] . '</div>';
}

echo $output;
