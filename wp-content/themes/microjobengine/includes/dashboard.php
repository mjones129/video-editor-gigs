<?php
function change_mjob_profile_link($permalink, $post){
	if( is_admin() && $post->post_type == 'mjob_profile' ){
		$permalink = get_author_posts_url($post->post_author);
	}
	return $permalink;

}
add_filter('post_type_link', 'change_mjob_profile_link', 10 , 2);



function mje_activate_add_cap () {

	$admin_role = get_role( 'administrator' );

    $admin_role->add_cap( 'edit_ae_message' );
    $admin_role->add_cap( 'read_ae_message' );
    $admin_role->add_cap( 'delete_ae_message' );
    $admin_role->add_cap( 'delete_others_ae_messages' );

    $admin_role->add_cap( 'edit_ae_messages' );
    $admin_role->add_cap( 'edit_others_ae_messages' );
    $admin_role->add_cap( 'publish_ae_messages' );
    $admin_role->add_cap( 'read_private_ae_messages' );
    $admin_role->add_cap( 'create_ae_messages' );
}
add_action('after_switch_theme', 'mje_activate_add_cap');