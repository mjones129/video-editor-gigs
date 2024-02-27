<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 8/2/2016
 * Time: 14:52
 */
if( ! function_exists('mjob_setting_payment_type')) {
    function mjob_setting_payment_type() {
        return array(
            'args' => array(
                'title' => __("Payment type", 'enginethemes') ,
                'id' => 'payment-type-settings',
                'icon' => '%',
                'class' => ''
            ) ,

            'groups' => array(
                array(
                    'args' => array(
                        'title' => __('Free to submit mJob', 'enginethemes'),
                        'desc' => '',
                        'id' => '',
                        'class' => '',
                    ),
                    'fields' => array(
                        array(
                            'id' => 'disable_plan',
                            'type' => 'switch',
                            'title' => __("Price mode", 'enginethemes') ,
                            'desc' => __("Enabling this will allow users to submit mJob for free.", 'enginethemes'),
                            'name' => 'disable_plan',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __('Payment Plans', 'enginethemes'),
                        'id' => 'list-package',
                        'class' => 'list-package',
                        'desc' => '',
                        'name' => 'payment_package',
                    ) ,
                    'type' => 'list',
                    'fields' => array(
                        'form' => '/admin-template/package-form.php',
                        'form_js' => '/admin-template/package-form-js.php',
                        'js_template' => '/admin-template/package-js-item.php',
                        'template' => '/admin-template/package-item.php'
                    )
                )
            )
        );
    }
}