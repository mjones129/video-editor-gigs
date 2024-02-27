<?php
    global $user_ID, $post, $ae_post_factory;
    $order_object = $ae_post_factory->get('ae_message');
    $custom = $order_object->convert($post);
    $label_status = isset($custom->label_status) ? $custom->label_status : '';
    $label_class = isset($custom->label_class) ? $custom->label_class : '';
    $short_content = wp_trim_words($custom->post_content,20);
    $mjob_title = isset($custom->mjob_title) ? $custom->mjob_title : '';
    $budget = mje_shorten_price(get_post_meta($custom->ID, "custom_order_budget", true));
    $deadline = (int)get_post_meta($custom->ID, "custom_order_deadline", true);
    $deadline = $deadline > 1 ? sprintf( __( '%s days', 'enginethemes' ), $deadline ) : sprintf( __( '%s day', 'enginethemes' ), $deadline );

    $custom->mjob_id = get_post_meta($custom->ID, "custom_order_mjob", true);
    if ($custom->mjob_id && empty($mjob_title) ){

        $mjob = get_post($custom->mjob_id);
        if( $mjob && ! is_wp_error( $mjob ) ){
            $mjob_title = $mjob->post_title;
            $mjob_guid = $mjob->guid;
        }
    }

?>
<li>
    <div id="custom-order-<?php echo $post->ID; ?>">
        <h2>
            <a data-id="<?php echo $custom->ID ?>"  title="<?php echo $mjob_title; ?>" class="name-customer-order">
                <?php if($mjob_title) echo $mjob_title; ?>
            </a>
        </h2>

        <?php if($label_status != "") : ?>
            <div class="label-status order-color <?php echo $label_class; ?>"><span><?php echo $label_status; ?></span></div>
        <?php endif; ?>


        <p class="post-content"><?php if($short_content) echo $short_content; ?> </p>
        <div class="outer-etd">
            <div class="deadline">
                <p>
                    <i class="fa fa-calendar" aria-hidden="true"></i>
                    <span>
                        <?php echo $deadline ?>
                    </span>
                </p>
            </div>

            <div class="budget"><p><span class="mje-price-text"><?php if($budget) echo $budget; ?></span></p></div>
        </div>
        <?php if($user_ID == $custom->to_user && $custom->status != 'offer_sent') : ?>
            <div class="custom-order-btn">
                <button class="btn-decline" data-custom-order="<?php echo $custom->ID; ?>"><?php _e('Decline', 'enginethemes'); ?></button>
                <button class="btn-send-offer" data-custom-order="<?php echo $custom->ID; ?>"><?php _e('Send offer', 'enginethemes') ?></button>
            </div>
        <?php endif; ?>
    </div>
</li>