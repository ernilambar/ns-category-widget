<?php
/**
 * Option
 *
 * @package NS_Category_Widget
 */

namespace NSCW\Core;

/**
 * Option class.
 *
 * @since 4.0.0
 */
class Option {

	/**
	 * Return plugin option.
	 *
	 * @since 4.0.0
	 *
	 * @param string $key Option key.
	 * @return mixed Option value.
	 */
	public static function get( $key ) {
		$default_options = self::get_defaults();

		if ( empty( $key ) ) {
			return;
		}

		$current_options = (array) get_option( 'nscw_plugin_options' );
		$current_options = wp_parse_args( $current_options, $default_options );

		$value = null;

		if ( array_key_exists( $key, $current_options ) ) {
			$value = $current_options[ $key ];
		}

		return $value;
	}

	/**
	 * Return default options.
	 *
	 * @since 4.0.0
	 *
	 * @return array Default options.
	 */
	public static function get_defaults() {
		return apply_filters(
			'nscw_option_defaults',
			array(
				'nscw_field_enable_ns_category_widget' => true,
				'nscw_field_enable_tree_script'        => true,
				'nscw_field_enable_tree_style'         => true,
			)
		);
	}

	/**
	 * Return default value of given key.
	 *
	 * @since 4.0.0
	 *
	 * @param string $key Option key.
	 * @return mixed Default option value.
	 */
	public static function defaults( $key ) {
		$value = null;

		$defaults = self::get_defaults();

		if ( ! empty( $key ) && array_key_exists( $key, $defaults ) ) {
			$value = $defaults[ $key ];
		}

		return $value;
	}
}
