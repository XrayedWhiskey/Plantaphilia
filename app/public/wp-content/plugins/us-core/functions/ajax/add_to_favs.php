<?php defined( 'ABSPATH' ) or die( 'This script cannot be accessed directly.' );

/**
 * AJAX handler for the add_to_favs shortcode
 */
if ( ! function_exists( 'us_ajax_add_post_to_favorites' ) ) {
	add_action( 'wp_ajax_nopriv_us_add_post_to_favorites', 'us_ajax_add_post_to_favorites' );
	add_action( 'wp_ajax_us_add_post_to_favorites', 'us_ajax_add_post_to_favorites' );
	function us_ajax_add_post_to_favorites() {
		if ( ! is_user_logged_in() ) {
			wp_send_json_error();
		}

		$post_ids = us_get_user_favorite_post_ids();

		if ( $post_id = (int) us_arr_path( $_POST, 'post_id' ) ) {
			if ( ( $i = array_search( $post_id, $post_ids, TRUE ) ) !== FALSE ) {
				unset( $post_ids[ $i ] );
			} else {
				$post_ids[] = $post_id;
			}
			update_user_meta( get_current_user_id(), 'us_favorite_post_ids', array_unique( $post_ids ) );
		}

		wp_send_json_success( implode( ',', $post_ids ) );
	}
}
