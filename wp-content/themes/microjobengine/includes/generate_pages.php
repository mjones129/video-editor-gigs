<?php

/**
 *@since Mje 1.3.9.7
 **/

function mje_generate_page( $args ) {
	$page_slug 		= $args['slug'];
	$page_title 	= $args['post_title'];
	$post_content 	= isset($args['post_content']) ? $args['post_content']: 'Please fill out the form below ';
	$page_args = array(
		'post_name' => $page_slug,
		'post_title'   => $page_title,
		'post_content' => $post_content,
		'post_type'    => 'page',
		'post_status'  => 'publish'
	);

	$pages = get_pages( array(
		'meta_key'    => '_wp_page_template',
		'meta_value'  => 'page-' . $page_slug . '.php',
		'numberposts' => 1
	) );
	$opt_page_name = 'page-' . $page_slug . '.php';

	// page not existed
	if ( empty( $pages ) || ! is_array( $pages ) ) {
		$id = wp_insert_post( $page_args );

		if ( $id ) {
			update_post_meta( $id, '_wp_page_template', 'page-' . $page_slug . '.php' );
		}

	} else {
		$page = array_shift( $pages );
		$id   = $page->ID;
	}
	if ( $id != - 1 ) {
		$return = get_permalink( $id );
	}
	/**
	 * update transient page link
	 */
	update_option( $opt_page_name, $return );
	return $id;
}

function mje_auto_generate_et_pages(){
	$pages = mje_get_page_default();
	foreach($pages as $key=>$title){

		$args = array(
			'slug' 			=> $key,
			'post_title' 	=> $title,
			'post_content' 	=> '.'
		);
		mje_generate_page($args);
	}
}
function mje_get_page_default(){
	return $args = array(
		'home-new' 			=> "Home New",
		'register' 			=> 'Sign Up',
		'reset-pass' 		=> 'Reset Password',
		'list-notification' => 'List Notification',
		'forgot-password' 	=> 'Forgot Password',
		'login' 			=> 'Login',
		'profile' 			=> 'Profile',
		'upgrade-account' 	=> 'Upgrade Account',
		'submit-project' 	=> 'Post a Project',
		'edit-project' 		=> 'Edit Project',
		'process-payment' 	=> 'Process Payment',
		'cancel-payment' 	=> 'Cancel Payment',
		'my-project'	 	=> 'My Project',
		'my-credit' 		=> 'My Credit',
		'tos' 				=> 'Terms of service',
	);
}


function mje_generate_default_page( $old_theme_name, $old_theme = false ) {
	// et_log('activate freelanceengine');
	mje_auto_generate_et_pages();
}
//add_action( 'after_switch_theme', 'mje_generate_default_page', 99, 2 );

function mje_reset_general_page($newname, $newtheme) {
	$pages = mje_get_page_default();
	foreach($pages as $key=>$title){
		$opt_page_name = 'page-' . $key . '.php';
		update_option($opt_page_name ,'');
	}
}
add_action("switch_theme", "mje_reset_general_page", 10 , 2);