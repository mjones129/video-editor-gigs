<?php
global $user_ID;

$timezone_default = date_default_timezone_get();
$current_time = current_time('timestamp');
$current_timestamp = $current_time;
if(get_option('timezone_string')) {
    $current_time = current_time('timestamp', true);
    $current_timestamp = $current_time;
}
$current_time = date('Y-m-d\TH:i', $current_time);

$expired_date = get_post_meta( $mjob_order->ID, 'et_order_expired_date', true );
$expired_date_format = date('h:i A (d/m/Y)', strtotime($expired_date));
$countdown_delivery = get_post_meta( $mjob_order->ID, 'order_countdown_delivery', true );
?>
<div class="countdown-wrapper">
    <input type="hidden" class="expired-date-format" data-expire-time="<?php echo strtotime($expired_date); ?>" value="<?php echo $expired_date_format; ?>">
    <?php if( ! empty( $expired_date ) && $current_timestamp >= strtotime( $expired_date ) && empty( $countdown_delivery ) ) : ?>
        <div class="countdown">
            <div class="notice">
                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                <?php printf( __('This order was expected to be delivered at %s','enginethemes'), $expired_date_format ); ?>
            </div>
        </div>
    <?php else : ?>
        <div class="countdown">
            <input type="hidden" class="order-id" value="<?php echo $mjob_order->ID; ?>">
            <input type="hidden" class="seller-id" value="<?php echo $mjob_order->mjob_author; ?>">
            <input type="hidden" class="current-time" value="<?php echo $current_time; ?>">

            <?php if( $mjob_order->post_status == 'publish' || $mjob_order->post_status == 'late' ) : ?>
                <?php if( $expired_date ) : ?>
                    <input type="hidden" class="expired-date" value="<?php echo $expired_date; ?>">
                <?php elseif( $mjob_order->mjob_author == $user_ID ) : ?>
                    <button class="<?php mje_button_classes( array( 'order-start-work') ); ?>">
                        <?php _e('Start', 'enginethemes'); ?>
                    </button>
                <?php endif ?>
            <?php else : ?>
                <?php
                if( $countdown_delivery ) :
                    echo get_post_meta( $mjob_order->ID, 'order_countdown_delivery', true );
                endif;
                ?>
            <?php endif ?>
        </div>

        <?php if( $expired_date && $user_ID == $mjob_order->mjob_author && $mjob_order->post_status == 'publish' ) : ?>
            <div class="delay">
                <button class="btn-delay"><i class="fa fa-clock-o" aria-hidden="true"></i><?php _e( 'Delay', 'enginethemes' ); ?></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>



