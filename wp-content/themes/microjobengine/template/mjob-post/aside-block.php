<?php global $user_ID; ?>
<div class="box-shadow box-aside-stat">
    <?php if( is_super_admin() || $user_ID == $mjob_post->post_author ): ?>
        <div class="text-center mjob-status <?php echo $mjob_post->post_status; ?>-text">
            <?php echo $mjob_post->status_text; ?>
        </div>
    <?php endif; ?>

    <div class="mjob-single-stat">
        <div class="stat-block clearfix">
            <div class="vote pull-left">
                <div class="rate-it" data-score="<?php echo round($mjob_post->rating_score, 1); ?>"></div>
                <span class="total-review"><?php printf('(%s)',  $mjob_post->mjob_total_reviews); ?></span>
            </div>
            <span class="price pull-right"><?php echo $mjob_post->et_budget_text; ?></span>
        </div>

        <div class="stat-block">
            <ul>
                <li class="clearfix">
                    <span class="pull-left"><i class="fa fa-star"></i><?php _e('Overall rate', 'enginethemes'); ?></span>
                    <div class="total-number pull-right"><?php echo round($mjob_post->rating_score, 1); ?></div>
                </li>
                <li class="clearfix">
                    <span class="pull-left"><i class="fa fa-commenting"></i><?php _e('Reviews', 'enginethemes'); ?></span>
                    <div class="total-number pull-right"><?php echo mje_get_total_reviews( $mjob_post->ID ); ?></div>
                </li>
                <li class="clearfix">
                    <span class="pull-left"><i class="fa fa-shopping-cart"></i><?php _e('Sales', 'enginethemes'); ?></span>
                    <div class="total-number pull-right"><?php echo mje_get_mjob_order_count($mjob_post->ID); ?></div>
                </li>
                <li class="clearfix">
                    <span class="pull-left"><i class="fa fa-calendar"></i><?php _e('Time of delivery', 'enginethemes'); ?></span>
                    <div class="total-number time-delivery-label pull-right"><?php printf(__('%s day(s)',
                    'enginethemes'),$mjob_post->time_delivery);?></div>
                </li>
            </ul>
        </div>
    </div>
    <div class="action">
        <?php if( $user_ID != $mjob_post->post_author ) {
            mje_render_order_button( $mjob_post );
			mje_render_buy_fee();
        } else{
            /**
             * Add button claim this job
             *
             * @since 1.3.1
             * @author Tan Hoai
             */
            do_action('mje_seller_mjob_button');
        }
        ?>
    </div>
    <div class="add-extra mjob-add-extra">
        <span class="extra"><?php _e('EXTRA', 'enginethemes') ;?></span>
        <div class="extra-container">
            <?php get_template_part('template/list', 'extras'); ?>
        </div>
    </div>
    <div class="custom-order-link">
        <?php
        if( $user_ID != $mjob_post->post_author ) {
            $conversation_parent = 0;
            $conversation_guid = '';
            if($conversation = mje_get_conversation( $user_ID, $mjob_post->post_author )) {
                $conversation_parent = $conversation[0]->ID;
                $conversation_guid = $conversation[0]->guid;
            }

            $send_custom_order_id = $class_link_order = 'bt-send-custom';
            if( in_array( $mjob_post->post_status, array( 'pause', 'pending', 'draft', 'reject', 'archive' ) ) ) {
                $send_custom_order_id = 'bt-send-custom-disable';
                $class_link_order= '';
            }
            ?>
            <div>
                <a id="<?php echo $send_custom_order_id; ?>" class="<?php echo $class_link_order;?>" data-mjob-name="<?php echo $mjob_post->post_title; ?>" data-mjob="<?php echo $mjob_post->ID ?>" data-conversation-guid="<?php echo $conversation_guid; ?>" data-conversation-parent="<?php echo $conversation_parent; ?>" data-to-user="<?php echo $mjob_post->post_author; ?>" data-from-user="<?php echo $user_ID ?>" style="cursor: pointer"><?php _e('Send custom order','enginethemes'); ?><i class="fa fa-paper-plane"></i></a>
            </div>
            <?php
        }
        ?>
    </div>
</div>
<?php if($mjob_post->opening_message && $mjob_post->opening_message != '' && ($user_ID == $mjob_post->post_author || is_super_admin())) : ?>
    <div class="box-shadow opening-message">
        <div class="aside-title">
            <?php _e('Opening Message', 'enginethemes') ?> <i class="fa fa-question-circle popover-opening-message" style="cursor: pointer" aria-hidden="true"></i>
        </div>
        <div class="content">
            <?php
            $opening_message = wpautop($mjob_post->opening_message);
            $num_opening_message = str_word_count($opening_message);
            if($num_opening_message > 40) {
                ?>
                <div class="content-opening-message hide-content gradient">
                    <?php
                    echo $opening_message;
                    ?>
                </div>
                <a class="show-opening-message"><?php _e('Show more', 'enginethemes') ?></a>
                <?php
            } else {
                echo '<div class="content-opening-message">';
                echo $opening_message;
                echo '</div>';
                echo '<a class="show-opening-message"></a>';

            }
            ?>
        </div>
    </div>
<?php endif; ?>
<?php get_sidebar('single-profile'); ?>