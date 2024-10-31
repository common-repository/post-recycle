<?php
/*
    Plugin Name: Post Recycle
    Plugin URI: http://zourbuth.com/?p=837
    Description: A powerfull addon for <a href="http://codecanyon.net/item/the-countdown-pro/3228499?ref=zourbuth">The Countdown Pro</a> plugin for handling the post or custom post type in recycle mode, displaying it in the content or in a sidebar widget and hide your content for a set of time.
    Version: 0.0.3
    Author: zourbuth
    Author URI: http://zourbuth.com
    License: GPL2
    
	Copyright 2013 zourbuth.com (email: zourbuth@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


/**
 * Exit if accessed directly
 * @since 0.0.2
 */
if ( ! defined( 'ABSPATH' ) )
	exit;

add_action( 'plugins_loaded', 'post_recycle_plugin_loaded', 10 );

// Set constant
define( 'POST_RECYCLE_VERSION', '0.0.3' );
define( 'POST_RECYCLE_DIR', plugin_dir_path( __FILE__ ) );
define( 'POST_RECYCLE_URL', plugin_dir_url( __FILE__ ) );


/**
 * Initializes the plugin and it's features
 * Load require file
 * Loads and registers the widgets
 * @since 0.0.1
 */
function post_recycle_plugin_loaded() {
	if( defined('THE_COUNTDOWN_PRO_URL') ) {
		require_once( POST_RECYCLE_DIR . 'shortcode.php' );
		add_action( 'widgets_init', 'post_recycle_load_widgets' );
	} else {
		add_action('admin_notices', 'post_recycle_admin_message');
	}
}


/**
 * Load widget, require additional file and register the widget
 * @since 0.0.1
 */
function post_recycle_load_widgets( $atts ) {
	require_once( POST_RECYCLE_DIR . 'widget.php' );
	register_widget( 'Post_Recycle_Widget' );
}


/**
 * Function to add additional admin message for 
 * administrator or user that can manage options
 * @since 0.0.1
 */
function post_recycle_admin_message() {

    if ( current_user_can( 'manage_options' ) ) {
		echo '<div id="pe-message" class="updated">
				<p>
					<strong>Post Recycle</strong><br />
					Please install <a href="http://codecanyon.net/item/the-countdown-pro/3228499?ref=zourbuth"><strong>The Countdown Pro</strong></a> plugin to make this addon plugin works!
				</p>
			</div>';
    }
}


/**
 * Function to display the post(s) in the front end.
 * Parameters set by widget option or the shortcode
 * @since 0.0.1
 */
function post_recycle( $args ) {
	extract( $args );
	
	// print_r($args);
	if( ! $posts ) 
		return __('Please set the posts first', 'post-recycle'); 
	
	$html = "";
	
	$cur_time = strtotime( current_time('mysql') );
	$set_time = strtotime( $until[0] . '/' . $until[1] . '/' . $until[2] . ' ' . $until[3] . ':' . $until[4] );

	//echo $cur_time . '<br />';
	//echo $set_time. '<br />';
	//echo $cur_time - $set_time . '<br />';
	
	$total_posts = count( $posts );
	$time_diff = $cur_time - $set_time;	
	$runtime = $time_diff / $cycle;			
	$next_cycle = ceil( $runtime ) * $cycle;	// total time to add to the current time
	$next = $set_time + $next_cycle;
	
	$cur_cycle = floor($runtime) * $cycle;
	$cur = $set_time + $cur_cycle + $duration;
	
	$cur_post = $runtime % $total_posts;	// get the current post number	

	$gp = get_post( $posts[$cur_post] );

	$html .= "<h3>{$gp->post_title}</h3>";
	$html .= '<div id="'. $id .'" class="cycle-count countdown-default" 
					data-date="' . date( "m/d/Y H:i:s", $next ) . '" 
					data-format="' . $format . '"
			  ></div>';

	if( $cur > $cur_time ) {
		$html .= do_shortcode( $gp->post_content ) . "<div class='duration-count' data-date='" . date("m/d/Y H:i:s", $cur) . "'></div>";
	}
	
	return $html; 
}