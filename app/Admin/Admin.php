<?php
/**
 * Admin
 *
 * @package NS_Category_Widget
 */

namespace NSCW\Admin;

use Nilambar\AdminNotice\Notice;

/**
 * Admin class.
 *
 * @since 4.0.0
 */
class Admin {

	/**
	 * Register.
	 *
	 * @since 4.0.0
	 */
	public function register() {
		add_action( 'admin_init', array( $this, 'setup_admin_notice' ) );
		add_filter( 'plugin_action_links_' . NS_CATEGORY_WIDGET_BASE_FILENAME, array( $this, 'customize_action_links' ) );
	}

	/**
	 * Customize plugin action links.
	 *
	 * @since 4.0.0
	 *
	 * @param array $links Action links.
	 * @return array Modified action links.
	 */
	public function customize_action_links( $links ) {
		return array_merge(
			array(
				'settings' => '<a href="' . esc_url( admin_url( 'options-general.php?page=' . NS_CATEGORY_WIDGET_SLUG ) ) . '">' . esc_html__( 'Settings', 'ns-category-widget' ) . '</a>',
			),
			$links
		);
	}

	/**
	 * Setup admin notice.
	 *
	 * @since 4.0.0
	 */
	public function setup_admin_notice() {
		Notice::init(
			array(
				'slug' => NS_CATEGORY_WIDGET_SLUG,
				'name' => esc_html__( 'NS Category Widget', 'ns-category-widget' ),
			)
		);
	}
}
