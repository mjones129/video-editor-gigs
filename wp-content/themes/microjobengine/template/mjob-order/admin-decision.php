<?php if( ( $mjob_order->post_status == 'disputing' && is_super_admin() ) ):  ?>
<div class="decided mjob-admin-dispute-form">
    <p class="text"><?php _e("Admin's decided", 'enginethemes') ?></p>
    <form class="et-form">
        <div class="form-group">
            <label class="text-result-choose"><?php _e('Select a winner to the dispute:', 'enginethemes'); ?></label>
            <div class="radio">
                <label>
                    <input type="radio" name="winner" id="winner" class="order_buyer order_person" value="<?php echo $mjob_order->post_author;  ?>" checked>
                    <span><?php echo $mjob_order->author_name; ?></span>
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" name="winner" id="winner" class="order_seller order_person" value="<?php echo $mjob_order->mjob_author ?>">
                    <span><?php echo $mjob_order->mjob_author_name; ?></span>
                </label>
            </div>
        </div>
		<div class="form-group select_order_change">
			<div class="checkbox">
				<label for="check_for_refun_fee_check">
					<input type="hidden" name="check_for_refun_fee" class="check_for_refun_fee" value="0" id="check_for_refun_fee">
					<input type="checkbox" name="check_for_refun_fee_check" class="check_for_refun_fee_check" id="check_for_refun_fee_check" >
					<span><?php echo _e( "Also refund the fee to the buyer's available fund", 'enginethemes' ); ?></span>
				</label>
			</div>
		</div>
        <div class="form-group">
            <label for="post_content"><?php _e( 'Your explanation', 'enginethemes' ); ?></label>
            <textarea name="post_content" id="post_content" rows="10"></textarea>
        </div>
        <div class="form-group">
            <button class="<?php mje_button_classes( array() ); ?>"><?php _e('Submit', 'enginethemes'); ?></button>
            <input name="to_user" type="hidden" value="<?php echo $mjob_order->to_user; ?>">
            <input type="hidden" class="input-item post-service_nonce" name="_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
        </div>
    </form>
</div>
<?php endif; ?>