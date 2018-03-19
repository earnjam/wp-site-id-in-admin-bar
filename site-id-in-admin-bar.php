<?php
/*
Plugin Name: Site ID in Admin Bar
Plugin URI: http://github.com/earnjam/site-id-in-admin-bar
Description: Show the ID of the current site in the admin bar for network admins
Version: 1.0.1
Author: William Earnhardt
Author URI: https://wearnhardt.com
License: GPLv2
*/

add_action( 'admin_bar_menu', 'jwe_add_site_id_to_admin_bar', 40, 1 );

function jwe_add_site_id_to_admin_bar( $wp_admin_bar ) {

	$title_link = $wp_admin_bar->get_node( 'site-name' );

	if ( is_super_admin() && ! is_network_admin() ) {
		$title_link->title .= ' [' . get_current_blog_id() . ']';
	}

	$wp_admin_bar->add_node( $title_link );

}