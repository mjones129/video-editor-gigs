<?php
global $post, $ae_post_factory, $user_ID;
$post_object    = $ae_post_factory->get('ae_message');
$current        = $post_object->convert($post);

$from_user      = $current->from_user;
$to_user        = $current->to_user;
$receiver_id    = $to_user;
$is_admin       = $only_view = 0;
$sender_or_receiver = 0;
// Get receiver_id


if ($user_ID == $from_user) {
	$receiver_id = $to_user;
    $sender_or_receiver = 1;
} else if ($user_ID == $to_user) {
	$receiver_id = $from_user;
    $sender_or_receiver = 1;
} else if( current_user_can('manage_options') ) {
    $only_view = 1;
    if($current->post_parent > 0){
        $conversation_link = get_permalink($current->post_parent);
        wp_redirect($conversation_link);
    }
} else {
    wp_redirect(et_get_page_link('dashboard'));
}
if($sender_or_receiver && $post->post_parent > 0){
    // fix view message detail from back-end.
    $conversation_link = get_permalink($current->post_parent);
    wp_redirect($conversation_link);
}
// Set converstaion status to read
if( ! $only_view )
 update_post_meta($post->ID, $user_ID . '_conversation_status', 'read');

$receiver_name  = get_the_author_meta('display_name', $receiver_id);
$receiver_url   = get_author_posts_url($receiver_id);
get_header();
?>
<div id="content" class="mjob_conversation_detail_page">
    <div class="block-page">
        <div class="container">
            <div class="row title-top-pages dashboard withdraw no-margin">
                <div class="box-shadow-title">
                    <p class="block-title"><?php printf(__('Conversation <span class="user-conversation">with <a href="%s" class="" target="_blank">%s</a></span>', 'enginethemes'), $receiver_url, $receiver_name);?></p>
                    <a href="<?php echo et_get_page_link('my-list-messages'); ?>" class="btn-back"><i class="fa fa-angle-left"></i><?php _e('Back to message list', 'enginethemes');?></a>
                </div>
            </div>
            <div class="row no-margin">
                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 conversation-form">
                     <div class="inner-conversation-form single-ae_message.php line-36">  <?php
$post_data = array();
$args = mje_get_default_conversation_query_args($receiver_id);
array_push($args['meta_query'], array(
	'key' => 'parent_conversation_id',
	'value' => $current->ID,
));

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

echo '<ul class="list-conversation">';
//get_template_part('template/message', 'item');
if( $messages_query->have_posts() ){
    while ($messages_query->have_posts()):
    	$messages_query->the_post();
    	$convert = $post_object->convert($post);
    	$post_data[] = $convert;
    	get_template_part('template/message', 'item');
    endwhile;
wp_reset_postdata();
} else {
    echo 'No mesage.'; // debug only
}
echo '</ul></div>';

/**
 * render post data for js
 */
//                       conversation-form

echo '<script type="data/json" class="message_postdata" >' . json_encode($post_data) . '</script>';

if( ! $only_view ){ ?>

        <div class="compose-conversation mjob-conversation-form">
            <form>
                <input type="hidden" id="from_user" value="<?php echo $user_ID; ?>">
                <input type="hidden" id="to_user" value="<?php echo $receiver_id; ?>">
                <input type="hidden" id="conversation_id" value="<?php echo $current->ID; ?>">
                <input type="hidden" class="input-item _wpnonce" name="_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync'); ?>" />
                <div class="form-group compose">
                    <div class="attachment-file gallery_container_single_conversation" id="message_modal_gallery_container">
                        <!-- attachments list-->

                        <!-- message input field -->
                        <div class="group-compose">
                            <div class="input-compose">
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
                            </div>
                            <a class="send-message mje-send-message" href="javascript:void(0)"><i class="fa fa-paper-plane" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    <?php } ?>
    </div>
</div>

<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 current">
    <div class="current-order custom-order-box box-shadow">
        <p class="title-column"><?php _e('Custom orders', 'enginethemes');?></p>
        <div class="list-order list-custom-order-wrapper">
            <ul class="list-custom-order">
                                <?php
$args = array(
	'post_type' => 'ae_message',
	'post_status' => 'publish',
	'meta_query' => array(
		'relation' => 'AND',
		array(
			'key' => 'parent_conversation_id',
			'value' => $post->ID,
		),
		array(
			'key' => 'type',
			'value' => 'custom_order',
		),
		array(
			'relation' => 'OR',
			array(
				'key' => 'custom_order_status',
				'value' => array('decline', 'checkout', 'reject'),
				'compare' => 'NOT IN',
			),
			array(
				'key' => 'custom_order_status',
				'compare' => 'NOT EXISTS',
			),
		),
	),
	'order' => 'DESC',
);
$custom_order_query = new WP_Query($args);
$post_data_custom = array();
$order_object = $ae_post_factory->get('ae_message');
if ($custom_order_query->have_posts()):
	while ($custom_order_query->have_posts()): $custom_order_query->the_post();
		$custom = $order_object->convert($post);
		$post_data_custom[] = $custom;
		get_template_part('template/custom-order', 'item');
	endwhile;

else:
	echo __('<p class="no-custom">No custom orders</p>', 'enginethemes');
endif;
?>
                            </ul>

                            <?php echo '<script type="data/json" class="custom_order_postdata" >' . json_encode($post_data_custom) . '</script>'; ?>
                            <div class="more">
                                <?php
echo '<div class="paginations-wrapper">';
ae_pagination($custom_order_query, get_query_var('paged'), 'load', __('Load more', 'enginethemes'));
echo '</div>';
?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
get_template_part('template/custom-order', 'detail');
?>
        </div>
        <div class="overlay-custom-detail"></div>
    </div>
</div>
<?php
//Load template js
get_template_part('template-js/custom-order', 'item');
echo '<script type="data/json" id="default-message-query">' . json_encode($messages_query->query) . '</script>';
get_footer();
?>
