<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: grid_filter
 */

$misc = us_config( 'elements_misc' );
$conditional_params = us_config( 'elements_conditional_options' );
$design_options_params = us_config( 'elements_design_options' );

$source_options = $tax_options = $numeric_options = $bool_options = $post_type_options = array();

if ( us_is_elm_editing_page() ) {
	$_group = '';

	foreach( us_get_list_filter_params() as $name => $param ) {

		if ( $_group != $param['group'] ) {
			$_group = $param['group'];
		}
		$source_options[ $_group ][ $name ] = $param['label'];

		if ( strpos( $name, 'tax|' ) === 0 ) {
			$tax_options[] = $name;
		}
		if ( isset( $param['value_type'] ) ) {
			if ( $param['value_type'] == 'numeric' ) {
				$numeric_options[] = $name;

			} elseif ( $param['value_type'] == 'bool' ) {
				$bool_options[] = $name;
			}
		}
	}

	$post_type_options = us_grid_available_post_types( TRUE );
	unset( $post_type_options['attachment'] );
}

/**
 * @return array
 */
return array(
	'title' => __( 'List Filter', 'us' ),
	'category' => __( 'Lists', 'us' ),
	'icon' => 'fas fa-filter',
	'params' => us_set_params_weight(

		// General section
		array(
			'filter_items' => array(
				'title' => __( 'Filter by', 'us' ),
				'type' => 'group',
				'show_controls' => TRUE,
				'is_sortable' => TRUE,
				'is_accordion' => TRUE,
				'accordion_title' => 'source',
				'params' => array(
					'source' => array(
						'title' => us_translate( 'Source' ),
						'type' => 'select',
						'options' => apply_filters( 'us_list_filter_source_options', $source_options ),
						'std' => 'post|type',
						'admin_label' => TRUE,
					),

					// Taxonomy params
					'term_compare' => array(
						'type' => 'select',
						'options' => array(
							'all' => __( 'All terms', 'us' ),
							'include' => __( 'Selected terms', 'us' ),
							'exclude' => __( 'Terms except selected', 'us' ),
						),
						'std' => 'all',
						'classes' => 'for_above',
						'show_if' => array( 'source', '=', $tax_options ),
					),
					'term_ids' => array(
						'type' => 'autocomplete',
						'search_text' => __( 'Select terms', 'us' ),
						'is_multiple' => TRUE,
						'is_sortable' => TRUE,
						'ajax_data' => array(
							'_nonce' => wp_create_nonce( 'us_ajax_get_terms_for_autocomplete' ),
							'action' => 'us_get_terms_for_autocomplete',
							'use_term_ids' => TRUE,
						),
						'options' => array(), // will be loaded via ajax
						'options_filtered_by_param' => 'source',
						'std' => '',
						'classes' => 'for_above',
						'show_if' => array( 'term_compare', '=', array( 'include', 'exclude' ) ),
					),
					'term_show_children' => array(
						'type' => 'switch',
						'switch_text' => __( 'Include child terms', 'us' ),
						'std' => 0,
						'classes' => 'for_above',
						'show_if' => array( 'term_compare', '=', 'all' ),
					),
					'term_exclude_children' => array(
						'type' => 'switch',
						'switch_text' => __( 'Also exclude child terms', 'us' ),
						'std' => 1,
						'classes' => 'for_above',
						'show_if' => array( 'term_compare', '=', 'exclude' ),
					),

					// Custom Fields
					'bool_value_label' => array(
						'title' => __( 'Value Label', 'us' ),
						'description' => __( 'This label will appear near the value.', 'us' ) . ' ' . __( 'Leave blank to use the default.', 'us' ),
						'type' => 'text',
						'std' => '',
						'show_if' => array( 'source', '=', $bool_options ),
					),
					'num_values_range' => array(
						'title' => __( 'Numeric Values Range', 'us' ),
						'description' => __( 'All existing values will be divided into groups by this range. Works for numeric values only. Leave blank to display actual values instead.', 'us' ),
						'type' => 'text',
						'std' => '10',
						'show_if' => array( 'source', '=', $numeric_options ),
					),

					// Post Author
					'post_author' => array(
						'type' => 'select',
						'options' => array(
							'all' => __( 'All authors', 'us' ),
							'include' => __( 'Selected authors', 'us' ),
							'exclude' => __( 'Exclude selected authors', 'us' ),
						),
						'std' => 'all',
						'show_if' => array( 'source', '=', 'post|author' ),
						'classes' => 'for_above',
					),
					'post_author_ids' => array(
						'type' => 'autocomplete',
						'search_text' => __( 'Select authors', 'us' ),
						'is_multiple' => TRUE,
						'is_sortable' => TRUE,
						'ajax_data' => array(
							'_nonce' => wp_create_nonce( 'us_ajax_get_user_ids_for_autocomplete' ),
							'action' => 'us_get_user_ids_for_autocomplete',
						),
						'options' => array(), // will be loaded via ajax
						'std' => '',
						'show_if' => array( 'post_author', '=', array( 'include', 'exclude' ) ),
						'classes' => 'for_above',
					),

					// Date
					'date_range' => array(
						'title' => __( 'Date Values Range', 'us' ),
						'type' => 'select',
						'options' => array(
							'yearly' => __( 'Yearly', 'us' ),
							'monthly' => __( 'Monthly', 'us' ),
						),
						'std' => 'yearly',
						'show_if' => array( 'source', '=', 'post|date' ),
					),

					// Post Type
					'post_type' => array(
						'type' => 'checkboxes',
						'options' => apply_filters( 'us_list_filter_post_types', $post_type_options ),
						'std' => '',
						'show_if' => array( 'source', '=', array( 'post|type', 'post|date' ) ),
						'classes' => 'for_above',
					),

					// Appearance
					'selection_type' => array(
						'title' => __( 'Selection Type', 'us' ),
						'type' => 'select',
						'options' => array(
							'checkbox' => __( 'Checkboxes', 'us' ),
							'radio' => __( 'Radio buttons', 'us' ),
							'dropdown' => __( 'Dropdown', 'us' ),
							// 'number_range' => __( 'Number Range (coming soon)', 'us' ),
						),
						'std' => 'checkbox',
					),
					'first_value_label' => array(
						'title' => __( 'First Value Label', 'us' ),
						'description' => __( 'Leave blank to not add the first (default) value.', 'us' ),
						'type' => 'text',
						'std' => __( 'Any', 'us' ),
						'show_if' => array( 'selection_type', '=', array( 'radio', 'dropdown' ) ),
					),
					'label' => array(
						'title' => us_translate( 'Title' ),
						'description' => __( 'Leave blank to use the default.', 'us' ),
						'type' => 'text',
						'std' => '',
						'admin_label' => TRUE,
					),
				),
				'std' => array(
					array(
						'source' => 'post|type',
						'term_compare' => 'all',
						'term_show_children' => 0,
						'term_exclude_children' => 1,
						'selection_type' => 'checkbox',
						'first_value_label' => __( 'Any', 'us' ),
						'label' => '',
					),
				),
				'usb_preview' => TRUE,
			),
		),

		// Appearance section
		array(
			'enable_toggles' => array(
				'switch_text' => __( 'Show as Toggles', 'us' ),
				'type' => 'switch',
				'std' => 0,
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'toggle_class' => 'togglable',
				),
			),
			'values_max_height' => array(
				'title' => __( 'Max Height of the list of values', 'us' ),
				'description' => $misc['desc_height'],
				'type' => 'text',
				'std' => '',
				'group' => us_translate( 'Appearance' ),
				'usb_preview' => array(
					'elm' => '.w-filter-item-values',
					'css' => 'max-height',
				),
			),
		),

		// Mobiles section
		array(
			'mobile_width' => array(
				'title' => __( 'Mobile view at screen width', 'us' ),
				'description' => __( 'Leave blank to not apply mobile view.', 'us' ),
				'type' => 'text',
				'std' => '600px',
				'group' => __( 'Mobiles', 'us' ),
				'usb_preview' => TRUE,
			),
			'mobile_button_label' => array(
				'title' => __( 'Button Label', 'us' ),
				'type' => 'text',
				'std' => __( 'Filters', 'us' ),
				'group' => __( 'Mobiles', 'us' ),
				'usb_preview' => array(
					'elm' => '.w-filter-opener > span',
					'attr' => 'html',
				),
			),
			'mobile_button_style' => array(
				'title' => __( 'Button Style', 'us' ),
				'description' => $misc['desc_btn_styles'],
				'type' => 'select',
				'options' => us_array_merge(
					array(
						'' => '– ' . us_translate( 'None' ) . ' –'
					),
					us_get_btn_styles()
				),
				'std' => '',
				'group' => __( 'Mobiles', 'us' ),
				'usb_preview' => array(
					'elm' => '.w-filter-opener',
					'mod' => 'us-btn-style',
				),
			),
			'mobile_button_icon' => array(
				'title' => __( 'Icon', 'us' ),
				'type' => 'icon',
				'std' => '',
				'group' => __( 'Mobiles', 'us' ),
				'usb_preview' => TRUE,
			),
			'mobile_button_iconpos' => array(
				'title' => __( 'Icon Position', 'us' ),
				'type' => 'radio',
				'options' => array(
					'left' => us_translate( 'Left' ),
					'right' => us_translate( 'Right' ),
				),
				'std' => 'left',
				'group' => __( 'Mobiles', 'us' ),
				'usb_preview' => TRUE,
			),
		),

		$conditional_params,
		$design_options_params
	),

	'usb_init_js' => '$elm.usListFilter()',
);
