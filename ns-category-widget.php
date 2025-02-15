<?php
/**
 * Plugin Name: NS Category Widget
 * Plugin URI: https://www.nilambar.net/2013/12/ns-category-widget-wordpress-plugin.html
 * Description: A widget plugin for listing categories and taxonomies in the way you want.
 * Version: 4.1.4
 * Requires at least: 6.0
 * Requires PHP: 7.2.24
 * Author: Nilambar Sharma
 * Author URI: https://www.nilambar.net
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
 * Text Domain: ns-category-widget
 *
 * @package NS_Category_Widget
 */

use NSCW\Init;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'NS_CATEGORY_WIDGET_VERSION', '4.1.4' );
define( 'NS_CATEGORY_WIDGET_SLUG', 'ns-category-widget' );
define( 'NS_CATEGORY_WIDGET_BASE_FILEPATH', __FILE__ );
define( 'NS_CATEGORY_WIDGET_BASE_FILENAME', plugin_basename( __FILE__ ) );
define( 'NS_CATEGORY_WIDGET_DIR', rtrim( plugin_dir_path( __FILE__ ), '/' ) );
define( 'NS_CATEGORY_WIDGET_URL', rtrim( plugin_dir_url( __FILE__ ), '/' ) );

// Include autoload.
if ( file_exists( NS_CATEGORY_WIDGET_DIR . '/vendor/autoload.php' ) ) {
	require_once NS_CATEGORY_WIDGET_DIR . '/vendor/autoload.php';
	require_once NS_CATEGORY_WIDGET_DIR . '/vendor/ernilambar/optioner/optioner.php';
}

if ( class_exists( 'NSCW\Init' ) ) {
	Init::register_services();
}
