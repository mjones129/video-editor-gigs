<div class="modal fade" id="delivery" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><img
                            src="<?php echo get_template_directory_uri() ?>/assets/img/icon-close.png" alt=""></span></button>
                <h4 class="modal-title" id="myModalLabelForgot">Delivery info</h4>
            </div>
            <div class="modal-body delivery-order">
                <div class="form-delivery-order">
                    <form class="et-form">
                        <div class="form-group">
                            <label for="post_content"><?php _e( 'Description', 'enginethemes' ); ?></label>
                            <textarea name="post_content"></textarea>
                        </div>
                        <div class="form-group clearfix">
                            <div class="attachment-file gallery_container" id="gallery_container">
                                <div class="attachment-image">
                                    <ul class="gallery-image carousel-list deliver-image-list" id="image-list">
                                    </ul>
                                    <?php mje_render_attach_file_button('deliver'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="hidden" class="input-item post-service_nonce" name="_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
                            <button class="<?php mje_button_classes( array( 'submit') ); ?>"><?php _e('Send', 'enginethemes'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>