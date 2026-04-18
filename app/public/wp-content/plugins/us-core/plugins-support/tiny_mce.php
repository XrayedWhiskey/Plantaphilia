<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * TinyMCE Support
 *
 * @link https://www.tinymce.com
 */

function us_tiny_mce_color_pickers( $init ) {

	$custom_colours = '';
	$predefined_colors = array(
		'color_content_primary',
		'color_content_secondary',
		'color_content_heading',
		'color_content_text',
		'color_content_faded',
		'color_content_border',
		'color_content_bg_alt',
		'color_content_bg',
	);
	foreach ( $predefined_colors as $color ) {
		$color = us_get_color( $color, /* Gradient */ FALSE, /* css_var */ FALSE );
		$color = us_rgba2hex( $color );
		$color = substr( $color, 1 );
		$custom_colours .= "\"$color\", \"#$color\",";
	}

	$default_colors = '
		"000000", "Black",
		"993300", "Burnt orange",
		"333300", "Dark olive",
		"003300", "Dark green",
		"003366", "Dark azure",
		"000080", "Navy Blue",
		"333399", "Indigo",
		"333333", "Very dark gray",
		"800000", "Maroon",
		"FF6600", "Orange",
		"808000", "Olive",
		"008000", "Green",
		"008080", "Teal",
		"0000FF", "Blue",
		"666699", "Grayish blue",
		"808080", "Gray",
		"FF0000", "Red",
		"FF9900", "Amber",
		"99CC00", "Yellow green",
		"339966", "Sea green",
		"33CCCC", "Turquoise",
		"3366FF", "Royal blue",
		"800080", "Purple",
		"999999", "Medium gray",
		"FF00FF", "Magenta",
		"FFCC00", "Gold",
		"FFFF00", "Yellow",
		"00FF00", "Lime",
		"00FFFF", "Aqua",
		"00CCFF", "Sky blue",
		"993366", "Red violet",
		"FFFFFF", "White",
		"FF99CC", "Pink",
		"FFCC99", "Peach",
		"FFFF99", "Light yellow",
		"CCFFCC", "Pale green",
		"CCFFFF", "Pale cyan",
		"99CCFF", "Light sky blue",
		"CC99FF", "Plum"
	';

	$init['textcolor_map'] = '[' . $custom_colours . $default_colors . ']';
	$init['textcolor_rows'] = 6;

	return $init;
}

add_filter( 'tiny_mce_before_init', 'us_tiny_mce_color_pickers' );


if ( ! function_exists( 'us_tiny_mce_tag_span' ) ) {
	add_filter( 'tiny_mce_before_init', 'us_tiny_mce_tag_span', 501, 1 );

	/**
	 * Allows the use of the span tag in TinyMCE.
	 *
	 * @param array $mce_init An array with TinyMCE config.
	 * @return array $mce_init Returns an extended array with the TinyMCE config.
	 */
	function us_tiny_mce_tag_span( $mce_init ) {
		if ( ! isset( $mce_init['extended_valid_elements'] ) ) {
			$mce_init['extended_valid_elements'] = 'span[*]';
		} else {
			$mce_init['extended_valid_elements'] .= ',span[*]';
		}
		return $mce_init;
	}
}
