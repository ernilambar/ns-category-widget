<?php
/**
 * Widget
 *
 * @package NS_Category_Widget
 */

namespace NSCW\Admin;

use NSCW\Widgets\CategoryWidget;

/**
 * Widget class.
 *
 * @since 4.0.0
 */
class Widget {

	/**
	 * Register.
	 *
	 * @since 4.0.0
	 */
	public function register() {
		add_action( 'admin_enqueue_scripts', array( $this, 'load_assets' ) );
		add_action( 'wp_ajax_populate_categories', array( $this, 'ajax_populate_categories' ) );
		add_action( 'wp_ajax_nopriv_populate_categories', array( $this, 'ajax_populate_categories' ) );
	}

	/**
	 * Ajax function to populate categories in widget settings.
	 *
	 * @since 4.0.0
	 */
	public function ajax_populate_categories() {
		$output = array();

		$taxonomy = ( isset( $_POST['taxonomy'] ) && ! empty( $_POST['taxonomy'] ) ) ? sanitize_text_field( wp_unslash( $_POST['taxonomy'] ) ) : '';
		$name     = ( isset( $_POST['name'] ) && ! empty( $_POST['name'] ) ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$id       = ( isset( $_POST['id'] ) && ! empty( $_POST['id'] ) ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';

		$cat_args = array(
			'orderby'         => 'slug',
			'taxonomy'        => $taxonomy,
			'echo'            => 0,
			'hide_empty'      => 0,
			'name'            => $name,
			'id'              => $id,
			'class'           => 'nscw-cat-list',
			'show_option_all' => esc_html__( 'Show All', 'ns-category-widget' ),
		);

		$output['html'] = wp_dropdown_categories( apply_filters( 'widget_categories_dropdown_args', $cat_args ) );

		wp_send_json_success( $output, 200 );
	}

	/**
	 * Load assets.
	 *
	 * @since 4.0.0
	 *
	 * @param string $hook Hook name.
	 */
	public function load_assets( $hook ) {
		if ( 'widgets.php' !== $hook ) {
			return;
		}

		wp_enqueue_script( 'nscw-widget-script', NS_CATEGORY_WIDGET_URL . '/build/widget.js', array(), NS_CATEGORY_WIDGET_VERSION, true );
	}
}
