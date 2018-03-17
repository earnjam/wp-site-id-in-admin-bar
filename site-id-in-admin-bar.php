<?php
/*
Plugin Name: Site ID in Admin Bar
Plugin URI: http://github.com/earnjam/site-id-in-admin-bar
Description: Show the ID of the current site in the admin bar for network admins
Version: 1.0
Author: William Earnhardt
Author URI: https://wearnhardt.com
License: GPLv2
*/

add_action( 'admin_bar_menu', 'jwe_add_site_id_to_admin_bar', 40, 1 );

function jwe_add_site_id_to_admin_bar( $wp_admin_bar ) {
	$wp_admin_bar->remove_node( 'site-menu' );

	// Don't show for logged out users.
	if ( ! is_user_logged_in() )
		return;

	// Show only when the user is a member of this site, or they're a super admin.
	if ( ! is_user_member_of_blog() && ! current_user_can( 'manage_network' ) ) {
		return;
	}

	$blogname = get_bloginfo('name');

	if ( ! $blogname ) {
		$blogname = preg_replace( '#^(https?://)?(www.)?#', '', get_home_url() );
	}

	if ( is_network_admin() ) {
		/* translators: %s: site name */
		$blogname = sprintf( __( 'Network Admin: %s' ), esc_html( get_network()->site_name ) );
	} elseif ( is_user_admin() ) {
		/* translators: %s: site name */
		$blogname = sprintf( __( 'User Dashboard: %s' ), esc_html( get_network()->site_name ) );
	}

	$title = wp_html_excerpt( $blogname, 40, '&hellip;' );

	if ( is_super_admin() && ! is_network_admin() ) {
		$title .= ' [' . get_current_blog_id() . ']';
	}

	$wp_admin_bar->add_menu( array(
		'id'    => 'site-name',
		'title' => $title,
		'href'  => ( is_admin() || ! current_user_can( 'read' ) ) ? home_url( '/' ) : admin_url(),
	) );

	// Create submenu items.

	if ( is_admin() ) {
		// Add an option to visit the site.
		$wp_admin_bar->add_menu( array(
			'parent' => 'site-name',
			'id'     => 'view-site',
			'title'  => __( 'Visit Site' ),
			'href'   => home_url( '/' ),
		) );

		if ( is_blog_admin() && is_multisite() && current_user_can( 'manage_sites' ) ) {
			$wp_admin_bar->add_menu( array(
				'parent' => 'site-name',
				'id'     => 'edit-site',
				'title'  => __( 'Edit Site' ),
				'href'   => network_admin_url( 'site-info.php?id=' . get_current_blog_id() ),
			) );
		}

	} else if ( current_user_can( 'read' ) ) {
		// We're on the front end, link to the Dashboard.
		$wp_admin_bar->add_menu( array(
			'parent' => 'site-name',
			'id'     => 'dashboard',
			'title'  => __( 'Dashboard' ),
			'href'   => admin_url(),
		) );

		// Add the appearance submenu items.
		wp_admin_bar_appearance_menu( $wp_admin_bar );
	}

}