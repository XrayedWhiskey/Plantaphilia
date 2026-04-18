<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Method for lists: List Filter, List Order, List Search, Post List, Product List
 */

if ( ! function_exists( 'us_get_list_filter_params' ) ) {
	/**
	 * Get available params for filtering, including URL names
	 */
	function us_get_list_filter_params() {

		$unique_url_names = array();

		static $params = array();
		if ( ! empty( $params ) ) {
			return $params;
		}

		$params['post|type'] = array(
			'label' => us_translate( 'Post Type' ),
			'group' => us_translate( 'Post Attributes' ),
			'url_name' => 'post_type',
		);
		$unique_url_names[] = 'post_type';
		$params['post|author'] = array(
			'label' => us_translate( 'Author' ),
			'group' => us_translate( 'Post Attributes' ),
			'url_name' => 'post_author',
		);
		$unique_url_names[] = 'post_author';
		$params['post|date'] = array(
			'label' => __( 'Date of creation', 'us' ),
			'group' => us_translate( 'Post Attributes' ),
			'url_name' => 'post_date',
		);
		$unique_url_names[] = 'post_date';

		if ( class_exists( 'woocommerce' ) ) {

			// Price and Stock Status are common custom fields, so keep the "cf" prefix
			$params['cf|_price'] = array(
				'label' => us_translate( 'Price', 'woocommerce' ),
				'group' => us_translate( 'WooCommerce', 'woocommerce' ),
				'url_name' => '_price',
				'value_type' => 'numeric',
			);
			$unique_url_names[] = '_price';
			$params['cf|_stock_status'] = array(
				'label' => us_translate( 'Stock status', 'woocommerce' ),
				'group' => us_translate( 'WooCommerce', 'woocommerce' ),
				'url_name' => '_stock_status',
				'value_type' => 'bool',
			);
			$unique_url_names[] = '_stock_status';

			// Definition Onsale and Featured products is more complex, so make the with "woo" prefix
			$params['woo|onsale'] = array(
				'label' => us_translate( 'On Sale', 'woocommerce' ),
				'group' => us_translate( 'WooCommerce', 'woocommerce' ),
				'url_name' => 'onsale',
				'value_type' => 'bool',
			);
			$unique_url_names[] = 'onsale';
			$params['woo|featured'] = array(
				'label' => us_translate( 'Featured', 'woocommerce' ),
				'group' => us_translate( 'WooCommerce', 'woocommerce' ),
				'url_name' => 'featured',
				'value_type' => 'bool',
			);
			$unique_url_names[] = 'featured';
		}

		foreach( us_get_taxonomies() as $slug => $label ) {

			$unique_tax_name = in_array( $slug, $unique_url_names )
				? $slug . '_' . count( $unique_url_names )
				: $slug;
			$unique_tax_name = sanitize_title( $unique_tax_name );

			$params['tax|' . $slug ] = array(
				'label' => $label,
				'group' => __( 'Taxonomies', 'us' ),
				'url_name' => $unique_tax_name,
			);

			$unique_url_names[] = $unique_tax_name;
		}

		// Add fields from "Advanced Custom Fields" plugin
		if ( function_exists( 'acf_get_field_groups' ) AND $acf_groups = acf_get_field_groups() ) {
			foreach ( $acf_groups as $group ) {
				foreach ( (array) acf_get_fields( $group['ID'] ) as $field ) {

					// ACF types supported by List Filter
					if ( in_array( $field['type'], array( 'text', 'number', 'range', 'select', 'checkbox', 'radio', 'button_group', 'true_false' ) ) ) {

						$unique_cf_name = in_array( $field['name'], $unique_url_names )
							? $field['name'] . '_' . count( $unique_url_names )
							: $field['name'];
						$unique_cf_name = sanitize_title( $unique_cf_name );

						$params[ 'cf|' . $field['name'] ] = array(
							'label' => $group['title'] . ': ' . $field['label'],
							'group' => us_translate( 'Custom Fields', ),
							'url_name' => $unique_cf_name,
						);

						$unique_url_names[] = $unique_cf_name;
					}

					// ACF types for numeric values
					if ( in_array( $field['type'], array( 'number', 'range' ) ) ) {
						$params[ 'cf|' . $field['name'] ]['value_type'] = 'numeric';
					}

					// ACF types for bool values
					if ( in_array( $field['type'], array( 'true_false' ) ) ) {
						$params[ 'cf|' . $field['name'] ]['value_type'] = 'bool';
					}
				}
			}
		}

		unset( $unique_url_names );

		return apply_filters( 'us_get_list_filter_params', $params );
	}
}

if ( ! function_exists( 'us_apply_filtering_to_list_query' ) ) {
	/**
	 * Apply the List Filter params to the provided query_args.
	 */
	function us_apply_filtering_to_list_query( &$query_args, $list_filter ) {

		$list_filter = json_decode( wp_unslash( $list_filter ), TRUE );

		if ( ! is_array( $list_filter ) ) {
			return;
		}

		foreach ( $list_filter as $source => $values ) {

			$query_args = apply_filters( 'us_apply_filtering_to_list_query', $query_args, $source, $values );

			$values = (array) $values;
			$source_name = $source_compare = '';

			// Source examples: 'tax|product_cat', 'cf|_price|between', 'cf|_stock_status', 'post|author', 'post|date|date_after'
			if ( strpos( $source, '|' ) !== FALSE ) {
				$source_type = strtok( $source, '|' );
				$source_name = strtok( '|' );
				$source_compare = strtok( '|' );
			} else {
				$source_type = $source;
			}

			switch ( $source_type ) {

				case 'post':
					if ( $source_name == 'type' ) {
						$query_args['post_type'] = $values;

					} elseif ( $source_name == 'author' ) {
						$query_args['author__in'] = $values;

					} elseif ( $source_name == 'date' ) {
						$query_args['date_query']['relation'] = 'OR';

						foreach ( $values as $value ) {
							$query_args['date_query'][] = array(
								'before' => $value,
								'after' => $value,
								'inclusive' => TRUE,
							);
						}
					}
					break;

				case 'tax':
					$query_args['tax_query']['relation'] = 'AND';

					$query_args['tax_query'][] = array(
						'taxonomy' => $source_name,
						'field' => 'slug',
						'terms' => $values,
					);
					break;

				case 'woo':
					if ( $source_name == 'onsale' AND $values ) {
						$onsale_ids = wc_get_product_ids_on_sale();

						// Exclude ids matching 'post__not_in' first
						if ( ! empty( $query_args['post__not_in'] ) ) {
							$onsale_ids = array_diff( $onsale_ids, $query_args['post__not_in'] );
						}

						// then add ids matching 'post__in' if set
						if ( ! empty( $query_args['post__in'] ) ) {
							$query_args['post__in'] = array_intersect( $onsale_ids, $query_args['post__in'] );
						} else {
							$query_args['post__in'] = $onsale_ids;
						}

						// Use the non-existing id to get no results, because empty 'post__in' is ignored by query
						if ( ! $query_args['post__in'] ) {
							$query_args['post__in'] = array( 0 );
						}

					} elseif ( $source_name == 'featured' AND $values ) {
						$featured_ids = wc_get_featured_product_ids();

						// Exclude ids matching 'post__not_in' first
						if ( ! empty( $query_args['post__not_in'] ) ) {
							$featured_ids = array_diff( $featured_ids, $query_args['post__not_in'] );
						}

						// then add ids matching 'post__in' if set
						if ( ! empty( $query_args['post__in'] ) ) {
							$query_args['post__in'] = array_intersect( $featured_ids, $query_args['post__in'] );
						} else {
							$query_args['post__in'] = $featured_ids;
						}

						// Use the non-existing id to get no results, because empty 'post__in' is ignored by query
						if ( ! $query_args['post__in'] ) {
							$query_args['post__in'] = array( 0 );
						}
					}
					break;

				case 'cf':
					$query_args['meta_query']['relation'] = 'AND';

					if ( $source_compare == 'between' ) {
						$_meta_query_inner = array(
							'relation' => 'OR',
						);
						foreach ( $values as $value ) {
							$_meta_query_inner[] = array(
								'key' => $source_name,
								'value' => explode( '-', $value ),
								'compare' => 'BETWEEN',
								'type' => 'DECIMAL(10,3)',
							);
						}
						$query_args['meta_query'][] = $_meta_query_inner;

					} elseif ( $source_compare == 'like' ) {
						$_meta_query_inner = array(
							'relation' => 'OR',
						);
						foreach ( $values as $value ) {
							$_meta_query_inner[] = array(
								'key' => $source_name,
								'value' => sprintf( ':"%s";', $value ),
								'compare' => 'LIKE',
							);
						}
						$query_args['meta_query'][] = $_meta_query_inner;

					} else {
						$query_args['meta_query'][] = array(
							'key' => $source_name,
							'value' => $values,
							'compare' => 'IN',
						);
					}
					break;
			}
		}
	}
}

if ( ! function_exists( 'us_apply_orderby_to_list_query' ) ) {
	/**
	 * Apply the orderby params to the provided query_args.
	 */
	function us_apply_orderby_to_list_query( &$query_args, $orderby, $orderby_custom_field, $orderby_custom_type, $order_invert ) {

		if ( empty( $orderby ) ) {
			return;
		}

		// Order by custom field
		if ( $orderby == 'custom' AND ! empty( $orderby_custom_field ) ) {
			$orderby = ! empty( $orderby_custom_type ) ? 'meta_value_num' : 'meta_value';
			$query_args['meta_key'] = $orderby_custom_field;
		}

		// If 'orderby' param is not set by the current query, use the element settings
		if ( $orderby != 'current_wp_query' ) {
			$query_args['orderby'] = $orderby;
			$query_args['order'] = ! empty( $order_invert ) ? 'DESC' : 'ASC';
		}

		if ( isset( $query_args['orderby'] ) ) {

			if ( $query_args['orderby'] == 'price' ) {
				$query_args['orderby'] = 'meta_value_num';
				$query_args['meta_key'] = '_price';

			} elseif ( $query_args['orderby'] == 'total_sales' ) {
				$query_args['orderby'] = 'meta_value_num';
				$query_args['meta_key'] = 'total_sales';

			} elseif ( $query_args['orderby'] == 'rating' ) {
				$query_args['orderby'] = 'meta_value_num';
				$query_args['meta_key'] = '_wc_average_rating';
			}
		}
	}
}

if ( ! function_exists( 'us_ajax_output_list_pagination' ) ) {
	/**
	 * Filters a page HTML to return the div with the "for_current_wp_query" class.
	 *
	 * @param string $content The post content.
	 * @return string Returns HTML of div with the "for_current_wp_query" class.
	 */
	function us_ajax_output_list_pagination( $content ) {
		if (
			class_exists( 'DOMDocument' )
			AND strpos( $content, 'for_current_wp_query' ) !== FALSE
		) {
			$document = new DOMDocument;
			// LIBXML_NOERROR is used to disable errors when HTML5 tags are not recognized by DOMDocument (which supports only HTML4).
			$document->loadHTML( '<meta http-equiv="Content-Type" content="text/html; charset="' . bloginfo( 'charset' ) . '">' . $content, LIBXML_NOERROR );
			$nodes = ( new DOMXpath( $document ) )->query('//div[contains(@class,"for_current_wp_query")]');
			if ( $nodes->count() ) {
				$element = $nodes->item( (int) us_arr_path( $_POST, 'us_ajax_list_index' ) );
				$new_document = new DOMDocument;
				$new_document->appendChild( $new_document->importNode( $element, TRUE ) );
				if ( $next_element = $element->nextSibling ) {
					$next_element_class = (string) $next_element->getAttribute( 'class' );
					if ( strpos( $next_element_class, 'w-grid-none' ) !== FALSE ) {
						$new_document->appendChild( $new_document->importNode( $next_element, TRUE ) );
					}
				}
				return $new_document->saveHTML();
			}
		}
		return $content;
	}
}

if ( ! function_exists( 'us_list_filter_for_current_wp_query' ) ) {
	add_action( 'pre_get_posts', 'us_list_filter_for_current_wp_query', 501 );
	/**
	 * Applies "List Filter" query to the global wp_query.
	 */
	function us_list_filter_for_current_wp_query( $wp_query ) {
		if (
			! is_admin()
			AND ! $wp_query->is_main_query()
			AND $list_filter = (string) us_arr_path( $_REQUEST, 'list_filter' )
		) {
			us_apply_filtering_to_list_query( $wp_query->query_vars, $list_filter );
		}
	}
}

if ( ! function_exists( 'us_list_search_for_current_wp_query' ) ) {
	add_action( 'pre_get_posts', 'us_list_search_for_current_wp_query', 501 );
	/**
	 * Applies "List Search" query to the global wp_query.
	 */
	function us_list_search_for_current_wp_query( $wp_query ) {
		if (
			! is_admin()
			AND ! $wp_query->is_main_query()
			AND $list_search = (string) us_arr_path( $_REQUEST, 'list_search' )
		) {
			$wp_query->set( 's', sanitize_text_field( $list_search ) );
		}
	}
}

if ( ! function_exists( 'us_list_order_for_current_wp_query' ) ) {
	add_action( 'pre_get_posts', 'us_list_order_for_current_wp_query', 501 );
	/**
	 * Applies "List Order" params to the global wp_query.
	 */
	function us_list_order_for_current_wp_query( $wp_query ) {
		if (
			! is_admin()
			AND ! $wp_query->is_main_query()
			AND $list_order = (string) us_arr_path( $_REQUEST, 'list_order' )
		) {
			$order_params = array_map( 'trim', explode( ',', $list_order ) );

			$orderby = $order_params[0] ?? '';
			$orderby_custom_field = $order_params[1] ?? '';
			$orderby_custom_type = in_array( 'numeric', $order_params );
			$order_invert = in_array( 'asc', $order_params );

			unset( $order_params );

			us_apply_orderby_to_list_query(
				$wp_query->query_vars,
				$orderby,
				$orderby_custom_field,
				$orderby_custom_type,
				$order_invert
			);
		}
	}
}
