<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 8/2/2016
 * Time: 13:59
 */
if( ! function_exists('mje_setting_currency_section')) {
    function mje_setting_currency_section() {
        return array(
            'args' => array(
                'title' => __("Currency", 'enginethemes') ,
                'id' => 'currency-settings',
                'icon' => 'y',
                'class' => ''
            ) ,
            'groups' => array(
                /* Payment Currency */
                array(
                    'args' => array(
                        'title' => __('Payment currency', 'enginethemes'),
                        'desc' => '',
                        'id' => '',
                        'class' => '',
                    ),
                    'fields' => array(
                        array(
                            'id' => 'currency',
                            'name' => 'currency',
                            'type' => 'combine',
                            'title' => __('Payment currency', 'enginethemes'),
                            'desc' => __( 'Enter the code and sign for your preferred currency. Besides, select the html align option.', 'enginethemes' ),
                            'children' => array(
                                array(
                                    'id' => 'currency-code',
                                    'type' => 'text',
                                    'title' => __("Code", 'enginethemes') ,
                                    'name' => 'code',
                                    'placeholder' => __("USD", 'enginethemes') ,
                                    'class' => 'option-item bg-grey-input '
                                ) ,
                                array(
                                    'id' => 'currency-sign',
                                    'type' => 'text',
                                    'title' => __("Sign", 'enginethemes') ,
                                    'name' => 'icon',
                                    'placeholder' => __("$", 'enginethemes') ,
                                    'class' => 'option-item bg-grey-input '
                                ) ,
                                array(
                                    'id' => 'currency-align',
                                    'type' => 'switch',
                                    'title' => __("Currency position", 'enginethemes') ,
                                    'name' => 'align',
                                    'class' => 'option-item bg-grey-input ',
                                    'label_1' => __("Left", 'enginethemes') ,
                                    'label_2' => __("Right", 'enginethemes') ,
                                ) ,
                            )
                        )
                    )
                ),
                /* Number format */
                array(
                    'args' => array(
                        'title' => __('Number Format', 'enginethemes'),
                        'desc' => '',
                        'id' => '',
                        'class' => '',
                    ),
                    'fields' => array(
                        array(
                            'id' => 'currency',
                            'name' => 'number_format',
                            'type' => 'combine',
                            'title' => __('Style the price number', 'enginethemes'),
                            'desc' => __('Format a number with grouped thousands.', 'enginethemes'),
                            'children' => array(
                                array(
                                    'id' => 'dec_thou',
                                    'type' => 'radio',
                                    'title' => __("Decimal point and thousand separator types", 'enginethemes') ,
                                    'name' => 'dec_thou',
                                    'class' => 'option-item bg-grey-input ',
                                    'data' => array(
                                        'type_1' => '1,234,567.89',
                                        'type_2' => '1.234.567,89',
                                    ),
                                    'default' => 'type_1'
                                ),
                            )
                        ),
                        array(
                            'id' => 'disable-long-price',
                            'type' => 'switch',
                            'title' => __("Custom number formatting", 'enginethemes') ,
                            'desc' => __("Enabling this will allow you to convert price to K, M, B format.", 'enginethemes') ,
                            'name' => 'disable_long_price',
                            'class' => 'option-item bg-grey-input',
                            'default' => 'enable'
                        ),
                        array(
                            'id' => 'disable-decimal-price',
                            'type' => 'switch',
                            'title' => __("Disable decimal in price", 'enginethemes') ,
                            'desc' => __("Enabling this will allow you to remove decimal in price.", 'enginethemes') ,
                            'name' => 'disable_decimal_price',
                            'class' => 'option-item bg-grey-input',
                            'default' => 0
                        )
                    )
                ),
            )
        );
    }
}