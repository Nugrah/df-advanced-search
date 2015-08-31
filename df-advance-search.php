<?php

/*
Plugin Name: Dahz Advance Search
Plugin URL: http://dahztheme.com/
Description:  Enhance default Wordpress Search Form
Version: 1.0.0
Author: Dahz
Author URI: http://dahztheme.com/
License: GPL2
*/

if ( ! defined('ABSPATH') ) { exit; }

/**
 * Adds Foo_Widget widget.
 */
class Dahz_advance_search extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		WP_Widget::__construct(
			'foo_widget', // Base ID
			__( 'Dahz Advance Search', 'dahztheme' ), // Name
			array( 'description' => __( 'Enhance default Wordpress Search Form', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] );
		?>
            <p>
                  <label for="<?php echo $this->get_field_id('title') ?>">
                  <?php echo __('Title: '); ?>
                  <input class="widefat"/>
                  </label>
            </p>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

} // class Dahz_advance_search
// register Dahz_advance_search widget
function register_foo_widget() {
    register_widget( 'Dahz_advance_search' );
}
add_action( 'widgets_init', 'register_foo_widget' );
