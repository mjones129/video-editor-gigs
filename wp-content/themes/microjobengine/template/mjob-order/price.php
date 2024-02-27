<div class="box-shadow">
    <div class="order-detail-price">
        <div class="order-price">

            <p class="title-cate"><?php _e('Price', 'enginethemes'); ?></p>
            <p class="price-items"><?php echo $mjob_order->mjob_price_text; ?></p>
            <p class="time-order"><i class="fa fa-clock-o" aria-hidden="true"></i><?php _e('Time of delivery', 'enginethemes'); ?></p>
            <?php
            if( ! $mjob_order_delivery = get_post_meta($mjob_order->ID, 'mjob_order_delivery', true))
                $mjob_order_delivery = $mjob_order->mjob_time_delivery;
            ?>
            <p class="days-order"><?php echo sprintf(__('%s day(s)', 'enginethemes'), $mjob_order_delivery); ?></p>
        </div>
        <div class="total-order">
            <p class="title-cate"><?php _e('Other Fees', 'enginethemes'); ?></p>
            <p class="price-items"><?php echo $mjob_order->fee_commission .'%' ?></p>
            <p class="no-extra"><?php _e('Buyer Commission Fee', 'enginethemes'); ?></p>
            <?php

            if( mje_enable_extra_fee() ){

                $first_label = ae_get_option('extra_fee_percent_label');
                $second_label = ae_get_option('extra_fee_fixed_label');


                $extra_fee_fixed = get_post_meta($mjob_order->ID,'extra_fee_fixed', true);
                $extra_fee_percent = get_post_meta($mjob_order->ID,'extra_fee_percent', true);
                $extra_fee_percent_value = get_post_meta($mjob_order->ID,'extra_fee_percent_value', true);


             ?>
             <div class="full" style="position: relative;">

                <p class="no-extra"><?php echo $second_label; ?> <span class="order-extra-fee"><?php echo mje_shorten_price($extra_fee_fixed);?></span></p>
                <p class="no-extra"><?php echo $first_label; ?> <span class="order-extra-fee"><?php echo mje_shorten_price($extra_fee_percent_value);?></span></p>
            </div>
            <?php } ?>
        </div>
        <div class="order-extra">
            <p class="title-cate"><?php _e('Extra', 'enginethemes'); ?></p>
            <?php
            $mjob_price = $mjob_order->mjob_price;
            if(!empty($mjob_order->extra_info)):
                ?>
                <ul>
                    <?php
                    foreach( $mjob_order->extra_info as $key=>$extra) {
                        $extra = (object)$extra;
                        ?>
                        <li>
                            <p class="extra-title"><?php echo $extra->post_title; ?></p>
                            <p class="price-items"><?php echo mje_shorten_price($extra->et_budget);  ?></p>
                        </li>
                    <?php } ?>
                </ul>
            <?php else: ?>
                <p class="no-extra">
                    <?php _e('There are no extra services', 'enginethemes'); ?>
                </p>
            <?php endif; ?>

        </div>
        <div class="total-order">
            <p class="title-cate"><?php _e('Total', 'enginethemes'); ?></p>
            <p class="price-items"><?php echo mje_shorten_price($mjob_order->amount); ?></p>
        </div>
    </div>
</div>