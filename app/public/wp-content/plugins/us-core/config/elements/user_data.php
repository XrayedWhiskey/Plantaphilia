<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for User Data element
 */

$misc = us_config( 'elements_misc' );
$conditional_params = us_config( 'elements_conditional_options' );
$design_options_params = us_config( 'elements_design_options' );
$hover_options_params = us_config( 'elements_hover_options' );

$available_post_types = array();
if ( us_is_elm_editing_page() ) {
	$available_post_types = us_grid_available_post_types();
}

/**
 * @return array
 */
return array(
	'title' => __( 'User Data', 'us' ),
	'icon' => 'fas fa-user',
	'params' => us_set_params_weight(

		// General section
		array(
			'type' => array(
				'title' => us_translate( 'Show' ),
				'type' => 'select',
				'options' => array(
					'display_name' => us_translate( 'User Display Name' ),
					'first_name' => us_translate( 'First Name' ),
					'last_name' => us_translate( 'Last Name' ),
					'nickname' => us_translate( 'Nickname' ),
					'user_email' => us_translate( 'Email' ),
					'user_url' => us_translate( 'Website' ),
					'description' => us_translate( 'Biographical Info' ),
					'role' => us_translate( 'Role' ),
					'user_registered' => __( 'Registration Date', 'us' ),
					'post_count' => __( 'Amount of posts', 'us' ),
					'custom' => __( 'Custom Field', 'us' ),
				),
				'std' => 'display_name',
				'usb_preview' => TRUE,
			),
			'post_type' => array(
				'title' => us_translate( 'Post Type' ),
				'type' => 'checkboxes',
				'options' => $available_post_types,
				'std' => 'post',
				'show_if' => array( 'type', '=', 'post_count' ),
				'usb_preview' => TRUE,
			),
			'custom_field' => array(
				'placeholder' => 'custom_field_name',
				'description' => __( 'Enter a custom field name to get its value.', 'us' ),
				'type' => 'text',
				'std' => '',
				'classes' => 'for_above',
				'show_if' => array( 'type', '=', 'custom' ),
				'usb_preview' => TRUE,
			),
			'date_format' => array(
				'title' => us_translate( 'Date Format' ),
				'description' => '<a href="https://wordpress.org/support/article/formatting-date-and-time/" target="_blank">' . __( 'Documentation on date and time formatting.', 'us' ) . '</a>',
				'type' => 'text',
				'std' => 'F j, Y',
				'show_if' => array( 'type', '=', 'user_registered' ),
				'usb_preview' => TRUE,
			),
			'link' => array(
				'title' => us_translate( 'Link' ),
				'type' => 'link',
				'dynamic_values' => array(
					'global' => array(
						'elm_value' => __( 'Clickable value (email, phone, website)', 'us' ),
					),
					'post' => array(),
				),
				'std' => '{"url":""}',
				'show_if' => array( 'type', '!=', 'description' ),
				'usb_preview' => TRUE,
			),
			'color_link' => array(
				'title' => __( 'Link Color', 'us' ),
				'type' => 'switch',
				'switch_text' => __( 'Inherit from text color', 'us' ),
				'std' => 0,
				'show_if' => array( 'type', '!=', 'description' ),
				'usb_preview' => TRUE,
			),
			'tag' => array(
				'title' => __( 'HTML tag', 'us' ),
				'type' => 'select',
				'options' => $misc['html_tag_values'],
				'std' => 'div',
				'usb_preview' => array(
					'attr' => 'tag',
				),
			),
			'text_before' => array(
				'title' => __( 'Text before value', 'us' ),
				'type' => 'text',
				'std' => '',
				'usb_preview' => array(
					'attr' => 'html',
					'elm' => '.w-post-elm-before',
				),
			),
			'text_after' => array(
				'title' => __( 'Text after value', 'us' ),
				'type' => 'text',
				'std' => '',
				'usb_preview' => array(
					'attr' => 'html',
					'elm' => '.w-post-elm-after',
				),
			),
		),

		$conditional_params,
		$design_options_params,
		$hover_options_params
	),
);
