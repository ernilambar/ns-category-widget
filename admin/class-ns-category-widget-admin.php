<?php
/**
 * NS Category Widget Admin.
 *
 * @package NS_Category_Widget
 */

use Nilambar\Optioner\Optioner;

/**
 * NS Category Widget Admin Class.
 *
 * @since 1.0.0
 */
class NS_Category_Widget_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Plugin options.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $options = array();

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {

		$plugin = NS_Category_Widget::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		$this->options = $plugin->get_options_array();

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		if ( true === rest_sanitize_boolean( $this->options['nscw_field_enable_ns_category_widget'] ) ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'nscw_scripts_enqueue' ) );
			add_action( 'wp_ajax_populate_categories', array( $this, 'ajax_populate_categories' ) );
			add_action( 'wp_ajax_nopriv_populate_categories', array( $this, 'ajax_populate_categories' ) );
		}

		$obj = new Optioner();

		$obj->set_page(
			array(
				'page_title'  => esc_html__( 'NS Category Widget', 'ns-category-widget' ),
				'menu_title'  => esc_html__( 'NS Category Widget', 'ns-category-widget' ),
				'capability'  => 'manage_options',
				'menu_slug'   => 'ns-category-widget',
				'option_slug' => 'nscw_plugin_options',
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
				'id'        => 'nscw_field_enable_ns_category_widget',
				'type'      => 'checkbox',
				'title'     => esc_html__( 'Enable NS Category Widget', 'ns-category-widget' ),
				'side_text' => esc_html__( 'Enable', 'ns-category-widget' ),
				'default'   => true,
			)
		);

		// Field: nscw_field_enable_tree_script.
		$obj->add_field(
			'nscw_settings_tab',
			array(
				'id'        => 'nscw_field_enable_tree_script',
				'type'      => 'checkbox',
				'title'     => esc_html__( 'Enable Tree Script', 'ns-category-widget' ),
				'side_text' => esc_html__( 'Enable', 'ns-category-widget' ),
				'default'   => true,
			)
		);

		// Field: nscw_field_enable_tree_style.
		$obj->add_field(
			'nscw_settings_tab',
			array(
				'id'        => 'nscw_field_enable_tree_style',
				'type'      => 'checkbox',
				'title'     => esc_html__( 'Enable Tree Style', 'ns-category-widget' ),
				'side_text' => esc_html__( 'Enable', 'ns-category-widget' ),
				'default'   => true,
			)
		);

		// Sidebar.
		$obj->set_sidebar(
			array(
				'render_callback' => array( $this, 'render_sidebar' ),
				'width'           => 30,
			)
		);

		// Run now.
		$obj->run();
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @return object A single instance of this class.
	 */
	public static function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Enqueue widget scripts.
	 *
	 * @since 1.0.0
	 */
	function nscw_scripts_enqueue( $hook ) {
		if ( 'widgets.php' !== $hook ) {
		    return;
		}
		wp_register_script( 'nscw-widget-script', NS_CATEGORY_WIDGET_URL . '/admin/assets/js/nscw-widget.js', array( 'jquery'), NS_CATEGORY_WIDGET_VERSION );
		wp_localize_script( 'nscw-widget-script', 'ns_category_widget_ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		wp_enqueue_script( 'nscw-widget-script' );

	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since 1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . esc_url( admin_url( 'options-general.php?page=' . $this->plugin_slug ) ) . '">' . __( 'Settings', 'ns-category-widget' ) . '</a>',
			),
			$links
		);

	}

	/**
	 * Ajax function to populate categories in widget settings.
	 *
	 * @since 1.0.0
	 */
	function ajax_populate_categories() {
		$output = array();

		$output['status'] = 0;

		$taxonomy = $_POST['taxonomy'];
		$name     = $_POST['name'];
		$id       = $_POST['id'];

		$cat_args = array(
			'orderby'         => 'slug',
			'taxonomy'        => $taxonomy,
			'echo'            => '0',
			'hide_empty'      => 0,
			'name'            => $name,
			'id'              => $id,
			'class'           => 'nscw-cat-list',
			'show_option_all' => __( 'Show All','ns-category-widget' ),
	    );

		$output['html'] = wp_dropdown_categories( apply_filters( 'widget_categories_dropdown_args', $cat_args ) );

		$output['status'] = 1;

		wp_send_json( $output );
	}

	/**
	 * Render sidebar.
	 *
	 * @since 3.1.1
	 */
	public function render_sidebar() {
		?>
		<div class="sidebox">
			<h3 class="box-heading">Help &amp; Support</h3>
			<div class="box-content">
				<ul>
					<li><strong>Questions, bugs or great ideas?</strong></li>
					<li><a href="https://wordpress.org/support/plugin/ns-category-widget/" target="_blank">Visit our plugin support page</a></li>
					<li><strong>Wanna help make this plugin better?</strong></li>
					<li><a href="https://wordpress.org/support/plugin/ns-category-widget/reviews/#new-post" target="_blank">Review and rate this plugin on WordPress.org</a></li>
				</ul>
			</div>
		</div><!-- .sidebox -->

		<div class="sidebox">
			<h3 class="box-heading">Recommended Plugins</h3>
			<div class="box-content">
				<ol>
					<li><a href="https://wpconcern.com/plugins/woocommerce-product-tabs/" target="_blank">WooCommerce Product Tabs</a></li>
					<li><a href="https://wpconcern.com/plugins/post-grid-elementor-addon/" target="_blank">Post Grid Elementor Addon</a></li>
					<li><a href="https://wpconcern.com/plugins/advanced-google-recaptcha/" target="_blank">Advanced Google reCAPTCHA</a></li>
					<li><a href="https://wordpress.org/plugins/nifty-coming-soon-and-under-construction-page/" target="_blank">Coming Soon & Maintenance Mode Page</a></li>
					<li><a href="https://wordpress.org/plugins/admin-customizer/" target="_blank">Admin Customizer</a></li>
					<li><a href="https://wordpress.org/plugins/prime-addons-for-elementor/" target="_blank">Prime Addons for Elementor</a></li>
				</ol>
			</div>
		</div><!-- .sidebox -->

		<div class="sidebox">
			<h3 class="box-heading">Recent Blog Posts</h3>
			<div class="box-content">
				<?php
				$rss = fetch_feed( 'https://www.nilambar.net/category/wordpress/feed' );

				$maxitems = 0;

				$rss_items = array();

				if ( ! is_wp_error( $rss ) ) {
					$maxitems  = $rss->get_item_quantity( 5 );
					$rss_items = $rss->get_items( 0, $maxitems );
				}
				?>

				<?php if ( ! empty( $rss_items ) ) : ?>

					<ul>
						<?php foreach ( $rss_items as $item ) : ?>
							<li><a href="<?php echo esc_url( $item->get_permalink() ); ?>" target="_blank"><?php echo esc_html( $item->get_title() ); ?></a></li>
						<?php endforeach; ?>
					</ul>

				<?php endif; ?>
			</div>
		</div><!-- .sidebox -->

		<?php
	}
}
