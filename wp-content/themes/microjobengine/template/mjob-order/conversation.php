<?php
global $user_ID, $ae_post_factory;
$ae_message_obj = $ae_post_factory->get('ae_message');

$role = ae_user_role($user_ID);
$order_status = $mjob_order->post_status; // disputing, disputed


// if ($role == "author" ||
// 	($role == "administrator" && ($order_status == "disputing" || $order_status == "disputed")) ||
// 	($role == "administrator" && ($user_ID == $mjob_order->buyer_id || $user_ID == $mjob_order->seller_id) ) ||
//     ( in_array($order_status, array("publish","disputed")) && ($user_ID == $mjob_order->buyer_id || $user_ID == $mjob_order->seller_id) )
// ):
$admin_view_chat = $current_user_is_partner  = 0;
if( current_user_can('manage_options') &&  in_array($order_status,array('disputed','disputing') ) ){
    $admin_view_chat = 1;
}
if( $user_ID == $mjob_order->buyer_id || $user_ID == $mjob_order->seller_id ){
    $current_user_is_partner = 1;
}

if  ( $current_user_is_partner  || $admin_view_chat ) : ?>
    <div class="conversation-form microjobengine\template\mjob-order\conversation.php line15">
        <div class="inner-conversation-form"><?php
                $post_data = array();
                $args = array(
                	'post_type' => 'ae_message',
                	'post_status' => array('publish', 'future'),
                	'post_parent' => $mjob_order->ID,
                	'orderby' => 'date',
                	'order' => 'DESC',
                );

                $messages_query = new WP_Query($args);
                $messages_query->posts = array_reverse($messages_query->posts);
                $messages_query->query = array_merge($messages_query->query, array(
                	'fetch_type' => 'message',
                ));
                echo '<div class="wrapper-list-conversation">';
                // Load more link
                echo '<div class="paginations-wrapper">';
                ae_pagination($messages_query, get_query_var('paged'), 'load', __('Load older messages', 'enginethemes'));
                echo '</div>';
                // End load more link
                echo '<ul class="list-conversation">';
                //get_template_part('template/message', 'item');
                while ($messages_query->have_posts()):
                	$messages_query->the_post();
                	global $post;
                	$convert = $ae_message_obj->convert($post);
                	$post_data[] = $convert;
                	get_template_part('template/message', 'item');
                endwhile;
                wp_reset_postdata();
                echo '</ul></div>';

            /**
             * render post data for js
             */
            echo '<script type="data/json" class="message_postdata" >' . json_encode($post_data) . '</script>';

            if ( $order_status != 'finished' && $order_status != 'disputed' && $current_user_is_partner ): ?>
                <div class="compose-conversation mjob-conversation-form">
                    <form>
                        <input type="hidden" id="from_user" value="<?php echo $user_ID; ?>">
                        <input type="hidden" id="to_user" value="<?php echo $mjob_order->to_user; ?>">
                        <input type="hidden" id="conversation_id" value="<?php echo $mjob_order->ID; ?>">
                        <input type="hidden" id="page_type" value="mjob_order">
                        <input type="hidden" class="input-item post-service_nonce" name="_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync'); ?>" />
                        <div class="form-group compose">
                            <div class="attachment-file gallery_container_single_conversation" id="message_modal_gallery_container">
                                <!-- message input field -->
                                <div class="group-compose">
                                    <div class="input-compose">
                                        <!-- attachments list-->
                                        <ul class="gallery-image carousel-list carousel_single_conversation-image-list" id="image-list">
                                        </ul>

                                        <!--<input type="text" name="post_content" id="post_content" placeholder="<?php /*_e('Type here to reply', 'enginethemes'); */?>">-->
                                        <textarea name="post_content" id="post_content" rows="1" placeholder="<?php _e('Type your message...', 'enginethemes');?>"></textarea>
                                    </div>

                                    <!-- attachment and send button-->
                                    <div class="action-link">
                                        <div class="attachment-image">
                                            <span class="plupload_buttons" id="carousel_single_conversation_container">
                                                <span class="img-gallery" id="carousel_single_conversation_browse_button">
                                                    <a href="#" class="add-img"><i class="fa fa-paperclip"></i></a>
                                                </span>
                                            </span>
                                            <span class="et_ajaxnonce" id="<?php echo wp_create_nonce('ad_carousels_et_uploader'); ?>"></span>
                                        </div>
                                       <!-- <button class="send-message"><?php /*_e('Send', 'enginethemes'); */?></button>-->

                                    </div>
                                    <a class="send-message mje-send-message" href="javascript:void(0)"><i class="fa fa-paper-plane" aria-hidden="true"></i></a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div> <?php
            endif; ?>
        </div>
        <?php if ($order_status == "publish" || $order_status == "delivery" || $order_status == "late"): ?>
         <div class="float-right dispute-link"><a href="#" class="dispute-button"><?php _e('Dispute', 'enginethemes');?></a> <i class="fa fa-gavel" aria-hidden="true"></i></div>
        <?php endif;?>
    </div> <?php
endif;
if (isset($messages_query->query)) {
	echo '<script type="data/json" id="default-message-query">' . json_encode($messages_query->query) . '</script>';
}
?>