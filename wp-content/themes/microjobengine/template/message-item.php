<?php
global $wp_query, $ae_post_factory, $post, $user_ID;
$post_object = $ae_post_factory->get('ae_message');
$current = $post_object->convert($post);
if($user_ID == $current->to_user) {
    update_post_meta($current->ID, "receiver_unread", "");
}
$message_type = $current->type;
$message_class= isset($current->message_class) ? $current->message_class : 'guest-message';

?>

<?php if($message_type == 'changelog'): ?>
    <li class="clearfix message-item block-changelog">
        <div class="changelog-item">
            <div class="changelog-text">
                <?php
                    echo $current->changelog;
                ?>
            </div>

            <div class="message-time 111">
                <?php echo $current->post_date; ?>
            </div>
        </div>
    </li>
<?php else: ?>
    <li class="clearfix message-item msg-item-<?php echo $current->ID;?> msg-type-<?php echo $message_type;?>">
        <div class="mesage-class <?php echo $message_class; ?>">
            <div class="img-avatar">
                <?php echo $current->author_avatar; ?>
            </div>

            <?php if($message_type == 'offer') { ?>
                <!-- Custom Offer Message -->
                <div class="conversation-text custom-offer">
                    <p class="offer-label"><a href="#" data-id="<?php echo $current->custom_order_id; ?>" class="color-custom-label name-customer-order"><?php _e('Custom Offer', 'enginethemes'); ?></a></p>
                    <?php echo $current->post_content_filtered; ?>
                    <?php echo $current->message_attachment; ?>
                    <div class="budget"><p><?php _e('Budget') ?><span class="mje-price-text"><?php if($current->budget) echo $current->budget ?></span></p></div>
                    <div class="deadline"><p><?php _e('Time of delivery', 'enginethemes') ?><span><?php if($current->deadline) echo $current->deadline; ?></span></p></div>
                </div>
                <!-- End Custom Offer Message -->
                <?php
            } else if($message_type == 'custom_order') {

                $mjob_title = isset($current->mjob_title) ? $current->mjob_title : '';
                $current->mjob_id = get_post_meta($current->ID, "custom_order_mjob", true);
                $budget = mje_shorten_price(get_post_meta($current->ID, "custom_order_budget", true));
                $deadline_int = (int)get_post_meta($current->ID, "custom_order_deadline", true);


                $deadline = $deadline_int > 1 ? sprintf( __( '%s days', 'enginethemes' ), $deadline_int ) : sprintf( __( '%s day', 'enginethemes' ), $deadline_int );

                 if ($current->mjob_id ){

                    $mjob = get_post($current->mjob_id);
                    if( $mjob && ! is_wp_error( $mjob ) ){
                        $mjob_title = $mjob->post_title;
                        $mjob_guid = $mjob->guid;
                    }
                }

                ?>
                <!-- Custom Order Message -->
                <div class="conversation-text custom-order okne custom_order">
                    <p class="offer-label"><?php echo $mjob_title; ?>  </p>
                    <p class="view-custom-order"><a class="link-view-custom-order name-customer-order" data-id="<?php echo $current->ID; ?>"><?php _e('View', 'enginethemes'); ?></a> <?php _e('this custom order', 'enginethemes'); ?></p>
                    <div class="budget"><p><?php _e('Budget') ?>
                            <span class="mje-price-text"><?php if ($budget) echo $budget ?></span></p></div>
                    <div class="deadline"><p><?php _e('Time of delivery', 'enginethemes') ?>
                            <span><?php  echo $deadline; ?> </span></p></div>
                </div>
                <?php
            } else if($message_type == 'decline') {
                ?>
                <div class="conversation-text css_decline">
                    <p class="offer-label name-customer-order" data-id="<?php echo $current->custom_order_id; ?>"><?php _e('CUSTOM ORDER DECLINED', 'enginethemes'); ?></p>
                    <?php echo $current->post_content_filtered; ?>
                </div>
                <?php
            } else if($message_type == 'reject') {
                ?>
                <div class="conversation-text css_reject">
                    <p class="offer-label name-customer-order" data-id="<?php echo $current->custom_order_id; ?>"><?php _e('OFFER REJECTED', 'enginethemes'); ?></p>
                    <?php echo $current->post_content_filtered; ?>
                </div>
                <!-- End Custom Order Message -->
             <?php } else { ?>

                <div class="conversation-text mesage_default">
                    <?php echo $current->post_content_filtered; ?>
                    <?php echo $current->message_attachment; ?>
                </div>
            <?php } ?>
            <div class="message-time ">
                <?php
                $admin_message = false;
                 if(is_super_admin($current->post_author)) {
                        $admin_message = true;
                    }

                if($admin_message == true) {
                    echo '<strong>' . __("by Admin", 'enginethemes') . '</strong> - ' . $current->post_date;
                } else {
                    $p_msg              = get_post($current->ID);
                    $from               = strtotime($p_msg->post_date_gmt); //2020-12-16 08:22:56
                    $gmt_date           = gmdate("M d Y H:i:s"); // v1.3.9.4
                    $to                 = strtotime($gmt_date);
                    $post_date  = sprintf(__('%s ago','enginethemes'),human_time_diff($from, $to ));

                    echo $post_date;
                }
                ?>
            </div>
        </div>
    </li>
<?php endif; ?>
