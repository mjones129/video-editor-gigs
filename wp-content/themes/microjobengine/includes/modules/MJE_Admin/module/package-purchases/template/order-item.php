<?php
global $post;
$order_object =	new AE_Order($post->ID);
$order_data = $order_object->get_order_data();
$products = $order_data['products'];
$package = array_pop($products);

$post_parent = '';
if($post->post_parent) {
	$post_parent = get_post($post->post_parent);
}

$support_gateway = apply_filters('ae_support_gateway', array(
	'cash' => __("<p class='cash'>Cash</p>", 'enginethemes'),
	'paypal' => __("<p class='paypal'>Paypal</p>", 'enginethemes'),
	'2checkout' => __("<p class='checkout'>2Checkout</p>", 'enginethemes'),
	'credit' => __("<p class='credit'>Credit</p>", 'enginethemes'),
));

$payment_name = mje_render_payment_name();
?>
<li class="clearfix purchase-packgate-item-<?php echo $post->ID;?>">
	<div class="row">
		<div class="col-md-2 col-sm-2 purchase-price">
			<?php echo ae_price_format($order_data['total']); ?>
		</div>
		<div class="col-md-6 col-sm-5 purchase-info">
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
                        }
                    ?>

					<?php
						if($post_parent) { ?>
							<?php
								echo mje_avatar($post->post_author, 58);
							?>
							<a target="_blank" href="<?php echo get_author_posts_url($post->post_author, $author_nicename = '') ?>" class="users-purchase">
								<?php echo get_the_author_meta('display_name',$post->post_author) ?>
							</a>
							<span><?php  _e(' has purchased ', 'enginethemes');?></span>
							<a target="_blank" href="<?php echo get_permalink( $post_parent->ID ) ?>" class="ad ad-name">
								<?php
									echo get_the_title( $post_parent->ID );
								?>
									<span class="pck-name"><?php echo ' (' .$package['NAME']. ')' ;
									?></span>
							</a>
						<?php }else { ?>
							<a href="#" class="ad ad-name">
								<?php echo '(' .$package['NAME']. ')' ; ?>
							</a>
						<?php
						}
									?>
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
						}?>
				<p class = "purchase-pack-time"><?php the_time( 'g:i a' );?> <?php  the_date();?></p>
		</div>
		<div class="col-md-2 col-sm-2">
			<div class="purchase-actions">
				<?php
				if($post->post_status == 'pending') : ?>
				<a title="<?php _e("Approve", 'enginethemes'); ?>" class="color-green action publish" data-id="<?php echo $post->ID; ?>" href="#">
					<i class="fa fa-check" aria-hidden="true"></i>
				</a>
				<a title="<?php _e("Decline", 'enginethemes'); ?>" class="color-red action decline" data-id="<?php echo $post->ID; ?>" href="#">
					<i class="fa fa-times" aria-hidden="true"></i>
				</a>
				<?php
				endif;
				?>
			</div>
		</div>
		<div class="col-md-2 col-sm-3 payment-type">
			<?php echo isset( $payment_name[$order_data['payment']] ) ? $payment_name[$order_data['payment']] : $order_data['payment'];?>
		</div>
	</div>
</li>