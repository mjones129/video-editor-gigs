<?php
global $user_ID;
$step = 4;

$disable_plan = ae_get_option('disable_plan', false);
if($disable_plan) $step--;
if($user_ID) $step--;

?>
<div class="post-job step-payment step-wrapper" id="step4">
    <p class="select-gateway"><?php _e('Please select your payment method.', 'enginethemes'); ?></p>

    <form method="post" action="" id="checkout_form">
        <div class="payment_info"></div>
        <div style="position:absolute; left : -7777px; " >
            <input type="submit" id="payment_submit" />
        </div>
    </form>
    <ul class="list-price "> <!-- list-payment-gateway add  in 1.3.9.2 !-->
        <div class="row">
            <?php
            $paypal = ae_get_option('paypal');
            if( isset($paypal['enable']) && $paypal['enable'] ) {
                ?>
                <li>
                    <div class="outer-payment-items hvr-underline-from-left">
                        <a href="#" class="btn-submit-price-plan select-payment" data-type="paypal"><img src="<?php echo get_template_directory_uri() ?>/assets/img/card-paypal.svg" alt="" class="img-logo">
                        <p class="text-bank"><?php _e("PAYPAL", 'enginethemes'); ?></p>
                        </a>
                    </div>
                </li>
            <?php }
            $co = ae_get_option('2checkout');
            if( isset($co['enable']) && $co['enable'] ) {
                ?>
                <li>
                    <div class="outer-payment-items hvr-underline-from-left">
                        <a href="#" class="btn-submit-price-plan select-payment" data-type="2checkout"><img src="<?php echo get_template_directory_uri() ?>/assets/img/card-2checkout.svg" alt="">
                        <p class="text-checkout"><?php _e("2CHECKOUT", 'enginethemes'); ?></p>
                        </a>
                    </div>
                </li>
                <?php
            }
            $cash = ae_get_option('cash');
            if( isset($cash['enable']) && $cash['enable'] ) {
                ?>
                <li>
                    <div class="outer-payment-items hvr-underline-from-left">
                        <a href="#" class="btn-submit-price-plan select-payment" data-type="cash"><img src="<?php echo get_template_directory_uri() ?>/assets/img/card-cash.svg" alt="">
                        <p class="text-cash"><?php _e("CASH", 'enginethemes'); ?></p></a>
                    </div>
                </li>
            <?php }

            $credit = ae_get_option('credit');
            if( isset($credit['enable']) && $credit['enable'] ) {
                ?>
                <li>
                    <div class="outer-payment-items hvr-underline-from-left">
                        <a href="#" id="credit-gateway" class="btn-submit-price-plan other-payment" data-type="credit" data-enable="true" data-checkout-type="checkout_package"><img src="<?php echo get_template_directory_uri() ?>/assets/img/card-credit.svg" alt="">
                        <p class="text-cash"><?php _e("CREDIT", 'enginethemes'); ?></p></a>
                    </div>
                </li>
            <?php }

            do_action( 'after_payment_list' );
            ?>
        </div>
    </ul>
    <?php do_action( 'after_payment_list_wrapper' ); ?>
</div>
