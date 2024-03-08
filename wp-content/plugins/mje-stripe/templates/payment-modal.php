<div class="modal fade" id="stripe_checkout_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        <img src="<?php echo MJE_STRIPE_URL . '/assets/img/icon-close.png'; ?>" alt="">
                    </span>
                </button>
                <h4 class="modal-title" id="myModalLabelForgot"><?php _e( 'Stripe', 'mje_stripe' ); ?></h4>
            </div>

            <div class="modal-body">
                <?php mje_stripe_get_template( 'payment-form.php' ); ?>
            </div>
        </div>
    </div>
</div>