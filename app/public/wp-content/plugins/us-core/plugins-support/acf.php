<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Advanced Custom Fields
 *
 * @link https://www.advancedcustomfields.com/
 *
 * TODO: Globally replace the architecture of storing and working fields,
 * use an identifier instead of a name, since now there is a problem if fields in
 * different groups have the same name does not work correctly.
 */

if ( ! class_exists( 'ACF' ) ) {
	return;
}

// Register Google Maps API key
// https://www.advancedcustomfields.com/resources/google-map/
if ( ! function_exists( 'us_acf_google_map_api' ) ) {
	function us_acf_google_map_api( $api ) {
		// Get the Google Maps API key from the Theme Options
		$gmaps_api_key = trim( esc_attr( us_get_option('gmaps_api_key', '') ) );
		/*
		 * Set the API key for ACF only if it is not empty,
		 * to prevent possible erase of the same value set in other plugins
		 */
		if ( ! empty( $gmaps_api_key ) ) {
			$api['key'] = $gmaps_api_key;
		}

		return $api;
	}

	add_filter( 'acf/fields/google_map/api', 'us_acf_google_map_api' );
}

// Removing custom plugin messages for ACF Pro
if ( ! function_exists( 'us_acf_pro_remove_update_message' ) ) {
	function us_acf_pro_remove_update_message() {
		if ( class_exists( 'ACF_Updates' ) ) {
			$class = new ACF_Updates();
			remove_filter( 'pre_set_site_transient_update_plugins', array( $class, 'modify_plugins_transient' ), 15 );
		}

		// Remove additional messages for buying license
		if (
			function_exists( 'acf_get_setting' )
			AND $acf_basename = acf_get_setting( 'basename' )
		) {
			remove_all_actions( 'in_plugin_update_message-' . $acf_basename );
		}
	}

	add_action( 'init', 'us_acf_pro_remove_update_message', 30 );
}

if ( ! function_exists( 'us_acf_get_fields' ) ) {
	/**
	 * Get a list of all fields
	 *
	 * @param string|array $types The field types to get
	 * @param bool $to_list If a list is given, the result will be [ group => [ key => value ] ]
	 * @param string $separator The separator for the "option" prefix, example: `option{separator}name`
	 * @return array Returns a list of fields
	 */
	function us_acf_get_fields( $types = array(), $to_list = FALSE, $separator = '|' ) {

		if ( ! is_array( $types ) AND ! empty( $types ) ) {
			$types = array( $types );
		}
		$result = array();

		// Bypass all field groups.
		foreach ( (array) acf_get_field_groups() as $group ) {

			/**
			 * Add the field prefix, if the group is used in ACF Options page.
			 * @link https://www.advancedcustomfields.com/resources/get-values-from-an-options-page/
			 */
			$options_prefix = '';
			if ( is_array( $group['location'] ) ) {
				foreach ( $group['location'] as $location_or ) {
					foreach ( $location_or as $location_and ) {
						if ( $location_and['param'] === 'options_page' AND $location_and['operator'] === '==' ) {
							$options_prefix = 'option' . $separator;
							break 2;
						}
					}
				}
			}

			// Get all the fields of the group and generating the result.
			$fields = array();
			foreach( (array) acf_get_fields( $group['ID'] ) as $field ) {

				// If types are given and the field does not correspond
				// to one type, then skip this field.
				if (
					! empty( $types )
					AND is_array( $types )
					AND ! in_array( $field['type'], $types )
				) {
					continue;
				}

				// If there is a prefix, then add all the field names.
				if ( $options_prefix ) {
					$field['name'] = $options_prefix . $field['name'];
				}

				// If the list format is specified, then we will form the list.
				if ( $to_list ) {
					$fields[ $field['name'] ] = $field['label'];
				} else {
					$fields[] = $field;
				}
			}

			if ( count( $fields ) ) {
				// This is the full name of the group that can be used for output in dropdowns or other controls
				$result[ $group['ID'] ] = array( '__group_label__' => $group['title'] );
				$result[ $group['ID'] ] += $fields;
			}
		}
		return $result;
	}
}

if ( ! function_exists( 'us_acf_get_fields_keys' ) ) {
	/**
	 * Get a list of all keys
	 *
	 * @param string|array $types The field types to get
	 * @param string $separator The separator for the "option" prefix, example: `option{separator}name`
	 * @return array Returns a list of all keys
	 */
	function us_acf_get_fields_keys( $types = array(), $separator = '|' ) {
		$keys = array();
		foreach( us_acf_get_fields( $types, /* to_list */TRUE, $separator ) as $fields ) {
			// Remove a group label
			if ( isset( $fields['__group_label__'] ) ) {
				unset( $fields['__group_label__'] );
			}
			$keys = array_merge( $keys, array_keys( $fields ) );
		}
		return $keys;
	}
}

if ( ! function_exists( 'us_acf_get_custom_field' ) ) {
	add_filter( 'us_get_custom_field', 'us_acf_get_custom_field', 2, 4 );
	/**
	 * Filters a custom field value to apply the ACF return format
	 *
	 * @param mixed $value The meta value
	 * @param string $name The field name
	 * @param int|string $current_id The current id
	 * @param string|null $meta_type The meta type
	 * @return mixed Returns a value given specific fields
	 */
	function us_acf_get_custom_field( $value, $name, $current_id, $meta_type = NULL ) {

		// Built-in fields where the name starts with us_ are returned unchanged
		if ( strpos( $name, 'us_' ) === 0 ) {
			return $value;
		}

		// Use the meta slug as prefix in the current id for ACF functions
		// https://www.advancedcustomfields.com/resources/get_field/#get-a-value-from-different-objects
		if (
			$meta_type == 'term'
			AND $term = get_term( $current_id )
			AND $term instanceof WP_Term
		) {
			$current_id = $term->taxonomy . '_' . $current_id;
		}
		if ( $meta_type == 'user' ) {
			$current_id = 'user_' . $current_id;
		}

		// Get field object
		// https://www.advancedcustomfields.com/resources/get_field_object/
		$field = get_field_object( $name, $current_id );

		// In case the field is not exist in ACF return the initial value
		// This allows getting values for non-ACF custom fields correctly (_wp_attachment_image_alt, etc.)
		if ( $field === FALSE ) {
			return $value;
		}

		// Return value from an field object
		return us_arr_path( $field, 'value', /* default */$value );
	}
}

if ( ! function_exists( 'us_acf_link_dynamic_values' ) ) {
	add_filter( 'us_link_dynamic_values', 'us_acf_link_dynamic_values' );
	/**
	 * Append ACF predefined field types to link dynamic values
	 *
	 * @param array $dynamic_values Groups of dynamic values
	 * @return array Returns an expanded array of variables
	 */
	function us_acf_link_dynamic_values( $dynamic_values ) {

		// Skip adding values if it's not edit mode
		if ( ! us_is_elm_editing_page() OR empty( $dynamic_values['acf_types'] ) ) {
			return $dynamic_values;
		}

		$acf_dynamic_values = array();

		foreach( us_acf_get_fields( $dynamic_values['acf_types'], /* to_list */TRUE, /* separator */'/' ) as $fields ) {
			$group_label = (string) us_arr_path( $fields, '__group_label__' );
			foreach ( $fields as $field_key => $field_name ) {
				if ( $field_key == '__group_label__' ) {
					continue;
				}
				$acf_dynamic_values[ $group_label ][ 'custom_field|' . $field_key ] = $field_name;
			}
		}

		return array_merge( $dynamic_values, $acf_dynamic_values );
	}
}

if ( ! function_exists( 'us_acf_dynamic_values' ) ) {
	add_filter( 'us_text_dynamic_values', 'us_acf_dynamic_values' );
	add_filter( 'us_image_dynamic_values', 'us_acf_dynamic_values' );
	/**
	 * Append ACF predefined field types to text dynamic values
	 *
	 * @param array $dynamic_values Groups of dynamic values
	 * @return array Returns an expanded array of variables
	 */
	function us_acf_dynamic_values( $dynamic_values ) {

		// Skip adding values if it's not edit mode
		if ( ! us_is_elm_editing_page() OR empty( $dynamic_values['acf_types'] ) ) {
			return $dynamic_values;
		}

		$acf_dynamic_values = array();

		foreach( us_acf_get_fields( $dynamic_values['acf_types'], /* to_list */TRUE, /* separator */'/' ) as $fields ) {
			$group_label = (string) us_arr_path( $fields, '__group_label__' );
			foreach ( $fields as $field_key => $field_name ) {
				if ( $field_key == '__group_label__' ) {
					continue;
				}
				$acf_dynamic_values[ $group_label ][ '{{' . $field_key . '}}' ] = $field_name;
			}
		}

		return array_merge( $dynamic_values, $acf_dynamic_values );

	}
}

if ( ! function_exists( 'us_acf_color_dynamic_values' ) ) {
	add_filter( 'usof_get_color_vars', 'us_acf_color_dynamic_values', 1, 1 );
	/**
	 * Append ACF predefined field types to color dynamic values
	 *
	 * @param array $result The dynamic colors.
	 * @return array Returns an array of dynamic colors.
	 */
	function us_acf_color_dynamic_values( $result ) {

		foreach( us_acf_get_fields( 'color_picker', /* to_list */TRUE, /* separator */'/' ) as $fields ) {
			$group_label = (string) us_arr_path( $fields, '__group_label__' );
			foreach ( $fields as $field_key => $field_name ) {
				if ( $field_key == '__group_label__' ) {
					continue;
				}
				$result[ $group_label ][] = array(
					'name' => sprintf( '{{%s}}', $field_key ),
					'title' => $field_name,
					'type' => 'cf_colors',
				);
			}
		}

		return $result;
	}
}

if ( ! function_exists( 'us_acf_post_list_element_config' ) ) {
	add_filter( 'us_config_elements/post_list', 'us_acf_post_list_element_config', 501, 1 );
	/**
	 * Extends the configuration of the "Post List" element to output posts from ACF.
	 *
	 * @param array $config The configuration.
	 * @return array Returns the extended configuration.
	 */
	function us_acf_post_list_element_config( $config ) {
		if (
			! isset( $config['params'], $config['params']['source'] )
			OR ! is_array( $config['params']['source']['options'] )
		) {
			return $config;
		}
		// Get a list of all fields of type "Post Object" and "Relationship"
		$fields = us_acf_get_fields( array( 'post_object', 'relationship' ), /*to_list*/TRUE );
		$field_list = array(
			'' => '– ' . us_translate( 'None' ) . ' –',
		);
		foreach ( $fields as $field ) {
			$group_label = (string) us_arr_path( $field, '__group_label__' );
			foreach ( $field as $field_key => $field_name ) {
				if ( $field_key !== '__group_label__' ) {
					$field_list[ $field_key ] = $group_label . ': ' . $field_name;
				}
			}
		}
		$config['params']['source']['options']['custom_field_posts'] = __( 'Posts from ACF custom field', 'us' );
		$config['params'] = us_array_merge_insert(
			$config['params'],
			array(
				'custom_field_name' => array(
					'type' => 'select',
					'options' => $field_list,
					'std' => '',
					'classes' => 'for_above',
					'show_if' => array( 'source', '=', 'custom_field_posts' ),
					'usb_preview' => TRUE,
				)
			),
			'after',
			'source'
		);
		foreach ( $config['params'] as &$param  ) {
			if ( isset( $param['weight'] ) ) {
				unset( $param['weight'] );
			}
		}
		unset( $param );
		$config['params'] = us_set_params_weight( $config['params'] );
		return $config;
	}
}

if ( ! function_exists( 'us_acf_posts_from_custom_field' ) ) {
	add_filter( 'us_post_list_query_args', 'us_acf_posts_from_custom_field', 501, 2 );
	/**
	 * Modify the post list query to return posts from ACF custom field.
	 *
	 * @param arrat $query_args The query arguments.
	 * @param array $filled_atts The filled atts.
	 * @return array Returns array of arguments passed to WP_Query.
	 */
	function us_acf_posts_from_custom_field( $query_args, $filled_atts ) {
		if (
			us_arr_path( $filled_atts, 'source' ) === 'custom_field_posts'
			AND $field_name = us_arr_path( $filled_atts, 'custom_field_name' )
			AND ! usb_is_template_preview()
		) {
			if ( wp_doing_ajax() ) {
				$object_id = (int) us_arr_path( $_POST, 'object_id' );
				$meta_type = (string) us_arr_path( $_POST, 'meta_type' );

				// Validate string
				if ( ! in_array( $meta_type, array( 'post', 'term', 'user' ) ) ) {
					$meta_type = 'post';
				}

			} else {
				$object_id = us_get_current_id();
				$meta_type = us_get_current_meta_type();
			}

			if ( ! $post_ids = us_get_custom_field( $field_name, /*acf_format*/FALSE, $object_id, $meta_type ) ) {
				$post_ids = array( 0 ); // Use the non-existing id to get no results, because empty 'post__in' is ignored by query
			}
			$query_args['post__in'] = $post_ids;
		}
		return $query_args;
	}
}
