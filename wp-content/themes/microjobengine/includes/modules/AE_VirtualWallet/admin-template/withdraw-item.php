<?php
global $post, $ae_post_factory;
$withdraw_obj = $ae_post_factory->get('ae_credit_withdraw');
$withdraw = $withdraw_obj->convert($post);
?>
<li class="withdraw-item clearfix">
	<div class="row">
		<div class="col-md-2 col-sm-2 purchase-price">
            <span><?php echo $withdraw->amount_formated; ?></span>
		</div>
		<div class="col-md-3 col-sm-3 purchase-info">
			<?php
			if( $post ) {
				switch ($post->post_status) {
					case 'pending':
						echo '<div class="status"><a title="' . __("Pending", 'enginethemes') . '" class="color-red error" href="#"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></a></div>';
						break;
					case 'publish':
						echo '<div class="status"><a title="'. __("Confirmed", 'enginethemes') . '" class="color-green" href="#"><i class="fa fa-check-circle" aria-hidden="true"></i></a></div>';
						break;
					default:
						echo '<div class="status"><a title="' .__("Failed", 'enginethemes') .'" class="color" style="color :grey;" href="#"><i class="fa fa-times-circle" aria-hidden="true"></i></a></div>';
						break;
				} ?>
				<?php echo $withdraw->author_avatar; ?>
				<a target="_blank" href="<?php echo $withdraw->edit_link; ?>" class="company">
					<?php echo $withdraw->author_name; ?>
				</a>
			<?php
			}
			?>
		</div>
		<div class="col-md-1 col-sm-1">
			<div class="purchase-actions">
				<?php
				if($post->post_status == 'pending') : ?>
					<a title="<?php _e("Approve", 'enginethemes'); ?>" data-action="approve" class="color-green action publish" data-id="<?php echo $post->ID; ?>" href="#">
						<i class="fa fa-check" aria-hidden="true"></i>
					</a>
					<a title="<?php _e("Decline", 'enginethemes'); ?>" data-action="decline-withdraw" class="color-red action decline" data-id="<?php echo $post->ID; ?>" href="#">
						<i class="fa fa-times" aria-hidden="true"></i>
					</a>
					<?php
				endif;
				?>
			</div>
		</div>
		<div class="col-md-4 col-sm-4 time-join">
			<span class="date"><?php echo date(get_option('date_format').' '.get_option('time_format'),$withdraw->post_date).' '.mje_text_timezone(); //$withdraw->time_request_formated; ?></span>
		</div>
		<div class="col-md-2 col-sm-2 withdraw-method payment-type">
			<span><?php echo $withdraw->payment_method_text; ?></span>
		</div>
	</div>
</li>