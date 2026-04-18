<?php defined( 'ABSPATH' ) or die( 'This script cannot be accessed directly.' );

/**
 * Add to Favorites element
 */

/** @var string $label_before_adding
 * @var string $label_after_adding
 * @var string $message_for_non_registered
 * @var bool $show_icon
 */

global $us_grid_item_type;

// Cases when the element shouldn't be shown
if ( $us_elm_context == 'grid' AND $us_grid_item_type == 'term' ) {
	return;
} elseif ( $us_elm_context == 'shortcode' AND is_archive() ) {
	return;
}

$_atts['class'] = 'w-btn-wrapper for_add_to_favs';
$_atts['class'] .= $classes ?? '';

// When some values are set in Design options, add the specific classes
if ( us_design_options_has_property( $css, array( 'width', 'max-width' ) ) ) {
	$_atts['class'] .= ' has_width';
}
if ( us_design_options_has_property( $css, array( 'height', 'max-height' ) ) ) {
	$_atts['class'] .= ' has_height';
}
if ( us_design_options_has_property( $css, array( 'background-color' ) ) ) {
	$_atts['class'] .= ' has_bg_color';
}

if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

$btn_atts['class'] = 'w-btn us_add_to_favs ';
$btn_atts['class'] .= $style ? us_get_btn_class( $style ) : 'default';
$btn_atts['class'] .= ' icon_atleft';
$btn_atts['type'] = 'button';

$post_ID = us_get_current_id();

if ( in_array( $post_ID, us_get_user_favorite_post_ids() ) ) {
	$btn_atts['class'] .= ' added';
	$btn_label = $label_after_adding;
} else {
	$btn_label = $label_before_adding;
}

$js_data = array(
	'post_ID' => $post_ID,
	'labelAfterAdding' => $label_after_adding,
	'labelBeforeAdding' => $label_before_adding,
);

if ( $btn_label == '' ) {
	$btn_atts['class'] .= ' text_none';
}

// Output the element
$output = '<div' . us_implode_atts( $_atts ) . '>';
$output .= '<button' . us_implode_atts( $btn_atts ) . us_pass_data_to_js( $js_data ) . '>';

if ( $show_icon ) {
	$output .= '<i class="far fa-heart"></i>';
}

if ( $btn_label != '' OR usb_is_preview() ) {
	$output .= '<span class="w-btn-label">' . strip_tags( $btn_label, '<br><strong>' ) . '</span>';
}
$output .= '</button>';

if ( ! is_user_logged_in() ) {
	$output .= '<span class="us-add-to-favs-tooltip">' . strip_tags( $message_for_non_registered, '<a><br><strong>' ) . '</span>';
}

$output .= '</div>';

echo $output;
