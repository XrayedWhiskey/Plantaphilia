<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: [us_list_search]
 */

$misc = us_config( 'elements_misc' );
$conditional_params = us_config( 'elements_conditional_options' );
$design_options_params = us_config( 'elements_design_options' );

// General
$general_params = array(
	'live_search' => array(
		'type' => 'switch',
		'switch_text' => __( 'Live search', 'us' ),
		'description' => __( 'Will search while typing', 'us' ),
		'classes' => 'desc_2',
		'std' => 1,
	),
	'text' => array(
		'title' => __( 'Placeholder', 'us' ),
		'type' => 'text',
		'std' => us_translate( 'Search' ),
		'admin_label' => TRUE,
		'usb_preview' => array(
			'elm' => 'input',
			'attr' => 'placeholder',
		),
	),
	'icon' => array(
		'title' => __( 'Icon', 'us' ),
		'type' => 'icon',
		'std' => 'fas|search',
		'usb_preview' => TRUE,
	),
	'icon_pos' => array(
		'title' => __( 'Icon Position', 'us' ),
		'type' => 'radio',
		'options' => array(
			'left' => __( 'Before text', 'us' ),
			'right' => __( 'After text', 'us' ),
		),
		'std' => 'right',
		'usb_preview' => array(
			'mod' => 'iconpos',
		),
	),
	'icon_size' => array(
		'title' => __( 'Icon Size', 'us' ),
		'description' => $misc['desc_font_size'],
		'type' => 'text',
		'std' => '18px',
		'usb_preview' => array(
			'css' => '--icon-size',
			'elm' => '.w-search-form-btn',
		),
	),
);

/**
 * @return array
 */
return array(
	'title' => __( 'List Search', 'us' ),
	'category' => __( 'Lists', 'us' ),
	'icon' => 'fas fa-search-location',
	'params' => us_set_params_weight(
		$general_params,
		$conditional_params,
		$design_options_params
	),

	'usb_init_js' => '$elm.usListSearch()',
);
