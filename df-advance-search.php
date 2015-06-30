<?php

/*
Plugin Name: Dahz DF Advance Search
Plugin URL: http://dahztheme.com/
Description:  Enhance default Wordpress Search Form
Version: 1.0.0
Author: Dahz
Author URI: http://dahztheme.com/
*/

if ( ! defined('ABSPATH') ) { exit; }

/**
 * new WordPress Widget format
 * Wordpress 2.8 and above
 * @see http://codex.wordpress.org/Widgets_API#Developing_Widgets
 */
class df_advanceSearch_Widget extends WP_Widget {

    /**
     * Constructor
     *
     * @return void
     **/
    function df_advanceSearch_Widget() {
        $widget_ops = array( 'classname' => 'advance-search-widget', 'description' => __('This widget is display an advance search', 'dahztheme') );
        
        $this->WP_Widget( 'advance-search-widget', __('DF Widget - Advance Search Form', 'dahztheme'), $widget_ops );
    }

    /**
     * Outputs the HTML for this widget.
     *
     * @param array  An array of standard parameters for widgets in this theme
     * @param array  An array of settings for this widget instance
     * @return void Echoes it's output
     **/
    function widget( $args, $instance ) {
        extract( $args, EXTR_SKIP );

        $title 	= apply_filters('widget_title', $instance['title'], $instance, $this->id_base);

        echo $before_widget;

        if ($title) {
            echo $before_title . esc_attr( $title ) . $after_title;
        } // End IF Statement

        echo $after_title;

    //
    // Widget display logic goes here
    //

    echo $after_widget;
    }

    /**
     * Deals with the settings when they are saved by the admin. Here is
     * where any validation should be dealt with.
     *
     * @param array  An array of new settings as submitted by the admin
     * @param array  An array of the previous settings
     * @return array The validated and (if necessary) amended settings
     **/
    function update( $new_instance, $old_instance ) {

        // update logic goes here
        $updated_instance = $new_instance;
        return $updated_instance;
    }

    /**
     * Displays the form for this widget on the Widgets page of the WP Admin area.
     *
     * @param array  An array of the current settings for this widget
     * @return void Echoes it's output
     **/
    function form( $instance ) {
        $defaults = array('title' => __('Advance Search', 'dahztheme'), 'adcode' => '', 'image' => '', 'href' => '', 'alt' => '');
        
        $instance = wp_parse_args( (array) $instance, $defaults );

        $read_only = '';
        if ( !current_user_can('unfiltered_html') ) {
        	$read_only = 'readonly="readonly"';
        }

        ?>

			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title (Optional):', 'dahztheme'); ?></label>	
				<input type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($instance['title']); ?>" class="" id="<?php echo $this->get_field_id('title'); ?>">
			</p>

        <?php

        // display field names here using:
        // $this->get_field_id( 'option_name' ) - the CSS ID
        // $this->get_field_name( 'option_name' ) - the HTML name
        // $instance['option_name'] - the option value
    }
}

add_action( 'widgets_init', create_function( '', "register_widget( 'df_advanceSearch_Widget' );" ) );