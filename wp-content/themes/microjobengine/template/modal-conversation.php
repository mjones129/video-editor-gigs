<div class="modal fade" id="conversation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><img
                            src="<?php echo get_template_directory_uri() ?>/assets/img/icon-close.png" alt=""></span></button>
                <h4 class="modal-title" id="myModalLabelForgot"><?php _e('Conversation', 'enginethemes'); ?></h4>
            </div>

            <div class="modal-body mjob-modal-conversation-form">
                <div class="outer-conversation">
                    <form class="et-form">
                        <div class="form-group">
                            <label for="modal_conversation_content"><?php _e( 'Your message', 'enginethemes' ); ?></label>
                            <textarea name="conversation_content" id="modal_conversation_content"></textarea>
                        </div>

                        <div class="form-group">
                            <div class="attachment-file gallery_container_modal_conversation" id="message_modal_gallery_container">
                                <div class="attachment-image">
                                    <ul class="gallery-image carousel-list modal_conversation-image-list" id="image-list">
                                    </ul>
                                    <?php mje_render_attach_file_button('modal_conversation'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <input type="hidden" class="input-item post-service_nonce" name="_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
                            <button class="<?php mje_button_classes( array('waves-effect', 'waves-light', 'submit' ) ); ?>"><?php _e('Send', 'enginethemes'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>