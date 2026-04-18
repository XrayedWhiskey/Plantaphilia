<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: color_scheme_switch
 */

$conditional_params = us_config( 'elements_conditional_options' );
$design_options_params = us_config( 'elements_design_options' );

$color_schemes = us_get_color_schemes( /* only_titles */TRUE );

/**
 * @return array
 */
return array(
	'title' => __( 'Color Scheme Switch' ),
	'category' => __( 'Interactive', 'us' ),
	'icon' => 'fas fa-palette',
	'params' => us_set_params_weight(

		// General section
		array(
			'color_scheme' => array(
				'title' => __( 'Color Scheme', 'us' ),
				'description' => __( 'The selected color scheme will be applied when a site visitor turns this switch on.', 'us' ),
				'type' => 'select',
				'options' => $color_schemes,
				'std' => key( $color_schemes ),
				'holder' => 'div',
				'usb_preview' => TRUE,
			),
			'text_before' => array(
				'title' => __( 'Text Before Switch', 'us' ),
				'type' => 'text',
				'std' => us_translate_x( 'Light', 'color scheme' ),
				'holder' => 'div',
				'usb_preview' => array(
					'elm' => '.w-color-switch-before',
					'attr' => 'html',
				),
			),
			'text_after' => array(
				'title' => __( 'Text After Switch', 'us' ),
				'type' => 'text',
				'std' => us_translate_x( 'Dark', 'color scheme' ),
				'holder' => 'div',
				'usb_preview' => array(
					'elm' => '.w-color-switch-after',
					'attr' => 'html',
				),
			),
			'inactive_switch_bg' => array(
				'title' => __( 'Inactive Switch Background', 'us' ),
				'type' => 'color',
				'clear_pos' => 'right',
				'with_gradient' => FALSE,
				'std' => '#ddd',
				'usb_preview' => array(
					'css' => '--color-inactive-switch-bg',
				),
			),
			'active_switch_bg' => array(
				'title' => __( 'Active Switch Background', 'us' ),
				'type' => 'color',
				'clear_pos' => 'right',
				'with_gradient' => FALSE,
				'std' => '#222',
				'usb_preview' => array(
					'css' => '--color-active-switch-bg',
				),
			),
		),

		$conditional_params,
		$design_options_params
	),
);
