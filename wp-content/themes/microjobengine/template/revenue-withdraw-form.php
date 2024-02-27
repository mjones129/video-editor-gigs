<div class="payment-method">
    <p class="choose-payment"><?php _e('Choose your way to get money', 'enginethemes'); ?></p>
    <p class="link-change-payment">
        <a href="<?php echo et_get_page_link('payment-method'); ?>"><?php _e('Change your payment method', 'enginethemes'); ?></a>
    </p>
</div>
<div id="withdrawForm">
    <form class="et-form">
        <div class="form-group check-payment">
            <div class="radio">
                <label for="paypal_account">
                    <input type="radio" name="account_type" id="paypal_account" value="paypal" checked>
                    <span><?php _e('PayPal account', 'enginethemes'); ?></span>
                </label>
            </div>
            <div class="radio">
                <label for="bank_account">
                    <input type="radio" name="account_type" id="bank_account" value="bank">
                    <span><?php _e('Bank account', 'enginethemes'); ?></span>
                </label>
            </div>
        </div>
        <div class="code-bank">
            <div class="form-group clearfix value-payment">
                <div class="input-group">
                    <label for="amount"><?php _e( 'Money amount', 'enginethemes' ); ?></label>
                    <input type="number" name="amount" id="amount" placeholder="<?php printf(__('Minimum: %s', 'enginethemes'), mje_format_price(MIN_WITHDRAW, "", true, false)); ?>">
                </div>
            </div>

            <div class="form-group clearfix value-payment">
                <div class="input-group">
                    <label for="secure_code"><?php _e( 'Secure code', 'enginethemes' ); ?></label>
                    <input type="password" name="secure_code" id="secure_code">
                </div>
            </div>

            <div class="form-group">
                <span><?php _e("Don't have secure code or forgot it? ", 'enginethemes'); ?><a href="#" class="request-secure-code"><?php _e('Request here', 'enginethemes'); ?></a></span>
            </div>

            <input type="hidden" name="_wpnonce" id="_wpnonce" value="<?php echo de_create_nonce('withdraw_action') ?>">

            <div class="form-group submit-bank">
                <button class="<?php mje_button_classes( array( 'btn-submit-bank', 'waves-effect', 'waves-light' ) ); ?>"><?php _e('Submit', 'enginethemes'); ?></button>
            </div>
        </div>
    </form>
</div>