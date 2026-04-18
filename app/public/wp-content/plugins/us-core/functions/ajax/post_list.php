<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * AJAX pagination for the Post List shortcode
 */
if ( ! function_exists( 'us_ajax_post_list' ) ) {
	add_action( 'wp_ajax_nopriv_us_ajax_post_list', 'us_ajax_post_list' );
	add_action( 'wp_ajax_us_ajax_post_list', 'us_ajax_post_list' );

	function us_ajax_post_list() {
		if ( ! check_ajax_referer( 'us_post_list', '_nonce', FALSE ) ) {
			wp_send_json_error(
				array(
					'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
				)
			);
		}

		$template_vars = us_get_HTTP_POST_json( 'template_vars' );

		// Exclude posts of previous lists
		if ( isset( $template_vars['us_post_ids_shown_by_grid'] ) ) {
			global $us_post_ids_shown_by_grid;
			$us_post_ids_shown_by_grid = array_map( 'intval', (array) $template_vars['us_post_ids_shown_by_grid'] );
		}

		$template_vars = us_shortcode_atts( $template_vars, 'us_post_list' );

		if ( isset( $_POST['paged'] ) ) {
			$template_vars['paged'] = (int) $_POST['paged'];
		}
		foreach ( array( 'list_search', 'list_order', 'list_filter' ) as $param_name ) {
			if ( isset( $_POST[ $param_name ] ) ) {
				$template_vars[ $param_name ] = (string) $_POST[ $param_name ];
			}
		}

		us_load_template( 'templates/elements/post_list', $template_vars );

		die;
	}
}

/**
 * AJAX pagination for the Product List shortcode
 */
if ( ! function_exists( 'us_ajax_product_list' ) ) {
	add_action( 'wp_ajax_nopriv_us_ajax_product_list', 'us_ajax_product_list' );
	add_action( 'wp_ajax_us_ajax_product_list', 'us_ajax_product_list' );

	function us_ajax_product_list() {
		if ( ! check_ajax_referer( 'us_product_list', '_nonce', FALSE ) ) {
			wp_send_json_error(
				array(
					'message' => us_translate( 'An error has occurred. Please reload the page and try again.' ),
				)
			);
		}

		$template_vars = us_get_HTTP_POST_json( 'template_vars' );

		// Exclude posts of previous lists
		if ( isset( $template_vars['us_post_ids_shown_by_grid'] ) ) {
			global $us_post_ids_shown_by_grid;
			$us_post_ids_shown_by_grid = array_map( 'intval', (array) $template_vars['us_post_ids_shown_by_grid'] );
		}

		$template_vars = us_shortcode_atts( $template_vars, 'us_product_list' );

		if ( isset( $_POST['paged'] ) ) {
			$template_vars['paged'] = (int) $_POST['paged'];
		}
		foreach ( array( 'list_search', 'list_order', 'list_filter' ) as $param_name ) {
			if ( isset( $_POST[ $param_name ] ) ) {
				$template_vars[ $param_name ] = (string) $_POST[ $param_name ];
			}
		}

		us_load_template( 'templates/elements/product_list', $template_vars );

		die;
	}
}
