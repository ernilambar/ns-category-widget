<?php
/**
 * Settings page
 *
 * @package NS_Category_Widget
 */

namespace NSCW\Admin;

use NSCW\Core\Option;
use Nilambar\Optioner\Optioner;

/**
 * Settings page class.
 *
 * @since 4.0.0
 */
class SettingsPage {

	/**
	 * Register.
	 *
	 * @since 4.0.0
	 */
	public function register() {
		add_action( 'optioner_admin_init', array( $this, 'register_plugin_options' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_assets' ) );
		add_action( 'wp_ajax_nopriv_nscw_nsbl_get_posts', array( $this, 'get_posts_ajax_callback' ) );
		add_action( 'wp_ajax_nscw_nsbl_get_posts', array( $this, 'get_posts_ajax_callback' ) );
	}

	/**
	 * Register options.
	 *
	 * @since 4.0.0
	 */
	public function register_plugin_options() {
		$obj = new Optioner();

		$obj->set_page(
			array(
				'page_title'    => esc_html__( 'NS Category Widget', 'ns-category-widget' ),
				/* translators: %s: Version. */
				'page_subtitle' => sprintf( esc_html__( 'Version: %s', 'ns-category-widget' ), NS_CATEGORY_WIDGET_VERSION ),
				'menu_title'    => esc_html__( 'NS Category Widget', 'ns-category-widget' ),
				'capability'    => 'manage_options',
				'menu_slug'     => 'ns-category-widget',
				'option_slug'   => 'nscw_plugin_options',
			)
		);

		$obj->set_quick_links(
			array(
				array(
					'text' => 'Plugin Page',
					'url'  => 'https://www.nilambar.net/2013/12/ns-category-widget-wordpress-plugin.html',
					'type' => 'primary',
				),
				array(
					'text' => 'Get Support',
					'url'  => 'https://wordpress.org/support/plugin/ns-category-widget/#new-post',
					'type' => 'secondary',
				),
			)
		);

		// Tab: nscw_settings_tab.
		$obj->add_tab(
			array(
				'id'    => 'nscw_settings_tab',
				'title' => esc_html__( 'Settings', 'ns-category-widget' ),
			)
		);

		// Field: nscw_field_enable_ns_category_widget.
		$obj->add_field(
			'nscw_settings_tab',
			array(
				'id'      => 'nscw_field_enable_ns_category_widget',
				'type'    => 'toggle',
				'title'   => esc_html__( 'Enable NS Category Widget', 'ns-category-widget' ),
				'default' => Option::defaults( 'nscw_field_enable_ns_category_widget' ),
			)
		);

		// Field: nscw_field_enable_tree_script.
		$obj->add_field(
			'nscw_settings_tab',
			array(
				'id'      => 'nscw_field_enable_tree_script',
				'type'    => 'toggle',
				'title'   => esc_html__( 'Enable Tree Script', 'ns-category-widget' ),
				'default' => Option::defaults( 'nscw_field_enable_tree_script' ),
			)
		);

		// Field: nscw_field_enable_tree_style.
		$obj->add_field(
			'nscw_settings_tab',
			array(
				'id'      => 'nscw_field_enable_tree_style',
				'type'    => 'toggle',
				'title'   => esc_html__( 'Enable Tree Style', 'ns-category-widget' ),
				'default' => Option::defaults( 'nscw_field_enable_tree_style' ),
			)
		);

		// Sidebar.
		$obj->set_sidebar(
			array(
				'render_callback' => array( $this, 'render_sidebar' ),
			)
		);

		// Run now.
		$obj->run();
	}

	/**
	 * Render sidebar.
	 *
	 * @since 4.0.0
	 *
	 * @param Optioner $optioner_object Instance of Optioner.
	 */
	public function render_sidebar( $optioner_object ) {
		$optioner_object->render_sidebar_box(
			array(
				'title'   => 'Help &amp; Support',
				'icon'    => 'dashicons-editor-help',
				'content' => '<h4>Questions, bugs or great ideas?</h4>
				<p><a href="https://wordpress.org/support/plugin/ns-category-widget/#new-post" target="_blank">Visit our plugin support page</a></p>
				<h4>Wanna help make this plugin better?</h4>
				<p><a href="https://wordpress.org/support/plugin/ns-category-widget/reviews/#new-post" target="_blank">Review and rate this plugin on WordPress.org</a></p>',
			),
			$optioner_object
		);

		$optioner_object->render_sidebar_box(
			array(
				'title'   => 'Recommended Plugins',
				'content' => $this->get_recommended_plugins_content(),
			),
			$optioner_object
		);

		$optioner_object->render_sidebar_box(
			array(
				'title'   => 'Recent Blog Posts',
				'content' => '<div class="ns-blog-list"></div>',
			),
			$optioner_object
		);
	}

	/**
	 * Returns recommended plugins markup.
	 *
	 * @since 4.0.0
	 *
	 * @return string Markup.
	 */
	public function get_recommended_plugins_content() {
		return '<ul>
			<li><a href="https://wordpress.org/plugins/ns-featured-posts/" target="_blank">NS Featured Posts</a></li>
			<li><a href="https://wordpress.org/plugins/ns-category-widget/" target="_blank">NS Category Widget</a></li>
			<li><a href="https://wordpress.org/plugins/admin-customizer/" target="_blank">Admin Customizer</a></li>
			<li><a href="https://wordpress.org/plugins/date-today-nepali/" target="_blank">Date Today Nepali</a></li>
		</ul>';
	}

	/**
	 * Load settings assets.
	 *
	 * @since 4.0.0
	 *
	 * @param string $hook Hook name.
	 */
	public function load_assets( $hook ) {
		if ( 'settings_page_ns-category-widget' !== $hook ) {
			return;
		}

		wp_enqueue_script( 'ns-category-widget-settings', NS_CATEGORY_WIDGET_URL . '/build/blog-posts.js', array(), NS_CATEGORY_WIDGET_VERSION, true );
	}

	/**
	 * AJAX callback for feed items.
	 *
	 * @since 4.0.0
	 */
	public function get_posts_ajax_callback() {
		$output = array();

		$posts = $this->get_blog_feed_items();

		if ( ! empty( $posts ) ) {
			$output = $posts;
		}

		if ( ! empty( $output ) ) {
			wp_send_json_success( $output, 200 );
		} else {
			wp_send_json_error( $output, 404 );
		}
	}

	/**
	 * Returns blog feed items.
	 *
	 * @since 1.0.0
	 *
	 * @return array Feed items.
	 */
	private function get_blog_feed_items() {
		$output = array();

		$rss = fetch_feed( 'https://www.nilambar.net/category/wordpress/feed' );

		$maxitems = 0;

		$rss_items = array();

		if ( ! is_wp_error( $rss ) ) {
			$maxitems  = $rss->get_item_quantity( 5 );
			$rss_items = $rss->get_items( 0, $maxitems );
		}

		if ( ! empty( $rss_items ) ) {
			foreach ( $rss_items as $item ) {
				$feed_item = array();

				$feed_item['title'] = $item->get_title();
				$feed_item['url']   = $item->get_permalink();

				$output[] = $feed_item;
			}
		}

		return $output;
	}
}
