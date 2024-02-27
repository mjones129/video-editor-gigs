<?php
get_header();
global $wp_query, $ae_post_factory, $post, $user_ID;
$mjob_order_obj = $ae_post_factory->get( 'mjob_order' );
$current = $mjob_order_obj->convert($post);

/* Get offer information */
$offer_id = get_post_meta($current->ID, 'custom_offer_id', true);
if( !empty( $offer_id ) ) {
    $offer = get_post($offer_id);
    $current->offer_content = wpautop($offer->post_content);
    $current->offer_attach_file = mje_get_list_attach_files($offer_id);
    $current->mjob_price  = get_post_meta($offer_id, 'custom_offer_budget', true);
    $current->mjob_time_delivery = get_post_meta($offer_id, 'custom_offer_etd', true);
    $current->mjob_price_text = mje_shorten_price( $current->mjob_price );
}

$can_not_view = false;
// echo '<pre>';
// var_dump('Offer_ID:'.$offer_id);
// var_dump('user_ID: '.$user_ID);
// var_dump('Order author: '.$current->post_author);
// var_dump('MJob author: '.$current->mjob_author);
// var_dump('Mjob pareant of this order:'.$current->post_parent);
// echo '</pre>';
if( $user_ID == $current->post_author ){
    $current->to_user = $current->mjob_author;
} elseif($user_ID == $current->mjob_author){
    $current->to_user = $current->post_author;
} else {
    $current->to_user = $current->mjob_author;
    $can_not_view = true;
}

$current->_wpnonce = de_create_nonce('ae-mjob_post-sync');

$current_time = current_time('timestamp', get_option('timezone_string'));

//Check expired_date add changelog
if(empty($current->order_delivery) && $expired_date = get_post_meta($current->ID, 'et_order_expired_date', true)) {
    if( ! get_post_meta($current->ID, 'mjob_finish_countdown', true) && ((strtotime($expired_date) - $current_time) <= 0)) {
        if($post_id = mje_add_mjob_order_changelog($current->ID, $current->seller_id, 'finish_countdown', '', date('Y/m/d H:i:s', strtotime($expired_date)))) {
            update_post_meta($current->ID, 'mjob_finish_countdown', 'done', true);
        }
    };
}

echo '<script type="text/template" id="order_single_data" >'.json_encode($current).'</script>';
?>
<div id="content" class="mjob-single-order-page mjob_conversation_detail_page">
    <div class="block-page">
        <div class="container">
            <div class="row">
                <?php if($can_not_view && !is_super_admin()): ?>
                   <p class="not-view"><?php _e("You can't view this order!", 'enginethemes'); ?></p>
                <?php else: ?>

                <!-- Mjob order primary content -->
                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                    <?php
                        /* Order header: title, date, status */
                        mje_get_template( 'template/mjob-order/title.php', array( 'mjob_order' => $current ) );
                    ?>

                    <?php if( !empty( $offer_id ) ) : ?>
                        <?php mje_get_template( 'template/mjob-order/offer-content.php', array( 'mjob_order' => $current ) ); ?>
                    <?php endif; ?>

                    <div class="text-description">
                        <?php if( !empty( $offer_id ) ) : ?>
                            <?php
                                echo '<p class="title-description">' . __('Original mJob detail', 'enginethemes') . '</p>';
                                mje_render_toggle_content( $current->mjob_content );
                            ?>
                        <?php else: ?>
                            <?php
                                echo '<p class="title-description">' . __('Order detail', 'enginethemes') . '</p>';
                                echo $current->mjob_content;
                            ?>
                        <?php endif; ?>
                    </div>

                    <div class="area-delivery">
                        <?php
                            /* Countdown */
                            mje_get_template( 'template/mjob-order/countdown.php', array( 'mjob_order' => $current ) );
                        ?>

                        <?php if(
                            // Show delivery button for seller
                            $user_ID == $current->mjob_author
                            && get_post_meta($current->ID, 'et_order_expired_date', true)
                            && in_array( $current->post_status, array( 'publish', 'late' ) )
                        ) : ?>
                            <div class="delivery">
                                <button class="<?php mje_button_classes( array( 'btn-delivery','order-delivery-btn', 'waves-effect', 'waves-light' ) ); ?>" data-id="<?php echo $current->ID; ?>" data-toggle="modal" ><?php _e('Deliver', 'enginethemes'); ?></button>
                            </div>
                            <div class="clearfix"></div>
                        <?php elseif ( $user_ID == $current->post_author && $current->post_status == 'delivery' ) : ?>
                            <div class="accept">
                                <button class="<?php mje_button_classes( array( 'btn-accept', 'waves-effect', 'waves-light') ); ?>"><?php _e( 'Accept', 'enginethemes' ); ?></button>
                            </div>
                        <?php endif; ?>

                        <?php
                            /* Delivery information */
                            mje_get_template( 'template/mjob-order/delivery-info.php', array( 'mjob_order' => $current ) );
                        ?>
                    </div>

                    <?php
                        /* Conversation box */
                        mje_get_template( 'template/mjob-order/conversation.php', array( 'mjob_order' => $current ) ); // conversation.php

                        /* Admin decision */
                        mje_get_template( 'template/mjob-order/admin-decision.php', array( 'mjob_order' => $current ) ); // admin-decision.php

                        /* Related mjob, only showing for buyer */
                        mje_get_template( 'template/mjob-order/related-jobs.php', array( 'mjob_order' => $current) ); // related-jobs.php
                    ?>
                </div>

                <!-- Mjob order sidebar -->
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 order-detail-sidebar profile">
                    <?php mje_get_template( 'template/mjob-order/price.php', array( 'mjob_order' => $current ) ); ?>
                    <?php get_sidebar('single-profile'); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php
get_footer();
