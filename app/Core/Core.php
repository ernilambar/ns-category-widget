<?php
/**
 * Core
 *
 * @package NS_Category_Widget
 */

namespace NSCW\Core;

use NSCW\Widgets\CategoryWidget;

/**
 * Core class.
 *
 * @since 4.0.0
 */
class Core {
	/**
	 * Register.
	 *
	 * @since 4.0.0
	 */
	public function register() {
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
	}

	/**
	 * Register widgets.
	 *
	 * @since 4.0.0
	 */
	public function register_widgets() {
		if ( true === rest_sanitize_boolean( Option::get( 'nscw_field_enable_ns_category_widget' ) ) ) {
			register_widget( CategoryWidget::class );
		}
	}
}
