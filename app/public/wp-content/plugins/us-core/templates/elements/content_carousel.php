<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_content_carousel
 *
 * @var   $shortcode      string Current shortcode name
 * @var   $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var   $content        string Shortcode's inner content
 * @var   $classes        string Extend class names
 */

wp_enqueue_script( 'us-owl' );

$_atts['class'] = 'w-content-carousel';
$_atts['class'] .= ' items_' . $items;
$_atts['class'] .= ' valign_' . $items_valign;
$_atts['class'] .= $classes ?? '';

// Fix for correct work in Live Builder
if ( usb_is_preview() ) {
	$_atts['class'] .= ' wrap';
}

if ( us_design_options_has_property( $css, array( 'height', 'max-height' ) ) ) {
	$_atts['class'] .= ' has_height';
}

if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

$carousel_atts['class'] = 'owl-carousel';
$carousel_atts['class'] .= ' dotstyle_' . $dots_style;
$carousel_atts['class'] .= ' navstyle_' . $arrows_style;
$carousel_atts['class'] .= ' navpos_' . $arrows_pos;
$carousel_atts['class'] .= ' owl-responsive-2000'; // needed for responsive states switch
$carousel_atts['style'] = '--items-gap:' . $items_gap . ';';

if ( $items == 1 AND $autoheight ) {
	$carousel_atts['class'] .= ' autoheight';
}
if ( $center_item ) {
	$carousel_atts['class'] .= ' center_item';
}
if ( $dots ) {
	$carousel_atts['class'] .= ' with_dots';
}
if ( $arrows ) {
	$carousel_atts['class'] .= ' with_arrows';
	if ( ! empty( $arrows_size ) ) {
		$carousel_atts['style'] .= '--arrows-size:' . $arrows_size . ';';
	}
	if ( ! in_array( $arrows_offset, array( '', '0', '0em', '0px' ) ) ) {
		$carousel_atts['style'] .= '--arrows-offset:' . $arrows_offset . ';';
	}
}

// Owl Carousel options https://owlcarousel2.github.io/OwlCarousel2/docs/api-options.html
$js_options = array(
	'aria_labels' => array(
		'prev' => us_translate( 'Previous' ),
		'next' => us_translate( 'Next' ),
	),
	'autoplayContinual' => (bool) $autoplay_continual,
	'autoplayHoverPause' => (bool) $autoplay_pause_on_hover,
	'autoplayTimeout' => (int) $autoplay_timeout * 1000,
	'margin' => (int) $items_gap,
	'mouseDrag' => (bool) $mouse_drag,
	'responsiveRefreshRate' => 100,
	'slideBy' => ( ! $slide_by_one OR $items == 'auto' ) ? 'page' : '1',
	'smartSpeed' => (int) $transition_speed,
	'touchDrag' => (bool) $touch_drag,
);

// Responsive options https://owlcarousel2.github.io/OwlCarousel2/demos/responsive.html
$breakpoints = array();
if ( is_string( $responsive ) ) {
	$responsive = json_decode( urldecode( $responsive ), TRUE );
}
if ( ! is_array( $responsive ) ) {
	$responsive = array();
}
foreach ( $responsive as $responsive_data ) {
	if ( $responsive_data['breakpoint'] == 'laptops' ) {
		$breakpoint_width = (int) us_get_option( 'laptops_breakpoint' ) + 1;
	} elseif ( $responsive_data['breakpoint'] == 'tablets' ) {
		$breakpoint_width = (int) us_get_option( 'tablets_breakpoint' ) + 1;
	} elseif ( $responsive_data['breakpoint'] == 'mobiles' ) {
		$breakpoint_width = (int) us_get_option( 'mobiles_breakpoint' ) + 1;
	} else {
		$breakpoint_width = (int) $responsive_data['breakpoint_width'];
	}
	$breakpoints[ $breakpoint_width ] = array(
		'autoHeight' => (bool) $responsive_data['autoheight'],
		'autoplay' => (bool) $responsive_data['autoplay'],
		'autoWidth' => ( $responsive_data['items'] == 'auto' ),
		'center' => ( $responsive_data['items'] == 'auto' ) ? FALSE : (bool) $responsive_data['center_item'],
		'dots' => (bool) $responsive_data['dots'],
		'items' => (int) $responsive_data['items'],
		'loop' => usb_is_preview() ? FALSE : (bool) $responsive_data['loop'],
		'nav' => (bool) $responsive_data['arrows'],
		'stagePadding' => (int) $responsive_data['items_offset'],
	);
}
ksort( $breakpoints );

$breakpoint_widths = array_merge( array( 0 ), array_keys( $breakpoints ) ); // e.g. array( 0, 601, 1025, 1381 )

$breakpoint_values = array_values( $breakpoints );

// Options below are NOT duplicated in the $js_options
$breakpoint_values[] = array(
	'autoHeight' => (bool) $autoheight,
	'autoplay' => (bool) $autoplay,
	'autoWidth' => ( $items == 'auto' ),
	'center' => ( $items == 'auto' ) ? FALSE : (bool) $center_item,
	'dots' => (bool) $dots,
	'items' => (int) $items,
	'loop' => usb_is_preview() ? FALSE : (bool) $loop,
	'nav' => (bool) $arrows,
	'stagePadding' => (int) $items_offset,
);

$js_options['responsive'] = array_combine( $breakpoint_widths, $breakpoint_values );

unset( $breakpoints, $breakpoint_widths, $breakpoint_values );

$carousel_atts['onclick'] = us_pass_data_to_js( apply_filters( 'us_content_carousel_js_options', $js_options ), /* onclick */FALSE );

// Output the element
echo '<div' . us_implode_atts( $_atts ) . '>';
echo '<div' . us_implode_atts( $carousel_atts ) . '>';

echo (string) apply_filters( 'us_content_carousel_items_html', do_shortcode( $content ) );

echo '</div>'; // .owl-carousel
echo '</div>'; // .w-content-carousel
