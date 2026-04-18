<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: add_to_favs
 */

$misc = us_config( 'elements_misc' );
$conditional_params = us_config( 'elements_conditional_options' );
$design_options_params = us_config( 'elements_design_options' );
$hover_options_params = us_config( 'elements_hover_options' );

/**
 * @return array
 */
return array(
	'title' => sprintf( __( '"%s" Button', 'us' ), __( 'Add to Favorites', 'us' ) ),
	'icon' => 'fas fa-heart',
	'category' => __( 'Post Elements', 'us' ),
	'params' => us_set_params_weight(

	// General section
		array(
			'style' => array(
				'title' => us_translate( 'Style' ),
				'description' => $misc['desc_btn_styles'],
				'type' => 'select',
				'options' => array( '0' => '– ' . us_translate( 'Default' ) . ' –' ) + us_get_btn_styles(),
				'std' => '0',
				'usb_preview' => array(
					'mod' => 'us-btn-style',
					'elm' => '.w-btn',
				),
			),
			'show_icon' => array(
				'type' => 'switch',
				'switch_text' => __( 'Show icon', 'us' ),
				'std' => 1,
				'usb_preview' => TRUE,
			),
			'label_before_adding' => array(
				'title' => __( 'Label before adding', 'us' ),
				'type' => 'text',
				'std' => __( 'Add to Favorites', 'us' ),
				'admin_label' => TRUE,
				'usb_preview' => array(
					'attr' => 'html',
					'elm' => '.w-btn-label',
				),
			),
			'label_after_adding' => array(
				'title' => __( 'Label after adding', 'us' ),
				'type' => 'text',
				'std' => __( 'In Favorites', 'us' ),
			),
			'message_for_non_registered' => array(
				'title' => __( 'Message for unregistered users', 'us' ),
				'type' => 'text',
				'std' => __( 'Please log in to add items to your favorites.', 'us' ),
			),
		),

		$conditional_params,
		$design_options_params,
		$hover_options_params
	)
);
