<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: carousel
 */

$misc = us_config( 'elements_misc' );
$conditional_params = us_config( 'elements_conditional_options' );
$design_options_params = us_config( 'elements_design_options' );

$laptops_breakpoint = (int) us_get_option( 'laptops_breakpoint' );
$tablets_breakpoint = (int) us_get_option( 'tablets_breakpoint' );
$mobiles_breakpoint = (int) us_get_option( 'mobiles_breakpoint' );

$exclude_elements = array(
	'us_carousel',
	'us_cform',
	'us_color_scheme_switch',
	'us_content_carousel',
	'us_dropdown',
	'us_gallery',
	'us_gmaps',
	'us_grid',
	'us_grid_filter',
	'us_grid_order',
	'us_image_slider',
	'us_list_filter',
	'us_list_order',
	'us_list_search',
	'us_login',
	'us_page_block',
	'us_post_list',
	'us_product_list',
	'us_search',
	'us_separator',
	'us_scroller',
	'us_term_list',
	'us_user_list',
	'vc_column',
	'vc_row',
	'vc_tta_accordion',
	'vc_tta_section',
	'vc_tta_tabs',
	'vc_tta_toggle_section',
	'vc_tta_tour',
);

return array(
	'title' => __( 'Content Carousel', 'us' ),
	'description' => __( 'Allows you to rotate any content.', 'us' ),
	'category' => __( 'Containers', 'us' ),
	'icon' => 'fas fa-laptop-code',
	'is_container' => TRUE,
	'as_parent' => array(
		'except' => implode( ',', $exclude_elements ),
	),
	'usb_moving_only_x_axis' => TRUE,
	'usb_root_container_selector' => '.owl-stage:first',
	'usb_reload_element' => TRUE,
	'show_settings_on_create' => FALSE,
	'js_view' => 'VcColumnView',
	'params' => us_set_params_weight(
		array(

			// General
			'items' => array(
				'title' => __( 'Number of Items to Show', 'us' ),
				'type' => 'select',
				'options' => array(
					'auto' => __( 'Auto (for items of different widths)', 'us' ),
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
					'7' => '7',
					'8' => '8',
					'9' => '9',
					'10' => '10',
				),
				'std' => '3',
				'cols' => 2,
				'usb_preview' => TRUE,
			),
			'items_offset' => array(
				'title' => __( 'Next Item Offset', 'us' ),
				'type' => 'slider',
				'std' => '0px',
				'options' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
						'step' => 5,
					),
				),
				'cols' => 2,
				'usb_preview' => TRUE,
			),
			'items_valign' => array(
				'title' => __( 'Items Vertical Alignment', 'us' ),
				'type' => 'select',
				'options' => array(
					'stretch' => __( 'Stretch', 'us' ),
					'top' => us_translate( 'Top' ),
					'middle' => us_translate( 'Middle' ),
					'bottom' => us_translate( 'Bottom' ),
				),
				'std' => 'stretch',
				'cols' => 2,
				'usb_preview' => array(
					'mod' => 'valign',
				),
			),
			'items_gap' => array(
				'title' => __( 'Gap between Items', 'us' ),
				'type' => 'slider',
				'std' => '30px',
				'options' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'cols' => 2,
				'usb_preview' => TRUE,
			),
			'center_item' => array(
				'type' => 'switch',
				'switch_text' => __( 'Current item in the center', 'us' ),
				'std' => 0,
				'show_if' => array( 'items', '!=', array( 'auto', '1' ) ),
				'usb_preview' => TRUE,
			),
			'autoheight' => array(
				'type' => 'switch',
				'switch_text' => __( 'Auto Height', 'us' ),
				'std' => 0,
				'show_if' => array( 'items', '=', '1' ),
				'usb_preview' => TRUE,
			),
			'loop' => array(
				'type' => 'switch',
				'switch_text' => us_translate( 'Loop' ),
				'std' => 0,
			),
			'autoplay' => array(
				'type' => 'switch',
				'switch_text' => __( 'Auto Rotation', 'us' ),
				'std' => 0,
				'usb_preview' => TRUE,
			),
			'autoplay_pause_on_hover' => array(
				'type' => 'switch',
				'switch_text' => __( 'Pause on hover', 'us' ),
				'std' => 0,
				'show_if' => array( 'autoplay', '=', 1 ),
			),
			'autoplay_continual' => array(
				'type' => 'switch',
				'switch_text' => __( 'Continual Rotation', 'us' ),
				'std' => 0,
				'show_if' => array( 'autoplay', '=', 1 ),
				'usb_preview' => TRUE,
			),
			'autoplay_timeout' => array(
				'title' => __( 'Auto Rotation Interval', 'us' ),
				'type' => 'slider',
				'std' => '3s',
				'options' => array(
					's' => array(
						'min' => 1,
						'max' => 10,
					),
				),
				'show_if' => array( 'autoplay', '=', 1 ),
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
				'usb_preview' => TRUE,
			),

			// Navigation
			'arrows' => array(
				'type' => 'switch',
				'switch_text' => __( 'Prev/Next arrows', 'us' ),
				'std' => 1,
				'group' => us_translate_x( 'Navigation', 'block title' ),
				'usb_preview' => TRUE,
			),
			'arrows_style' => array(
				'title' => __( 'Arrows Style', 'us' ),
				'description' => $misc['desc_btn_styles'],
				'type' => 'select',
				'options' => us_array_merge(
					array(
						'circle' => '– ' . __( 'Circles', 'us' ) . ' –',
						'block' => '– ' . __( 'Full height blocks', 'us' ) . ' –',
					), us_get_btn_styles()
				),
				'std' => 'circle',
				'cols' => 2,
				'show_if' => array( 'arrows', '=', 1 ),
				'group' => us_translate_x( 'Navigation', 'block title' ),
				'usb_preview' => array(
					'elm' => '.owl-carousel',
					'mod' => 'navstyle',
				),
			),
			'arrows_size' => array(
				'title' => __( 'Arrows Size', 'us' ),
				'type' => 'slider',
				'std' => '1.5rem',
				'options' => array(
					'px' => array(
						'min' => 10,
						'max' => 50,
					),
					'rem' => array(
						'min' => 1.0,
						'max' => 3.0,
						'step' => 0.1,
					),
					'em' => array(
						'min' => 1.0,
						'max' => 3.0,
						'step' => 0.1,
					),
				),
				'cols' => 2,
				'show_if' => array( 'arrows', '=', 1 ),
				'group' => us_translate_x( 'Navigation', 'block title' ),
				'usb_preview' => array(
					'elm' => '.owl-carousel',
					'css' => '--arrows-size',
				),
			),
			'arrows_pos' => array(
				'title' => __( 'Arrows Position', 'us' ),
				'type' => 'radio',
				'options' => array(
					'outside' => __( 'Outside', 'us' ),
					'inside' => __( 'Inside', 'us' ),
				),
				'std' => 'outside',
				'cols' => 2,
				'show_if' => array( 'arrows', '=', 1 ),
				'group' => us_translate_x( 'Navigation', 'block title' ),
				'usb_preview' => array(
					'elm' => '.owl-carousel',
					'mod' => 'navpos',
				),
			),
			'arrows_offset' => array(
				'title' => __( 'Arrows Offset', 'us' ),
				'type' => 'slider',
				'std' => '0px',
				'options' => array(
					'px' => array(
						'min' => -60,
						'max' => 60,
					),
					'rem' => array(
						'min' => -3.0,
						'max' => 3.0,
						'step' => 0.1,
					),
					'em' => array(
						'min' => -3.0,
						'max' => 3.0,
						'step' => 0.1,
					),
				),
				'cols' => 2,
				'show_if' => array( 'arrows', '=', 1 ),
				'group' => us_translate_x( 'Navigation', 'block title' ),
				'usb_preview' => array(
					'elm' => '.owl-carousel',
					'css' => '--arrows-offset',
				),
			),
			'slide_by_one' => array(
				'type' => 'switch',
				'switch_text' => __( 'Slide by one item', 'us' ),
				'std' => 1,
				'show_if' => array( 'arrows', '=', 1 ),
				'group' => us_translate_x( 'Navigation', 'block title' ),
				'usb_preview' => TRUE,
			),
			'dots' => array(
				'type' => 'switch',
				'switch_text' => __( 'Dots', 'us' ),
				'std' => 0,
				'group' => us_translate_x( 'Navigation', 'block title' ),
				'usb_preview' => TRUE,
			),
			'dots_style' => array(
				'title' => __( 'Dots Style', 'us' ),
				'type' => 'radio',
				'options' => array(
					'circle' => '1',
					'diamond' => '2',
					'dash' => '3',
					'smudge' => '4',
				),
				'std' => 'circle',
				'show_if' => array( 'dots', '=', 1 ),
				'group' => us_translate_x( 'Navigation', 'block title' ),
				'usb_preview' => array(
					'elm' => '.owl-carousel',
					'mod' => 'dotstyle',
				),
			),
			'mouse_drag' => array(
				'type' => 'switch',
				'switch_text' => __( 'Slide by mouse drag', 'us' ),
				'std' => 1,
				'group' => us_translate_x( 'Navigation', 'block title' ),
			),
			'touch_drag' => array(
				'type' => 'switch',
				'switch_text' => __( 'Slide by touch drag', 'us' ),
				'std' => 1,
				'group' => us_translate_x( 'Navigation', 'block title' ),
			),

			// Responsive
			'responsive' => array(
				'type' => 'group',
				'show_controls' => TRUE,
				'is_sortable' => TRUE,
				'is_accordion' => TRUE,
				'accordion_title' => 'breakpoint',
				'params' => array(
					'breakpoint' => array(
						'title' => __( 'Breakpoint Width', 'us' ),
						'description' => __( 'Options below will apply to screen widths smaller than the selected value.', 'us' ),
						'type' => 'select',
						'options' => array(
							'laptops' => sprintf( '%s (%spx)', __( 'Laptops', 'us' ), $laptops_breakpoint + 1 ),
							'tablets' => sprintf( '%s (%spx)', __( 'Tablets', 'us' ), $tablets_breakpoint + 1 ),
							'mobiles' => sprintf( '%s (%spx)', __( 'Mobiles', 'us' ), $mobiles_breakpoint + 1 ),
							'custom' => __( 'Custom', 'us' ),
						),
						'std' => 'laptops',
						'classes' => 'desc_4',
						'admin_label' => TRUE,
					),
					'breakpoint_width' => array(
						'type' => 'slider',
						'options' => array(
							'px' => array(
								'min' => 320,
								'max' => 2560,
							),
						),
						'std' => '1024px',
						'classes' => 'for_above',
						'show_if' => array( 'breakpoint', '=', 'custom' ),
					),
					'items' => array(
						'title' => __( 'Number of Items to Show', 'us' ),
						'type' => 'select',
						'options' => array(
							'auto' => __( 'Auto (for items of different widths)', 'us' ),
							'1' => '1',
							'2' => '2',
							'3' => '3',
							'4' => '4',
							'5' => '5',
							'6' => '6',
							'7' => '7',
							'8' => '8',
							'9' => '9',
							'10' => '10',
						),
						'std' => '1',
						'cols' => 2,
					),
					'items_offset' => array(
						'title' => __( 'Next Item Offset', 'us' ),
						'type' => 'slider',
						'options' => array(
							'px' => array(
								'min' => 0,
								'max' => 100,
								'step' => 5,
							),
						),
						'std' => '0px',
						'cols' => 2,
					),
					'center_item' => array(
						'type' => 'switch',
						'switch_text' => __( 'Current item in the center', 'us' ),
						'std' => 0,
						'show_if' => array( 'items', '!=', array( 'auto', '1' ) ),
					),
					'autoheight' => array(
						'type' => 'switch',
						'switch_text' => __( 'Auto Height', 'us' ),
						'std' => 0,
						'show_if' => array( 'items', '=', '1' ),
					),
					'loop' => array(
						'type' => 'switch',
						'switch_text' => us_translate( 'Loop' ),
						'std' => 0,
					),
					'autoplay' => array(
						'type' => 'switch',
						'switch_text' => __( 'Auto Rotation', 'us' ),
						'std' => 0,
					),
					'arrows' => array(
						'type' => 'switch',
						'switch_text' => __( 'Prev/Next arrows', 'us' ),
						'std' => 0,
					),
					'dots' => array(
						'type' => 'switch',
						'switch_text' => __( 'Dots', 'us' ),
						'std' => 1,
					),
				),
				'std' => array(
					array(
						'breakpoint' => 'mobiles',
						'items' => '1',
						'items_offset' => '0px',
						'center_item' => 0,
						'autoheight' => 0,
						'loop' => 0,
						'autoplay' => 0,
						'arrows' => 0,
						'dots' => 1,
					),
				),
				'group' => __( 'Responsive', 'us' ),
				'usb_preview' => TRUE,
			),
		),

		$conditional_params,
		$design_options_params
	),
	'usb_init_js' => '$elm.usContentCarousel()',
);
