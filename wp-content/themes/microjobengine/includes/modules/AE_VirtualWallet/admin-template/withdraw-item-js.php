<?php global $post; ?>
<script type="text/template" id="fre-credit-withdraw-loop">
	<div class="row">
		<div class="col-md-2 col-sm-2 purchase-price">
			<span>{{= amount_formated}}</span>
		</div>
		<div class="col-md-3 col-sm-3 purchase-info">
			<?php if( $post ) { ?>
			<#	if(post_status == 'pending') { #>
				<div class="status"><a title="<?php _e("Pending", 'enginethemes')?>" class="color-red error" href="#"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></a></div>
				<#	}else if( post_status == 'publish'){ #>
					<div class="status"><a title="<?php _e("Confirmed", 'enginethemes') ?>" class="color-green" href="#"><i class="fa fa-check-circle" aria-hidden="true"></i></a></div>
					<# } else{ #>
						<div class="status"><a title="<?php _e("Failed", 'enginethemes') ?>" class="color" style="color :grey;" href="#"><i class="fa fa-times-circle" aria-hidden="true"></i></a></div>
						<#		}  #>
							<?php	} ?>
					{{= author_avatar }}
					<a target="_blank" href="{{= edit_link }}" class="company">
						{{= author_name }}
					</a>
		</div>
		<div class="col-md-1 col-sm-1">
			<div class="purchase-actions">
				<#	if(post_status == 'pending') { #>
					<a title="<?php _e("Approve", 'enginethemes'); ?>" data-action="approve" class="color-green action publish" data-id="{{= ID }}" href="#">
						<i class="fa fa-check" aria-hidden="true"></i>
					</a>
					<a title="<?php _e("Decline", 'enginethemes'); ?>" data-action="decline-withdraw" class="color-red action decline" data-id="{{= ID }}" href="#">
						<i class="fa fa-times" aria-hidden="true"></i>
					</a>
					<# } #>
			</div>
		</div>
		<div class="col-md-4 col-sm-4 time-join">
			<span class="date">{{= date_text }}</span>
		</div>
		<div class="col-md-2 col-sm-2 withdraw-method payment-type">
			<span>{{= payment_method_text}}</span>
		</div>
	</div>
</script>