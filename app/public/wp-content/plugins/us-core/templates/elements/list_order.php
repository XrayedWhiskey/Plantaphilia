<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: [us_list_order]
 *
 * @var string $shortcode Current shortcode name
 * @var string $shortcode_base The original called shortcode name (differs if called an alias)
 * @var string $classes Extend class names
 *
 * @param string $el_class Extra class name
 * @param string $el_id Element ID
 * @param string $orderby_items Order options
 * @param string $first_label First Value Label
 * @param string $text_before Text before dropdown
 * @param bool $width_full Stretch to the full width
 */

// Never output inside list items or specific Reusable Blocks
global $us_grid_outputs_items, $us_is_page_block_in_no_results, $us_is_page_block_in_menu;
if (
	$us_grid_outputs_items
	OR $us_is_page_block_in_no_results
	OR $us_is_page_block_in_menu
) {
	return;
}

// Never output List Order on AMP
if ( us_amp() ) {
	return;
}

// Don't output the List Order if there are no items for it
if ( empty( $orderby_items ) AND ! usb_is_post_preview() ) {
	return;
}

static $us_list_orderby_labels;
if ( ! $us_list_orderby_labels ) {
	$us_list_orderby_labels = (array) us_config( 'elements/list_order.params.orderby_items.params.value.options' );
}

$_atts = array(
	'class' => 'w-order for_list',
	'action' => '',
	'method' => 'post',
	'onsubmit' => 'return false;',
);
$_atts['class'] .= $width_full ? ' width_full' : '';
$_atts['class'] .= $classes ?? '';

$select_atts = array(
	'id' => $shortcode . '_' . us_uniqid(),
	'name' => 'list_order',
	'aria-label' => us_translate( 'Order' ),
);

if ( $text_before ) {
	$label_atts = array(
		'for' => $select_atts['id'],
		'class' => 'w-order-label',
	);
	$text_before = '<label' . us_implode_atts( $label_atts ) . '>' . strip_tags( $text_before ) . '</label>';
}

if ( is_string( $orderby_items ) ) {
	$orderby_items = json_decode( urldecode( $orderby_items ), TRUE );
}
if ( ! is_array( $orderby_items ) ) {
	$orderby_items = array();
}
foreach ( $orderby_items as &$option ) {
	$option_value = $option['value'];
	$custom_field = $option['custom_field'] ?? '';

	if ( $option_value == 'custom' ) {
		$option['value'] .= ',' . $custom_field;
	}
	if ( $option['custom_field_numeric'] ) {
		$option['value'] .= ',numeric';
	}
	if ( $option['invert'] ) {
		$option['value'] .= ',asc';
	}

	if ( empty( $option['label'] ) ) {
		$option['label'] = $us_list_orderby_labels[ $option_value ];

		if ( $option_value == 'custom' AND $custom_field ) {
			$option['label'] .= ': ' . $custom_field;
		}
		if ( $option['invert'] ) {
			$option['label'] .= ' | ' . __( 'Invert order', 'us' );
		}
	}
}
unset( $option );

// Output the element
$output = '<form ' . us_implode_atts( $_atts ) . '>';
$output .= $text_before;
$output .= '<div class="w-order-select">';
$output .= '<select ' . us_implode_atts( $select_atts ) .  '>';

if ( $first_label ) {
	$output .= '<option value="">' . esc_html( $first_label ) . '</option>';
}

foreach ( $orderby_items as $option ) {
	$output .= '<option value="' . esc_attr( $option['value'] ) . '">' . esc_html( $option['label'] ) . '</option>';
}

$output .= '</select>';
$output .= '</div>'; // .w-order-select
$output .= '</form>';// .w-order

echo $output;
