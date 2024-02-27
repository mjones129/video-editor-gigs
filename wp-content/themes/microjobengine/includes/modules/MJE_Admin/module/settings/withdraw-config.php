<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 8/2/2016
 * Time: 15:09
 */
if( ! function_exists('mjob_setting_widthdraw_config')) {
    function mjob_setting_widthdraw_config() {
        return array(
            'args' => array(
                'title' => __("Withdraw", 'enginethemes') ,
                'id' => 'withdraw-settings',
                'icon' => '%',
                'class' => ''
            ) ,
            'groups' => array(
                array(
                    'args' => array(
                        'title' => __("Minimum amount of money for withdrawal", 'enginethemes') ,
                        'id' => '',
                        'class' => '',
                        'name' => '',
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'minimum_withdraw',
                            'type' => 'number',
                            'title' => __("Minimum amount of money for withdrawal", 'enginethemes') ,
                            'desc' => sprintf(__("Set up the minimum amount allowed for withdrawal. (%s)", 'enginethemes'), ae_currency_sign(false)),
                            'name' => 'minimum_withdraw',
                            'placeholder' => __("Enter amount of money", 'enginethemes') ,
                            'class' => 'option-item bg-grey-input positive_int_zero',
                            'default' => '20'
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Withdraw Related Mail Template", 'enginethemes') ,
                        'id' => '',
                        'class' => '',
                        'name' => '',
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'secure_code_mail',
                            'type' => 'editor',
                            'title' => __('New secure code request', 'enginethemes'),
                            'desc' => __("Send to user when he requests a secure code to withdraw money", 'enginethemes'),
                            'class' => '',
                            'name' => 'secure_code_mail',
                            'reset' => 1,
                            'toggle' => true
                        ),
                        array(
                            'id' => 'new_withdraw',
                            'type' => 'editor',
                            'title' => __('New withdrawal request', 'enginethemes'),
                            'desc' => __("Send to admin when a user requests to withdraw money", 'enginethemes'),
                            'class' => '',
                            'name' => 'new_withdraw',
                            'reset' => 1,
                            'toggle' => true
                        ),
                        array(
                            'id' => 'approve_withdraw',
                            'type' => 'editor',
                            'title' => __('Withdrawal request approved', 'enginethemes'),
                            'desc' => __("Send to user when his withdrawal has been approved", 'enginethemes'),
                            'class' => '',
                            'name' => 'approve_withdraw',
                            'reset' => 1,
                            'toggle' => true
                        ),
                        array(
                            'id' => 'decline_withdraw',
                            'type' => 'editor',
                            'title' => __('Withdrawal request declined', 'enginethemes'),
                            'desc' => __("Send to user when his withdrawal request has been declined", 'enginethemes'),
                            'class' => '',
                            'name' => 'decline_withdraw',
                            'reset' => 1,
                            'toggle' => true
                        )
                    )
                ),
            )
        );
    }
}