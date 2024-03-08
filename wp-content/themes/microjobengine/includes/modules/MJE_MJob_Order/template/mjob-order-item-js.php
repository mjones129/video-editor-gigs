<?php
global $post;
?>
<script type="text/template" id="mjob-order-loop">
	<div class="row item-ID-{{= ID }}">
		<div class="col-md-5 col-sm-3 purchase-info ">
			<?php if( $post ) { ?>
			<#	if(post_status == 'pending') { #>
				<div class="status"><a title="<?php _e("Pending", 'enginethemes')?>" class="color-red error" href="#"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></a></div>
				<#	}else if( post_status == 'draft'){ #>
					<div class="status"><a title="<?php _e("Failed", 'enginethemes') ?>" class="color" style="color :grey;" href="#"><i class="fa fa-times-circle" aria-hidden="true"></i></a></div>
					<# } else{ #>
						<div class="status"><a title="" class="color-green" href="#"><i class="fa fa-check-circle" aria-hidden="true"></i></a></div>
						<#		}  #>
							<?php	} ?>
			<?php
				if($post) {
			?>
				<img src="{{= avatar }}" alt="Avatar">
			<?php
				}
			?>

			<div class="entry-content">
				<?php if( $post ): ?>
					<a target="_blank" href="{{= mjob_order_author_url }}" class="company">
						{{= mjob_order_author_name }}
					</a>
				<?php endif;
				_e(' has ordered ', 'enginethemes'); ?>
				<a target="_blank" href="{{= mjob_order_link }}" class="ad ad-name">
					{{= post_title }}
				</a>
			</div>
		</div>
		<div class="col-md-2 col-sm-2">
			<div class="purchase-actions">
				<#	if(post_status == 'pending') { #>
					<a title="<?php _e("Approve", 'enginethemes'); ?>" data-action="approve" class="color-green action publish" data-id="{{= ID }}" href="#">
						<i class="fa fa-check" aria-hidden="true"></i>
					</a>
					<a title="<?php _e("Decline", 'enginethemes'); ?>" data-action="decline-mjob-order" class="color-red action decline" data-id="{{= ID }}" href="#">
						<i class="fa fa-times" aria-hidden="true"></i>
					</a>
					<# } #>
			</div>
		</div>
		<div class="col-md-3 col-sm-4 time-join">
			<span class="date">{{= order_human_time }}</span>
		</div>
		<div class="col-md-2 col-sm-3 payment-type">
			{{= icon_gateway }}
		</div>
	</div>
</script>