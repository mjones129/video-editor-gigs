<div class="modal fade" id="topup-changelog-modal-front" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><img
                            src="<?php echo get_template_directory_uri() ?>/assets/img/icon-close.png" alt=""></span></button>
                <h4 class="modal-title" id="myModalLabelForgot"><?php _e('Credit Top-up History', 'enginethemes'); ?></h4>
            </div>

            <div class="modal-body">
                <div class="inner">
                    <div class="loading">
                        <div class="loading-img"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" class="nonce" value="<?php echo wp_create_nonce( 'mje_topup_user_front' ); ?>">
</div>