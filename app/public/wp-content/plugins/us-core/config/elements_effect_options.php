<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * EFFECTS settings for shortcodes
 */

return array(

	'scroll_effect' => array(
		'switch_text' => __( 'Scrolling Effects', 'us' ),
		'type' => 'switch',
		'std' => 0,
		'classes' => 'beta',
		'group' => __( 'Effects', 'us' ),
		'context' => array( 'header', 'shortcode' ),
		'usb_preview' => array(
			array(
				'toggle_class' => 'has_scroll_effects',
			),
			array(
				'scroll_effects' => TRUE,
			),
		),
	),

	// Vertical Shift
	'wrapper_start_translate_y' => array(
		'type' => 'wrapper_start',
		'show_if' => array( 'scroll_effect', '=', 1 ),
		'group' => __( 'Effects', 'us' ),
		'context' => array( 'header', 'shortcode' ),
	),
	'scroll_translate_y' => array(
		'switch_text' => __( 'Vertical Shift', 'us' ),
		'type' => 'switch',
		'std' => 0,
		'group' => __( 'Effects', 'us' ),
		'context' => array( 'header', 'shortcode' ),
		'usb_preview' => array(
			array(
				'toggle_class' => 'has_translate_y',
			),
			array(
				'scroll_effects' => TRUE,
			),
		),
	),
	'scroll_translate_y_direction' => array(
		'type' => 'radio',
		'std' => 'up',
		'options' => array(
			'up' => _x( 'Up', 'direction', 'us' ),
			'down' => _x( 'Down', 'direction', 'us' ),
		),
		'show_if' => array( 'scroll_translate_y', '=', 1 ),
		'group' => __( 'Effects', 'us' ),
		'context' => array( 'header', 'shortcode' ),
		'usb_preview' => array(
			array(
				'attr' => 'data-translate_y_direction',
			),
			array(
				'scroll_effects' => TRUE,
			),
		),
	),
	'scroll_translate_y_speed' => array(
		'title' => __( 'Speed', 'us' ),
		'type' => 'slider',
		'std' => '0.5x',
		'options' => array(
			'x' => array(
				'min' => 0.1,
				'max' => 2.0,
				'step' => 0.1,
			),
		),
		'show_if' => array( 'scroll_translate_y', '=', 1 ),
		'group' => __( 'Effects', 'us' ),
		'context' => array( 'header', 'shortcode' ),
		'usb_preview' => array(
			array(
				'attr' => 'data-translate_y_speed',
			),
			array(
				'scroll_effects' => TRUE,
			),
		),
	),
	'wrapper_end_translate_y' => array(
		'type' => 'wrapper_end',
		'group' => __( 'Effects', 'us' ),
		'context' => array( 'header', 'shortcode' ),
	),

	// Horizontal Shift
	'wrapper_start_translate_x' => array(
		'type' => 'wrapper_start',
		'show_if' => array( 'scroll_effect', '=', 1 ),
		'group' => __( 'Effects', 'us' ),
		'context' => array( 'header', 'shortcode' ),
	),
	'scroll_translate_x' => array(
		'switch_text' => __( 'Horizontal Shift', 'us' ),
		'type' => 'switch',
		'std' => 0,
		'group' => __( 'Effects', 'us' ),
		'context' => array( 'header', 'shortcode' ),
		'usb_preview' => array(
			array(
				'toggle_class' => 'has_translate_x',
			),
			array(
				'scroll_effects' => TRUE,
			),
		),
	),
	'scroll_translate_x_direction' => array(
		'type' => 'radio',
		'std' => 'left',
		'options' => array(
			'left' => _x( 'Left', 'direction', 'us' ),
			'right' => _x( 'Right', 'direction', 'us' ),
		),
		'show_if' => array( 'scroll_translate_x', '=', 1 ),
		'group' => __( 'Effects', 'us' ),
		'context' => array( 'header', 'shortcode' ),
		'usb_preview' => array(
			array(
				'attr' => 'data-translate_x_direction',
			),
			array(
				'scroll_effects' => TRUE,
			),
		),
	),
	'scroll_translate_x_speed' => array(
		'title' => __( 'Speed', 'us' ),
		'type' => 'slider',
		'std' => '0.5x',
		'options' => array(
			'x' => array(
				'min' => 0.1,
				'max' => 2.0,
				'step' => 0.1,
			),
		),
		'show_if' => array( 'scroll_translate_x', '=', 1 ),
		'group' => __( 'Effects', 'us' ),
		'context' => array( 'header', 'shortcode' ),
		'usb_preview' => array(
			array(
				'attr' => 'data-translate_x_speed',
			),
			array(
				'scroll_effects' => TRUE,
			),
		),
	),
	'wrapper_end_translate_x' => array(
		'type' => 'wrapper_end',
		'group' => __( 'Effects', 'us' ),
		'context' => array( 'header', 'shortcode' ),
	),

	// Transparency
	'wrapper_start_opacity' => array(
		'type' => 'wrapper_start',
		'show_if' => array( 'scroll_effect', '=', 1 ),
		'group' => __( 'Effects', 'us' ),
		'context' => array( 'header', 'shortcode' ),
	),
	'scroll_opacity' => array(
		'switch_text' => __( 'Transparency', 'us' ),
		'type' => 'switch',
		'std' => 0,
		'group' => __( 'Effects', 'us' ),
		'context' => array( 'header', 'shortcode' ),
		'usb_preview' => array(
			array(
				'toggle_class' => 'has_opacity',
			),
			array(
				'scroll_effects' => TRUE,
			),
		),
	),
	'scroll_opacity_direction' => array(
		'type' => 'select',
		'options' => array(
			'out-in' => sprintf( '%s → %s', __( 'Transparent', 'us' ), __( 'Visible', 'us' ) ),
			'in-out' => sprintf( '%s → %s', __( 'Visible', 'us' ), __( 'Transparent', 'us' ) ),
			'out-in-out' => sprintf( '%s → %s → %s', __( 'Transparent', 'us' ), __( 'Visible', 'us' ), __( 'Transparent', 'us' ) ),
			'in-out-in' => sprintf( '%s → %s → %s', __( 'Visible', 'us' ), __( 'Transparent', 'us' ), __( 'Visible', 'us' ) ),
		),
		'std' => 'out-in',
		'show_if' => array( 'scroll_opacity', '=', 1 ),
		'group' => __( 'Effects', 'us' ),
		'context' => array( 'header', 'shortcode' ),
		'usb_preview' => array(
			array(
				'attr' => 'data-opacity_direction',
			),
			array(
				'scroll_effects' => TRUE,
			),
		),
	),
	'wrapper_end_opacity' => array(
		'type' => 'wrapper_end',
		'group' => __( 'Effects', 'us' ),
		'context' => array( 'header', 'shortcode' ),
	),

	// Scale
	'wrapper_start_scale' => array(
		'type' => 'wrapper_start',
		'show_if' => array( 'scroll_effect', '=', 1 ),
		'group' => __( 'Effects', 'us' ),
		'context' => array( 'header', 'shortcode' ),
	),
	'scroll_scale' => array(
		'switch_text' => __( 'Scale', 'us' ),
		'type' => 'switch',
		'std' => 0,
		'group' => __( 'Effects', 'us' ),
		'context' => array( 'header', 'shortcode' ),
		'usb_preview' => array(
			array(
				'toggle_class' => 'has_scale',
			),
			array(
				'scroll_effects' => TRUE,
			),
		),
	),
	'scroll_scale_direction' => array(
		'type' => 'radio',
		'std' => 'up',
		'options' => array(
			'up' => __( 'Scale Up', 'us' ),
			'down' => __( 'Scale Down', 'us' ),
		),
		'show_if' => array( 'scroll_scale', '=', 1 ),
		'group' => __( 'Effects', 'us' ),
		'context' => array( 'header', 'shortcode' ),
		'usb_preview' => array(
			array(
				'attr' => 'data-scale_direction',
			),
			array(
				'scroll_effects' => TRUE,
			),
		),
	),
	'scroll_scale_speed' => array(
		'title' => __( 'Speed', 'us' ),
		'type' => 'slider',
		'std' => '0.5x',
		'options' => array(
			'x' => array(
				'min' => 0.1,
				'max' => 2.0,
				'step' => 0.1,
			),
		),
		'show_if' => array( 'scroll_scale', '=', 1 ),
		'group' => __( 'Effects', 'us' ),
		'context' => array( 'header', 'shortcode' ),
		'usb_preview' => array(
			array(
				'attr' => 'data-scale_speed',
			),
			array(
				'scroll_effects' => TRUE,
			),
		),
	),
	'wrapper_end_scale' => array(
		'type' => 'wrapper_end',
		'group' => __( 'Effects', 'us' ),
		'context' => array( 'header', 'shortcode' ),
	),

	// Delay
	'scroll_delay' => array(
		'title' => __( 'Delay', 'us' ),
		'type' => 'slider',
		'std' => '0.1s',
		'options' => array(
			's' => array(
				'min' => 0.0,
				'max' => 1.0,
				'step' => 0.1,
			),
		),
		'show_if' => array( 'scroll_effect', '=', 1 ),
		'group' => __( 'Effects', 'us' ),
		'context' => array( 'header', 'shortcode' ),
		'usb_preview' => array(
			array(
				'attr' => 'data-delay',
			),
			array(
				'scroll_effects' => TRUE,
			),
		),
	),

	'scroll_from_initial_position' => array(
		'switch_text' => __( 'Animate this element from its initial position', 'us' ),
		'type' => 'switch',
		'std' => '0',
		'show_if' => array( 'scroll_effect', '=', 1 ),
		'group' => __( 'Effects', 'us' ),
		'context' => array( 'shortcode' ),
		'usb_preview' => array(
			array(
				'attr' => 'data-from_initial_position',
			),
			array(
				'scroll_effects' => TRUE,
			),
		),
	),

	// Animation Start Position
	'scroll_start_position' => array(
		'title' => __( 'Animation Start Position', 'us' ),
		'description' => __( 'Distance from the bottom screen edge, where the element starts its animation', 'us' ),
		'type' => 'slider',
		'std' => '0%',
		'options' => array(
			'%' => array(
				'min' => 0,
				'max' => 50,
				'step' => 5,
			),
		),
		'show_if' => array( 'scroll_from_initial_position', '=', 0 ),
		'classes' => 'desc_4',
		'group' => __( 'Effects', 'us' ),
		'context' => array( 'shortcode' ),
		'usb_preview' => array(
			array(
				'attr' => 'data-start_position',
			),
			array(
				'scroll_effects' => TRUE,
			),
		),
	),

	// Animation End Position
	'scroll_end_position' => array(
		'title' => __( 'Animation End Position', 'us' ),
		'description' => __( 'Distance from the bottom screen edge, where the element ends its animation', 'us' ),
		'type' => 'slider',
		'std' => '100%',
		'options' => array(
			'%' => array(
				'min' => 50,
				'max' => 100,
				'step' => 5,
			),
		),
		'show_if' => array( 'scroll_from_initial_position', '=', 0 ),
		'classes' => 'desc_4',
		'group' => __( 'Effects', 'us' ),
		'context' => array( 'shortcode' ),
		'usb_preview' => array(
			array(
				'attr' => 'data-end_position',
			),
			array(
				'scroll_effects' => TRUE,
			),
		),
	),

);
