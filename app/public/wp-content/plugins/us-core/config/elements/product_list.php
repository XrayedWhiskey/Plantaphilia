<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Configuration for shortcode: [us_product_list]
 */

$misc = us_config( 'elements_misc' );
$conditional_params = us_config( 'elements_conditional_options' );
$design_options_params = us_config( 'elements_design_options' );

$source_options = array(
	'all' => us_translate( 'All products', 'woocommerce' ),
	'post__in' => __( 'Selected products', 'us' ),
	'post__not_in' => __( 'Products except selected', 'us' ),
	'upsells' => us_translate( 'Upsells', 'woocommerce' ),
	'crosssell' => us_translate( 'Cross-sells', 'woocommerce' ),
	'recently_viewed' => us_translate( 'Recently Viewed Products', 'woocommerce' ),
	'user_favorite_ids' => __( 'Favorites of the current user', 'us' ),
	'current_wp_query' => __( 'Products of the current query (archives and search results)', 'us' ),
);

$orderby_options = array(
	'date' => __( 'Date of creation', 'us' ),
	'modified' => __( 'Date of update', 'us' ),
	'price' => us_translate( 'Price', 'woocommerce' ),
	'rating' => us_translate( 'Rating', 'woocommerce' ),
	'comment_count' => us_translate( 'Reviews', 'woocommerce' ),
	'total_sales' => us_translate( 'Sales', 'woocommerce' ),
	'menu_order' => us_translate( 'Menu order', 'woocommerce' ),
	'title' => us_translate( 'Title' ),
	'rand' => us_translate( 'Random' ),
	'post__in' => __( 'Order of selected products', 'us' ),
	'current_wp_query' => __( 'Order of the current query (archives and search results)', 'us' ),
	'custom' => __( 'Custom Field', 'us' ),
);

// General
$general_params = array(

	'source' => array(
		'title' => us_translate( 'Show' ),
		'type' => 'select',
		'options' => apply_filters( 'us_product_list_source_options', $source_options ),
		'std' => 'all',
		'admin_label' => TRUE,
		'usb_preview' => TRUE,
	),
	'ids' => array(
		'type' => 'autocomplete',
		'search_text' => __( 'Select products', 'us' ),
		'ajax_data' => array(
			'_nonce' => wp_create_nonce( 'us_ajax_get_post_ids_for_autocomplete' ),
			'action' => 'us_get_post_ids_for_autocomplete',
			'post_type' => 'product',
		),
		'options' => us_is_elm_editing_page() ? us_get_post_ids_for_autocomplete( 'product' ) : array(),
		'is_multiple' => TRUE,
		'is_sortable' => TRUE,
		'classes' => 'for_above',
		'show_if' => array( 'source', '=', array( 'post__in', 'post__not_in' ) ),
		'usb_preview' => TRUE,
	),
	'onsale_only' => array(
		'type' => 'switch',
		'switch_text' => us_translate( 'On-sale products', 'woocommerce' ),
		'std' => 0,
		'usb_preview' => TRUE,
	),
	'featured_only' => array(
		'type' => 'switch',
		'switch_text' => us_translate( 'Featured products', 'woocommerce' ),
		'std' => 0,
		'classes' => 'for_above',
		'usb_preview' => TRUE,
	),
	'exclude_out_of_stock' => array(
		'type' => 'switch',
		'switch_text' => __( 'Exclude out of stock', 'us' ),
		'std' => 0,
		'classes' => 'for_above',
		'usb_preview' => TRUE,
	),
	'exclude_hidden' => array(
		'type' => 'switch',
		'switch_text' => __( 'Exclude hidden products', 'us' ),
		'std' => 1,
		'classes' => 'for_above',
		'show_if' => array( 'source', '!=', 'current_wp_query' ),
		'usb_preview' => TRUE,
	),
	'exclude_current_product' => array(
		'type' => 'switch',
		'switch_text' => __( 'Exclude the current product', 'us' ),
		'std' => 1,
		'classes' => 'for_above',
		'show_if' => array( 'source', '!=', array( 'upsells', 'crosssell', 'current_wp_query' ) ),
		'usb_preview' => TRUE,
	),
	'exclude_prev_posts' => array(
		'type' => 'switch',
		'switch_text' => __( 'Exclude products of previous lists', 'us' ),
		'std' => 0,
		'classes' => 'for_above',
		'show_if' => array( 'source', '!=', array( 'upsells', 'crosssell', 'current_wp_query' ) ),
		'usb_preview' => TRUE,
	),

	// PRICE
	'price_compare' => array(
		'title' => __( 'Products with specific price', 'us' ),
		'type' => 'select',
		'options' => array(
			'none' => us_translate( 'None' ),
			'greater' => '>',
			'greater_equal' => '≥',
			'less' => '<',
			'less_equal' => '≤',
			'equal' => '=',
			'not_equal' => '!=',
			'in_range' => __( 'In range', 'us' ),
		),
		'std' => 'none',
		'usb_preview' => TRUE,
	),
	'price' => array(
		'placeholder' => us_translate( 'Price', 'woocommerce' ),
		'type' => 'text',
		'std' => '99.99',
		'classes' => 'for_above',
		'show_if' => array( 'price_compare', '!=', 'none' ),
		'usb_preview' => TRUE,
	),
	'price_max' => array(
		'placeholder' => us_translate( 'Max price', 'woocommerce' ),
		'type' => 'text',
		'std' => '299.99',
		'classes' => 'for_above',
		'show_if' => array( 'price_compare', '=', 'in_range' ),
		'usb_preview' => TRUE,
	),

	// PRODUCT TAXONOMIES
	'tax_query_relation' => array(
		'title' => __( 'Products with specific taxonomies', 'us' ),
		'type' => 'select',
		'options' => array(
			'none' => us_translate( 'None' ),
			'AND' => __( 'If EVERY condition below is met', 'us' ),
			'OR' => __( 'If ANY condition below is met', 'us' ),
		),
		'std' => 'none',
		'usb_preview' => TRUE,
	),
	'tax_query' => array(
		'type' => 'group',
		'show_controls' => TRUE,
		'label_for_add_button' => __( 'Add condition', 'us' ),
		'is_sortable' => FALSE,
		'is_accordion' => FALSE,
		'accordion_title' => 'taxonomy',
		'params' => array(
			'operator' => array(
				'title' => __( 'Show products', 'us' ),
				'type' => 'select',
				'options' => array(
					'IN' => __( 'with ANY of selected terms', 'us' ),
					'AND' => __( 'with ALL selected terms', 'us' ),
					'NOT IN' => __( 'WITHOUT selected terms', 'us' ),
					'CURRENT' => __( 'with the same terms of the current product', 'us' ),
				),
				'std' => 'IN',
			),
			'taxonomy' => array(
				'type' => 'select',
				'options' => us_is_elm_editing_page()
					? us_get_taxonomies( /* public_only */FALSE, /* show_slug */TRUE, /* output */'woocommerce_only' )
					: array(),
				'std' => 'product_cat',
				'classes' => 'for_above',
				'admin_label' => TRUE,
			),
			'terms' => array(
				'type' => 'autocomplete',
				'search_text' => __( 'Select terms', 'us' ),
				'is_multiple' => TRUE,
				'is_sortable' => FALSE,
				'ajax_data' => array(
					'_nonce' => wp_create_nonce( 'us_ajax_get_terms_for_autocomplete' ),
					'action' => 'us_get_terms_for_autocomplete',
					'use_term_ids' => TRUE, // use ids instead of slugs
				),
				'options' => array(), // will be loaded via ajax
				'options_filtered_by_param' => 'taxonomy',
				'std' => '',
				'classes' => 'for_above',
				'show_if' => array( 'operator', '!=', array( 'CURRENT' ) ),
			),
			'include_children' => array(
				'type' => 'switch',
				'switch_text' => __( 'Include child terms', 'us' ),
				'std' => 0,
				'classes' => 'for_above',
			),
		),
		'std' => array(
			array(
				'operator' => 'IN',
				'taxonomy' => 'product_cat',
				'terms' => '',
				'include_children' => 0,
			),
		),
		'show_if' => array( 'tax_query_relation', '!=', 'none' ),
		'usb_preview' => TRUE,
	),

	// CUSTOM FIELDS
	'meta_query_relation' => array(
		'title' => __( 'Products with specific custom fields', 'us' ),
		'type' => 'select',
		'options' => array(
			'none' => us_translate( 'None' ),
			'AND' => __( 'If EVERY condition below is met', 'us' ),
			'OR' => __( 'If ANY condition below is met', 'us' ),
		),
		'std' => 'none',
		'usb_preview' => TRUE,
	),
	'meta_query' => array(
		'type' => 'group',
		'show_controls' => TRUE,
		'label_for_add_button' => __( 'Add condition', 'us' ),
		'is_sortable' => FALSE,
		'is_accordion' => FALSE,
		'accordion_title' => 'key',
		'params' => array(
			'key' => array(
				'title' => __( 'Custom Field', 'us' ),
				'placeholder' => us_translate( 'Field name' ),
				'type' => 'text',
				'std' => 'custom_field_name',
				'admin_label' => TRUE,
			),
			'compare' => array(
				'type' => 'select',
				'options' => array(
					'=' => '=',
					'!=' => '!=',
					'>' => '>',
					'>=' => '≥',
					'<' => '<',
					'<=' => '≤',
					'LIKE' => __( 'Includes', 'us' ),
					'NOT LIKE' => __( 'Excludes', 'us' ),
					'EXISTS' => __( 'Has a value', 'us' ),
					'NOT EXISTS' => __( 'Doesn\'t have a value', 'us' ),
				),
				'std' => '=',
				'classes' => 'for_above',
			),
			'value' => array(
				'placeholder' => us_translate( 'Value' ),
				'type' => 'text',
				'std' => '',
				'show_if' => array( 'compare', '!=', array( 'EXISTS', 'NOT EXISTS' ) ),
				'classes' => 'for_above',
			),
		),
		'std' => array(
			array(
				'key' => 'custom_field_name',
				'compare' => '=',
				'value' => '',
			),
		),
		'show_if' => array( 'meta_query_relation', '!=', 'none' ),
		'usb_preview' => TRUE,
	),
);

$order_pagination_params = array(

	// ORDER
	'orderby' => array(
		'title' => __( 'Order by', 'us' ),
		'type' => 'select',
		'options' => apply_filters( 'us_product_list_orderby_options', $orderby_options ),
		'std' => 'date',
		'show_if' => array( 'source', '!=', 'recently_viewed' ),
		'group' => __( 'Order & Quantity', 'us' ),
		'usb_preview' => TRUE,
	),
	'orderby_custom_field' => array(
		'description' => __( 'Enter custom field name to order items by its value', 'us' ),
		'type' => 'text',
		'std' => '',
		'classes' => 'for_above',
		'show_if' => array( 'orderby', '=', 'custom' ),
		'group' => __( 'Order & Quantity', 'us' ),
		'usb_preview' => TRUE,
	),
	'orderby_custom_type' => array(
		'type' => 'switch',
		'switch_text' => __( 'Order by numeric values', 'us' ),
		'std' => 0,
		'classes' => 'for_above',
		'show_if' => array( 'orderby', '=', 'custom' ),
		'group' => __( 'Order & Quantity', 'us' ),
		'usb_preview' => TRUE,
	),
	'order_invert' => array(
		'type' => 'switch',
		'switch_text' => __( 'Invert order', 'us' ),
		'std' => 0,
		'classes' => 'for_above',
		'show_if' => array( 'orderby', '!=', array( 'rand', 'post__in', 'current_wp_query' ) ),
		'group' => __( 'Order & Quantity', 'us' ),
		'usb_preview' => TRUE,
	),

	// QUANTITY
	'show_all' => array(
		'title' => __( 'Quantity', 'us' ),
		'type' => 'switch',
		'switch_text' => __( 'Show all products', 'us' ),
		'std' => 0,
		'show_if' => array( 'source', '!=', 'current_wp_query' ),
		'group' => __( 'Order & Quantity', 'us' ),
		'usb_preview' => TRUE,
	),
	'quantity' => array(
		'type' => 'slider',
		'options' => array(
			'' => array(
				'min' => 1,
				'max' => 30,
			),
		),
		'std' => '12',
		'classes' => 'for_above',
		'show_if' => array( 'show_all', '=', 0 ),
		'group' => __( 'Order & Quantity', 'us' ),
		'usb_preview' => TRUE,
	),

	// NO RESULTS
	'no_items_action'=> array(
		'title' => __( 'Action when no results found', 'us' ),
		'type' => 'select',
		'options' => array(
			'message' => __( 'Show the message', 'us' ),
			'page_block' => __( 'Show the Reusable Block', 'us' ),
			'hide_grid' => __( 'Hide this element', 'us' ),
		),
		'std' => 'message',
		'group' => __( 'Order & Quantity', 'us' ),
		'usb_preview' => TRUE,
	),
	'no_items_message' => array(
		'type' => 'text',
		'std' => us_translate( 'No results found.' ),
		'classes' => 'for_above',
		'show_if' => array( 'no_items_action', '=', 'message' ),
		'group' => __( 'Order & Quantity', 'us' ),
		'usb_preview' => array(
			'elm' => '.w-grid-none',
			'attr' => 'html',
		),
	),
	'no_items_page_block' => array(
		'options' => us_is_elm_editing_page()
			? array( '' => '– ' . us_translate( 'None' ) . ' –' ) + us_get_posts_titles_for( 'us_page_block' )
			: array(),
		'type' => 'select',
		'hints_for' => 'us_page_block',
		'std' => '',
		'classes' => 'for_above',
		'show_if' => array( 'no_items_action', '=', 'page_block' ),
		'group' => __( 'Order & Quantity', 'us' ),
	),

	// PAGINATION
	'pagination' => array(
		'title' => us_translate( 'Pagination' ),
		'type' => 'select',
		'options' => array(
			'none' => us_translate( 'None' ),
			'numbered' => __( 'Numbered pagination', 'us' ),
			'load_on_btn' => __( 'Load products on button click', 'us' ),
			'load_on_scroll' => __( 'Load products on page scroll', 'us' ),
		),
		'std' => 'none',
		'show_if' => array( 'orderby', '!=', 'rand' ),
		'group' => __( 'Order & Quantity', 'us' ),
		'usb_preview' => TRUE,
	),
	'pagination_style' => array(
		'title' => __( 'Pagination Style', 'us' ),
		'description' => $misc['desc_btn_styles'],
		'type' => 'select',
		'options' => us_array_merge(
			array(
				'' => '– ' . us_translate( 'Default' ) . ' –',
			), us_get_btn_styles()
		),
		'std' => '',
		'show_if' => array( 'pagination', '=', 'numbered' ),
		'group' => __( 'Order & Quantity', 'us' ),
		'usb_preview' => array(
			array(
				'elm' => 'nav.pagination:first > .nav-links',
				'toggle_class' => 'custom',
			),
			array(
				'elm' => 'nav.pagination:first > .nav-links',
				'mod' => 'us-nav-style',
			),
		),
	),
	'pagination_btn_text' => array(
		'title' => __( 'Button Label', 'us' ),
		'type' => 'text',
		'dynamic_values' => TRUE,
		'std' => __( 'Load More', 'us' ),
		'cols' => 2,
		'show_if' => array( 'pagination', '=', 'load_on_btn' ),
		'group' => __( 'Order & Quantity', 'us' ),
		'usb_preview' => array(
			'elm' => '.g-loadmore:first .w-btn-label',
			'attr' => 'text',
		),
	),
	'pagination_btn_size' => array(
		'title' => __( 'Button Size', 'us' ),
		'description' => $misc['desc_font_size'],
		'type' => 'text',
		'std' => '',
		'cols' => 2,
		'show_if' => array( 'pagination', '=', 'load_on_btn' ),
		'group' => __( 'Order & Quantity', 'us' ),
		'usb_preview' => array(
			'elm' => '.g-loadmore:first .w-btn',
			'css' => 'font-size',
		),
	),
	'pagination_btn_style' => array(
		'title' => __( 'Button Style', 'us' ),
		'description' => $misc['desc_btn_styles'],
		'type' => 'select',
		'options' => us_get_btn_styles(),
		'std' => '1',
		'show_if' => array( 'pagination', '=', 'load_on_btn' ),
		'group' => __( 'Order & Quantity', 'us' ),
		'usb_preview' => array(
			'elm' => '.g-loadmore:first .w-btn',
			'mod' => 'us-btn-style',
		),
	),
	'pagination_btn_fullwidth' => array(
		'type' => 'switch',
		'switch_text' => __( 'Stretch to the full width', 'us' ),
		'std' => 0,
		'show_if' => array( 'pagination', '=', 'load_on_btn' ),
		'group' => __( 'Order & Quantity', 'us' ),
		'usb_preview' => array(
			'elm' => '.g-loadmore:first',
			'toggle_class' => 'width_full',
		),
	),
);

// Appearance
$appearance_params = array(
	'items_layout' => array(
		'title' => __( 'Grid Layout', 'us' ),
		'description' => $misc['desc_grid_layout'],
		'type' => 'select',
		'options' => us_get_grid_layouts_for_selection( array( 'shop' ) ),
		'std' => 'shop_standard',
		'classes' => 'for_grid_layouts',
		'settings' => array(
			'html-data' => array(
				'edit_link' => admin_url( '/post.php?post=%d&action=edit' ),
			),
		),
		'admin_label' => TRUE,
		'group' => us_translate( 'Appearance' ),
		'usb_preview' => TRUE,
	),
	'type' => array(
		'title' => __( 'Display as', 'us' ),
		'type' => 'select',
		'options' => array(
			'grid' => __( 'Regular Grid', 'us' ),
			'masonry' => __( 'Masonry', 'us' ),
			'metro' => __( 'METRO (works with square items only)', 'us' ),
		),
		'std' => 'grid',
		'group' => us_translate( 'Appearance' ),
		'usb_preview' => TRUE,
	),
	'items_valign' => array(
		'switch_text' => __( 'Center items vertically', 'us' ),
		'type' => 'switch',
		'std' => 0,
		'classes' => 'for_above',
		'show_if' => array( 'type', '=', 'grid' ),
		'group' => us_translate( 'Appearance' ),
		'usb_preview' => TRUE,
	),
	'ignore_items_size' => array(
		'switch_text' => __( 'Ignore items custom size', 'us' ),
		'type' => 'switch',
		'std' => 0,
		'classes' => 'for_above',
		'show_if' => array( 'type', '!=', 'metro' ),
		'group' => us_translate( 'Appearance' ),
		'usb_preview' => TRUE,
	),
	'load_animation' => array(
		'title' => __( 'Items animation on load', 'us' ),
		'type' => 'select',
		'options' => array(
			'none' => us_translate( 'None' ),
			'fade' => __( 'Fade', 'us' ),
			'afc' => __( 'Appear From Center', 'us' ),
			'afl' => __( 'Appear From Left', 'us' ),
			'afr' => __( 'Appear From Right', 'us' ),
			'afb' => __( 'Appear From Bottom', 'us' ),
			'aft' => __( 'Appear From Top', 'us' ),
			'hfc' => __( 'Height Stretch', 'us' ),
			'wfc' => __( 'Width Stretch', 'us' ),
		),
		'std' => 'none',
		'group' => us_translate( 'Appearance' ),
	),
	'columns' => array(
		'title' => us_translate( 'Columns' ),
		'type' => 'slider',
		'options' => array(
			'' => array(
				'min' => 1,
				'max' => 10,
			),
		),
		'std' => '4',
		'admin_label' => TRUE,
		'cols' => 2,
		'show_if' => array( 'type', '!=', 'metro' ),
		'group' => us_translate( 'Appearance' ),
		'usb_preview' => TRUE,
	),
	'items_gap' => array(
		'title' => __( 'Gap between Items', 'us' ),
		'type' => 'slider',
		'std' => '1.5rem',
		'options' => array(
			'px' => array(
				'min' => 0,
				'max' => 60,
			),
			'%' => array(
				'min' => 0,
				'max' => 5,
				'step' => 0.5,
			),
			'rem' => array(
				'min' => 0.0,
				'max' => 4.0,
				'step' => 0.1,
			),
			'vw' => array(
				'min' => 0.0,
				'max' => 4.0,
				'step' => 0.1,
			),
			'vh' => array(
				'min' => 0.0,
				'max' => 4.0,
				'step' => 0.1,
			),
		),
		'cols' => 2,
		'group' => us_translate( 'Appearance' ),
		'usb_preview' => TRUE,
	),
	'img_size' => array(
		'title' => __( 'Product Image Size', 'us' ),
		'description' => $misc['desc_img_sizes'],
		'type' => 'select',
		'options' => us_array_merge(
			array( 'default' => __( 'As in Grid Layout', 'us' ) ), us_get_image_sizes_list()
		),
		'std' => 'default',
		'cols' => 2,
		'group' => us_translate( 'Appearance' ),
		'usb_preview' => TRUE,
	),
	'title_size' => array(
		'title' => __( 'Product Title Size', 'us' ),
		'description' => $misc['desc_font_size'],
		'type' => 'text',
		'std' => '',
		'cols' => 2,
		'group' => us_translate( 'Appearance' ),
		'usb_preview' => TRUE,
	),
	'items_ratio' => array(
		'title' => __( 'Items Aspect Ratio', 'us' ),
		'type' => 'select',
		'options' => array(
			'default' => __( 'As in Grid Layout', 'us' ),
			'1x1' => '1x1 ' . __( 'square', 'us' ),
			'4x3' => '4x3 ' . __( 'landscape', 'us' ),
			'3x2' => '3x2 ' . __( 'landscape', 'us' ),
			'16x9' => '16:9 ' . __( 'landscape', 'us' ),
			'2x3' => '2x3 ' . __( 'portrait', 'us' ),
			'3x4' => '3x4 ' . __( 'portrait', 'us' ),
			'custom' => __( 'Custom', 'us' ),
		),
		'std' => 'default',
		'show_if' => array( 'type', '!=', 'metro' ),
		'group' => us_translate( 'Appearance' ),
		'usb_preview' => TRUE,
	),
	'items_ratio_width' => array(
		'description' => __( 'Examples:', 'us' ) . ' <span class="usof-example">21</span>, <span class="usof-example">1200</span>, <span class="usof-example">640px</span>',
		'type' => 'text',
		'std' => '21',
		'cols' => 2,
		'classes' => 'for_above',
		'show_if' => array( 'items_ratio', '=', 'custom' ),
		'group' => us_translate( 'Appearance' ),
		'usb_preview' => TRUE,
	),
	'items_ratio_height' => array(
		'description' => __( 'Examples:', 'us' ) . ' <span class="usof-example">9</span>, <span class="usof-example">750</span>, <span class="usof-example">380px</span>',
		'type' => 'text',
		'std' => '9',
		'cols' => 2,
		'classes' => 'for_above',
		'show_if' => array( 'items_ratio', '=', 'custom' ),
		'group' => us_translate( 'Appearance' ),
		'usb_preview' => TRUE,
	),
	'overriding_link' => array(
		'title' => __( 'Overriding Link', 'us' ),
		'description' => __( 'Applies to every product of this list.', 'us' ) . ' ' . __( 'All inner elements become not clickable.', 'us' ),
		'type' => 'link',
		'dynamic_values' => array(
			'post' => array(
				'post' => __( 'Product Link', 'us' ),
				'popup_post' => __( 'Open Product Page in a Popup', 'us' ),
				'popup_image' => __( 'Open Product Image in a Popup', 'us' ),
				'custom_field|us_tile_link' => sprintf( '%s: %s', __( 'Additional Settings', 'us' ), __( 'Custom Link', 'us' ) ),
			),
		),
		'std' => '{"url":""}',
		'group' => us_translate( 'Appearance' ),
		'usb_preview' => TRUE,
	),
	'popup_width' => array(
		'title' => __( 'Popup Width', 'us' ),
		'description' => $misc['desc_width'],
		'type' => 'text',
		'std' => '',
		'show_if' => array( 'overriding_link', 'str_contains', 'popup_post' ),
		'group' => us_translate( 'Appearance' ),
		'usb_preview' => TRUE,
	),
	'popup_arrows' => array(
		'switch_text' => __( 'Prev/Next arrows', 'us' ),
		'type' => 'switch',
		'std' => 1,
		'show_if' => array( 'overriding_link', 'str_contains', 'popup_post' ),
		'group' => us_translate( 'Appearance' ),
		'usb_preview' => TRUE,
	),
);

// Responsive Options
$responsive_params = us_config( 'elements_responsive_options' );

/**
 * @return array
 */
return array(
	'title' => __( 'Product List', 'us' ),
	'category' => __( 'Lists', 'us' ),
	'icon' => 'fas fa-th-large',
	'place_if' => class_exists( 'woocommerce' ),
	'params' => us_set_params_weight(
		$general_params,
		$order_pagination_params,
		$appearance_params,
		$responsive_params,
		$conditional_params,
		$design_options_params
	),
	// Note: Initializing wGrid after ProductList is necessary so that ProductList
	// has higher priority. In the future we need to get rid of wGrid.
	'usb_init_js' => '
		$elm.wProductList();
		$elm.wGrid();
		$us.$window.trigger( \'scroll.waypoints\' );
		jQuery( \'[data-content-height]\', $elm ).usCollapsibleContent()
	',
);
