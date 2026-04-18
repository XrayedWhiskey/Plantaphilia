<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Post Views Counter Support
 *
 * https://wordpress.org/plugins/post-views-counter/
 */

if ( ! class_exists( 'Post_Views_Counter' ) ) {
	return;
}

if ( ! function_exists( 'us_pvc_enqueue_styles' ) ) {
	add_filter( 'pvc_enqueue_styles', 'us_pvc_enqueue_styles', 100 );
	/**
	 * Removing styles from the Post Views counter plugin
	 *
	 * @return bool
	 */
	function us_pvc_enqueue_styles() {
		if ( us_get_option( 'optimize_assets' ) AND is_plugin_active( 'post-views-counter/post-views-counter.php' ) ) {
			return FALSE;
		}
		return TRUE;
	}
}

if ( ! function_exists( 'us_pvc_post_list_orderby_options' ) ) {
	add_filter( 'us_post_list_orderby_options', 'us_pvc_post_list_orderby_options', 501, 1 );
	add_filter( 'us_product_list_orderby_options', 'us_pvc_post_list_orderby_options', 501, 1 );
	/**
	 * Expand "orderby" options in Post List element.
	 *
	 * @param array $options The options list.
	 * @return array Returns a list of "orderby" options.
	 */
	function us_pvc_post_list_orderby_options( $options ) {
		$options += array(
			'post_views' => __( 'Total views', 'us' ),
			'post_views_today' => __( 'Views today', 'us' ),
			'post_views_this_week' => __( 'Views this week', 'us' ),
			'post_views_this_month' => __( 'Views this month', 'us' ),
			'post_views_this_year' => __( 'Views this year', 'us' ),
		);
		// Custom option is always at the end
		if ( isset( $options['custom'] ) ) {
			$custom = $options['custom'];
			unset( $options['custom'] );
			$options['custom'] = $custom;
		}
		return $options;
	}
}

if ( ! function_exists( 'us_pvc_post_list_query_args' ) ) {
	add_filter( 'us_post_list_query_args', 'us_pvc_post_list_query_args', 501, 2 );
	add_filter( 'us_product_list_query_args', 'us_pvc_post_list_query_args', 501, 2 );
	/**
	 * Modify the database query to use post_views parameter.
	 *
	 * @param arrat $query_args The query arguments.
	 * @param array $filled_atts The filled atts.
	 * @return array Returns array of arguments passed to WP_Query.
	 */
	function us_pvc_post_list_query_args( $query_args, $filled_atts ) {
		if ( $orderby = us_arr_path( $filled_atts, 'orderby' ) ) {

			// This year
			if ( $orderby == 'post_views_this_year' ) {
				$query_args['orderby'] = 'post_views';
				$query_args['views_query'] = array(
					'year' => wp_date( 'Y' ),
				);

				// This month
			} elseif ( $orderby == 'post_views_this_month' ) {
				$query_args['orderby'] = 'post_views';
				$query_args['views_query'] = array(
					'year' => wp_date( 'Y' ),
					'month' => wp_date( 'm' ),
				);

				// This week
			} elseif ( $orderby == 'post_views_this_week' ) {
				$query_args['orderby'] = 'post_views';
				$query_args['views_query'] = array(
					'year' => wp_date( 'Y' ),
					'week' => wp_date( 'W' ),
				);

				// Today
			} elseif ( $orderby == 'post_views_today' ) {
				$query_args['orderby'] = 'post_views';
				$query_args['views_query'] = array(
					'year' => wp_date( 'Y' ),
					'month' => wp_date( 'm' ),
					'day' => wp_date( 'd' ),
				);
			}
		}
		return $query_args;
	}
}
