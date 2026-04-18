<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode [vc_widget_sidebar]
 */

$_atts['class'] = 'wpb_widgetised_column wpb_content_element';
$_atts['class'] .= $classes ?? '';

if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

// Output the element
$output = '<div'. us_implode_atts( $_atts ) .'>';
$output .= '<div class="wpb_wrapper">';

if ( $title ) {
	$output .= '<h2 class="wpb_heading wpb_widgetised_column_heading">' . esc_html( $title ) . '</h2>';
}

ob_start();
dynamic_sidebar( $sidebar_id );
$output .= ob_get_clean();

$output .= '</div>';
$output .= '</div>';

echo $output;
