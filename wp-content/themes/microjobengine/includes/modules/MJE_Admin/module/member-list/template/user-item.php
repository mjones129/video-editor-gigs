<?php
	global $user, $wp_roles;
	$user_role	=	$user->roles;
	$user_role	=	array_pop($user_role);
	$role_names	=	$wp_roles->role_names;

?>
<li class="et-member" data-id="<?php echo $user->ID; ?>">
	<?php do_action( 'ae_admin_before_user_item', $user ); ?>
	<div class="row et-mem-container">
		<div class="col-md-4 col-sm-4 purchase-info">
			<a href="<?php echo isset($user->author_url) ? $user->author_url : '#'; ?>" target="_blank" title="<?php _e('View this public profile', 'enginethemes'); ?>">
				<?php
					echo isset($user->avatar) ? $user->avatar : '';
				?>
			</a>
			<a href="<?php echo isset($user->author_url) ? $user->author_url : '#'; ?>" target="_blank" title="<?php _e('View this public profile', 'enginethemes'); ?>">
				<span class="name"><?php echo $user->display_name; ?></span>
			</a>
		</div>
		<?php do_action( 'ae_admin_before_user_details', $user ); ?>
		<div class="col-md-2 col-sm-2">
			<div class="et-act purchase-actions">
				<!-- Manual Confirm User -->
				<?php if( $user->register_status == "unconfirm" && current_user_can( 'administrator' ) ){ ?>
					<a class="action et-act-confirm" data-act="confirm" title="Confirm this user">
						<i class="fa fa-check" aria-hidden="true"></i>
					</a>
				<?php } ?>
				<!-- Manual Confirm User -->
			</div>
		</div>
		<div class="col-md-3 col-sm-3 time-join">
			<span class="date"><?php printf(__("%s ago", 'enginethemes'), human_time_diff(strtotime($user->user_registered), current_time('timestamp') )) ; ?></span>
		</div>
		<div class="col-md-3 col-sm-3 payment-type">
			<span class="mjob_delivery_order">
			<?php
				if($user->mjob_delivery_order)
					printf(__('%s', 'enginethemes'), $user->mjob_delivery_order);
				else
					echo '0';
			?>
			</span>
		</div>
		<?php do_action( 'ae_admin_after_user_details', $user ); ?>
	</div>
</li>