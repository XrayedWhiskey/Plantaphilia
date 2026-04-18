<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Output Post Taxonomy element
 *
 * @var $taxonomy_name string Taxonomy name
 * @var $link string Link type: 'post' / 'archive' / 'custom' / 'none'
 * @var $custom_link array
 * @var $color string Custom color
 * @var $icon string Icon name
 * @var $design_options array
 *
 * @var $classes string
 * @var $id string
 */

global $us_grid_item_type;

// Cases when the element shouldn't be shown
if ( $us_elm_context == 'grid' AND $us_grid_item_type == 'term' ) {
	return;
} elseif ( $us_elm_context == 'shortcode' AND is_archive() ) {
	return;
} elseif (
	(
		empty( $taxonomy_name )
		OR ! taxonomy_exists( $taxonomy_name )
	)
	AND ! usb_is_post_preview()
) {
	return;
}

// In Live Builder for Reusable Block / Page Template show placeholder for shortcode
if ( usb_is_template_preview() AND $us_elm_context == 'shortcode' ) {
	$dummy_term = (object) array(
		'term_id' => 0,
		'slug' => $taxonomy_name,
		'name' => $taxonomy_name,
	);
	$terms = array( $dummy_term );
} else {
	$terms = get_the_terms( get_the_ID(), $taxonomy_name );
}

if ( ! is_array( $terms ) OR count( $terms ) == 0 ) {

	// Output empty container for USBuilder
	if ( usb_is_post_preview() ) {
		echo '<div class="w-post-elm"></div>';
	}
	return;
}

// Sorting terms from woocommerce
if ( strpos( $taxonomy_name, 'pa_' ) === 0 ) {
	$_terms = array();
	foreach ( $terms as $term ) {
		if ( ( $order = get_term_meta( $term->term_id, 'order', TRUE ) ) == FALSE OR ! is_numeric( $order ) ) {
			$order = count( $_terms ) + 1;
		}
		$_terms[ $order ] = $term;
	}
	ksort( $_terms );
	$terms = $_terms;
}

$_atts['class'] = 'w-post-elm post_taxonomy';
$_atts['class'] .= isset( $classes ) ? $classes : '';
$_atts['class'] .= ' style_' . $style;

if ( $color_link ) {
	$_atts['class'] .= ' color_link_inherit';
}

// Show color swatches (only for product attributes)
if ( $show_color_swatch ) {
	$_atts['class'] .= ' with_color_swatch';
	if ( $hide_color_swatch_label ) {
		$_atts['class'] .= ' hide_color_swatch_label';
	}
}

if ( ! empty( $el_id ) AND $us_elm_context == 'shortcode' ) {
	$_atts['id'] = $el_id;
}

$text_before = trim( (string) $text_before );
if ( $text_before != '' ) {
	$text_before = '<span class="w-post-elm-before">' . $text_before . ' </span>';
}

$text_after = trim( (string) $text_after );
if ( $text_after !== '' ) {
	$text_after = '<span class="w-post-elm-after"> ' . $text_after . '</span>';
}

// Output the element
$output = '<div' . us_implode_atts( $_atts ) . '>';
if ( ! empty( $icon ) ) {
	$output .= us_prepare_icon_tag( $icon );
}
$output .= $text_before;
if ( $style == 'badge' AND count( $terms ) > 1 ) {
	$output .= '<div class="w-post-elm-list">';
}

$i = 1;
foreach ( $terms as $term ) {

	// Set Button Style class
	if ( $style == 'badge' ) {

		// Skip the "badge" style
		if ( $btn_style == 'badge' ) {
			$btn_class = 'us-btn-style_badge';
		} else {
			$btn_class = us_get_btn_class( $btn_style );
		}

		$btn_atts['class'] = 'w-btn ' . $btn_class . ' term-' . $term->term_id . ' term-' . $term->slug;
	} else {
		$btn_atts['class'] = 'term-' . $term->term_id . ' term-' . $term->slug;
	}

	// Get link attributes
	$link_atts = us_generate_link_atts( $link, /* additional data */array( 'term_id' => $term->term_id ) );

	// Show color swatches (only for product attributes)
	if ( $show_color_swatch AND strpos( $taxonomy_name, 'pa_' ) === 0 ) {
		$color_swatch_style = '';
		if ( $color_swatch = (string) get_term_meta( $term->term_id, 'color_swatch', /* single */TRUE ) ) {
			$color_swatch_style = ' style="background:' . esc_attr( $color_swatch ) . '"';
		}
		$term_name = '<span class="w-color-swatch" title="' . esc_attr( $term->name ) . '"' . $color_swatch_style . '></span>';
		$term_name .= '<span>' . $term->name . '</span>';

	} else {
		$term_name = $term->name;
	}

	// Wrap a name with a span for correct display buttons styles
	if ( $style == 'badge' ) {
		$term_name = '<span class="w-btn-label">' . $term_name . '</span>';
	}

	if ( ! empty( $link_atts['href'] ) ) {
		$output .= '<a' . us_implode_atts( $btn_atts + $link_atts ) . '>' . $term_name . '</a>';
	} else {
		$output .= '<span' . us_implode_atts( $btn_atts ) . '>' . $term_name . '</span>';
	}

	// Output a separator after anchor except the last one
	if (
		$style != 'badge'
		AND $i != count( $terms )
		AND ! $hide_color_swatch_label
	) {
		$output .= '<b>' . $separator . '</b>';
	}
	$i++;
}

if ( $style == 'badge' AND count( $terms ) > 1 ) {
	$output .= '</div>';
}
$output .= $text_after;
$output .= '</div>';

echo $output;
