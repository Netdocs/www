<?php

function streamium_add_login_logout_register_menu( $items, $args ) {

	if ( $args->theme_location != 'streamium-header-menu' ) {
		return $items;
 	}
 
 	if ( is_user_logged_in() ) {
 
 		$items .= '<li><a href="' . wp_logout_url() . '">' . __( 'Log Out' ) . '</a></li>';
 
 	} else {
 	
 		$items .= '<li><a href="' . wp_login_url() . '">' . __( 'Login In' ) . '</a></li>';
 		$items .= '<li><a href="' . wp_registration_url() . '">' . __( 'Sign Up' ) . '</a></li>';

 	}
 	return $items;

}
 
add_filter( 'wp_nav_menu_items', 'streamium_add_login_logout_register_menu', 199, 2 );