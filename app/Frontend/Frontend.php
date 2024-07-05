<?php
/**
 * Frontend
 *
 * @package NS_Category_Widget
 */

namespace NSCW\Frontend;

use NSCW\Core\Option;

/**
 * Frontend class.
 *
 * @since 4.0.0
 */
class Frontend {

	/**
	 * Register.
	 *
	 * @since 4.0.0
	 */
	public function register() {
		add_action( 'wp_enqueue_scripts', array( $this, 'load_assets' ) );
	}

	/**
	 * Load assets.
	 *
	 * @since 4.0.0
	 */
	public function load_assets() {
		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( true === rest_sanitize_boolean( Option::get( 'nscw_field_enable_tree_script' ) ) ) {
			wp_enqueue_script( 'tree-script', NS_CATEGORY_WIDGET_URL . '/third-party/jstree/js/jstree' . $min . '.js', array( 'jquery' ), '3.3.16', true );
		}

		if ( true === rest_sanitize_boolean( Option::get( 'nscw_field_enable_tree_style' ) ) ) {
			wp_enqueue_style( NS_CATEGORY_WIDGET_SLUG . '-tree-style', NS_CATEGORY_WIDGET_URL . '/third-party/jstree/css/themes/default/style' . $min . '.css', array(), '3.3.16' );
		}
	}
}
