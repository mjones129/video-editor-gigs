<?php
/**
 * Class render user manage in engine themes backend
 * - list user
 * - search user
 * - load more user
 * @since 1.0
 * @author Dakachi
*/
class AE_UsersContainer {

	/**
	 * construct a user container
	*/
	function __construct( $args = array(), $roles = '' ) {
		$this->args		=	$args;
		$this->roles	=	$roles;
	}
	/**
	 *
	*/
    function render()
	{
		global $wp_roles, $user;
		$number = get_option('posts_per_page');
		$args = array(
			'number' => $number,
			'count_total' => true,
			'orderby' => 'user_registered',
			'order' => 'desc'
		);
		// array role name
		$role_names = $wp_roles->role_names;

		$ae_users = new AE_Users();
		//Update user meta delivery order if don't exist
		if( ! get_option('updated_mjob_user_delivery_order')) {
			$ae_users->update_all_user_meta_delivery_order($role_names);
			update_option('updated_mjob_user_delivery_order', true);
		}

		$result = $ae_users->fetch($args);

		$users = $result['data'];

		$pagination = $result['paginate'];
			require_once('page-users.php');
		}
	}