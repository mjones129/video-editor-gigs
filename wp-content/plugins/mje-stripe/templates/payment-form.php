<form action="#" method="POST" id="payment-form" class="et-form">
    <div class="form-row">
        <label for="card-element">
            <?php _e( 'Credit or debit card', 'mje_stripe' ); ?>
        </label>
        <div id="card-element">
            <!-- a Stripe Element will be inserted here. -->
        </div>

        <!-- Used to display form errors -->
        <div id="card-errors" class="payment-errors"></div>
    </div>

    <div class="clearfix">
        <button type="submit" class="<?php echo mje_button_classes(  array( 'submit-payment', 'waves-effect', 'waves-light' ) ); ?>"><?php _e( 'Pay Now', 'mje_stripe' );  ?></button>
        <img class="powered-by" src="<?php echo MJE_STRIPE_URL . '/assets/img/powered_by_stripe.svg' ?>" alt="powered-by-stripe">
    </div>
</form>