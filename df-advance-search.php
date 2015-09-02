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


// File Security Checked
if ( ! defined('ABSPATH') ) { exit; }

/**
 *
 */
class Dahz_Advance_Search extends WP_Widget{

	function Dahz_Advance_Search(){
		WP_Widget::__construct(false, $name = __('DF Widget - Advance Search', 'dahztheme'));
	}

	function widget($args, $instance){
		extract($args);

		$title = apply_filters('widget_title',$instance['title']);

		echo $before_widget;

		echo '<div>';

		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

		echo '</div>';

		echo $after_widget;
	}

	function update($new_instance, $old_instance){
		$instance = $old_instance;

		// Field
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['text'] = strip_tags($new_instance['text']);
		return $instance;
	}

	function form($instance){
		if ($instance) {
			$title = esc_attr($instance['title']);
		} else {
			$title = '';
		}

		?>
			<p>
				<label for="<?php echo $this->get_field_id('title') ?>"><?php _e('Title :', 'dahztheme'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" type="text" name="<?php echo $this->get_field_name('title') ?>" value="<?php echo $title; ?>">
			</p>
		<?php
	}
}
add_action('widgets_init', create_function('', 'return register_widget("Dahz_Advance_Search");'), 1);
