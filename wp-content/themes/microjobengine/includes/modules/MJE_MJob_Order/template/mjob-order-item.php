<?php
global $post, $ae_post_factory;
$mjob_order_obj = $ae_post_factory->get('mjob_order');
$mjob_order = $mjob_order_obj->convert($post);
$class_status = "mjob-order-".$mjob_order->post_status;
?>
<li class="mjob-order-item <?php echo $class_status;?> includes\modules\MJE_MJob_Order\template\mjob-order-item.php type-<?php echo $mjob_order->post_type;?> item-id-<?php echo $mjob_order->ID;?>">
	<div class="row">
		<div class="col-md-5 col-sm-3 purchase-info">
			<?php
			if( $post ) {
			switch ($post->post_status) {
				case 'pending':
					echo '<div class="status"><a title="' . __("Pending", 'enginethemes') . '" class="color-red error" href="#"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></a></div>';
					break;
				case 'draft':
					echo '<div class="status"><a title="' .__("Failed", 'enginethemes') .'" class="color" style="color :grey;" href="#"><i class="fa fa-times-circle" aria-hidden="true"></i></a></div>';
					break;
				default:
					echo '<div class="status"><a title="" class="color-green" href="#"><i class="fa fa-check-circle" aria-hidden="true"></i></a></div>';
					break;
			} ?>
				<?php
					if($mjob_order->avatar) {
						echo "<img src='$mjob_order->avatar' alt='Avatar'>";
					}
				?>
				<div class="entry-content">
					<?php if($mjob_order): ?>
						<a target="_blank" href="<?php echo get_author_posts_url($post->post_author, $author_nicename = '') ?>" class="company">
							<?php echo get_the_author_meta('display_name',$post->post_author) ?>
						</a>
					<?php endif;
					_e(' has ordered ', 'enginethemes'); ?>
					<a target="_blank" href="<?php echo $mjob_order->mjob_order_link; ?>" class="ad ad-name">
						<?php echo $mjob_order->post_title; ?>
					</a>
				</div>
				<?php
			} else {
				$author	=	'<a target="_blank" href="'.get_author_posts_url($post->post_author).'" class="company">' .
					get_the_author_meta('display_name',$post->post_author) .
					'</a>';
				?>
				<span>
						<?php printf (__("This post has been deleted by %s", 'enginethemes') , $author ); ?>
					</span>
				<?php
			}
			?>
		</div>
		<div class="col-md-2 col-sm-2">
			<div class="purchase-actions">
				<?php
				if($post->post_status == 'pending') : ?>
					<a title="<?php _e("Approve", 'enginethemes'); ?>" data-action="approve" class="color-green action publish" data-id="<?php echo $post->ID; ?>" href="#">
						<i class="fa fa-check" aria-hidden="true"></i>
					</a>
					<a title="<?php _e("Decline", 'enginethemes'); ?>" data-action="decline-mjob-order" class="color-red action decline" data-id="<?php echo $post->ID; ?>" href="#">
						<i class="fa fa-times" aria-hidden="true"></i>
					</a>
					<?php
				endif;
				?>
			</div>
		</div>

		<div class="col-md-3 col-sm-4 time-join">
			<span class="date"><?php echo $mjob_order->order_human_time ?></span>
		</div>
		<div class="col-md-2 col-sm-3 payment-type">
			<?php
			if($mjob_order->icon_gateway)
				echo $mjob_order->icon_gateway;
			?>
		</div>
	</div>
</li>