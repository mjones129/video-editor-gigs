<?php
    global $user_ID;
    $revenues       = ae_credit_balance_info($user_ID);
    $total_earned   = mje_format_price($revenues['working']->balance + $revenues['available']->balance +$revenues['freezable']->balance);
?>
<div class="revenues box-shadow">
    <div class="title"><?php _e('Balance', 'enginethemes'); ?></div>
    <div class="line">
        <span class="line-distance"></span>
    </div>
    <ul class="row">
        <li class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
            <p class="cate"><?php _e('Working', 'enginethemes'); ?></p>
            <p class="currency"><?php echo $revenues['working_text']; ?></p>
        </li>

        <li class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
            <p class="cate"><?php _e('Available', 'enginethemes'); ?>
                <span class="sub"><?php _e( 'Earning', 'enginethemes' ); ?> + <a href="javascript:void(0)" class="topup-user-show"><?php _e( 'Top-up', 'enginethemes' ); ?></a></span>
            </p>
            <p class="currency available-text"><?php echo $revenues['available_text']; ?></p>
        </li>

        <li class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
            <p class="cate"><?php _e('Pending', 'enginethemes'); ?></p>
            <p class="currency freezable-text"><?php echo $revenues['freezable_text']; ?></p>
        </li>
    </ul>

	<?php echo apply_filters('revenue_total_balance',$content="", $total_earned);  ?>
	<?php echo apply_filters('revenue_withdraw_spent',$content="", $revenues['withdrew_text'], $revenues['checkout_text']);  ?>
</div>