<?php
/**
 * Template Name: Payment Methods
 */
global $current_user, $ae_post_factory;
// Get user info
$user = mJobUser::getInstance();
$user_data = $user->convert($current_user->data);

// Bank account data
$bank_first_name = isset($user_data->payment_info['bank']) ? $user_data->payment_info['bank']['first_name'] : '';
$bank_middle_name = isset($user_data->payment_info['bank']) ? $user_data->payment_info['bank']['middle_name'] : '';
$bank_last_name = isset($user_data->payment_info['bank']) ? $user_data->payment_info['bank']['last_name'] : '';
$bank_name = isset($user_data->payment_info['bank']) ? $user_data->payment_info['bank']['name'] : '';
$bank_swift_code = isset($user_data->payment_info['bank']) ? $user_data->payment_info['bank']['swift_code'] : '';
$bank_account_no = isset($user_data->payment_info['bank']) ? $user_data->payment_info['bank']['account_no'] : '';

// Paypal account data
$paypal_email = isset($user_data->payment_info['paypal']) ? $user_data->payment_info['paypal'] : '';

get_header();
?>
<div id="content">
    <div class="container mjob-payment-method-page dashboard withdraw">
        <div class="row title-top-pages">
            <p class="block-title"><?php _e('Payment method', 'enginethemes');?></p>
            <p><a href="<?php echo et_get_page_link('dashboard'); ?>" class="btn-back"><i class="fa fa-angle-left"></i><?php _e('Back to dashboard', 'enginethemes');?></a></p>
        </div>
        <div class="row profile">
            <div class="col-lg-3 col-md-3 col-sm-12 col-sx-12 profile">
                <?php get_sidebar('my-profile');?>
            </div>

            <div class="col-lg-9 col-md-9 col-sm-12 col-sx-12 payment-method box-shadow">
                <div class="tabs-information">
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="<?php echo (!isset($_GET['tab']) || (isset($_GET['tab']) && $_GET['tab'] == "bank-account")) ? 'active' : '' ?>"><a href="#bank-account" aria-controls="bank-account" role="tab" data-toggle="tab"><?php _e('Add a bank account', 'enginethemes');?></a></li>
                        <li role="presentation" class="<?php echo ((isset($_GET['tab']) && $_GET['tab'] == "paypal-account")) ? 'active' : '' ?>"><a href="#paypal-account" aria-controls="paypal-account" role="tab" data-toggle="tab"><?php _e('Paypal', 'enginethemes');?></a></li>
						<?php
/******
 * add action more bank
 * author @tanhoai
 * version 1.4
 ****/
do_action('mje_after_tab_payment_name');
?>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane <?php echo (!isset($_GET['tab']) || (isset($_GET['tab']) && $_GET['tab'] == "bank-account")) ? 'active' : '' ?>" id="bank-account">
                            <div id="bankAccountForm">
                                <form class="et-form">
                                    <div class="row clearfix">
                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 clearfix">
                                            <div class="input-group">
                                                <label for="bank_first_name"><?php _e('First name', 'enginethemes');?></label>
                                                <input type="text" name="bank_first_name" id="bank_first_name" placeholder="" value="<?php echo $bank_first_name; ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 clearfix">
                                            <div class="input-group">
                                                <label for="bank_middle_name"><?php _e('Middle name', 'enginethemes');?></label>
                                                <input type="text" name="bank_middle_name" id="bank_middle_name" placeholder="" value="<?php echo $bank_middle_name; ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 clearfix">
                                            <div class="input-group">
                                                <label for="bank_last_name"><?php _e('Last name', 'enginethemes');?></label>
                                                <input type="text" name="bank_last_name" id="bank_last_name" placeholder="" value="<?php echo $bank_last_name; ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group clearfix">
                                        <div class="input-group">
                                            <label for="bank_name"><?php _e('Bank name', 'enginethemes');?></label>
                                            <input type="text" name="bank_name" id="bank_name" placeholder=""  value="<?php echo $bank_name; ?>">
                                        </div>
                                    </div>

                                    <div class="form-group clearfix">
                                        <div class="input-group">
                                            <label for="bank_swift_code"><?php _e('SWIFT code', 'enginethemes');?></label>
                                            <input type="text" name="bank_swift_code" id="bank_swift_code" placeholder=""  value="<?php echo $bank_swift_code; ?>">
                                        </div>
                                    </div>

                                    <div class="form-group clearfix">
                                        <div class="input-group">
                                            <label for="bank_account_no"><?php _e('Account number', 'enginethemes');?></label>
                                            <input type="text" name="bank_account_no" id="bank_account_no" placeholder=""  value="<?php echo $bank_account_no; ?>">
                                        </div>
                                    </div>

                                    <div class="form-group send-button-method clearfix">
                                        <button class="<?php mje_button_classes(array('btn-send'));?>"><?php _e('Save', 'enginethemes');?></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane <?php echo ((isset($_GET['tab']) && $_GET['tab'] == "paypal-account")) ? 'active' : '' ?>" id="paypal-account">
                            <div id="paypalAccountForm">
                                <form class="et-form">
                                    <div class="form-group clearfix">
                                        <div class="input-group">
                                            <label for="paypal_email"><?php _e('Email', 'enginethemes');?></label>
                                            <input type="text" name="paypal_email" id="paypal_email" placeholder="" value="<?php echo $paypal_email; ?>">
                                        </div>
                                    </div>
                                    <div class="form-group clearfix save-button-paypal">
                                        <button class="<?php mje_button_classes(array('btn-save'));?>"><?php _e('Save', 'enginethemes');?></button>
                                    </div>
                                </form>
                            </div>
                        </div>
						<?php
/******
 * add action more bank
 * author @tanhoai
 * version 1.4
 ****/
do_action('mje_after_tab_payment_content');
?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
get_footer();
?>