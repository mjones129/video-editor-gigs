<?php
global $user_ID, $total;

$subtotal = (float) $product->et_budget;

/* Get extra services */
$extras_ids = array();
if (isset($_GET['extras_ids'])) {
	$extras_ids = $_GET['extras_ids'];
}
if (!empty($extras_ids)) {
	foreach ($extras_ids as $key => $value) {
		$extra = mje_extra_action()->get_extra_of_mjob($value, $product->ID);
		if ($extra) {
			$subtotal += (float) $extra->et_budget;
		} else {
			unset($extras_ids[$key]);
		}
	}
}
$total = $subtotal;

$subtotal_text = mje_format_price($subtotal);



// Generate order args
$order_args = array();

$total = mje_get_price_after_commission_for_buyer($subtotal);
$buyer_fee = mje_get_fee_buy($subtotal);

$default_order_args = array(
	'mjob_name' => $product->post_title,
	'post_title' => sprintf(__('Order for %s ', 'enginethemes'), $product->post_title),
	'post_content' => sprintf(__('Order for %s ', 'enginethemes'), $product->post_title),
	'post_parent' => $product->ID,
	'et_budget' => $product->et_budget,
    'subtotal' => $subtotal,
	'total' => $total,
	'extra_ids' => $extras_ids,
	'post_type' => 'mjob_order',
	'method' => 'create',
    'ef_fixed' => 0, //extension extra fee
    'ef_percent' => 0, //extension extra fee
	'_wpnonce' => de_create_nonce('ae-mjob_post-sync'),
	'custom_fee' => $buyer_fee,
    'discount' => 0,
    '_coupon_code' => '',
);

// Opening message
if (!empty($product->opening_message)) {
	$default_order_args['opening_message'] = $product->opening_message;
}


if( mje_enable_extra_fee() ){
    $extra_fixed    = ae_get_option('extra_fee_fixed') ? (int) ae_get_option('extra_fee_fixed') : 0;
    $extra_percent  = ae_get_option('extra_fee_percentage') ? (int) ae_get_option('extra_fee_percentage') : 0;

    $default_order_args['ef_fixed'] = $extra_fixed;
    $default_order_args['ef_percent'] = $extra_percent;
    $fee1 = $fee2 = 0;
    if($extra_fixed > 0){
        $fee1 = $extra_fixed;
    }
    if($extra_percent > 0){
        $fee2 =  $extra_percent*$subtotal/100;
    }

    $total = $total + $fee1 + $fee2;


    $default_order_args['total'] = $total;

}

// Merge order args with default order args
$order_args = wp_parse_args($order_args, $default_order_args);

if ($user_ID != $product->post_author):
?>
    <div class="title-top-pages">
        <p class="block-title"><?php _e('Checkout details', 'enginethemes')?></p>
    </div>

    <div class="row order-information checkout-mjob.php">
        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 items-chosen">
            <div class="block-items">
                <p class="title-sub"><?php _e('Service name', 'enginethemes');?></p>
                <div class="mjob-list">
                    <?php mje_get_template('template/mjob-item.php', array('current' => $product));?>
                </div>
            </div>
        </div>

        <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 order mjob-order-info">
            <div class="row inner">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="title-sub"><?php _e('Summary description', 'enginethemes');?></div>
                    <?php echo $product->post_content; ?>
                    <div class="row">
                        <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
                            <p class="title-sub">
                                <?php _e('Price', 'enginethemes');?>
                            </p>
                        </div>
                        <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 float-right">
                            <span class="price"><?php echo $product->et_budget_text; ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="add-extra">
                <span class="title-sub"><?php _e('Extra', 'enginethemes');?></span>
                <div class="extra-container">
                    <?php mje_get_template('template/checkout/list-extras.php', array('post' => $product));?>
                </div>
            </div>
            <div class="float-right action-order subtotal-row">
                <p>
                    <span class="total-text"><?php _e('Subtotal', 'enginethemes');?></span>
                    <span class="subtotal-price  price"><?php echo $subtotal_text; ?></span>
                </p>

            </div>
            <?php  do_action('mje_coupon_form', $subtotal, $extras_ids); ?>

            <?php
            $commission = ae_get_option('order_commission_buyer', 0);

            ?>

            <div class="row inner other-fee-row" >
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                     <div class="title-sub"><?php _e('Other Fees', 'enginethemes');?></div>
                     <div class="row">
                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                            <p style = "margin-bottom: 20px;"><?php printf(__('Commission fee (%s%%) you must pay for this order.', 'enginethemes'), $commission);?></p>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 float-right">
                            <span class="price fee-buyer"><?php echo mje_format_price(mje_get_fee_buy($subtotal)) ?></span>
                        </div>
                    </div>
                    <?php do_action('hook_mje_extra_fee',$subtotal);?>
                </div>
            </div>
            <?php

            $total_text = mje_format_price($total);
            ?>

            <div class="float-right action-order checkout-mjob.php total-row">
                <p>
                    <span class="total-text"><?php _e('Total', 'enginethemes');?></span>
                    <span class="total-price mjob-price"><?php echo $total_text; ?></span>
                </p>

                <button class="<?php mje_button_classes(array('btn-checkout', 'mjob-btn-checkout', 'waves-effect', 'waves-light'));?>"><?php _e('Checkout now', 'enginethemes');?></button>
            </div>
        </div>
    </div>

    <?php
//echo '<script type="text/template" id="mjob_single_data" >' . json_encode($product) . '</script>';
echo '<script type="text/template" id="mje-checkout-info">' . json_encode($order_args) . '</script>';
echo '<script type="text/template" id="mje-extra-ids">' . json_encode($extras_ids) . '</script>';
?>
<?php else: ?>
    <div class="error-block">
        <p><?php _e('You cannot make an order for your own mJob', 'enginethemes');?></p>
        <p><?php printf(__('Please browsing other <a href="%s">mJobs</a> to find the correct one.', 'enginethemes'), get_post_type_archive_link('mjob_post'));?></p>
    </div>
<?php endif;?>
