<?php
/**
 * Plugin Name: NS Category Widget
 * Plugin URI: http://wordpress.org/plugins/ns-category-widget/
 * Description: A widget plugin for listing categories and taxonomies in the way you want.
 * Version: 3.1.0
 * Author: Nilambar Sharma
 * Author URI: http://nilambar.net
 * Text Domain: ns-category-widget
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package NS_Category_Widget
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'NS_CATEGORY_WIDGET_BASENAME', basename( dirname( __FILE__ ) ) );
define( 'NS_CATEGORY_WIDGET_DIR', rtrim( plugin_dir_path( __FILE__ ), '/' ) );
define( 'NS_CATEGORY_WIDGET_URL', rtrim( plugin_dir_url( __FILE__ ), '/' ) );

// Public-Facing Functionality.
require_once( plugin_dir_path( __FILE__ ) . 'public/class-ns-category-widget.php' );

require_once( NS_CATEGORY_WIDGET_DIR . '/widgets/nscw-widget.php' );

register_activation_hook( __FILE__, array( 'NS_Category_Widget', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'NS_Category_Widget', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'NS_Category_Widget', 'get_instance' ) );

// Dashboard and Administrative Functionality.
if ( is_admin() ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-ns-category-widget-admin.php' );
	add_action( 'plugins_loaded', array( 'NS_Category_Widget_Admin', 'get_instance' ) );

}
/**
 * Register plugin widgets.
 *
 * @since 1.0.0
 */
function nscw_register_plugin_widgets() {
	$obj_nscw = NS_Category_Widget::get_instance();
	$nscw_field_enable_ns_category_widget = $obj_nscw->get_option( 'nscw_field_enable_ns_category_widget' );
	if ( 1 === absint( $nscw_field_enable_ns_category_widget ) ) {
		register_widget( 'NSCW_Widget' );
	}
}

add_action( 'widgets_init', 'nscw_register_plugin_widgets' );

function nscw_hierarchical_category_tree( $cat_id, $args, $is_tree ) {
	$args['parent'] = absint( $cat_id );

	$next = get_terms( $args );

	if ( $next ) {
		foreach( $next as $cat ) {
			echo '<ul><li><a href="' . esc_url( get_term_link( $cat->term_id ) ) . '">' . esc_html( $cat->name ) ;
			if ( 1 === absint( $is_tree ) && 1 === absint( $args['show_count'] ) ) {
				echo ' <span>(' . absint( $cat->count ) . ')</span>';
			}
			echo '</a>';

			if ( 1 !== absint( $is_tree ) && 1 === absint( $args['show_count'] ) ) {
				echo ' <span>(' . absint( $cat->count ) . ')</span>';
			}

			nscw_hierarchical_category_tree( $cat->term_id, $args, $is_tree );
		}
	}

	echo '</li></ul>';
}
