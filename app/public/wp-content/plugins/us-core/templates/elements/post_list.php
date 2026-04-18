<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Post List
 */

// Never output this element inside other Grids
global $us_grid_outputs_items;
if ( $us_grid_outputs_items ) {
	return;
}

if ( $post_author == 'current_user' AND ! is_user_logged_in() ) {
	return;
}

// Define relevant values into global variable
global $us_grid_no_results;
$us_grid_no_results = array(
	'action' => $no_items_action,
	'message' => $no_items_message,
	'page_block' => $no_items_page_block,
);

// "Hide on" values are needed for the "No results" block
global $us_grid_hide_on_states;
$us_grid_hide_on_states = $hide_on_states;

// Required checks, when this template is loaded via ajax
$filled_atts = $filled_atts ?? $vars;
$paged = $paged ?? 1;

// Force "Numbered" pagination for AMP version to avoid AMP ajax developing
if ( us_amp() AND $pagination != 'none' ) {
	$pagination = 'numbered';
}
global $us_ajax_list_pagination;
if (
	$pagination == 'numbered'
	OR $us_ajax_list_pagination
) {
	// Fix for get_query_var() that is empty on AMP frontpage
	$request_paged = ( is_front_page() AND ! us_amp() ) ? 'page' : 'paged';

	if ( get_query_var( $request_paged ) ) {
		$paged = get_query_var( $request_paged );
	}
}

// Get the ID of the current object (post, term, user)
$current_object_id = us_get_current_id();

/*
 * Generate query for WP_Query
 */
$query_args = array(
	'ignore_sticky_posts' => TRUE, // speeds up the query
	'post__not_in' => array(),
	'tax_query' => array(),
	'meta_query' => array(),
	'posts_per_page' => $show_all ? 999 : (int) $quantity,
	'paged' => $paged,
);

// Append the current query args if set
if (
	$source == 'current_wp_query'
	AND ! usb_is_template_preview()
) {
	global $wp_query;
	$query_args += $wp_query->query_vars;
	$query_args['posts_per_page'] = $wp_query->posts_per_page;
} else {
	$query_args['post_type'] = 'any'; // required change, because the 'post' is used when 'post_type' is not set
}

// If there is no pagination, then disable page count
if ( $pagination == 'none' ) {
	$query_args['no_found_rows'] = TRUE; // speeds up the query
}

// Fix post status because in Live preview they are different from the front-end
if ( usb_is_preview() ) {
	$query_args['post_status'] = array( 'publish', 'private' );
}

if ( $post_type AND $source != 'current_wp_query' ) {
	$query_args['post_type'] = explode( ',', $post_type );
}

// Exclude child posts
if ( $exclude_children ) {
	$query_args['post_parent'] = '0';
}

// Selected posts
if ( $source == 'post__in' ) {
	$query_args['post__in'] = explode( ',', $ids );

	// Exclude selected posts
} elseif ( $source == 'post__not_in' ) {
	$query_args['post__not_in'] = explode( ',', $ids );

	// Child posts of the current one
} elseif ( $source == 'child_posts_of_current' AND ! usb_is_template_preview() ) {
	$query_args['post_parent'] = $current_object_id;
	$query_args['post_type'] = 'any';

	// Child posts of selected posts
} elseif ( $source == 'child_posts_of_selected' ) {
	$query_args['post_parent__in'] = explode( ',', $ids );

	// Favorites of the current user
} elseif ( $source == 'user_favorite_ids' AND ! usb_is_preview() ) {
	$user_favorite_ids = us_get_user_favorite_post_ids();

	// Use the non-existing id to get no results, because empty 'post__in' is ignored by query
	if ( ! $user_favorite_ids ) {
		$user_favorite_ids = array( 0 );
	}

	$query_args['post__in'] = (array) $user_favorite_ids;
}

// Exclude the current post from the query
if (
	$exclude_current_post
	AND $current_object_id
	AND $source != 'current_wp_query'
) {
	// Separate conditions because 'post__in' and 'post__not_in' can't work together
	if ( ! empty( $query_args['post__in'] ) ) {
		$_post_ids = array_diff( $query_args['post__in'], array( $current_object_id ) );
		$query_args['post__in'] = $_post_ids ? $_post_ids : array( 0 );
	} else {
		$query_args['post__not_in'] = array_merge( $query_args['post__not_in'], array( $current_object_id ) );
	}
}

$list_end_vars = array();

// Exclude posts of previous lists
if ( $exclude_prev_posts ) {
	global $us_post_ids_shown_by_grid;
	if ( $us_post_ids_shown_by_grid ) {
		$list_end_vars['us_post_ids_shown_by_grid'] = $query_args['post__not_in'] = array_merge( $query_args['post__not_in'], $us_post_ids_shown_by_grid );
	}
}

// Post Author
if ( $post_author == 'include' ) {
	$query_args['author__in'] = explode( ',', $post_author_ids );
} elseif ( $post_author == 'exclude' ) {
	$query_args['author__not_in'] = explode( ',', $post_author_ids );
} elseif ( $post_author == 'current_author' ) {
	$query_args['author'] = get_the_author_meta( 'ID' );
} elseif ( $post_author == 'current_user' ) {
	$query_args['author'] = get_current_user_id();
}

// Tax Query
if ( is_string( $tax_query ) ) {
	$tax_query = json_decode( urldecode( $tax_query ), TRUE );
}
if ( ! is_array( $tax_query ) ) {
	$tax_query = array();
}
if ( $tax_query_relation != 'none' AND ! empty( $tax_query ) ) {
	foreach ( $tax_query as &$_tax ) {

		// Explode terms IDs to array
		if ( ! empty( $_tax['terms'] ) ) {
			$_tax['terms'] = explode( ',', $_tax['terms'] );
		}

		// Get terms of the current post
		if ( $_tax['operator'] == 'CURRENT' AND ! usb_is_template_preview() ) {
			$_tax['terms'] = wp_get_object_terms( $current_object_id, $_tax['taxonomy'], array( 'fields' => 'ids' ) );
			$_tax['operator'] = 'IN';
		}

		// Check #4124 issue for explanation
		if ( empty( $_tax['terms'] ) ) {
			$_tax['operator'] = ( $_tax['operator'] == 'NOT IN' ) ? 'NOT EXISTS' : 'EXISTS';
		}

		// Transfer to bool var type
		$_tax['include_children'] = (bool) $_tax['include_children'];
	}
	unset( $_tax );
	$tax_query['relation'] = $tax_query_relation;
	$query_args['tax_query'] = $tax_query;
}

// Meta Query
if ( is_string( $meta_query ) ) {
	$meta_query = json_decode( urldecode( $meta_query ), TRUE );
}
if ( ! is_array( $meta_query ) ) {
	$meta_query = array();
}
if ( $meta_query_relation != 'none' AND ! empty( $meta_query ) ) {
	foreach ( $meta_query as &$_meta ) {

		// Set the NUMERIC type for specific "compare" values
		if ( in_array( $_meta['compare'], array( '>', '>=', '<', '<=' ) ) ) {
			$_meta['type'] = 'NUMERIC';
		}

		// Force date/time type if the relevant dynamic value is set
		if ( $_meta['value'] == '{{today_now}}' OR strpos( $_meta['value'], '{{date|') !== FALSE ) {
			$_meta['type'] = 'DATETIME';
		} elseif ( $_meta['value'] == '{{today}}' ) {
			$_meta['type'] = 'DATE';
		} elseif ( $_meta['value'] == '{{now}}' ) {
			$_meta['type'] = 'TIME';
		}

		// Unset the field value for specific "compare" values
		if ( in_array( $_meta['compare'], array( 'EXISTS', 'NOT EXISTS' ) ) AND isset( $_meta['value'] ) ) {
			unset( $_meta['value'] );
		} else {
			$_meta['value'] = us_replace_dynamic_value( $_meta['value'] );
		}
	}
	unset( $_meta );
	$meta_query['relation'] = $meta_query_relation;
	$query_args['meta_query'] = $meta_query;
}

// List Filter element support
if ( isset( $list_filter ) ) {
	us_apply_filtering_to_list_query( $query_args, $list_filter );
}

// List Search element support
if ( isset( $list_search ) ) {
	$query_args['s'] = sanitize_text_field( $list_search );
}

// List Order element support
if ( isset( $list_order ) ) {
	$order_params = array_map( 'trim', explode( ',', $list_order ) );

	$orderby = $order_params[0] ?? '';
	$orderby_custom_field = $order_params[1] ?? '';
	$orderby_custom_type = in_array( 'numeric', $order_params );
	$order_invert = in_array( 'asc', $order_params );

	unset( $order_params );
}

us_apply_orderby_to_list_query(
	$query_args,
	$orderby,
	$orderby_custom_field,
	$orderby_custom_type,
	$order_invert
);

// Apply filter for developers purposes
$query_args = apply_filters( 'us_post_list_query_args', $query_args, $filled_atts );

$post_list_query = new WP_Query( $query_args );
unset( $query_args );

// Override global $wp_query for correct pagination on non-archive pages
// It must be reseted in the end of the output
if ( $source != 'current_wp_query' ) {
	global $wp_query;
	$wp_query = $post_list_query;
}

$no_results = $post_list_query->have_posts() ? FALSE : TRUE;

$grid_layout_settings = us_get_grid_layout_settings( $items_layout, /* default_template */ 'blog_1' );

$list_start_vars = array(
	'classes' => $classes ?? '',
	'grid_atts' => $_atts ?? array(),
	'grid_elm_id' => ! empty( $el_id ) ? $el_id : sprintf( 'us_post_list_%s', us_uniqid() ),
	'grid_layout_settings' => $grid_layout_settings,
	'no_results' => $no_results,
);

$list_start_vars['classes'] .= ' us_post_list';

// Needed for Ajax pagination
if ( $source == 'current_wp_query' ) {
	$list_start_vars['classes'] .= ' for_current_wp_query';
}

us_load_template( 'templates/us_grid/listing-start', $list_start_vars + $filled_atts );

if ( ! $no_results ) {

	$list_post_vars = array(
		'columns' => $columns,
		'grid_layout_settings' => $grid_layout_settings,
		'type' => $type,
		'ignore_items_size' => $ignore_items_size,
		'load_animation' => $load_animation,
		'overriding_link' => $overriding_link,
		'current_page_id' => $exclude_current_post ? 0 : $current_object_id,
	);

	while ( $post_list_query->have_posts() ) {
		$post_list_query->the_post();

		us_load_template( 'templates/us_grid/listing-post', $list_post_vars );
	}
}

$list_end_vars += array(
	'paged' => $paged,
	'max_num_pages' => $post_list_query->max_num_pages,
	'no_results' => $no_results,
);
us_load_template( 'templates/us_grid/list-end', $list_end_vars + $filled_atts );
