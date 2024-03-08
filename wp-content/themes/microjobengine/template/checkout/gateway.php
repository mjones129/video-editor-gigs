<?php
global $user_ID;
?>
<div class="post-job step-payment" id="checkout-step2">
    <p class="float-center note"><?php _e('Please select the most appropriate payment gateway for you.', 'enginethemes'); ?></p>
    <form method="post" action="" id="checkout_form">
        <div class="payment_info"></div>
        <div style="position:absolute; left : -7777px; " >
            <input type="submit" id="payment_submit" />
        </div>
    </form>
    <ul class="list-price list-payment-gateway">
        <?php
        $paypal = ae_get_option('paypal');
        if( isset( $paypal['enable'] ) && $paypal['enable'] ) :
            ?>
            <li>
                <div class="outer-payment-items hvr-underline-from-left">
                    <a href="#" class="btn-submit-price-plan select-payment" data-type="paypal">
                        <img src="<?php echo get_template_directory_uri() ?>/assets/img/card-paypal.svg" alt="">
                        <p><?php _e("PAYPAL", 'enginethemes'); ?></p>
                    </a>
                </div>
            </li>
        <?php endif; ?>
        <?php
        $co = ae_get_option('2checkout');
        if( isset($co['enable']) && $co['enable'] ) { ?>
            <li>
                <div class="outer-payment-items hvr-underline-from-left">
                    <a href="#" class="btn-submit-price-plan select-payment" data-type="2CHECKOUT">
                        <img height="72" src="<?php echo get_template_directory_uri() ?>/assets/img/2Co.svg" alt="">
                        <p><?php _e("2CHECKOUT", 'enginethemes'); ?></p>
                    </a>
                </div>
            </li>

        <?php }?>


        <?php
        $cash = ae_get_option('cash');
        if(isset($cash['enable']) && $cash['enable']) :
            ?>
            <li>
                <div class="outer-payment-items hvr-underline-from-left">
                    <a href="#" class="btn-submit-price-plan select-payment" data-type="cash">
                        <img src="<?php echo get_template_directory_uri() ?>/assets/img/card-cash.svg" alt="">
                        <p><?php _e("CASH", 'enginethemes'); ?></p>
                    </a>
                </div>
            </li>
        <?php endif; ?>

        <?php
        /**
         * Credit Payment Gateway
         */
        $credit = ae_get_option('credit');
        if( isset($credit['enable']) && $credit['enable'] ) :
            ?>
            <li>
                <div class="outer-payment-items hvr-underline-from-left">
                    <a href="#" id="credit-gateway" class="btn-submit-price-plan" data-checkout-type="checkout_order" data-type="credit" data-enable="true">
                        <img src="<?php echo get_template_directory_uri() ?>/assets/img/card-credit.svg" alt="">
                        <p class="text-cash"><?php _e("CREDIT", 'enginethemes'); ?></p>
                    </a>
                </div>
            </li>
        <?php endif; ?>

        <?php do_action( 'mje_after_payment_list' ); ?>
    </ul>
</div><!-- end #checkout-step2 -->
