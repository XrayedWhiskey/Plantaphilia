<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * End part of post/product list output
 */

$output = '</div>'; // .w-grid-list

// Reset global $wp_query and $post variables.
if ( $source != 'current_wp_query' ) {
	wp_reset_query();
}

// Reset grid items counter
global $us_grid_item_counter;
$us_grid_item_counter = 0;

// Reset grid outputing items
global $us_grid_outputs_items;
$us_grid_outputs_items = FALSE;

// Reset the grid item type
global $us_grid_item_type;
$us_grid_item_type = NULL;

// Reset the image size for the next grid/list element
global $us_grid_img_size;
$us_grid_img_size = NULL;

// Global preloader type
$preloader_type = us_get_option( 'preloader' );
if ( ! is_numeric( $preloader_type ) ) {
	$preloader_type = '1';
}

/**
 * @var array JSON data to export to frontend
 */
$json_data = array(
	'max_num_pages' => $max_num_pages,
	'pagination' => $pagination,
	'paged' => $paged,
	'ajaxData' => array(),
);

// Numbered pagination
if (
	$pagination == 'numbered'
	AND (
		$max_num_pages > 1
		OR usb_is_preview()
	)
) {
	// The main parameters for the formation of pagination
	$paginate_args = array(
		'after_page_number' => '</span>',
		'before_page_number' => '<span>',
		'mid_size' => 3,
		'next_text' => '<span>' . us_translate( 'Next' ) . '</span>',
		'prev_text' => '<span>' . us_translate( 'Previous' ) . '</span>',
		'total' => $max_num_pages,
	);

	// Removes from `admin-ajax.php` links
	$paginate_links = paginate_links( $paginate_args );
	$paginate_home_url = trailingslashit(
		has_filter( 'us_tr_home_url' )
			? apply_filters( 'us_tr_home_url', NULL )
			: home_url()
	);
	$paginate_links = str_replace( $paginate_home_url . 'wp-admin/admin-ajax.php', '', $paginate_links );

	$paginate_class = 'nav-links';
	if ( ! empty( $pagination_style ) ) {
		$paginate_class .= ' custom us-nav-style_' . (int) $pagination_style;
	}

	$output .= '<nav class="pagination navigation" role="navigation">';
	$output .= '<div class="' . $paginate_class . '">' . $paginate_links . '</div>';
	$output .= '</nav>'; // .pagination
}

// Always output "Load more" block to show preloader on ajax requests
$loadmore_class = 'g-loadmore';
if ( $pagination_btn_fullwidth ) {
	$loadmore_class .= ' width_full';
}
if ( $max_num_pages <= 1 ) {
	$loadmore_class .= ' hidden';
}
$output .= '<div class="' . $loadmore_class . '">';
$output .= '<div class="g-preloader type_' . $preloader_type . '"><div></div></div>';

if ( $max_num_pages > 1 AND $pagination == 'load_on_btn'  ) {
	$output .= '<button class="w-btn ' . us_get_btn_class( $pagination_btn_style ) . '"' . us_prepare_inline_css( array( 'font-size' => $pagination_btn_size ) ) . '>';
	$output .= '<span class="w-btn-label">' . us_replace_dynamic_value( $pagination_btn_text ) . '</span>';
	$output .= '</button>'; // .w-btn
}

$output .= '</div>'; // .g-loadmore

// Popup html
if ( strpos( $overriding_link, 'popup_post' ) !== FALSE ) {
	$popup_vars = array(
		'overriding_link' => $overriding_link,
		'popup_width' => $popup_width,
		'preloader_type' => $preloader_type,
		'popup_arrows' => $popup_arrows,
	);
	$output .= us_get_template( 'templates/us_grid/popup', $popup_vars );
}

// Ajax data for pagination
if ( $source == 'current_wp_query' ) {

	// Set the link template for get pages
	if ( in_array( $pagination, array( 'load_on_btn', 'load_on_scroll' ) ) ) {
		global $wp;
		$url_params = $wp->query_vars;
		$url_params['paged'] = rawurlencode( '{num_page}' );
		if ( isset( $_GET['list_search'] ) ) {
			$url_params['list_search'] = get_query_var( 's' );
		}
		if ( isset( $_GET['list_order'] ) ) {
			$url_params['list_order'] = (string) $_GET['list_order'];
		}
		if ( isset( $_GET['list_filter'] ) ) {
			$url_params['list_filter'] = urlencode( wp_unslash( (string) $_GET['list_filter'] ) );
		}
		$json_data['ajaxUrl'] = add_query_arg( $url_params, trailingslashit( home_url() ) );
	}

	global $us_post_list_index;
	if ( is_null( $us_post_list_index ) ) {
		$us_post_list_index = 0;
	}
	$json_data['ajaxData'] = array(
		'us_ajax_list_pagination' => 1,
		'us_ajax_list_index' => $us_post_list_index++,
	);

} else {
	$json_data['ajaxData'] = array(
		'_nonce' => ! empty( $is_product_list ) ? wp_create_nonce( 'us_product_list' ) : wp_create_nonce( 'us_post_list' ),
		'action' => ! empty( $is_product_list ) ? 'us_ajax_product_list' : 'us_ajax_post_list',
		'template_vars' => $vars,
	);
}

// Add these ajax variables only after the wp_reset_query()
$json_data['ajaxData'] += array(
	'meta_type' => us_get_current_meta_type(),
	'object_id' => us_get_current_id(),
);

$output .= '<div class="w-grid-list-json hidden"' . us_pass_data_to_js( $json_data ) . '></div>';
$output .= '</div>'; // .w-grid

echo $output;

// Output the "No results" block after the "w-grid" div container
if ( $no_results ) {
	us_grid_shows_no_results();
}
