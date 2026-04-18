<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: vc_widget_sidebar
 */

$conditional_params = us_config( 'elements_conditional_options' );
$design_options_params = us_config( 'elements_design_options' );
$effect_options_params = us_config( 'elements_effect_options' );

/**
 * @return array
 */
return array(
	'title' => __( 'Sidebar with Widgets', 'us' ),
	'icon' => 'fas fa-ruler-vertical',
	'params' => us_set_params_weight(
		array(
			'sidebar_id' => array(
				'title' => __( 'Sidebar', 'us' ),
				'description' => sprintf( __( 'Add or edit a Sidebar on the %s page', 'us' ), '<a href="' . admin_url( 'widgets.php' ) . '" target="_blank">' . us_translate( 'Widgets' ) . '</a>' ),
				'type' => 'select',
				'options' => us_is_elm_editing_page() ? us_get_sidebars() : array(),
				'std' => 'default_sidebar',
				'admin_label' => TRUE,
				'usb_preview' => TRUE,
			),
			'title' => array(
				'title' => us_translate( 'Title' ),
				'type' => 'text',
				'std' => '',
				'admin_label' => TRUE,
				'usb_preview' => TRUE,
			),
		),
		$conditional_params,
		$effect_options_params,
		$design_options_params
	),
);
