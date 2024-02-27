<form action="de-add-package" class="engine-payment-form add-pack-form">
	<div class="payment-plan">
		<div class="form-item clearfix">
			<div class="row">
				<div class="col-lg-2 col-md-2 col-sm-4 sku">
					<label><?php _e("SKU",'enginethemes');?></label>
					<input class="required" name="sku" type="text" />
				</div>
				<div class="col-lg-10 col-md-8 col-sm-8 package-name-plan">
					<label><?php _e("Package name",'enginethemes');?></label>
					<input class="required" name="post_title" type="text" />
				</div>
			</div>
		</div>
		<div class="form-item clearfix">
			<div class="row">
				<div class="col-lg-2 col-md-2 col-sm-2 price-package">
					<label><?php _e("Price",'enginethemes');?> (<?php
						ae_currency_sign();
						?>)</label>

					<input type="number" class=""  min= "0" name="et_price" />
				</div>
				<div class="col-lg-4 col-md-6 col-sm-6 duration">
					<label><?php _e("Duration",'enginethemes');?></label>
					<input class="positive_int" min= 1 type="number" name="et_duration" />
					<div class="permanent">
						<span><?php _e('days or','enginethemes');?></span>
						<span><input type="checkbox" name="et_permanent" value="1" data-disable="et_duration"/> <?php _e('&nbsp;permanent', 'enginethemes'); ?></span>
					</div>
				</div>
				<div class="col-lg-4 col-md-6 col-sm-6 number-post-job">
					<label><?php _e("Number of mJob can post",'enginethemes');?></label>
					<input class="positive_int" min= 1 type="number" name="et_number_posts" />
					<span><?php _e("posts",'enginethemes');?></span>
				</div>
			</div>
		</div>
		<div class="form-item description">
			<label><?php _e("Short description about this package",'enginethemes');?></label>
			<input class="not-empty" name="post_content" type="text" />
		</div>
		<?php if(function_exists('mje_featured_loaded') ){?>
			<div class="form-item featured">
				<label><?php _e("Featured mJob",'enginethemes');?></label>
				<input type="checkbox" name="et_featured" value="1"/>
				<span><?php _e("This plan will be featured.",'enginethemes');?></span>
			</div>
		<?php }?>
		<div class="submit">
			<button class="btn-save engine-submit-btn add_payment_plan"><?php _e("Save Plan",'enginethemes');?></button>
		</div>
	</div>
</form>