<div class="dashboard mjob-profile-page">
    <a href="<?php echo et_get_page_link('my-list-order'); ?>" class="btn-back"><i class="fa fa-angle-left"></i><?php _e('Back to my orders listing', 'enginethemes'); ?></a>
</div>

<div class="order-name">
    <h2><?php echo $mjob_order->post_title; ?></h2>
    <div class="functions-items">
        <p class="date">
            <span class="text-date"><?php _e('Modified date: ', 'enginethemes'); ?></span>
            <?php echo $mjob_order->modified_date; ?>
        </p>

        <div class="status-order-detail">
            <span class="order_status <?php echo $mjob_order->status_text_color; ?>"><?php echo $mjob_order->status_text ?></span>
        </div>
    </div>
</div>