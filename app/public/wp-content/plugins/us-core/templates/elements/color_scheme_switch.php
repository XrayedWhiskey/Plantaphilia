<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output Color Scheme Switch element
 *
 */

$_atts = array(
	'class' => 'w-color-switch',
	'style' => '',
);

$_atts['class'] .= isset( $classes ) ? $classes : '';

if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

if ( $inactive_switch_bg ) {
	$_atts['style'] .= '--color-inactive-switch-bg:' . us_get_color( $inactive_switch_bg ) . ';';
}
if ( $active_switch_bg ) {
	$_atts['style'] .= '--color-active-switch-bg:' . us_get_color( $active_switch_bg ) . ';';
}

$scheme_output = '';
global $us_color_scheme_switch_is_used;

// Output JS and CSS only once if multiple switches are shown
if ( ! $us_color_scheme_switch_is_used AND $color_scheme ) {
	$us_color_scheme_switch_is_used = TRUE;

	$color_schemes = us_get_color_schemes();

	$scheme_output .= '<style id="us-color-scheme-switch-css">';
	$scheme_output .= 'html.us-color-scheme-on {';
	if ( isset( $color_schemes[ $color_scheme ]['values'] ) ) {
		foreach( $color_schemes[ $color_scheme ]['values'] as $color_schemes_option => $color_value ) {
			if ( ! empty( $color_value ) ) {
				$scheme_output .= '--' . str_replace( '_', '-', $color_schemes_option ) . ': ' . us_get_color( $color_value, FALSE, FALSE ) . ';';

				// Add separate values from color pickers that support gradients
				foreach( us_config( 'theme-options.colors.fields' ) as $color_option => $color_option_params ) {
					if ( ! empty( $color_option_params['with_gradient'] ) AND $color_option === $color_schemes_option ) {
						$scheme_output .= '--' . str_replace( '_', '-', $color_schemes_option ) . '-grad: ' . us_get_color( $color_value, TRUE, FALSE ) . ';';
					}
				}

				if ( $color_schemes_option === 'color_content_primary' ) {
					$scheme_output .= '--color-content-primary-faded:' . us_hex2rgba( us_get_color( $color_value, FALSE, FALSE ), 0.15 ) . ';';
				}
			}
		}
	}
	$scheme_output .= '}';
	$scheme_output .= '</style>';
}

// Text before switch
if ( $text_before !== '' OR usb_is_preview() ) {
	$text_before = '<span class="w-color-switch-before">' . $text_before . '</span>';
}

// Text after switch
if ( $text_after !== '' OR usb_is_preview() ) {
	$text_after = '<span class="w-color-switch-after">' . $text_after . '</span>';
}

// Output the element
$output = '<div' . us_implode_atts( $_atts ) . '>';
$output .= $scheme_output;
$output .= '<label>';
$output .= '<input class="screen-reader-text" type="checkbox" name="us-color-scheme-switch"' . checked( ! empty( $_COOKIE['us_color_scheme_switch_is_on'] ), TRUE, FALSE ) . '>';
$output .= $text_before;
$output .= '<span class="w-color-switch-box"><i></i></span>';
$output .= $text_after;
$output .= '</label>';
$output .= '</div>';

echo $output;
