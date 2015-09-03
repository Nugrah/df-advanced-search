<?php
/*
Plugin Name: Dahz Advance Search
Plugin URL: http://dahztheme.com/
Description:  Enhance default Wordpress Search Form
Version: 1.0
Author: Dahz
Author URI: http://dahztheme.com/
License: GPL2
*/


// File Security Checked
if ( ! defined('ABSPATH') ) { exit; }

/**
 * Define Class for Dahz Advance Search
 *
 * @since 1.0
 */
class Dahz_Advance_Search extends WP_Widget{

	private static $instance;

	function __construct(){
		self::$instance =& $this;

		WP_Widget::__construct(false, $name = __('DF Widget - Advance Search', 'dahztheme'));

		add_action('wp_enqueue_scripts', array($this, 'enqueue'));
		// Add action to jquery callback
		add_action('wp_ajax_dahz_suggestion', array( $this, 'dahz_suggestion_callback'));
		add_action('wp_ajax_nopriv_dahz_suggestion', array( $this, 'dahz_suggestion_callback'));
	}

	function widget($args, $instance){
		extract($args);

		$title = apply_filters('widget_title',$instance['title']);

		echo $before_widget;

		echo '<div>';

		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
		?>
		<form role="search" method="get" class="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label>
				<span class="screen-reader-text"><?php _e( 'Search for:', 'dahztheme' ); ?></span>
				<span class="searchtext">
				<input type="search" class="search-field" placeholder="<?php esc_attr_e( 'Search ...', 'dahztheme' ); ?>" value="<?php echo get_search_query(); ?>" name="s" title="<?php esc_attr_e( 'Search for:', 'dahztheme' ); ?>" autocomplete="off">
				<span class="suggestion"><!-- --></span>
				</span>
			</label>
		</form>
		<?php

		echo '</div>';

		echo $after_widget;
	}

	function update($new_instance, $old_instance){
		$instance = $old_instance;

		// Field
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

	function form($instance){
		if ($instance) {
			$title = strip_tags($instance['title']);
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

	function enqueue(){
		wp_register_style('das-style', plugins_url('\assets\style.css',__FILE__));
		wp_enqueue_style('das-style');
		wp_enqueue_script('das-script', plugins_url('\assets\main.js', __FILE__), '1.0', true);
		wp_enqueue_script('mousewheel', plugins_url('\assets\jquery.mousewheel.js',__FILE__), '', true);
		wp_localize_script( 'das-script', 'das', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	}

	function dahz_get_all_tags_in_cat($id){
		global $wpdb;

		if ( $id != 0 ) {
			$childs = get_categories(array('parent'=>$id));
		} else {
			$childs = get_categories(array('hierarchical'=>$id));
		}

		$tags = array();
		if (count($childs) > 0) {
			foreach ($childs as $child) {
				$tags = array_merge($tags, $this->dahz_get_all_tags_in_cat($child->cat_ID));
			}
		}

		if ( $id != 0 ) {
			$tags1 = $wpdb->get_results($wpdb->prepare("
			SELECT DISTINCT terms2.term_id as tag_id, terms2.name as tag_name, terms2.slug as tag_slug, null as tag_link
			FROM
				wp_posts as p1
				LEFT JOIN wp_term_relationships as r1 ON p1.ID = r1.object_ID
				LEFT JOIN wp_term_taxonomy as t1 ON r1.term_taxonomy_id = t1.term_taxonomy_id
				LEFT JOIN wp_terms as terms1 ON t1.term_id = terms1.term_id,

				wp_posts as p2
				LEFT JOIN wp_term_relationships as r2 ON p2.ID = r2.object_ID
				LEFT JOIN wp_term_taxonomy as t2 ON r2.term_taxonomy_id = t2.term_taxonomy_id
				LEFT JOIN wp_terms as terms2 ON t2.term_id = terms2.term_id
			WHERE
				t1.taxonomy = 'category' AND p1.post_status = 'publish' AND terms1.term_id = %d AND
				t2.taxonomy = 'post_tag' AND p2.post_status = 'publish'
				AND p1.ID = p2.ID
			ORDER by tag_name
			",$id));
			$count = 0;
			foreach ($tags1 as $tag) {
				$tags1[$count]->tag_link = get_tag_link($tag->tag_id);
				$count++;
			}
			$tags = array_merge($tags,$tags1);
		}

		$result = array();
		foreach ($tags as $tag) {
			$not_added = true;
			foreach ($result as $res) {
				if ($tag->tag_name == $res->tag_name) {
					$not_added = false;
					break;
				}
			}
			if ($not_added) {
				$result[] = $tag;
			}
		}
		return $result;
	}

	function dahz_suggestion_callback(){
		global $wpdb;

		// get value from search form
		$s = $_POST['s'];
		$max = 10;
		if(!is_numeric($max) || $max < 0) $max = 10;

		$args = array('s' => $s, 'edit_posts_per_page' => $max);

		if (!empty($tag)) {
			$args['tag'] = $tag;
		}

		$df_query = new WP_Query($args);
		$html = '<ul>';
			if ($df_query->have_posts()) {
				while ($df_query->have_posts()) {
					$df_query->next_post();
					if (strpos(strtolower($df_query->post->post_title), strtolower($s)) !== false ) {
						$html .= '<li><a href="'. get_permalink($df_query->post->ID) .'">';
						$html .= str_replace($s, '<strong>'.$s.'</strong>',$df_query->post->post_title);
						$html .= '</a></li>';
					}
				}
			}
			wp_reset_query();

			// Seach On Category
			$terms = get_terms('category',array('search' => $s));
			if (count($terms) > 0) {
				foreach ($terms as $term) {
					$html .= '<li><a href="' . get_term_link($term, 'category') . '">';
					$html .= str_replace($s, '<strong>'.$s.'</strong>', $term->name);
					$html .= '</a></li>';
				}
			}
		$html .= '</ul>';
		echo $html;

		// required to return a proper result
		die();
	}


}
add_action('widgets_init', create_function('', 'return register_widget("Dahz_Advance_Search");'), 1);
