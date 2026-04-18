<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: image_slider
 */

$misc = us_config( 'elements_misc' );
$conditional_params = us_config( 'elements_conditional_options' );
$design_options_params = us_config( 'elements_design_options' );
$effect_options_params = us_config( 'elements_effect_options' );

/**
 * @return array
 */
return array(
	'title' => __( 'Image Slider', 'us' ),
	'category' => __( 'Interactive', 'us' ),
	'icon' => 'fas fa-images',
	'params' => us_set_params_weight(

		// General section
		array(
			'ids' => array(
				'title' => us_translate( 'Images' ),
				'type' => 'upload',
				'is_multiple' => TRUE,
				'dynamic_values' => TRUE,
				'extension' => 'png,jpg,jpeg,gif,svg', // sets available file types
				'usb_preview' => TRUE,
			),
			'include_post_thumbnail' => array(
				'type' => 'switch',
				'switch_text' => __( 'Include Featured image', 'us' ),
				'std' => 0,
				'classes' => 'for_above',
				'usb_preview' => TRUE,
			),
			'meta' => array(
				'type' => 'switch',
				'switch_text' => __( 'Show image title and description', 'us' ),
				'std' => 0,
				'usb_preview' => TRUE,
			),
			'orderby' => array(
				'type' => 'switch',
				'switch_text' => us_translate( 'Random Order' ),
				'std' => 0,
				'usb_preview' => TRUE,
			),
			'has_ratio' => array(
				'switch_text' => __( 'Set Aspect Ratio', 'us' ),
				'type' => 'switch',
				'std' => 0,
				'usb_preview' => TRUE,
			),
			'ratio' => array(
				'type' => 'select',
				'options' => array(
					'1x1' => '1x1 ' . __( 'square', 'us' ),
					'4x3' => '4x3 ' . __( 'landscape', 'us' ),
					'3x2' => '3x2 ' . __( 'landscape', 'us' ),
					'16x9' => '16:9 ' . __( 'landscape', 'us' ),
					'2x3' => '2x3 ' . __( 'portrait', 'us' ),
					'3x4' => '3x4 ' . __( 'portrait', 'us' ),
					'custom' => __( 'Custom', 'us' ),
				),
				'std' => '1x1',
				'classes' => 'for_above',
				'show_if' => array( 'has_ratio', '=', 1 ),
				'usb_preview' => TRUE,
			),
			'ratio_width' => array(
				'placeholder' => us_translate( 'Width' ),
				'type' => 'text',
				'std' => '21',
				'cols' => 2,
				'classes' => 'for_above',
				'show_if' => array( 'ratio', '=', 'custom' ),
				'usb_preview' => TRUE,
			),
			'ratio_height' => array(
				'placeholder' => us_translate( 'Height' ),
				'type' => 'text',
				'std' => '9',
				'cols' => 2,
				'classes' => 'for_above',
				'show_if' => array( 'ratio', '=', 'custom' ),
				'usb_preview' => TRUE,
			),
			'img_fit' => array(
				'title' => __( 'Image Fit', 'us' ),
				'type' => 'select',
				'options' => array(
					'scaledown' => __( 'Initial', 'us' ),
					'contain' => __( 'Fit to Area', 'us' ),
					'cover' => __( 'Fill Area', 'us' ),
				),
				'std' => 'scaledown',
				'cols' => 2,
				'usb_preview' => array(
					'mod' => 'fit',
				),
			),
			'img_size' => array(
				'title' => __( 'Image Size', 'us' ),
				'description' => $misc['desc_img_sizes'],
				'type' => 'select',
				'options' => us_get_image_sizes_list(),
				'std' => 'large',
				'cols' => 2,
				'usb_preview' => TRUE,
			),
			'style' => array(
				'title' => __( 'Images Style', 'us' ),
				'type' => 'select',
				'options' => array(
					'none' => us_translate( 'None' ),
					'phone6-1' => __( 'Phone 6 Black Realistic', 'us' ),
					'phone6-2' => __( 'Phone 6 White Realistic', 'us' ),
					'phone6-3' => __( 'Phone 6 Black Flat', 'us' ),
					'phone6-4' => __( 'Phone 6 White Flat', 'us' ),
				),
				'std' => 'none',
				'show_if' => array( 'has_ratio', '=', 0 ),
				'context' => array( 'shortcode' ),
				'usb_preview' => TRUE, // Note: Generating some properties on the backend
			),

			// Slider Options
			'fullscreen' => array(
				'type' => 'switch',
				'switch_text' => __( 'Full Screen view', 'us' ),
				'std' => 0,
				'group' => __( 'Slider', 'us' ),
				'usb_preview' => TRUE,
			),
			'autoplay' => array(
				'type' => 'switch',
				'switch_text' => __( 'Auto Rotation', 'us' ),
				'std' => 0,
				'context' => array( 'shortcode' ),
				'group' => __( 'Slider', 'us' ),
				'usb_preview' => TRUE,
			),
			'pause_on_hover' => array(
				'type' => 'switch',
				'switch_text' => __( 'Pause on hover', 'us' ),
				'std' => 1,
				'show_if' => array( 'autoplay', '=', 1 ),
				'context' => array( 'shortcode' ),
				'group' => __( 'Slider', 'us' ),
				'usb_preview' => TRUE,
			),
			'autoplay_period' => array(
				'title' => __( 'Auto Rotation Interval', 'us' ),
				'type' => 'slider',
				'std' => '3s',
				'options' => array(
					's' => array(
						'min' => 1.0,
						'max' => 9.0,
						'step' => 0.5,
					),
				),
				'show_if' => array( 'autoplay', '=', 1 ),
				'context' => array( 'shortcode' ),
				'group' => __( 'Slider', 'us' ),
				'usb_preview' => TRUE,
			),
			'arrows' => array(
				'title' => __( 'Prev/Next arrows', 'us' ),
				'type' => 'select',
				'options' => array(
					'always' => __( 'Show always', 'us' ),
					'hover' => __( 'Show on hover', 'us' ),
					'hide' => us_translate( 'Hide' ),
				),
				'std' => 'always',
				'group' => __( 'Slider', 'us' ),
				'usb_preview' => TRUE,
			),
			'nav' => array(
				'title' => __( 'Additional Navigation', 'us' ),
				'type' => 'radio',
				'options' => array(
					'none' => us_translate( 'None' ),
					'dots' => __( 'Dots', 'us' ),
					'thumbs' => __( 'Thumbnails', 'us' ),
				),
				'std' => 'none',
				'group' => __( 'Slider', 'us' ),
				'usb_preview' => TRUE,
			),
			'thumbs_width' => array(
				'title' => __( 'Thumbnails Width', 'us' ),
				'type' => 'slider',
				'options' => array(
					'px' => array(
						'min' => 50,
						'max' => 150,
						'step' => 10,
					),
					'rem' => array(
						'min' => 3.0,
						'max' => 10.0,
						'step' => 0.5,
					),
				),
				'std' => '4rem',
				'show_if' => array( 'nav', '=', 'thumbs' ),
				'group' => __( 'Slider', 'us' ),
				'usb_preview' => TRUE,
			),
			'thumbs_gap' => array(
				'title' => __( 'Gap between Thumbnails', 'us' ),
				'type' => 'slider',
				'options' => array(
					'px' => array(
						'min' => 0,
						'max' => 20,
					),
				),
				'std' => '4px',
				'show_if' => array( 'nav', '=', 'thumbs' ),
				'group' => __( 'Slider', 'us' ),
				'usb_preview' => TRUE,
			),
			'transition' => array(
				'title' => __( 'Transition Effect', 'us' ),
				'type' => 'radio',
				'options' => array(
					'slide' => __( 'Slide', 'us' ),
					'crossfade' => __( 'Fade', 'us' ),
				),
				'std' => 'slide',
				'cols' => 2,
				'group' => __( 'Slider', 'us' ),
				'usb_preview' => TRUE,
			),
			'transition_speed' => array(
				'title' => __( 'Transition Duration', 'us' ),
				'type' => 'slider',
				'std' => '250ms',
				'options' => array(
					'ms' => array(
						'min' => 0,
						'max' => 2000,
						'step' => 50,
					),
				),
				'cols' => 2,
				'group' => __( 'Slider', 'us' ),
				'usb_preview' => TRUE,
			),
		),

		$effect_options_params,
		$conditional_params,
		$design_options_params
	),
	'usb_init_js' => '$elm.usImageSlider()',
);
