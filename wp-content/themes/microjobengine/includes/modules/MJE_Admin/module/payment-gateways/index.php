<?php
function mje_get_payment_gateway_sections() {
    $options = AE_Options::get_instance();
    $temp = array();
    $sections = array();
    $test_mode = ae_get_option( 'test_mode', true);

    $notification_url   =  et_get_page_link('process-payment');
    $enable_auto_return = 'https://www.paypal.com/businessmanage/preferences/website';
    if($test_mode){
        $enable_auto_return = 'https://www.sandbox.paypal.com/businessmanage/preferences/website';
    }

    $setting_url  = get_template_directory_uri() . '/assets/img/payal_url.jpg';
    $note_paypal = "
       <br /> Note:
            Turn on Auto return and Set URL for  <a target='_blank' href='".$enable_auto_return."'>Auto return for website payments</a> in paypal account. <br />
            <strong>Set Payment data transfer</strong> ON.
             <a target='_blank' href='".$setting_url."' >References screenshot</a>";

    $sections['general'] = array(
        'args' => array(
            'title' => __("General", 'enginethemes') ,
            'id' => 'payment-gateways-settings',
            'icon' => 'y',
            'class' => ''
        ) ,
        'groups' => array(
            /* Payment test mode */
            array(
                'args' => array(
                    'title' => __('Payment Test Mode', 'enginethemes'),
                    'desc' => '',
                    'id' => '',
                    'class' => '',
                ),
                'fields' => array(
                    array(
                        'id' => 'test-mode',
                        'type' => 'switch',
                        'title' => __("Payment test mode", 'enginethemes') ,
                        'desc' => __("Enabling this will allow you to test payment without charging your account.", 'enginethemes') ,
                        'name' => 'test_mode',
                        'class' => 'option-item bg-grey-input '
                    )
                )
            ),
            /* Payment Gateways */


            array(
                'args' => array(
                    'title' => __('Default Payment Gateways', 'enginethemes'),
                    'desc' => __('Set payment plans your users can choose when posting new mJobs.', 'enginethemes'),
                    'id' => '',
                    'class' => 'group-payment-gateways',
                ),
                'fields' => array(
                    /* PayPal API */
                    array(
                        'id' => 'paypal',
                        'name' => 'paypal',
                        'type' => 'combine',
                        'title' => __('PayPal', 'enginethemes'),
                        'desc' => __( 'User can select PayPal gateway as a payment method to buy package for mJob listings or make any transactions on your site.', 'enginethemes'),
                        'class' => 'field-payment',
                        'children' => array(
                            array(
                                'id' => 'paypal_enable',
                                'type' => 'switch',
                                'title' => __("Enable PayPal API", 'enginethemes') ,
                                'desc' => __('Enabling this will allow your users to pay via PayPal.', 'enginethemes'),
                                'name' => 'enable',
                                'class' => 'option-item bg-grey-input '
                            ) ,
                            array(
                                'id' => 'api_username',
                                'type' => 'text',
                                'title' => __("PayPal Business Account", 'enginethemes') ,
                                'name' => 'api_username',
                                'class' => 'option-item bg-grey-input ',
                                'placeholder' => __('Enter your PayPal Business Account(email) address', 'enginethemes'),
                               'desc'  => __( "Use a business account and enable <a href='https://developer.paypal.com/api/nvp-soap/ipn/IPNSetup/' target='_blank'><strong>Instant payment notifications(IPN)</strong></a><br /> PayPay IPN URL:<strong>".$notification_url."</strong>.".$note_paypal, 'enginethemes' ),
                            ),
                            array(
                                'id' => 'api_invoice_prefix',
                                'type' => 'text',
                                'title' => __("Invoice prefix", 'enginethemes') ,
                                'desc' => __('Enter the unique prefix for your invoice numbers if your PayPal account is used for multiple websites, since PayPal won’t allow orders with the same invoice number.', 'enginethemes'),
                                'name' => 'invoice_prefix',
                                'class' => 'option-item bg-grey-input ',
                                'placeholder' => __('Enter your invoice prefix', 'enginethemes')
                            )
                        )
                    ),
                    /* 2Checkout API */
                    array(
                        'id' => '2checkout',
                        'name' => '2checkout',
                        'type' => 'combine',
                        'title' => __('2Checkout', 'enginethemes'),
                        'desc' => __("Go to <a href='https://www.2checkout.com/'>https://www.2checkout.com</a> signup to get 2Checkout Seller ID & Your 2Checkout Secret Key.</br><em>Since 2CheckOut prohibits selling Products/Services as an Agent for a Third Party (<a href='https://www.2checkout.com/policies/prohibited-product-list/' target='_blank'>https://www.2checkout.com/policies/prohibited-product-list/</a>), 2CheckOut can only be used for selling package to post mJobs in your marketplace.</em>", 'enginethemes'),
                        'class' => 'field-payment',
                        'children' => array(
                            array(
                                'id' => '2checkout_enable',
                                'type' => 'switch',
                                'title' => __("Enable 2Checkout API", 'enginethemes') ,
                                'desc' => __('Enabling this will allow your users to pay via 2Checkout.', 'enginethemes'),
                                'name' => 'enable',
                                'class' => 'option-item bg-grey-input '
                            ) ,
                            array(
                                'id' => 'sid',
                                'type' => 'text',
                                'title' => __("Seller ID", 'enginethemes') ,
                                'name' => 'sid',
                                'class' => 'option-item bg-grey-input ',
                                'placeholder' => __('Enter your 2Checkout seller ID', 'enginethemes')
                            ) ,
                            array(
                                'id' => 'secret_key',
                                'type' => 'text',
                                'title' => __("Secret key", 'enginethemes') ,
                                'name' => 'secret_key',
                                'class' => 'option-item bg-grey-input ',
                                'placeholder' => __('Enter your 2Checkout secret key', 'enginethemes')
                            )
                        )
                    ),
                    /* Cash */
                    array(
                        'id' => 'cash',
                        'name' => 'cash',
                        'title' => __('Cash', 'enginethemes'),
                        'type' => 'combine',
                        'desc' => __( 'User can select Cash as a payment method to buy package for mJob listings or make any transactions on your site. Each transaction paid by Cash is pending until you approve the payment.', 'enginethemes' ),
                        'class' => 'field-payment',
                        'children' => array(
                            array(
                                'id' => 'cash_message_enable',
                                'type' => 'switch',
                                'title' => __("Enable Cash", 'enginethemes') ,
                                'desc' => __('Enabling this will allow your user to send cash to your bank account.','enginethemes'),
                                'name' => 'enable',
                                'class' => 'option-item bg-grey-input '
                            ) ,
                            array(
                                'id' => 'cash_message',
                                'type' => 'editor',
                                'title' => __("Cash message", 'enginethemes') ,
                                'name' => 'cash_message',
                                'class' => 'option-item bg-grey-input ',
                            )
                        )
                    ),
                    /* Credit */
                    array(
                        'id' => 'credit',
                        'name' => 'credit',
                        'title' => __('Credit', 'enginethemes'),
                        'type' => 'combine',
                        'desc' => __( 'Credit payment method allows users to use credits in Available fund to buy package for mJob listings or make any transactions on your site.', 'enginethemes' ),
                        'class' => 'field-payment',
                        'children' => array(
                            array(
                                'id' => 'checkout_credit_enable',
                                'type' => 'switch',
                                'title' => __("Enable Credit", 'enginethemes') ,
                                'desc' => __("Enabling this will allow your user to pay by their Available Fund.", 'enginethemes') ,
                                'name' => 'enable',
                                'class' => 'option-item bg-grey-input '
                            ) ,
                            array(
                                'id' => 'secure_code_enable',
                                'type' => 'switch',
                                'title' => __("Enable Secure Code", 'enginethemes') ,
                                //'desc' => __("Enabling this and buye has to input secure code in the checkout order popup.", 'enginethemes') ,
                                'name' => 'secure_code_enable',
                                'default' => 'enable',
                                'class' => 'option-item bg-grey-input '
                            ) ,
                        )
                    ),
                )
            ),
            /* Pay Package Related Mail Template */
            array(
                'args' => array(
                    'title' => __('Pay Package Related Mail Template', 'enginethemes'),
                    'desc' => __('Email templates used for pay package-related event', 'enginethemes'),
                    'id' => '',
                    'class' => '',
                ),
                'fields' => array(
                    array(
                        'id' => 'pay_package_by_cash',
                        'type' => 'editor',
                        'title' => __('Cash payment receipt notification', 'enginethemes'),
                        'desc' => __("Send to user when he pays by cash", 'enginethemes'),
                        'class' => '',
                        'name' => 'pay_package_by_cash',
                        'reset' => 1,
                        'toggle' => true
                    ),
                    array(
                        'id' => 'ae_receipt_mail',
                        'type' => 'editor',
                        'title' => __('Payment receipt notification', 'enginethemes'),
                        'desc' => __("Send to user when he pays via other payment gateways (exclude Cash)", 'enginethemes'),
                        'class' => '',
                        'name' => 'ae_receipt_mail',
                        'reset' => 1,
                        'toggle' => true
                    )
                )
            ),
        )
    );

    /**
     * Filter: mje_payment_gateway_setting_sections
     *
     * @params array $sections
     * @since 1.3.2
     * @author Tat Thien
     */
    $sections = apply_filters( 'mje_payment_gateway_setting_sections', $sections );

    foreach ( $sections as $section ) {
        $temp[] = new AE_section( $section['args'], $section['groups'], $options );
    }

    return $temp;
}