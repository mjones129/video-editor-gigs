<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 8/2/2016
 * Time: 13:28
 */

if( ! function_exists('mjob_setting_microjob')) {
    function mjob_setting_microjob() {
        return array(
            'args' => array(
                'title' => __("Microjob", 'enginethemes') ,
                'id' => 'microjob-settings',
                'icon' => 'l',
                'class' => ''
            ) ,
            'groups' => array(
                /* mJob Default Image*/
				array(
                    'args' => array(
                        'title' => __(" mJob Default Thumbnail", 'enginethemes') ,
                        'id' => '',
                        'class' => '',
                        'name' => '',
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'opt-ace-editor-js',
                            'type' => 'image',
                            'title' => __("Photo setting", 'enginethemes') ,
                            'desc' => __("Your logo should be in PNG, GIF or JPG format, minimum size is 768 x 435px and less than 1500Kb.", 'enginethemes'),
                            'name' => 'default_mjob',
                            'default' =>  get_template_directory_uri().'/assets/img/mjob_thumbnail.png',
                            'class' => '',
                            'size' => array(
                                '768',
                                '435'
                            ),
                        ),
                    )
                ),
				/* mJob Price Setting*/
                array(
                    'args' => array(
                        'title' => __("Microjob Price", 'enginethemes') ,
                        'desc' => __("mJob Price allows sellers to set either a custom price per mJob or fixed price.", 'enginethemes') ,
                        'id' => 'mjob-price-group',
                        'class' => '',
                        'name' => '',
                    ) ,
                    'fields' => array(
                        /* mJob price */
                        array(
                            'id' => 'custom_price_mode',
                            'type' => 'switch',
                            'title' => __("Price mode", 'enginethemes') ,
                            'desc' => __('If you choose custom option, sellers can set a price for their mJobs.', 'enginethemes'),
                            'name' => 'custom_price_mode',
                            'class' => 'option-item bg-grey-input ',
                            'label_1' => __('Custom', 'enginethemes'),
                            'label_2' => __('Fixed', 'enginethemes')
                        ),
                        array(
                            'id' => 'fixed_price',
                            'type' => 'number',
                            'title' => sprintf(__("Fixed price (%s)", 'enginethemes'), ae_currency_sign(false)) ,
                            'desc' => __("Enter the default price for all mJobs.", 'enginethemes'),
                            'name' => 'mjob_price',
                            'placeholder' => ae_currency_sign(false),
                            'class' => 'option-item bg-grey-input positive_int',
                            'default' => '5'
                        ),
                        array(
                            'id' => 'custom_price',
                            'name' => '',
                            'type' => 'combine',
                            'title' => sprintf(__("Custom price (%s)", 'enginethemes'), ae_currency_sign(false)) ,
                            'desc' => __("Set the minimum & maximum prices applied for mJobs.", 'enginethemes'),
                            'children' => array(
                                array(
                                    'id' => 'mjob_min_price',
                                    'type' => 'number',
                                    'title' => __("Minimum price", 'enginethemes') ,
                                    'name' => 'mjob_min_price',
                                    'placeholder' => ae_currency_sign(false),
                                    'class' => 'option-item bg-grey-input positive_int min_price_validate',
                                    'default' => '5'
                                ),
                                array(
                                    'id' => 'mjob_max_price',
                                    'type' => 'number',
                                    'title' => __("Maximum price", 'enginethemes') ,
                                    'name' => 'mjob_max_price',
                                    'placeholder' => ae_currency_sign(false),
                                    'class' => 'option-item bg-grey-input positive_int max_price_validate',
                                    'default' => '15'
                                )
                            )
                        ),
                        array(
                            'id' => 'order-commission',
                            'type' => 'number',
                            'title' => __("Commission fee (%)", 'enginethemes') ,
                            'desc' => __("Set up commission fee as percentage of mJob price you want to charge to seller.", 'enginethemes') ,
                            'name' => 'order_commission',
                            'placeholder' => __("10", 'enginethemes') ,
                            'class' => 'option-item bg-grey-input positive_int_zero',
                            'default'=> 10
                        ),
                        array(
                            'id' => 'order-commission-buyer',
                            'type' => 'number',
                            'title' => __("Buyer commission fee(%)", 'enginethemes') ,
                            'desc' => __("Set up the fee charged to the buyer as percentage of mJob price.", 'enginethemes') ,
                            'name' => 'order_commission_buyer',
                            'placeholder' => __("0", 'enginethemes') ,
                            'class' => 'option-item bg-grey-input positive_int_zero',
                            'default'=> 0
                        )
                    )
                ),
                /* mJob settings */
                array(
                    'args' => array(
                        'title' => __("mJob Verification", 'enginethemes') ,
                        'desc' => '',
                        'id' => '',
                        'class' => '',
                    ),
                    'fields' => array(
                        array(
                            'id' => 'use_pending',
                            'type' => 'switch',
                            'title' => __("Pending review", 'enginethemes'),
                            'desc' => __("Enabling this will make every new mJob posted pending until you review and approve it manually.", 'enginethemes') ,
                            'name' => 'use_pending',
                            'class' => 'option-item bg-grey-input '
                        ),
                        array(
                            'id' => 'edit_mjob',
                            'type' => 'switch',
                            'title' => __("Editing mJob", 'enginethemes'),
                            'desc' => __("Enabling this will allow author to edit his mJob, even when his mJob is active.", 'enginethemes') ,
                            'name' => 'edit_mjob',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ),
                /* mJob order settings*/
                array(
                    'args' => array(
                        'title' => __("Microjob order settings", 'enginethemes') ,
                        'desc' => __("Set up microjob's order settings.", 'enginethemes'),
                        'id' => '',
                        'class' => '',
                    ),
                    'fields' => array(
                        array(
                            'id' => 'mjob_order_finish_duration',
                            'type' => 'number',
                            'title' => __("Time Limit for Order Completion", 'enginethemes'),
                            'desc' => __("Set up the time (days) that buyer has to finish an order once seller delivers his mJob", 'enginethemes'),
                            'name' => 'mjob_order_finish_duration',
                            'placeholder' => "",
                            'class' => 'option-item bg-grey-input positive_int',
                            'default' => 7
                        ),
                    )
                ),
                /* File settings */
                array(
                    'args' => array(
                        'title' => __("File settings", 'enginethemes') ,
                        'id' => '',
                        'class' => '',
                        'desc' => ''
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'file_types',
                            'type' => 'text',
                            'title' => __("File types", 'enginethemes') ,
                            'desc' => __('Default: allows only pdf, doc, docx, zip, jpg or png files.', 'enginethemes'),
                            'name' => 'file_types',
                            'placeholder' => "",
                            'default' => 'pdf,doc,docx,zip,jpg,png',
                            'class' => 'option-item bg-grey-input',
                            'text' => ''
                        ),
                        array(
                            'id' => 'max_file_size',
                            'type' => 'number',
                            'title' => __("Maximum file size", 'enginethemes') ,
                            'desc' => sprintf(__('Give a maximum file size in mb. Default is %s', 'enginethemes'), wp_max_upload_size() / (1024*1024).'mb'),
                            'name' => 'max_file_size',
                            'placeholder' => 'mb',
                            'class' => 'option-item bg-grey-input positive_int',
                        )
                    )
                ),
                /* Banned word list */
                array(
                    'args' => array(
                        'title' => __('Banned word list', 'enginethemes'),
                        'desc' => '',
                        'id' => '',
                        'class' => '',
                    ),
                    'fields' => array(
                        array(
                            'id'    => 'filter_bad_words',
                            'type'  => 'textarea',
                            'title' => __("List bad words", 'enginethemes'),
                            'desc'  => __("Each word is separated by comma (,). E.g. foo, boo, too.", 'enginethemes'),
                            'name'  => 'filter_bad_words',
                            'class' => 'option-item bg-grey-input',
                        ),
                        array(
                            'id' => 'bad_word_replace',
                            'type' => 'text',
                            'title' => __("Replace them", 'enginethemes') ,
                            'desc' => __('Give a replacement word for all bad words.', 'enginethemes'),
                            'name' => 'bad_word_replace',
                            'placeholder' => __("Enter a word", 'enginethemes') ,
                            'class' => 'option-item bg-grey-input',
                        )
                    )
                ),
                /* Microjob Related Mail Template */
                array(
                    'args' => array(
                        'title' => __('Microjob related mail template', 'enginethemes'),
                        'desc' => __('Email templates used for microjob-related event. You can use placeholders to include some specific content', 'enginethemes'),
                        'id' => '',
                        'class' => '',
                    ),
                    'fields' => array(
                        array(
                            'id' => 'new_mjob_mail_template',
                            'type' => 'editor',
                            'title' => __("New post notification", 'enginethemes') ,
                            'desc' => __("Send to admin when a user posts a new mJob", 'enginethemes'),
                            'class' => '',
                            'name' => 'new_mjob_mail_template',
                            'reset' => 1,
                            'toggle' => true
                        ),
                        array(
                            'id' => 'approve_mjob_mail_template',
                            'type' => 'editor',
                            'title' => __("mJob approved notification", 'enginethemes') ,
                            'desc' => __("Send to a user to notify that one of his posted mJobs has been approved.", 'enginethemes'),
                            'class' => '',
                            'name' => 'approve_mjob_mail_template',
                            'reset' => 1,
                            'toggle' => true
                        ),
                        array(
                            'id' => 'reject_mail_template',
                            'type' => 'editor',
                            'title' => __('mJob rejected notification', 'enginethemes'),
                            'desc' => __("Send to a user to notify that one of his posted mJobs has been rejected.", 'enginethemes'),
                            'class' => '',
                            'name' => 'reject_mail_template',
                            'reset' => 1,
                            'toggle' => true
                        ),
                        array(
                            'id' => 'archived_mjob_mail_template',
                            'type' => 'editor',
                            'title' => __('mJob archived notification', 'enginethemes'),
                            'desc' => __("Send to a user to notify that one of his posted mJobs has been archive.", 'enginethemes'),
                            'class' => '',
                            'name' => 'archived_mjob_mail_template',
                            'reset' => 1,
                            'toggle' => true
                        )
                    )
                ),
                /* Order Related Mail Template */
                array(
                    'args' => array(
                        'title' => __('Order Related Mail Template', 'enginethemes'),
                        'desc' => __('Email templates used for order-related event.', 'enginethemes'),
                        'id' => '',
                        'class' => '',
                    ),
                    'fields' => array(
                        array(
                            'id' => 'new_order',
                            'type' => 'editor',
                            'title' => __('New order notification', 'enginethemes'),
                            'desc' => __("Send to seller when a user orders his mJob.", 'enginethemes'),
                            'class' => '',
                            'name' => 'new_order',
                            'reset' => 1,
                            'toggle' => true
                        ),
                        array(
                            'id' => 'new_order_admin',
                            'type' => 'editor',
                            'title' => __('New order notification for admin', 'enginethemes'),
                            'desc' => __("Send to admin when has a new order in website.", 'enginethemes'),
                            'class' => '',
                            'name' => 'new_order_admin',
                            'reset' => 1,
                            'toggle' => true
                        ),
                        array(
                            'id' => 'delivery_order',
                            'type' => 'editor',
                            'title' => __('Delivered order', 'enginethemes'),
                            'desc' => __("Send to buyer when seller delivers the order.", 'enginethemes'),
                            'class' => '',
                            'name' => 'delivery_order',
                            'reset' => 1,
                            'toggle' => true
                        ),
                        array(
                            'id' => 'accepted_order',
                            'type' => 'editor',
                            'title' => __('Accepted order', 'enginethemes'),
                            'desc' => __("Send to seller when buyer accepts his mJob delivery.", 'enginethemes'),
                            'class' => '',
                            'name' => 'accepted_order',
                            'reset' => 1,
                            'toggle' => true
                        ),
                        array(
                            'id' => 'dispute_order',
                            'type' => 'editor',
                            'title' => __('Order dispute (template for admin)', 'enginethemes'),
                            'desc' => __("Send to admin when seller or buyer disputes over a mJob order.", 'enginethemes'),
                            'class' => '',
                            'name' => 'dispute_order',
                            'reset' => 1,
                            'toggle' => true
                        ),
                        array(
                            'id' => 'dispute_order_user',
                            'type' => 'editor',
                            'title' => __('Order dispute (template for user)', 'enginethemes'),
                            'desc' => __("Send to user when his partner disputes over a mJob order.", 'enginethemes'),
                            'class' => '',
                            'name' => 'dispute_order_user',
                            'reset' => 1,
                            'toggle' => true
                        ),
                        array(
                            'id' => 'decline_mjob_order',
                            'type' => 'editor',
                            'title' => __('Micro job order request declined', 'enginethemes'),
                            'desc' => __("Send to user when his micro job order request has been declined.", 'enginethemes'),
                            'class' => '',
                            'name' => 'decline_mjob_order',
                            'reset' => 1,
                            'toggle' => true
                        ),
                        array(
                            'id' => 'finished_automatically_order',
                            'type' => 'editor',
                            'title' => __('Order Automatically Finished by System', 'enginethemes'),
                            'desc' => __('Send to both buyer & seller when the status of an order is automatically switched to "Finished" after the waiting duration set by admin.', 'enginethemes'),
                            'class' => '',
                            'name' => 'finished_automatically_order',
                            'reset' => 1,
                            'toggle' => true
                        ),
                        array(
                            'id' => 'finished_order_commission',
                            'type' => 'editor',
                            'title' => __('Order Completed - Commission Notification', 'enginethemes'),
                            'desc' => __('Send to admins when the status of an order is switched to either "Finished" or "Resolved".', 'enginethemes'),
                            'class' => '',
                            'name' => 'finished_order_commission',
                            'reset' => 1,
                            'toggle' => true
                        ),
                        array(
                            'id' => 'admin_delete_order_mail_template',
                            'type' => 'editor',
                            'title' => __('Inform users regarding deleted order by Admin', 'enginethemes'),
                            'desc' => __("Send to users when admin deletes an order in backend.", 'enginethemes'),
                            'class' => '',
                            'name' => 'admin_delete_order_mail_template',
                            'reset' => 1,
                            'toggle' => true
                        ),
                        array(
                            'id' => 'admin_restore_order_mail_template',
                            'type' => 'editor',
                            'title' => __('Inform users regarding restored order by Admin', 'enginethemes'),
                            'desc' => __("Send to users when admin restores an order in backend.", 'enginethemes'),
                            'class' => '',
                            'name' => 'admin_restore_order_mail_template',
                            'reset' => 1,
                            'toggle' => true
                        )
                    )
                ),
                /* Group comment */
                array(
                    'args' => array(
                        'title' => __('Custom Order Related Mail Template', 'enginethemes'),
                        'desc' => __('Email templates used for custom order-related event', 'enginethemes'),
                        'id' => '',
                        'class' => '',
                    ),
                    'fields' => array(
                        array(
                            'id' => 'custom_order_send',
                            'type' => 'editor',
                            'title' => __('Inform sellers about a new Custom order', 'enginethemes'),
                            'desc' => __("Send to sellers when they have a new custom order from a buyer", 'enginethemes'),
                            'class' => '',
                            'name' => 'custom_order_send',
                            'reset' => 1,
                            'toggle' => true
                        ),
                        array(
                            'id' => 'new_offer_mail_template',
                            'type' => 'editor',
                            'title' => __('Inform buyers when sellers send the offer back', 'enginethemes'),
                            'desc' => __("Send to buyers when they receive an offer back for their custom order.", 'enginethemes'),
                            'class' => '',
                            'name' => 'new_offer_mail_template',
                            'reset' => 1,
                            'toggle' => true
                        ),
                        array(
                            'id' => 'decline_custom_order',
                            'type' => 'editor',
                            'title' => __('Inform buyers when seller decline their Custom order', 'enginethemes'),
                            'desc' => __("Send to buyers when their custom order is declined by the seller", 'enginethemes'),
                            'class' => '',
                            'name' => 'decline_custom_order',
                            'reset' => 1,
                            'toggle' => true
                        ),
                        array(
                            'id' => 'reject_custom_order',
                            'type' => 'editor',
                            'title' => __('Inform sellers when the buyer reject their Offer', 'enginethemes'),
                            'desc' => __("Send to sellers when the buyer rejects their offer.", 'enginethemes'),
                            'class' => '',
                            'name' => 'reject_custom_order',
                            'reset' => 1,
                            'toggle' => true
                        )
                    )
                ),
                /* Dispute Decision */
                array(
                    'args' => array(
                        'title' => __('Dispute Decision', 'enginethemes'),
                        'desc' => __("Send to user when admin makes a decision on a disputed order", 'enginethemes') ,
                        'id' => '',
                        'class' => '',
                    ),
                    'fields' => array(
                        array(
                            'id' => 'dispute_seller_win',
                            'type' => 'editor',
                            'title' => __('Seller wins', 'enginethemes'),
                            'desc' => __('Send to both sellers and buyers in case sellers win a dispute', 'enginethemes'),
                            'class' => '',
                            'name' => 'dispute_seller_win',
                            'reset' => 1,
                            'toggle' => true
                        ),
                        array(
                            'id' => 'dispute_buyer_win',
                            'type' => 'editor',
                            'title' => __('Buyer wins', 'enginethemes'),
                            'desc' => __('Send to both sellers and buyers in case buyers win a dispute', 'enginethemes'),
                            'class' => '',
                            'name' => 'dispute_buyer_win',
                            'reset' => 1,
                            'toggle' => true
                        )
                    )
                ),
            )
        );
    }
}