<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Dropdown <select>
 *
 * @action Before the template: 'us_before_template:templates/us_grid/filter-ui-types/dropdown'
 * @action After the template: 'us_after_template:templates/us_grid/filter-ui-types/dropdown'
 * @filter Template variables: 'us_template_vars:templates/us_grid/filter-ui-types/dropdown'
 */

if ( empty( $item_values ) ) {
	return;
}

$select_atts = array(
	'class' => 'w-filter-item-value-select',
	'name' => $item_name,
);

$output = '<select' . us_implode_atts( $select_atts ) . '>';

foreach ( $item_values as $i => $item_value ) {

	$_value = $item_value['value'] ?? $item_value;
	if ( $_value == '' ) {
		continue;
	}

	$_atts = array(
		'class' => '',
		'value' => $_value,
	);

	$_label = '';

	// Prepend non-breaking spaces for visual hierarchy
	if ( ! empty( $item_value['depth'] ) ) {
		$_label .= implode( '', array_fill( 0, $item_value['depth'] - 1, html_entity_decode( '&nbsp;&nbsp;&nbsp;' ) ) );
	}

	$_label .= $item_value['label'] ?? $_value;

	if ( ! empty( $show_amount ) AND isset( $item_value['count'] ) ) {
		$_label .= ' (' . $item_value['count'] . ')';
	}

	$output .= '<option' . us_implode_atts( $_atts ) . '>' . esc_html( apply_filters( 'us_list_filter_value_label', $_label, $_value, $item_name ) ) . '</option>';
}

$output .= '</select>'; // w-filter-item-value-select

echo $output;
