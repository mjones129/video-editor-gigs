<?php
/**
 * Source: https://github.com/PieterT2000/elementor-hacks/blob/master/script.php
 * And you can always wp_dequeue_style(), wp_deregister_style(), wp_deregister_script(), wp_dequeue_script() any asset Elementor adds that you don't want.

 **/

/**
 *  Remove unused scripts loaded by Elementor
 *  @since v1.3.9.8
 **/

function mje_disable_elementor_frontend_scripts() {
    //you can change yourself for which pages the conditional logic below accounts
	if( is_page_template('page-post-service.php') || is_singular('mjob_post') || is_singular('ae_message') || is_tax('mjob_category') || is_post_type_archive('mjob_post') || is_search() || is_singular('mjob_order') || is_page_template('page-profile.php') ) {

		// Dequeue and deregister swiper
		// wp_dequeue_script( 'swiper' );
		// wp_deregister_script( 'swiper' );

		// // Dequeue and deregister elementor-dialog
		// wp_dequeue_script( 'elementor-dialog' );
		// wp_deregister_script( 'elementor-dialog' );

		// // Dequeue and deregister elementor-frontend
		// wp_dequeue_script( 'elementor-frontend' );
		// wp_deregister_script( 'elementor-frontend' );

		wp_dequeue_script( 'backbone-marionette' );
		wp_deregister_script( 'backbone-marionette' );
		//backbone-marionette-js

		// Re-register elementor-frontend without the elementor-dialog/swiper dependency.
		// $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		// wp_register_script(
		// 	'elementor-frontend',
		// 	ELEMENTOR_ASSETS_URL . 'js/frontend' . $suffix . '.js',
		// 	[
		// 		'elementor-frontend-modules',
		// 		'elementor-waypoints'
		// 	],
		// 	ELEMENTOR_VERSION,
		// 	true
		// );
	}
}
add_action( 'wp_enqueue_scripts', 'mje_disable_elementor_frontend_scripts', 999 );

/**
 * Fix hide the button Upload theme/ Upload plugin.
 * @since 1.3.9.8
**/
function mje_disable_load_admin_js(){
	$is_admin = 0;
	global $pagenow;
    if( is_admin() && in_array( $pagenow, array("plugins.php","themes.php","theme-install.php","plugin-install.php") ) ){

    	wp_dequeue_script( 'backbone-marionette' );
		wp_deregister_script( 'backbone-marionette' );
		wp_deregister_script('elementor-common');
		wp_dequeue_script('elementor-common');
		//elementor-app-loader
		wp_deregister_script('elementor-app-loader');
		wp_dequeue_script('elementor-app-loader');

		wp_deregister_style('elementor-admin-top-bar');
		wp_dequeue_style('elementor-admin-top-bar');

    }
}
add_action('admin_enqueue_scripts','mje_disable_load_admin_js', 99);