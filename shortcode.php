<?php
/**
    The Countdown Pro - Shortcodes
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


add_shortcode('post-recycle', 'post_recycle_sc');


/*
 * Main function to generate shortcode using total_users_pro() function
 * See $defaults arguments for using total_users_pro() function
 * Shortcode does not generate the custom style and script
 * @since 0.0.1
 */
function post_recycle_sc($atts, $content) {

	extract( shortcode_atts( array(
		'id'	=> null
	), $atts )); 
	
	if( ! isset( $id ) )
		return __( 'Invalid ID', 'post-recycle');
		
	$options = get_option('widget_post-recycle');

	$html = post_recycle( $options[$id] );
	return $html;
}
?>