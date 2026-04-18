<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Shortcode: us_list_filter
 */

// Never output inside Grid items or specific Reusable Blocks
global $us_grid_outputs_items, $us_is_page_block_in_no_results, $us_is_page_block_in_menu;
if (
	$us_grid_outputs_items
	OR $us_is_page_block_in_no_results
	OR $us_is_page_block_in_menu
) {
	return;
}

// Never output the Grid Filter on AMP pages
if ( us_amp() ) {
	return;
}

// Don't output if there are no items
if ( empty( $filter_items ) AND ! usb_is_post_preview() ) {
	return;
}

if ( is_string( $filter_items ) ) {
	$filter_items = json_decode( urldecode( $filter_items ), TRUE );
}
if ( ! is_array( $filter_items ) ) {
	$filter_items = array();
}

$_atts = array(
	'class' => 'w-filter for_list state_desktop',
	'action' => '',
	'onsubmit' => 'return false;',
);

$_atts['class'] .= $classes ?? '';
$_atts['class'] .= ' layout_ver';
$_atts['class'] .= ' items_' . count( $filter_items );

if ( $enable_toggles ) {
	$_atts['class'] .= ' togglable';
}

if ( ! empty( $el_id ) ) {
	$_atts['id'] = $el_id;
}

$json_data = array(
	'mobileWidth' => (int) $mobile_width,
);

$output = '<form' . us_implode_atts( $_atts ) . us_pass_data_to_js( $json_data ) . '>';
$output .= '<div class="w-filter-list">';

if ( ! empty( $mobile_width ) ) {
	$output .= '<div class="w-filter-list-title">' . strip_tags( $mobile_button_label ) . '</div>';
	$output .= '<button class="w-filter-list-closer" title="' . esc_attr( us_translate( 'Close' ) ) . '" aria=label="' . esc_attr( us_translate( 'Close' ) ) . '"></button>';
}

/**
 * Get the number of hierarchy level for the provided term id
 */
$func_get_term_depth = function ( $id, $term_parents ) {
	$depth = 0;
	while ( $id > 0 ) {
		if ( $depth > 5 ) { // limit hierarchy by 5 levels
			break;
		}
		if ( isset( $term_parents[ $id ] ) ) {
			$id = $term_parents[ $id ];
			$depth ++;
		} else {
			$id = 0;
		}
	}

	return $depth;
};

$_available_post_types = us_grid_available_post_types( TRUE, FALSE );
unset( $_available_post_types['attachment'] );
asort( $_available_post_types );

$_available_post_types = apply_filters( 'us_list_filter_post_types', $_available_post_types );

// This variable limits the output of values in HTML (for browser performance reasons).
// Means every filter item can't show more than 250 checkboxes/radio buttons/options by default
$_values_output_limit = usb_is_preview() ? 99 :	(int) apply_filters( 'us_list_filter_values_output_limit', 250 );

$output_items = '';

foreach ( $filter_items as $i => $filter_item ) {

	if ( empty( $filter_item['source'] ) ) {
		continue;
	}

	$selector_type = $filter_item['selection_type'] ?? 'checkbox';
	$item_title = $filter_item['label'] ?? '';

	$selector_vars = array(
		'item_name' => $filter_item['source'],
		'show_amount' => $filter_item['show_amount'] ?? NULL,
		'item_values' => array(),
	);

	$item_atts = array(
		'class' => sprintf( 'w-filter-item number_%s type_%s', $i + 1, $selector_type ),
		'data-source' => $filter_item['source'],
	);

	if ( strpos( $filter_item['source'], '|' ) !== FALSE ) {
		$source_type = strtok( $filter_item['source'], '|' );
		$source_name = strtok( '|' );
	} else {
		$source_type = $filter_item['source'];
	}

	if ( ! empty( $filter_item['post_type'] ) ) {
		$_specified_post_types = explode( ',', $filter_item['post_type'] );
	} else {
		$_specified_post_types = array();
	}

	// Source: Taxonomy 
	if ( $source_type == 'tax' AND ! empty( $source_name ) ) {

		if ( ! $taxanomy_obj = get_taxonomy( $source_name ) ) {
			continue;
		}

		if ( $item_title == '' ) {
			$item_title = $taxanomy_obj->labels->singular_name;
		}

		$term_compare = $filter_item['term_compare'] ?? 'all';
		$term_ids = $filter_item['term_ids'] ?? '';

		$_include_term_depth = $taxanomy_obj->hierarchical;

		$terms_args = array(
			'taxonomy' => $source_name,
			'hide_empty' => TRUE,
			'number' => $_values_output_limit,
			'orderby' => 'menu_order',
		);

		// Archive taxonomy page should show its child terms, if no childs - show nothing
		$current_term_id = 0;
		if (
			is_tax( $source_name )
			OR (
				is_category()
				AND $source_name == 'category'
			)
			OR (
				is_tag()
				AND $source_name == 'post_tag'
			)
		) {
			if ( $taxanomy_obj->hierarchical AND $current_term_id = get_queried_object_id() ) {
				$terms_args['child_of'] = $current_term_id;
			} else {
				continue;
			}
		}

		// Include selected terms
		if ( $term_compare == 'include' ) {
			$terms_args['include'] = explode( ',', $term_ids );
			$terms_args['orderby'] = 'include';

			// Disable depth which doesn't make sense for selected terms
			$_include_term_depth = FALSE;

			// Exclude selected terms
		} elseif ( $term_compare == 'exclude' ) {

			// Exclude child terms or not
			if ( ! empty( $filter_item['term_exclude_children'] ) AND $term_ids ) {
				$terms_args['exclude_tree'] = explode( ',', $term_ids );
			} else {
				$terms_args['exclude'] = explode( ',', $term_ids );
			}

			// All terms
		} else {
			if ( empty( $filter_item['term_show_children'] ) ) {
				$terms_args['parent'] = $current_term_id;
			}
		}

		$terms_args = apply_filters( 'us_list_filter_terms_args', $terms_args, $filter_item );

		$terms = get_terms( $terms_args );

		$term_parents = array();

		foreach( $terms as $term ) {
			$selector_vars['item_values'][] = array(
				'id' => $term->term_id,
				'label' => $term->name,
				'value' => $term->slug,
				'count' => $term->count,
			);
			if ( $_include_term_depth ) {
				$term_parents[ $term->term_id ] = $term->parent;
			}
		}

		// Calculate depth for every term based on their hierarchy
		if ( $_include_term_depth ) {
			foreach( $selector_vars['item_values'] as &$value ) {
				$value['depth'] = $func_get_term_depth( $value['id'], $term_parents );
			}
		}

		// Source: WooCommerce
	} elseif ( $source_type == 'woo' AND ! empty( $source_name ) ) {

		// Bool logic for Onsale products
		if ( $source_name == 'onsale' ) {
			if ( $item_title == '' ) {
				$item_title = us_translate( 'On Sale', 'woocommerce' );
			}
			$selector_vars['item_values'][] = array(
				'label' => ! empty( $filter_item['bool_value_label'] )
					? $filter_item['bool_value_label']
					: us_translate( 'On-sale products', 'woocommerce' ),
				'value' => '1',
			);

			// Bool logic for Featured products
		} elseif ( $source_name == 'featured' ) {
			if ( $item_title == '' ) {
				$item_title = us_translate( 'Featured', 'woocommerce' );
			}
			$selector_vars['item_values'][] = array(
				'label' => ! empty( $filter_item['bool_value_label'] )
					? $filter_item['bool_value_label']
					: us_translate( 'Featured products', 'woocommerce' ),
				'value' => '1',
			);
		}

		// Source: Custom Field 
	} elseif ( $source_type == 'cf' AND ! empty( $source_name ) ) {

		$_has_numeric_values = ( $source_name == '_price' );

		// Bool logic for Stock Status custom field 
		if ( $source_name == '_stock_status' ) {
			$selector_vars['item_values'][] = array(
				'label' => ! empty( $filter_item['bool_value_label'] )
					? $filter_item['bool_value_label']
					: us_translate( 'In stock', 'woocommerce' ),
				'value' => 'instock',
			);
		}

		// ACF related conditions
		if ( function_exists( 'acf_get_field' ) AND $acf_field = acf_get_field( $source_name ) ) {

			if ( $item_title == '' ) {
				$item_title = us_arr_path( $acf_field, 'label', '' );
			}

			// Use choise values from custom fields with predefined choises
			if ( in_array( $acf_field['type'], array( 'button_group', 'checkbox', 'radio', 'select' ) ) ) {
				foreach ( us_arr_path( $acf_field, 'choices', array() ) as $choise_value => $choise_label ) {
					$selector_vars['item_values'][] = array(
						'label' => $choise_label,
						'value' => $choise_value,
					);
				}

				// True/False type support
			} elseif ( $acf_field['type'] == 'true_false' ) {
				$selector_vars['item_values'][] = array(
					'label' => ! empty( $filter_item['bool_value_label'] )
						? $filter_item['bool_value_label']
						: us_arr_path( $acf_field, 'message', '' ),
					'value' => '1',
				);

				// Numeric types support
			} elseif ( in_array( $acf_field['type'], array( 'number', 'range' ) ) ) {
				$_has_numeric_values = TRUE;
			}

			// ACF checkbox keeps the value as serialize array, so we need use the "LIKE" type compare for list meta_query
			if ( $acf_field['type'] == 'checkbox' ) {
				$item_atts['data-source-compare'] = 'like';
			}
		}

		// For numeric values we need to get MIN and MAX existing values
		if ( $_has_numeric_values AND empty( $selector_vars['item_values'] ) ) {
			global $wpdb;
			$minmax = $wpdb->get_row( "
				SELECT
					MIN( cast( meta_value as DECIMAL(10,3) ) ) AS min,
					MAX( cast( meta_value as DECIMAL(10,3) ) ) AS max
				FROM {$wpdb->postmeta}
				LEFT JOIN {$wpdb->posts} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id
				WHERE
					meta_key = " . $wpdb->prepare( '%s', $source_name ) . "
					AND meta_value != ''
					AND {$wpdb->posts}.post_type != 'revision'
					AND {$wpdb->posts}.post_status = 'publish'
				LIMIT 1;
			", ARRAY_A );

			// Generate values divided by step size
			if (
				$_step = abs( (float) us_arr_path( $filter_item, 'num_values_range', '' ) )
				AND $_step < $minmax['max']
			) {
				for ( $i = 0, $count = 0; $i < $minmax['max']; $i += $_step ) {

					if ( $i + $_step < $minmax['min'] ) {
						continue;
					} else {
						$count++;
					}
					if ( $count > $_values_output_limit ) {
						break;
					}

					$value = sprintf( '%s-%s', $i, $i + $_step );

					$selector_vars['item_values'][] = array(
						'label' => $value,
						'value' => $value,
					);
				}

				$item_atts['data-source-compare'] = 'between';
			}
		}

		// In other cases get all existing values from specific custom field
		if ( empty( $selector_vars['item_values'] ) ) {

			global $wpdb;
			$meta_values = $wpdb->get_col( "
				SELECT DISTINCT meta_value
				FROM {$wpdb->postmeta}
				LEFT JOIN {$wpdb->posts} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id
				WHERE
					meta_key = " . $wpdb->prepare( '%s', $source_name ) . "
					AND meta_value != ''
					AND {$wpdb->posts}.post_type != 'revision'
					AND {$wpdb->posts}.post_status = 'publish'
				LIMIT {$_values_output_limit}
			" );

			$meta_values = array_map( 'trim', $meta_values );

			natsort( $meta_values );

			$selector_vars['item_values'] = $meta_values;
		}

		if ( $item_title == '' ) {
			if ( $source_name == '_price' ) {
				$item_title = us_translate( 'Price', 'woocommerce' );
			} elseif ( $source_name == '_stock_status' ) {
				$item_title = us_translate( 'Stock status', 'woocommerce' );
			} else {
				$item_title = $source_name;
			}
		}

		// Source: Post Author
	} elseif ( $source_type == 'post' AND $source_name == 'author' AND ! is_author() ) {

		if ( $item_title == '' ) {
			$item_title = us_translate( 'Author' );
		}

		$author_args = array(
			'has_published_posts' => TRUE,
			'number' => $_values_output_limit,
		);

		$post_author = $filter_item['post_author'] ?? 'all';
		$post_author_ids = $filter_item['post_author_ids'] ?? '';

		if ( $post_author == 'include' ) {
			$author_args['include'] = explode( ',', $post_author_ids );
			$author_args['orderby'] = 'include';

		} elseif ( $post_author == 'exclude' ) {
			$author_args['exclude'] = explode( ',', $post_author_ids );
		}

		$author_args = apply_filters( 'us_list_filter_author_args', $author_args );

		foreach( get_users( $author_args ) as $user ) {

			// Show first and last names disregarding displayed name
			$_label = $user->first_name . ' ' . $user->last_name;

			if ( trim( $_label ) == '' ) {
				$_label = $user->display_name;
			}

			$selector_vars['item_values'][] = array(
				'label' => ucfirst( trim( $_label ) ), // remove spaces and up the first letter for correct sorting
				'value' => $user->ID,
			);
		}

		// Order alphabetically because get_users() doesn't allow to order by first/last name
		if ( $post_author != 'include' ) {
			asort( $selector_vars['item_values'] );
		}

		// Source: Post Type
	} elseif ( $source_type == 'post' AND $source_name == 'type' AND ! is_post_type_archive() ) {

		if ( $item_title == '' ) {
			$item_title = us_translate( 'Post Type' );
		}

		foreach( $_available_post_types as $post_type => $post_type_label ) {
			if ( $_specified_post_types AND ! in_array( $post_type, $_specified_post_types ) ) {
				continue;
			}
			$selector_vars['item_values'][] = array(
				'label' => $post_type_label,
				'value' => $post_type,
			);
		}

		// Source: Publication Date
	} elseif ( $source_type == 'post' AND $source_name == 'date' AND ! is_date() ) {

		if ( $item_title == '' ) {
			$item_title = us_translate( 'Published' );
		}

		$date_range = $filter_item['date_range'] ?? 'yearly';

		$_post_types = array_keys( $_available_post_types );

		foreach( $_post_types as $i => $post_type ) {
			if ( $_specified_post_types AND ! in_array( $post_type, $_specified_post_types ) ) {
				unset( $_post_types[ $i ] );
			}
		}
		$_sql_post_types = '"' . implode( '","', $_post_types ) . '"';

		$_sql_select = ( $date_range == 'monthly' )
			? 'YEAR(post_date) AS `year`, MONTH(post_date) AS `month`'
			: 'YEAR(post_date) AS `year`';

		$_sql_group_by = ( $date_range == 'monthly' )
			? 'YEAR(post_date), MONTH(post_date)'
			: 'YEAR(post_date)';

		// Get grouped results by date range, used wp_get_archives() as reference
		global $wpdb, $wp_locale;
		$_query = "
			SELECT {$_sql_select}, count(ID) as posts
			FROM $wpdb->posts
			WHERE post_type IN ({$_sql_post_types}) AND post_status = 'publish'
			GROUP BY {$_sql_group_by}
			ORDER BY post_date DESC
			LIMIT {$_values_output_limit}";

		foreach( $wpdb->get_results( $_query ) as $_result ) {
			$selector_vars['item_values'][] = array(

				'label' => ( $date_range == 'monthly' )
					? sprintf( us_translate( '%1$s %2$d' ), $wp_locale->get_month( $_result->month ), $_result->year )
					: $_result->year,

				'value' => ( $date_range == 'monthly' )
					? sprintf( '%s-%s', $_result->year, zeroise( $_result->month, 2 ) )
					: $_result->year,

				'count' => $_result->posts,
			);
		}
	}

	if ( in_array( $selector_type, array( 'radio', 'dropdown' ) ) AND ! empty( $filter_item['first_value_label'] ) ) {

		// Add the "placeholder" value to the beginning of values array
		array_unshift( $selector_vars['item_values'],
			array(
				'id' => 0,
				'label' => $filter_item['first_value_label'],
				'value' => '*',
			)
		);
	}

	if ( empty( $selector_vars['item_values'] ) ) {
		continue;
	}

	// Output single item
	$output_items .= '<div' . us_implode_atts( $item_atts ) . '>';
	$output_items .= '<button class="w-filter-item-title">';
	$output_items .= strip_tags( $item_title );

	// When filter has togglable appearance, the "Reset" link shoud be inside the item title
	if ( $enable_toggles ) {
		$output_items .= ' <span class="w-filter-item-reset" role="button">' . strip_tags( __( 'Reset', 'us' ) ) . '</span>';

		// In other cases use empty span for "Horizontal > Dropdown" styles to indicate selected values
	} else {
		$output_items .= '<span></span>';
	}

	$output_items .= '</button>'; // w-filter-item-title

	if ( ! $enable_toggles ) {
		$output_items .= '<a class="w-filter-item-reset" href="#" title="' . esc_attr( __( 'Reset', 'us' ) ) . '">';
		$output_items .= '<span>' . strip_tags( __( 'Reset', 'us' ) ) . '</span>';
		$output_items .= '</a>';
	}

	$item_values_atts = array(
		'class' => 'w-filter-item-values',
		'data-maxheight' => $values_max_height,
	);
	if ( ! empty( $values_max_height ) ) {
		$item_values_atts['style'] = 'max-height:' . $values_max_height;
	}
	$output_items .= '<div' . us_implode_atts( $item_values_atts ) . '>';

	$output_items .= us_get_template( 'templates/us_grid/filter-ui-types/' . $selector_type, $selector_vars );

	if ( count( $selector_vars['item_values'] ) >= $_values_output_limit ) {
		$output_items .= '<small>' . sprintf( __( 'Only first %s values are shown', 'us' ), $_values_output_limit ) . '</small>';
	}

	$output_items .= '</div>'; // w-filter-item-values
	$output_items .= '</div>'; // w-filter-item
}

$output .= $output_items;

$output .= '</div>'; // w-filter-list

// Mobiles related button and styles
if ( ! empty( $mobile_width ) AND $output_items !== '' ) {
	$output .= '<div class="w-filter-list-panel">';
	$output .= '<button class="w-btn ' . us_get_btn_class() . '">';
	$output .= '<span class="w-btn-label">' . strip_tags( us_translate( 'Apply' ) ) . '</span>';
	$output .= '</button>';
	$output .= '</div>';

	$mobile_button_atts = array(
		'class' => 'w-filter-opener',
	);

	// Make link as Button if set
	if ( ! empty( $mobile_button_style ) ) {
		$mobile_button_atts['class'] .= ' w-btn ' . us_get_btn_class( $mobile_button_style );
	}

	$mobile_button_icon_html = '';
	if ( ! empty( $mobile_button_icon ) ) {
		$mobile_button_icon_html = us_prepare_icon_tag( $mobile_button_icon );
		$mobile_button_atts['class'] .= ' icon_at' . $mobile_button_iconpos;

		if ( is_rtl() ) {
			$mobile_button_iconpos = ( $mobile_button_iconpos == 'left' ) ? 'right' : 'left';
		}
	}

	// Force aria-label when label is empty
	if ( empty( $mobile_button_label ) ) {
		$mobile_button_atts['class'] .= ' text_none';
		$mobile_button_atts['aria-label'] = __( 'Filters', 'us' );
	}

	$style = '@media( max-width:' . (int) $mobile_width . 'px ) {';
	$style .= '.w-filter.state_desktop .w-filter-list,';
	$style .= '.w-filter-item-title > span { display: none; }';
	$style .= '.w-filter-opener { display: inline-block; }';
	$style .= '}';

	$output .= '<style>' . us_minify_css( $style ) . '</style>';
	$output .= '<button' . us_implode_atts( $mobile_button_atts ) . '>';
	if ( $mobile_button_iconpos == 'left' ) {
		$output .= $mobile_button_icon_html;
	}
	$output .= '<span>' . strip_tags( $mobile_button_label ) . '</span>';
	if ( $mobile_button_iconpos == 'right' ) {
		$output .= $mobile_button_icon_html;
	}
	$output .= '</button>'; // w-filter-opener
}

$output .= '</form>'; // w-filter

echo $output;
