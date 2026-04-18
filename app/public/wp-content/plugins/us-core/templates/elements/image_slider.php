<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_image_slider
 *
 * Dev note: if you want to change some of the default values or acceptable attributes, overload the shortcodes config.
 *
 * @var $shortcode      string Current shortcode name
 * @var $shortcode_base string The original called shortcode name (differs if called an alias)
 * @var $content        string Shortcode's inner content
 * @var $ids            string|int Dynamic value or ids of images
 */

$_atts['class'] = 'w-slider';
$_atts['class'] .= isset( $classes ) ? $classes : '';
$_atts['class'] .= ' style_' . $style;
$_atts['class'] .= ' fit_' . $img_fit;
$_atts['class'] .= ' nav_' . $nav;
$_atts['style'] = '';

// When some values are set in Design options, add the specific classes
if ( us_design_options_has_property( $css, 'border-radius' ) ) {
	$_atts['class'] .= ' has_border_radius';
}

if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

// If we are in WPB front end editor mode, make sure the slider has an ID
if ( function_exists( 'vc_is_page_editable' ) AND vc_is_page_editable() AND empty( $_atts['id'] ) ) {
	$_atts['id'] = us_uniqid();
}

if ( strpos( $ids, '_product_image_gallery' ) !== FALSE ) {
	$_atts['class'] .= ' for_product_image_gallery';
}

$dynamic_value_is_set = FALSE;
if ( preg_match( '/{{([\dA-z\/\|\-_]+)}}/', $ids ) ) {
	$dynamic_value_is_set = TRUE;
	$ids = us_replace_dynamic_value( $ids, /* acf_format */ FALSE );
}

// Include Featured image
if ( $include_post_thumbnail AND $post_thumbnail_id = get_post_thumbnail_id() ) {
	$ids = $post_thumbnail_id . ',' . $ids;
}

// If a dynamic value is set but empty, do not output a slider
if (
	$dynamic_value_is_set
	AND empty( $ids )
	AND ! usb_is_preview()
) {
	return;
}

// Royal Slider options
$js_options = array(
	'autoScaleSlider' => TRUE,
	'addActiveClass' => TRUE,
	'loop' => TRUE,
	'fadeInLoadedSlide' => FALSE,
	'slidesSpacing' => 0,
	'imageScalePadding' => 0,
	'numImagesToPreload' => 2,
	'arrowsNav' => ( $arrows != 'hide' ),
	'arrowsNavAutoHide' => ( $arrows == 'hover' ),
	'transitionType' => ( $transition == 'crossfade' ) ? 'fade' : 'move',
	'transitionSpeed' => (int) $transition_speed,
	'block' => array(
		'moveEffect' => 'none',
		'speed' => 300,
	),
	'thumbs' => array(
		'fitInViewport' => FALSE,
		'firstMargin' => FALSE,
		'spacing' => (int) $thumbs_gap,
	),
);
if ( $nav == 'dots' ) {
	$js_options['controlNavigation'] = 'bullets';

} elseif ( $nav == 'thumbs' ) {
	$js_options['controlNavigation'] = 'thumbnails';
	$_atts['style'] .= '--thumbs-width:' . $thumbs_width . ';';
	$_atts['style'] .= '--thumbs-gap:' . $thumbs_gap . ';';

} else {
	$js_options['controlNavigation'] = 'none';
}

if ( $autoplay AND $autoplay_period ) {
	$js_options['autoplay'] = array(
		'enabled' => TRUE,
		'pauseOnHover' => $pause_on_hover ? TRUE : FALSE,
		'delay' => (float) $autoplay_period * 1000,
	);
}

if ( $fullscreen ) {
	$js_options['fullscreen'] = array( 'enabled' => TRUE );
}

// Disable slider auto-scale if the height is set
if ( us_design_options_has_property( $css, 'height' ) ) {
	$js_options['autoScaleSlider'] = FALSE;
}

// Set Aspect Ratio values
if ( $has_ratio ) {
	$ratio_array = us_get_aspect_ratio_values( $ratio, $ratio_width, $ratio_height );
	$_atts['class'] .= ' has_ratio';
	$_atts['style'] .= sprintf( '--aspect-ratio: %s/%s;', $ratio_array[0], $ratio_array[1] );
	$js_options['autoScaleSlider'] = FALSE;
}

if ( ! in_array( $img_size, get_intermediate_image_sizes() ) ) {
	$img_size = 'full';
}

// Getting images
$query_args = array(
	'include' => $ids,
	'post_status' => 'inherit',
	'post_type' => 'attachment',
	'post_mime_type' => 'image',
	'orderby' => $orderby ? 'rand' : 'post__in',
	'numberposts' => empty( $ids ) ? 3 : -1,
);
$attachments = get_posts( $query_args );

// When no images found, we need to show placeholders instead
if ( ! is_array( $attachments ) OR empty( $attachments ) ) {
	$attachments = array( 0 => '', 1 => '', 2 => '' );
}

$_atts['class'] .= ' count_' . count( $attachments );

// Hide thumbnails if images less than 2
if ( count( $attachments ) < 2 ) {
	$js_options['controlNavigation'] = 'none';
}

$images_html = '';
foreach ( $attachments as $i => $attachment ) {

	// Set fallback placeholders
	if ( empty( $attachment ) ) {
		$image = array(
			0 => US_CORE_URI . '/assets/images/placeholder.svg',
			1 => '1024',
			2 => '1024',
		);
	} else {
		$image = wp_get_attachment_image_src( $attachment->ID, $img_size );
	}

	// Skip not existing images
	if ( ! $image ) {
		continue;
	}

	// Correct width and height for SVG files
	if ( preg_match( '~\.svg$~', $image[0] ) ) {

		$size_array = us_get_image_size_params( $img_size );
		if ( $size_array['width'] ) {
			$image[1] = $image[2] = $size_array['width'];
		} elseif ( $size_array['height'] ) {
			$image[1] = $image[2] = $size_array['height'];
		} else {
			$image[1] = $image[2] = '2000'; // fallback for non-numeric values
		}
	}

	$full_image_attr = '';
	if ( ! empty( $attachment ) ) {
		if ( $fullscreen ) {
			$full_image = wp_get_attachment_image_url( $attachment->ID, 'full' );
			if ( ! $full_image ) {
				$full_image = $image[0];
			}
			$full_image_attr = ' data-rsBigImg="' . $full_image . '"';
		}

		// Get Alt
		$img_alt = trim( strip_tags( get_post_meta( $attachment->ID, '_wp_attachment_image_alt', TRUE ) ) );

		// Use the Caption as a Title
		$image_title = trim( strip_tags( $attachment->post_excerpt ) );

		// If not, Use the Alt
		if ( empty( $image_title ) ) {
			$image_title = $img_alt;
		}

		// If no Alt, use the Title
		if ( empty( $image_title ) ) {
			$image_title = trim( strip_tags( $attachment->post_title ) );
		}

	} else {
		$image_title = us_translate( 'Title' ); // set fallback title
		$img_alt = '';
	}

	$images_html .= '<div class="rsContent">';

	// For the first image only
	if ( $i == 0 ) {
		if ( $js_options['autoScaleSlider'] ) {
			$js_options['autoScaleSliderWidth'] = $image[1];
			$js_options['autoScaleSliderHeight'] = $image[2];
		}
		$first_image_atts = array(
			'src' => $image[0],
			'width' => $image[1],
			'height' => $image[2],
			'alt' => $img_alt,
			'loading' => 'lazy',
		);
	}
	$images_html .= '<a class="rsImg" data-rsw="' . $image[1] . '" data-rsh="' . $image[2] . '"' . $full_image_attr . ' href="' . $image[0] . '">';
	$images_html .= '<span data-alt="' . $img_alt . '"></span>';
	$images_html .= '</a>';

	// Thumbnails Navigation
	if ( $nav == 'thumbs' ) {
		if ( ! empty( $attachment ) ) {
			$images_html .= wp_get_attachment_image( $attachment->ID, 'thumbnail', FALSE, array( 'class' => 'rsTmb' ) );
		} else {
			$images_html .= '<img class="rsTmb" src="' . US_CORE_URI . '/assets/images/placeholder.svg" alt="">';
		}
	}

	// Title and Description
	if ( $meta ) {
		$images_html .= '<div class="rsABlock" data-fadeEffect="false" data-moveEffect="none">';
		if ( $image_title != '' ) {
			$images_html .= '<div class="w-slider-item-title">' . $image_title . '</div>';
		}
		if ( ! empty( $attachment->post_content ) ) {
			$images_html .= '<div class="w-slider-item-description">' . $attachment->post_content . '</div>';
		}
		$images_html .= '</div>';
	}

	$images_html .= '</div>';
}

// Add extra mockup as backround for Phone Styles
$inner_style = '';
global $us_template_directory_uri;
if ( $style == 'phone6-1' ) {
	$inner_style = ' style="background-image: url(' . esc_url( $us_template_directory_uri ) . '/img/phone-6-black-real.png)"';
}
if ( $style == 'phone6-2' ) {
	$inner_style = ' style="background-image: url(' . esc_url( $us_template_directory_uri ) . '/img/phone-6-white-real.png)"';
}
if ( $style == 'phone6-3' ) {
	$inner_style = ' style="background-image: url(' . esc_url( $us_template_directory_uri ) . '/img/phone-6-black-flat.png)"';
}
if ( $style == 'phone6-4' ) {
	$inner_style = ' style="background-image: url(' . esc_url( $us_template_directory_uri ) . '/img/phone-6-white-flat.png)"';
}

wp_enqueue_script( 'us-royalslider' );

$output = '<div' . us_implode_atts( $_atts ) . '>';
$output .= '<div class="w-slider-h"' . $inner_style . '>';
if ( ! us_amp() ) {
	$output .= '<div class="royalSlider">' . $images_html . '</div>';
}
// Output first image as fallback on page load
if ( ! empty( $first_image_atts ) ) {
	$output .= '<img' . us_implode_atts( $first_image_atts ) . '>';
}
$output .= '</div>';
if ( ! us_amp() ) {
	$js_options = apply_filters( 'us_image_slider_js_options', $js_options );
	$output .= '<div class="w-slider-json"' . us_pass_data_to_js( $js_options ) . '></div>';
}
$output .= '</div>';

// If we are in WPB front end editor mode, apply JS to the slider
if ( function_exists( 'vc_is_page_editable' ) AND vc_is_page_editable() ) {
	$output .= '<script>
	jQuery( function( $ ) {
		if ( $.isFunction( $.fn.usImageSlider ) ) {
			$( "#' . $_atts['id'] . '" ).usImageSlider();
		}
	} );
	</script>';
}

echo $output;
