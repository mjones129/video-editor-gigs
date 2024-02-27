<div class="action-form" id="send-offer">

    <a class="btn-back btn-back-custom-order"><i class="fa fa-angle-left"></i><?php _e('Back', 'enginethemes'); ?></a>
    <div class="outer-detail-custom">
        <form class="form-delivery-order et-form">
            <p class="cata-title"><?php _e('Send offer', 'enginethemes'); ?></p>

            <div class="form-group clearfix">
                <label for="post_content"><?php _e('Description', 'enginethemes'); ?></label>
                <textarea name="post_content" id="post_content"></textarea>
                <div class="describe-custom-order">
                    <p><i class="fa fa-exclamation-circle" aria-hidden="true"></i> <span><?php _e('Describe the differences from the original mJob you are offering to this Buyer. This will be considered as contract terms and added to the Checkout detail page.', 'enginethemes'); ?></span></p>
                </div>
            </div>


            <div class="form-group clearfix">
                <label for="form-budget"><?php _e( 'Budget', 'enginethemes' ); ?> (<?php ae_currency_code(); ?>)</label>
                <input type="number" name="budget" id="budget" class="form-control">
            </div>

            <div class="form-group clearfix">
                <label for="etd"><?php _e('Time of delivery (Day)', 'enginethemes'); ?></label>
                <input type="number" name="etd" id="etd" class="form-control">
            </div>

            <div class="form-group clearfix">
                <div class="attachment-file gallery_container_send_offer" id="gallery_container">
                    <div class="attachment-image">
                        <ul class="gallery-image carousel-list send_offer-image-list" id="image-list">
                        </ul>

                        <?php mje_render_attach_file_button('send_offer'); ?>
                    </div>
                </div>
            </div>
            <input type="hidden" class="input-item _wpnonce" name="_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
            <button class="<?php mje_button_classes( array('submit', 'waves-effect', 'waves-light' ) ); ?>"><?php _e('Send offer', 'enginethemes'); ?></button>
        </form>
    </div>
</div>