<?php

include 'questions.php';

add_action( 'wp_enqueue_scripts', 'enqueue_parent_theme_style' );
function enqueue_parent_theme_style() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
}

// always show admin bar
// based on code by scribu
add_action( 'show_admin_bar', '__return_true', 999 );
add_action( 'template_redirect', 'erg_admin_bar_login_css' );
function erg_admin_bar_login_css() {
	if ( is_user_logged_in() )
		return;

	wp_enqueue_style( 'admin-bar-login', plugins_url( 'admin-bar-login.css', __FILE__ ), array(), '1.0.1' );

	add_action( 'admin_bar_menu', 'erg_admin_bar_login_menu' );
}

function erg_admin_bar_login_menu( $wp_admin_bar ) {
	$wp_admin_bar->add_menu( array(
		'id'     => 'login',
		'title'  => __( 'Log in' ),
		'href' => wp_login_url()
	) );

// 	$wp_admin_bar->add_menu( array(
// 		'id'     => 'lostpassword',
// 		'title'  => __( 'Lost your password?' ),
// 		'href' => wp_lostpassword_url()
// 	) );

	if ( get_option( 'users_can_register' ) ) {
		$wp_admin_bar->add_menu( array(
			'id'     => 'register',
			'title'  => __( 'Register' ),
			'href' => site_url( 'wp-login.php?action=register', 'login' )
		) );
	}
}

add_action('admin_menu', 'erg_admin_menu',1000);
function erg_admin_menu() {
	if ( current_user_can( 'manage_options' ) )
		return;
	remove_menu_page('jetpack');
}

