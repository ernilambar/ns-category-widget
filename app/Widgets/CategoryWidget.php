<?php
/**
 * Category widget
 *
 * @package NS_Category_Widget
 */

namespace NSCW\Widgets;

use NSCW\Core\Option;
use NSCW\Extension\CategoryWalker;
use WP_Widget;

/**
 * Category widget class.
 *
 * @since 1.0.0
 */
class CategoryWidget extends WP_Widget {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'widget_ns_category_widget',
			'description' => esc_html__( 'Widget for displaying categories in your way', 'ns-category-widget' ),
		);

		parent::__construct( 'ns-category-widget', esc_html__( 'NS Category Widget', 'ns-category-widget' ), $widget_ops );
	}

	/**
	 * Echo the widget content.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args     Display arguments.
	 * @param array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {
		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		$widget_id = $args['widget_id'];

		$taxonomy         = ! empty( $instance['taxonomy'] ) ? $instance['taxonomy'] : 'category';
		$parent_category  = ! empty( $instance['parent_category'] ) ? $instance['parent_category'] : 0;
		$depth            = ! empty( $instance['depth'] ) ? $instance['depth'] : 0;
		$orderby          = ! empty( $instance['orderby'] ) ? $instance['orderby'] : 'name';
		$order            = ! empty( $instance['order'] ) ? $instance['order'] : 'asc';
		$hide_empty       = ! empty( $instance['hide_empty'] ) ? $instance['hide_empty'] : 0;
		$show_post_count  = ! empty( $instance['show_post_count'] ) ? $instance['show_post_count'] : 0;
		$number           = ! empty( $instance['number'] ) ? $instance['number'] : '';
		$include_category = ! empty( $instance['include_category'] ) ? $instance['include_category'] : '';
		$exclude_category = ! empty( $instance['exclude_category'] ) ? $instance['exclude_category'] : '';
		$enable_tree      = ! empty( $instance['enable_tree'] ) ? $instance['enable_tree'] : 0;
		$tree_show_icons  = array_key_exists( 'tree_show_icons', $instance ) ? $instance['tree_show_icons'] : 0;
		$tree_show_dots   = array_key_exists( 'tree_show_dots', $instance ) ? $instance['tree_show_dots'] : 1;
		$tree_save_state  = array_key_exists( 'tree_save_state', $instance ) ? $instance['tree_save_state'] : 1;

		if ( ! empty( $exclude_category ) ) {
			$include_category = '';
		}

		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		$cat_args = array(
			'title_li'   => '',
			'depth'      => $depth,
			'orderby'    => $orderby,
			'order'      => $order,
			'hide_empty' => $hide_empty,
			'show_count' => $show_post_count,
			'number'     => $number,
			'include'    => wp_parse_id_list( $include_category ),
			'exclude'    => wp_parse_id_list( $exclude_category ),
			'taxonomy'   => $taxonomy,
			'walker'     => new CategoryWalker(),
		);

		if ( $parent_category ) {
			$cat_args['child_of'] = $parent_category;
		}

		$class_text = 'nscw-inactive-tree';

		if ( 1 === $enable_tree ) {
			$class_text = 'nscw-active-tree';
		}

		echo '<div class="' . esc_attr( $class_text ) . '">';
		echo '<ul class="cat-list">';
		wp_list_categories( apply_filters( 'widget_categories_args', $cat_args ) );
		echo '</ul>';
		echo '</div>';

		if ( 1 === $enable_tree && true === Option::get( 'nscw_field_enable_tree_script' ) ) {
			$tree_plugins = array();
			if ( 1 === $tree_save_state ) {
				$tree_plugins[] = 'state';
			}
			?>
			<script>
			(function ( $ ) {
				"use strict";

				$(function () {

					$('#<?php echo esc_attr( $widget_id ); ?> div').jstree({
						'plugins':[<?php echo '"' . implode( '","', $tree_plugins ) . '"'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>],
						'core' : {
						'themes' : {
							'icons' : <?php echo ( 1 === $tree_show_icons ) ? 'true' : 'false'; ?>,
							'dots' : <?php echo ( 1 === $tree_show_dots ) ? 'true' : 'false'; ?>
						}
						}
					});
					//
					$('body').on('click','#<?php echo esc_attr( $widget_id ); ?> div a', function(e){
						window.location = $(this).attr('href');
					});
				});

			}(jQuery));

			</script>
			<?php
		} // End if.

		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Update widget instance.
	 *
	 * @since 1.0.0
	 *
	 * @param array $new_instance New settings for this instance.
	 * @param array $old_instance Old settings for this instance.
	 * @return array Settings to save or bool false to cancel saving.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance['title']            = sanitize_text_field( $new_instance['title'] );
		$instance['parent_category']  = absint( $new_instance['parent_category'] );
		$instance['taxonomy']         = sanitize_text_field( $new_instance['taxonomy'] );
		$instance['depth']            = absint( $new_instance['depth'] );
		$instance['orderby']          = sanitize_text_field( $new_instance['orderby'] );
		$instance['order']            = sanitize_text_field( $new_instance['order'] );
		$instance['hide_empty']       = ! empty( $new_instance['hide_empty'] ) ? 1 : 0;
		$instance['show_post_count']  = ! empty( $new_instance['show_post_count'] ) ? 1 : 0;
		$instance['number']           = ( ! empty( $new_instance['number'] ) ) ? absint( $new_instance['number'] ) : '';
		$instance['include_category'] = trim( sanitize_text_field( $new_instance['include_category'] ) );

		$str = explode( ',', $instance['include_category'] );
		array_walk( $str, 'intval' );
		$instance['include_category'] = implode( ',', $str );

		$instance['exclude_category'] = sanitize_text_field( $new_instance['exclude_category'] );

		$str = explode( ',', $instance['exclude_category'] );
		array_walk( $str, 'intval' );
		$instance['exclude_category'] = implode( ',', $str );

		$instance['enable_tree']     = ! empty( $new_instance['enable_tree'] ) ? 1 : 0;
		$instance['tree_show_icons'] = ! empty( $new_instance['tree_show_icons'] ) ? 1 : 0;
		$instance['tree_show_dots']  = ! empty( $new_instance['tree_show_dots'] ) ? 1 : 0;
		$instance['tree_save_state'] = ! empty( $new_instance['tree_save_state'] ) ? 1 : 0;

		return $instance;
	}

	/**
	 * Output the settings update form.
	 *
	 * @since 1.0.0
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		global $wp_taxonomies;

		$instance = wp_parse_args(
			(array) $instance,
			array(
				'title'            => esc_html__( 'Categories', 'ns-category-widget' ),
				'taxonomy'         => 'category',
				'parent_category'  => '',
				'depth '           => 1,
				'orderby'          => 'name',
				'order'            => 'asc',
				'hide_empty'       => 0,
				'show_post_count'  => 0,
				'number'           => '',
				'include_category' => '',
				'exclude_category' => '',
				'enable_tree'      => 0,
				'tree_show_icons'  => 0,
				'tree_show_dots'   => 1,
				'tree_save_state'  => 1,
			)
		);

		$title            = htmlspecialchars( $instance['title'], ENT_COMPAT );
		$taxonomy         = htmlspecialchars( $instance['taxonomy'], ENT_COMPAT );
		$parent_category  = isset( $instance['parent_category'] ) ? esc_attr( $instance['parent_category'] ) : '';
		$depth            = isset( $instance['depth'] ) ? esc_attr( $instance['depth'] ) : '';
		$orderby          = isset( $instance['orderby'] ) ? esc_attr( $instance['orderby'] ) : '';
		$order            = isset( $instance['order'] ) ? esc_attr( $instance['order'] ) : '';
		$number           = htmlspecialchars( $instance['number'], ENT_COMPAT );
		$include_category = htmlspecialchars( $instance['include_category'], ENT_COMPAT );
		$exclude_category = htmlspecialchars( $instance['exclude_category'], ENT_COMPAT );
		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
			<?php esc_html_e( 'Title:', 'ns-category-widget' ); ?>
			</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
			name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'taxonomy' ) ); ?>">
			<?php esc_html_e( 'Taxonomy:', 'ns-category-widget' ); ?>
			</label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'taxonomy' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'taxonomy' ) ); ?>" class="nscw-taxonomy" data-name="<?php echo esc_attr( $this->get_field_name( 'parent_category' ) ); ?>" data-id="<?php echo esc_attr( $this->get_field_id( 'parent_category' ) ); ?>" >
				<?php
				foreach ( $wp_taxonomies as $taxonomy_item ) {
					if ( false === in_array( $taxonomy_item->name, array( 'post_format', 'link_category', 'nav_menu', 'wp_theme', 'wp_template_part_area', 'wp_pattern_category' ), true ) ) {
						echo '<option ' . selected( $taxonomy, $taxonomy_item->name, false ) . ' value="' . esc_attr( $taxonomy_item->name ) . '">' . esc_html( $taxonomy_item->label ) . '</option>';
					}
				}
				?>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'parent_category' ) ); ?>">
			<?php esc_html_e( 'Parent Category:', 'ns-category-widget' ); ?>
			</label>
			<?php
			$cat_args = array(
				'orderby'         => 'slug',
				'hide_empty'      => 0,
				'taxonomy'        => $taxonomy,
				'name'            => $this->get_field_name( 'parent_category' ),
				'id'              => $this->get_field_id( 'parent_category' ),
				'class'           => 'nscw-cat-list',
				'selected'        => $parent_category,
				'show_option_all' => esc_html__( 'Show All', 'ns-category-widget' ),
			);
			wp_dropdown_categories( apply_filters( 'widget_categories_dropdown_args', $cat_args ) );
			?>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'depth' ) ); ?>">
			<?php esc_html_e( 'Depth:', 'ns-category-widget' ); ?>
			</label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'depth' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'depth' ) ); ?>">
			<option value="0" <?php selected( $depth, '0' ); ?>><?php esc_html_e( 'Show All', 'ns-category-widget' ); ?> </option>
			<option value="1" <?php selected( $depth, '1' ); ?>>1</option>
			<option value="2" <?php selected( $depth, '2' ); ?>>2</option>
			<option value="3" <?php selected( $depth, '3' ); ?>>3</option>
			<option value="4" <?php selected( $depth, '4' ); ?>>4</option>
			<option value="5" <?php selected( $depth, '5' ); ?>>5</option>
			<option value="6" <?php selected( $depth, '6' ); ?>>6</option>
			<option value="7" <?php selected( $depth, '7' ); ?>>7</option>
			<option value="8" <?php selected( $depth, '8' ); ?>>8</option>
			<option value="9" <?php selected( $depth, '9' ); ?>>9</option>
			<option value="10" <?php selected( $depth, '10' ); ?>>10</option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>">
			<?php esc_html_e( 'Sort By:', 'ns-category-widget' ); ?>
			</label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>">
			<option value="ID" <?php selected( $orderby, 'ID' ); ?>><?php esc_html_e( 'ID', 'ns-category-widget' ); ?></option>
			<option value="name" <?php selected( $orderby, 'name' ); ?>><?php esc_html_e( 'Name', 'ns-category-widget' ); ?></option>
			<option value="slug" <?php selected( $orderby, 'slug' ); ?>><?php esc_html_e( 'Slug', 'ns-category-widget' ); ?></option>
			<option value="count" <?php selected( $orderby, 'count' ); ?>><?php esc_html_e( 'Count', 'ns-category-widget' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>">
			<?php esc_html_e( 'Sort Order:', 'ns-category-widget' ); ?>
			</label>
			<select name="<?php echo esc_attr( $this->get_field_name( 'order' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>">
			<option value="asc" <?php selected( $order, 'asc' ); ?>><?php esc_html_e( 'Ascending', 'ns-category-widget' ); ?></option>
			<option value="desc" <?php selected( $order, 'desc' ); ?>><?php esc_html_e( 'Descending', 'ns-category-widget' ); ?></option>
			</select>
		</p>
		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'hide_empty' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'hide_empty' ) ); ?>" type="checkbox" <?php checked( isset( $instance['hide_empty'] ) ? $instance['hide_empty'] : 0 ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'hide_empty' ) ); ?>">
			<?php esc_html_e( 'Hide Empty', 'ns-category-widget' ); ?>
			</label>
		</p>
		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'show_post_count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_post_count' ) ); ?>" type="checkbox" <?php checked( isset( $instance['show_post_count'] ) ? $instance['show_post_count'] : 0 ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_post_count' ) ); ?>">
			<?php esc_html_e( 'Show Post Count', 'ns-category-widget' ); ?>
			</label>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>">
			<?php esc_html_e( 'Limit:', 'ns-category-widget' ); ?>
			<input type="number" min="0" id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"
			name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3"/>&nbsp;<small><?php esc_html_e( 'Enter limit in number', 'ns-category-widget' ); ?></small>
			</label>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'include_category' ) ); ?>">
			<?php esc_html_e( 'Include category:', 'ns-category-widget' ); ?>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'include_category' ) ); ?>"
			name="<?php echo esc_attr( $this->get_field_name( 'include_category' ) ); ?>" type="text" value="<?php echo esc_attr( $include_category ); ?>"/>
			<small><?php esc_html_e( 'Category IDs, separated by commas.', 'ns-category-widget' ); ?>[<strong><?php esc_html_e( 'Only displays these categories', 'ns-category-widget' ); ?></strong>]</small>
			</label>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'exclude_category' ) ); ?>">
			<?php esc_html_e( 'Exclude category:', 'ns-category-widget' ); ?>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'exclude_category' ) ); ?>"
			name="<?php echo esc_attr( $this->get_field_name( 'exclude_category' ) ); ?>" type="text" value="<?php echo esc_attr( $exclude_category ); ?>"/>
			<small><?php esc_html_e( 'Category IDs, separated by commas.', 'ns-category-widget' ); ?></small>
			</label>
		</p>
		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'enable_tree' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'enable_tree' ) ); ?>" type="checkbox" <?php checked( isset( $instance['enable_tree'] ) ? $instance['enable_tree'] : 0 ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'enable_tree' ) ); ?>">
			<?php esc_html_e( 'Enable Tree', 'ns-category-widget' ); ?>
			</label>
		</p>
		<p><a href="#" class="btn-show-advanced-tree-settings"><?php esc_html_e( 'Advanced Tree settings', 'ns-category-widget' ); ?></a></p>
		<div class="advanced-tree-settings-wrap" style="display:none;">
			<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'tree_show_icons' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tree_show_icons' ) ); ?>" type="checkbox" <?php checked( isset( $instance['tree_show_icons'] ) ? $instance['tree_show_icons'] : 0 ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'tree_show_icons' ) ); ?>">
				<?php esc_html_e( 'Show Tree Icons', 'ns-category-widget' ); ?>
			</label>
			</p>
			<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'tree_show_dots' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tree_show_dots' ) ); ?>" type="checkbox" <?php checked( isset( $instance['tree_show_dots'] ) ? $instance['tree_show_dots'] : 0 ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'tree_show_dots' ) ); ?>">
				<?php esc_html_e( 'Show Dots', 'ns-category-widget' ); ?>
			</label>
			</p>
			<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'tree_save_state' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tree_save_state' ) ); ?>" type="checkbox" <?php checked( isset( $instance['tree_save_state'] ) ? $instance['tree_save_state'] : 0 ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'tree_save_state' ) ); ?>">
				<?php esc_html_e( 'Save Tree State', 'ns-category-widget' ); ?>
			</label>
			</p>
		</div><!-- .advanced-tree-settings-wrap -->

		<?php
	}
}
