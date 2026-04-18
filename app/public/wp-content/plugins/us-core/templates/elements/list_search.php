<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output list search element
 *
 * @var $text string Placeholder Text
 * @var $icon string
 * @var $icon_size string
 * @var $live_search int
 * @var $classes string
 * @var $id string
 */

$_atts['class'] = 'w-search for_list layout_simple';
$_atts['class'] .= isset( $classes ) ? $classes : '';
$_atts['class'] .= ' iconpos_' . $icon_pos;

if ( $live_search ) {
	$_atts['class'] .= ' live_search';
}

if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}
if ( us_amp() AND empty( $el_id ) ) {
	$_atts['id'] = str_replace( ':', '_', $id );;
}

// Apply filter to button label
$text = us_replace_dynamic_value( $text );

$input_atts = array(
	'aria-label' => $text,
	'name' => 'list_search',
	'placeholder' => $text,
	'type' => 'text',
	'value' => '',
);

// Clickable button
$button_atts = array(
	'aria-label' => us_translate( 'Search' ),
	'class' => 'w-search-form-btn w-btn',
	'type' => 'submit',
);

if ( ! empty( $icon_size ) ) {
	$button_atts['style'] = sprintf( '--icon-size:%s;', $icon_size );
}

// Output the element
$output = '<div' . us_implode_atts( $_atts ) . '>';
$output .= '<form class="w-form-row for_text" role="search" method="get">';

$output .= '<div class="w-form-row-field">';
$output .= '<input' . us_implode_atts( $input_atts ) . '/>';
$output .= '</div>';

$output .= '<button class="w-search-reset" type="button" aria-label="' . us_translate( 'Reset' ) . '">';
$output .= '</button>';

$output .= '<button' . us_implode_atts( $button_atts ) . '>';
if ( ! empty( $icon ) ) {
	$output .= us_prepare_icon_tag( $icon );
}
$output .= '</button>';

$output .= '</form>'; // .w-form-row
$output .= '<div class="w-search-message hidden"></div>';
$output .= '</div>'; // .w-search

echo $output;
