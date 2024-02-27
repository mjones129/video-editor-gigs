<script type="text/template" id="template_edit_form">
	<form action="qa-update-badge" class="edit-plan engine-payment-form" novalidate="novalidate">
		<input type="hidden" name="id" value="{{= id }}">

		<div class="payment-plan">
			<div class="form-item clearfix">
				<div class="row">
					<div class="col-lg-2 col-md-2 col-sm-4 sku">
						<label><?php _e("SKU", 'enginethemes'); ?></label>
						<input value="{{= sku }}" class="not-empty required" name="sku" type="text" />
					</div>
					<div class="col-lg-10 col-md-8 col-sm-8 package-name-plan">
						<label for="package-name"><?php _e("Package name", 'enginethemes'); ?></label>
						<input value="{{= post_title }}" class="not-empty required" name="post_title" type="text">
					</div>
				</div>
			</div>
			<div class="form-item clearfix">
				<div class="row">
					<div class="col-lg-2 col-md-2 col-sm-2 price-package">
						<label><?php _e("Price", 'enginethemes'); ?> (<?php
							ae_currency_sign();
							?>)</label>
						<input value="{{= et_price }}" name="et_price" type="number" min="0"/>
					</div>
					<div class="col-lg-4 col-md-6 col-sm-6 duration">
						<label><?php _e("Duration",'enginethemes');?></label>
						<input value="{{= et_duration }}" class="positive_int" type="number" min="1" name="et_duration" <# if(typeof et_permanent !== 'undefinded' && et_permanent == "1") { #>disabled <# } #>/>
							<div class="permanent">
								<span><?php _e('days or','enginethemes');?></span>
								<span><input type="checkbox" name="et_permanent" value="1" data-disable="et_duration" <# if (typeof et_permanent !== 'undefined' && et_permanent == 1 ) { #> checked="checked" <# } #> /><?php _e('&nbsp;permanent', 'enginethemes'); ?></span>
							</div>
					</div>
					<div class="col-lg-4 col-md-6 col-sm-6 number-post-job">
						<label><?php _e("Number of mJob can post", 'enginethemes'); ?></label>
						<input value="{{= et_number_posts }}" class="positive_int" type="number" name="et_number_posts" min="1"/>
					</div>
				</div>
			</div>

			<div class="form-item description">
				<label><?php _e("Short description about this package",'enginethemes');?></label>
				<input class="not-empty" name="post_content" type="text" value="{{= post_content }}" />
			</div>
			<?php if(function_exists('mje_featured_loaded') ){?>
			<div class="form-item featured">
				<label><?php _e("Featured mJob",'enginethemes');?></label>
				<input type="checkbox" name="et_featured" value="1" <# if (typeof et_featured !== 'undefined' && et_featured == 1 ) { #> checked="checked" <# } #>  />
				<span><?php _e("This plan will be featured.",'enginethemes');?></span>
			</div>
			<?php }?>
			<div class="submit">
				<button  class="btn-save engine-submit-btn add_payment_plan"><?php _e( 'Save Package' , 'enginethemes' ); ?></button>
				<span class="or">or</span> <a href="#" class="cancel-edit"><?php _e( "Cancel" , 'enginethemes' ); ?></a>
			</div>
		</div>
	</form>
</script>