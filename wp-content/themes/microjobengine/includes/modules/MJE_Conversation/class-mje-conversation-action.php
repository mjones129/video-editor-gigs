<?php
class MJE_Conversation_Action extends MJE_Post_Action
{
    // Mail class
    public $mail = '';

    public static $instance;

    /**
     * get_instance method
     *
     */
    public static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * the constructor of this class
     *
     */
    public function __construct($post_type = 'ae_message')
    {
        parent::__construct($post_type);
        $this->add_ajax('mjob_conversation_sync', 'conversation_sync');
        $this->add_action('ae_after_message', 'after_send_message', 10, 2);
        $this->add_action('wp_enqueue_scripts', 'add_conversation_scripts');
        $this->add_action('ae_message_validate_before_sync', 'validate_data_before_sync');
        $this->add_filter('ae_convert_ae_message', 'convert_conversation');
        $this->add_filter('ae_message_response', 'filter_message_response', 10, 2);
        $this->add_filter('mjob_check_pending_account', 'check_pending_account', 10, 2);
        $this->mail = new MJE_Mailing();
    }


    /**
     * Conversation Sync
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Conversation
     * @author Tat Thien
     */
    public function conversation_sync() {
        $request = $_REQUEST;
        switch($request['do_action']) {
            case 'mark_as_read':
                $this->mark_unread();
                break;
        }
    }

    public function mark_unread() {
        global $user_ID, $ae_post_factory;
        $post_object = $ae_post_factory->get('ae_message');

        if(!$user_ID) {
            wp_send_json(array(
                'success' => false,
                'msg' => __('Invalid user!', 'enginethemes')
            ));
        }

        // Update unread meta
        $unread_conversation = mje_get_unread_conversation();

        if(!empty($unread_conversation)) {
            foreach($unread_conversation as $unread) {
                update_post_meta($unread->ID, $user_ID . '_conversation_status', 'read');

                // Update read for message
                $unread_messages = mje_get_unread_message($post_object->convert($unread));
                if(!empty($unread_messages)) {
                    foreach($unread_messages as $message) {
                        update_post_meta($message->ID, 'receiver_unread', "");
                    }
                }
            }

            wp_send_json(array(
                'success' => true,
                'msg' => __('Successful', 'enginethemes')
            ));
        } else {
            wp_send_json(array(
                'success' => true,
                'msg' => __('No unread messages found!', 'enginethemes')
            ));
        }
    }

    /**
     * Action after create a message
     * @param object $message;
     * @param array $request
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Conversation
     * @author Tat Thien
     */
    public function after_send_message($message, $request) {
        global $user_ID;
        if(isset($message['data']) && !empty($message['data'])) {
            $message_data = $message['data'];
            // Update latest reply
            if (isset($message_data->is_conversation) && $message_data->is_conversation == '1') {
                update_post_meta($message_data->ID, 'latest_reply', $message_data->ID);
                update_post_meta($message_data->ID, 'latest_reply_timestamp', time());
                update_post_meta($message_data->ID, 'parent_conversation_id', $message_data->ID);
            } else {
                update_post_meta($message_data->post_parent, 'latest_reply', $message_data->ID);
                update_post_meta($message_data->post_parent, 'latest_reply_timestamp', time());
                update_post_meta($message_data->ID, 'parent_conversation_id', $message_data->post_parent);
            }
            // Update user unread
            if ($message_data->from_user == $user_ID) {
                update_post_meta($message_data->ID, 'receiver_unread', true);
                update_post_meta($message_data->ID, 'sender_unread', false);
                // Update unread for conversation
                if ( isset($message_data->post_parent) && $message_data->post_parent ) {
                    update_post_meta($message_data->post_parent, $message_data->to_user . '_conversation_status', 'unread');
                    $message_data->conversation_id = $message_data->post_parent;
                } else {
                    update_post_meta($message_data->ID, $message_data->to_user . '_conversation_status', 'unread');
                    $message_data->conversation_id = $message_data->ID;
                }
            }
            if($message_data->type == 'custom_order') {
                if ($request['budget']) {
                    update_post_meta($message_data->ID, 'custom_order_budget', absint($request['budget']));
                }
                if ($request['deadline']) {
                    update_post_meta($message_data->ID, 'custom_order_deadline', absint($request['deadline']));
                }
                if($request['mjob']) {
                    update_post_meta($message_data->ID, 'custom_order_mjob', $request['mjob']);
                }

//                Send email when buyer send custom order
                $mail = new MJE_Mailing();
                $mail->notify_new_custom_order($message_data);
            }

            /* Update offer information */
            if($message_data->type == 'offer') {
                if(isset($request['custom_order_id']) && !empty($request['custom_order_id'])) {
                    update_post_meta($request['custom_order_id'], 'custom_order_status', 'offer_sent');
                }

                // Update budget
                if(isset($request['budget']) && !empty($request['budget'])) {
                    update_post_meta($message_data->ID, 'custom_offer_budget', absint($request['budget']));
                }

                // Update time of delivery
                if(isset($request['etd']) && !empty($request['etd'])) {
                    update_post_meta($message_data->ID, 'custom_offer_etd', absint($request['etd']));
                }

                update_post_meta($request['custom_order_id'], 'custom_offer_id', $message_data->ID);

                $mjob_id = get_post_meta($request['custom_order_id'], "custom_order_mjob", true);
                $this->mail->notify_new_offer($request['to_user'], $message_data->post_author, $message_data->post_parent, $mjob_id);
            }

//            Add info meta if custom order to be decline
            if($message_data->type == 'decline') {

                if(isset($request['custom_order_id']) and $request['custom_order_id'] != '') {
                    update_post_meta($request['custom_order_id'],'custom_order_status','decline');
                    update_post_meta($message_data->ID,'custom_order',$request['custom_order_id']);
                }

                if(isset($request['why_decline']) and $request['why_decline'] != '') {
                    wp_update_post(array(
                        'ID' => $message_data->ID,
                        'post_content' => $request['why_decline']
                        ));
                }
//                Send mail
                $this->mail->decline_custom_order($request);
            }

//            Add info meta if reject custom order
            if($message_data->type == 'reject') {
                if(isset($request['custom_order_id']) and $request['custom_order_id'] != '') {
                    update_post_meta($request['custom_order_id'],'custom_order_status','reject');
                    update_post_meta($message_data->ID,'custom_order',$request['custom_order_id']);
                }

                if(isset($request['why_reject']) and $request['why_reject'] != '') {
                    wp_update_post(array(
                        'ID' => $message_data->ID,
                        'post_content' => $request['why_reject']
                    ));
                }
                $this->mail->reject_custom_order($request);
            }

            // Send email to user
            if($message_data->type == 'message' || $message_data->type == 'conversation') {


                if( mje_is_subscriber( $message_data->to_user ) ){ // @since 1.3.72
                    $to_user = get_userdata($message_data->to_user);
                    $this->mail->inbox_mail($to_user, $message_data);
                }
            }
        }
    }

    /**
     * Conversation scripts
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Conversation
     * @author Tat Thien
     */
    public function add_conversation_scripts() {
        global $current_user;
        wp_enqueue_script('conversation', get_template_directory_uri() . '/assets/js/conversation.js', array(
            'jquery',
            'underscore',
            'backbone',
            'appengine',
            'front',
            'mjob-auth',
            'ae-message-js'), ET_VERSION, true);

        wp_localize_script('conversation', 'conversation_global', array(
            'file_max_size' => '',
            'file_types' => '',
            'conversation_title' => __('Conversation by ' . $current_user->display_name, 'enginethemes'),
            'message_title' => __('Message from ' . $current_user->display_name, 'enginethemes')
        ));
    }

    /**
     * Validate conversation before sync
     * @param array $request
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author Tat Thien
     */
    public function validate_data_before_sync($request) {
        // Check conversation exist between two users
        $flag = true;
        $msg = "";
        if($request['type'] == 'conversation') {
            if(isset($request['from_user']) && isset($request['to_user'])) {
                if(mje_is_has_conversation($request['from_user'], $request['to_user'])) {
                    $flag = false;
                    $msg = __('You have created a conversation with this user. Please go to conversation detail to add reply.', 'enginethemes');
                }
            } else {
                $flag = false;
                $msg = __('Don\'t try to hack.', 'enginethemes');
            }
        }

        // If conversation of order, validate order status
        if(isset($request['page']) && $request['page'] == 'mjob_order') {
            if(isset($request['post_parent']) && !empty($request['post_parent'])) {
                $order = get_post($request['post_parent']);
                if($order->post_status == 'finished' || $order->post_status == 'disputed') {
                    $flag = false;
                    $msg = __('You can not send message when order was finished or resolved', 'enginethemes');
                }
            }
        }
        //Validate sending decline
        if(isset($request['type']) && $request['type'] == 'decline') {
            //Check custom order exist
            if(isset($request['custom_order_id']) and get_post($request['custom_order_id'],'ARRAY_A') == null) {
                wp_send_json(array(
                    'success' => false,
                    'msg' => __('This custom order does not exist.', 'enginethemes')
                ));
            }
            $status = get_post_meta($request['custom_order_id'], 'custom_order_status', true);
            if($status == 'decline') {
                wp_send_json(array(
                    'success' => false,
                    'msg' => __('This custom order has been declined.', 'enginethemes')
                ));
            }
        }

        //Validate sending reject
        if(isset($request['type']) && $request['type'] == 'reject') {
            //Check custom order exist
            if(isset($request['custom_order_id']) and get_post($request['custom_order_id'],'ARRAY_A') == null) {
                wp_send_json(array(
                    'success' => false,
                    'msg' => __('This custom order does not exist.', 'enginethemes')
                ));
            }

            $status = get_post_meta($request['custom_order_id'], 'custom_order_status', true);
            if($status == 'reject') {
                wp_send_json(array(
                    'success' => false,
                    'msg' => __('This custom order has been rejected.', 'enginethemes')
                ));
            }
        }


        /* Validate sending offer */
        if(isset($request['type']) && $request['type'] == 'offer') {
            $request['post_title'] = sprintf(__('Offer for custom order: %s', 'enginethemes'), $request['custom_order_id']);
            $status = get_post_meta($request['custom_order_id'], 'custom_order_status', true);
            if($status == 'offer_sent') {
                wp_send_json(array(
                    'success' => false,
                    'msg' => __('This custom order has been sent offer.', 'enginethemes')
                ));
            }

            $request['budget'] = ltrim($request['budget'], '0');
            $request['etd'] = ltrim($request['etd'], '0');

            $rules = array(
                'budget' => 'Required|Integer|Min:1',
                'etd' => 'Required|Integer|Min:1',
            );
            $error_msg = array(
                'budget.required' => __('The budget cannot be empty.', 'enginethemes'),
                'budget.integer' => __('The budget must be an integer.', 'enginethemes'),
                'budget.min' => __('The budget must be greater than 0.', 'enginethemes'),
                'etd.required' => __('The time of delivery cannot be empty.', 'enginethemes'),
                'etd.integer' => __('The time of delivery must be an integer.', 'enginethemes'),
                'etd.min' => __('The time of delivery must be greater than 0.', 'enginethemes'),
            );
            $validator = new AE_Validator($request, $rules, array(), $error_msg);
            if($validator->fails()) {
                wp_send_json(array(
                    'success' => false,
                    'msg' => $validator->getMessages()
                ));
            }
        }

        if(!$flag) {
            wp_send_json(array(
                'success' => false,
                'msg' => $msg
            ));
        }
    }

    /**
     * Filter the converted message
     * @param object $result
     * @return object $result
     * @since 1.0
     * @package MicrojobEngine
     * @category Conversation
     * @author Tat Thien
     */
    public function convert_conversation($result) {

        global $user_ID;
        $from_user = $result->from_user;
        $to_user = $result->to_user;

        if($result->is_conversation == "1") {

            if($user_ID == $from_user) {
                $user_id = $to_user;
            } else if($user_ID == $to_user) {
                $user_id = $from_user;
            } else if( current_user_can('manage_options')){
                $user_id = $from_user;
            } else{
                return $result;
            }
            /**
             * Latest reply
             */
            if(isset($result->latest_reply) and get_post($result->latest_reply)) {
                $message = get_post($result->latest_reply);
                //If message content null set message content is message title
                if($message->post_content == '')
                    $message->post_content = $message->post_title;

                if($message->post_author == $user_ID) {
                    $result->latest_reply_text = __('You: ', 'enginethemes') . mje_filter_message_content($message->post_content);
                } else {
                    $result->latest_reply_text = mje_filter_message_content($message->post_content);
                }
                $result->latest_reply_time = et_the_time(get_the_time('U', $message->ID));
            }

            $user_data = get_userdata($user_id);
            $result->author_name = '';
            if(!empty($user_data)) {
                $result->author_name = $user_data->display_name;
            }
            $result->author_url = get_author_posts_url($user_id);
            $result->author_avatar = '<a href="' . $result->author_url . '" target="_blank" title="' . $result->author_name . '">' . mje_avatar($user_id, 80) . '</a>';
            $result->author_avatar_img = mje_avatar($user_id, 80);

            // Message parent
            $conversation_status = get_post_meta($result->ID, $user_ID . '_conversation_status', true);
            if($conversation_status == "unread") {
                // If unread
                $result->unread_class = "unread";
            } else {
                $result->unread_class = "";
            }
        } else {

            // Message child
            $user_id = "";
            if($user_ID != $result->post_author) {
                $user_id = $result->post_author;
            } else {
                $user_id = $user_ID;
            }
            $user_name = get_the_author_meta('display_name', $user_id);
            $result->author_avatar = '<a href="' . get_author_posts_url($user_id) . '" target="_blank" title="' . $user_name . '">' . mje_avatar($user_id, 80) . '</a>';
        }

        $result->post_content_filtered = mje_filter_message_content($result->post_content);
        //$result->post_date =  et_the_time(get_the_time('U', $result->ID)); //
        // replase line 431
       // $gmt_timestamp  = get_post_time( 'U', true, $result->ID );
        $p_msg              = get_post($result->ID);
        $from               = strtotime($p_msg->post_date_gmt); //2020-12-16 08:22:56
        $gmt_date           = gmdate("M d Y H:i:s"); // v1.3.9.4
        $to                 = strtotime($gmt_date);
        $result->post_date  = sprintf(__('%s ago','enginethemes'),human_time_diff($from, $to ));
        // end replaceline 431

        // Get message attachment
        $output = '<ul>';
        if( !empty($result->et_files) ):
            foreach($result->et_files as $key=> $value){
                $output .= '<li class="image-item" id="'. $value->ID .'">';
                $output .= '<a class="ellipsis"  target="_blank" title="'. $value->post_title .'" href="'. $value->guid . '"><i class="fa fa-paperclip"></i>' .$value->post_title. '</a>';
                $output .= '</li>';
            }
        endif;
        $output .= '</ul>';
        $result->message_attachment = $output;
        $result->message_class = mje_get_message_class($result->post_author);
        if( empty($result->message_class) ){
            $result->message_class = '...';
        }
        $result->admin_message = false;
        if(is_super_admin($result->post_author)) {
            $result->admin_message = true;
        }

        /**
         * Convert change log
         */
        if('changelog' == $result->type) {
            $result->changelog = "";
            $author_url = get_author_posts_url($result->post_author);
            $author_name = get_the_author_meta('display_name', $result->post_author);
            $author_link = sprintf('<a href="%s" target="_blank">%s</a>', $author_url, $author_name);
            $changelog_time = get_the_time(get_option('date_format') . ' ' . get_option('time_format'));
            switch($result->action_type) {
                case 'dispute':
                    $result->changelog = sprintf(__('%s sent a dispute for this order - %s.', 'enginethemes'), $author_link, $changelog_time);
                    break;
                case 'late':
                    $result->changelog = sprintf(__('%s marked this order as Late - %s.', 'enginethemes'), $author_link, $changelog_time);
                    break;
                case 'admin_decide':
                    $winner_id = get_post_meta($result->ID, 'winner', true);
                    if(!empty($winner_id)) {
                        $author_url = get_author_posts_url($winner_id);
                        $author_name = get_the_author_meta('display_name', $winner_id);
                        $author_link = sprintf('<a href="%s" target="_blank">%s</a>', $author_url, $author_name);
                    }
                    $result->changelog = sprintf(__("The dispute was decided in %s's favor - %s. This Order was marked as Resolved. Its fund was returned to %s's Available fund.", 'enginethemes'), $author_link, $changelog_time, $author_link);
                    break;
                case 'resolve':
                    $result->changelog = sprintf(__("This order was marked as Resolved as well - %s.", 'enginethemes'), $changelog_time);
                    break;
                case 'start_work':
                    $result->changelog = sprintf(__("%s started working on this order - %s.", 'enginethemes'), $author_link, $changelog_time);
                    break;
                case 'delivery':
                    $auto_finish_duration = ae_get_option('mjob_order_finish_duration', 7);
                    $deliver_time = get_the_time("m/d/Y g:i a");
                    $deliver_timestamp = strtotime($deliver_time);
                    $date_finish_timestamp = strtotime("+" . $auto_finish_duration . "day", $deliver_timestamp);
                    $date_finish = date(get_option('date_format') . ' ' . get_option('time_format'), $date_finish_timestamp);

                    $result->changelog = sprintf(__("%s delivered the work - %s <br><br> The Order will be marked as Finished at %s if no dispute is sent.", 'enginethemes'), $author_link, $changelog_time, $date_finish);
                    break;
                case 'accept':
                    $result->changelog = sprintf(__("%s accepted the delivery of this order - %s.", 'enginethemes'), $author_link, $changelog_time);
                    break;
                case 'auto_finish':
                    $result->changelog = sprintf(__("The Order was marked as Finished at %s due to no further actions from both sides.", 'enginethemes'), $changelog_time);
                    break;
                case 'finish_countdown':
                    $result->changelog = sprintf(__("This order was expected to be delivered at %s.", 'enginethemes'),  $changelog_time);
                    break;
            }
        }

        /* Convert data for CUSTOM ORDER */
        if($result->type == "custom_order") {
            $result->budget = mje_shorten_price(get_post_meta($result->ID, "custom_order_budget", true));
            $result->deadline = (int)get_post_meta($result->ID, "custom_order_deadline", true);
            $result->deadline = $result->deadline > 1 ? sprintf( __( '%s days', 'enginethemes' ), $result->deadline ) : sprintf( __( '%s day', 'enginethemes' ), $result->deadline );

            $result->mjob_id = get_post_meta($result->ID, "custom_order_mjob", true);
            if ($result->mjob_id ){

                $mjob = get_post($result->mjob_id);
                if( $mjob && ! is_wp_error( $mjob ) ){
                    $result->mjob_title = $mjob->post_title;
                    $result->mjob_guid = $mjob->guid;
                }
            }
            $result->status = get_post_meta($result->ID, 'custom_order_status', true);
            if($result->status == "offer_sent") {
                $result->label_class = "get-offer-color";
                if($user_ID == $result->from_user) {
                    $result->label_status = __('Got offer', 'enginethemes');
                } else {
                    $result->label_status = __('Offer sent', 'enginethemes');
                }
            } else {
                $result->label_class = "";
                if($user_ID == $result->from_user)
                    $result->label_status = __('Order sent', 'enginethemes');
                else
                    $result->label_status = '';
            }

            $result->short_content = wp_trim_words($result->post_content,20);
        }

        /* Convert data for OFFER */
        if($result->type == 'offer') {
            $result->budget = mje_shorten_price(get_post_meta($result->ID, "custom_offer_budget", true));
            $result->deadline = (int)get_post_meta($result->ID, "custom_offer_etd", true);
            $result->deadline = $result->deadline > 1 ? sprintf( __( '%s days', 'enginethemes' ), $result->deadline ) : sprintf( __( '%s day', 'enginethemes' ), $result->deadline );

            $result->custom_order_id = 0;
            $arr = array(
                'post_type' => 'ae_message',
                'meta_query' => array(
                    array(
                        'key'     => 'custom_offer_id',
                        'value'   => $result->ID
                    )
                )
            );
            $get_post = get_posts($arr);
            if(isset($get_post['0']))
                $result->custom_order_id = $get_post[0]->ID;
        }

        /*Convert data for decline*/
        if($result->type == 'decline') {
            $result->custom_order_id = get_post_meta($result->ID,'custom_order', true);
        }

        /*Convert data for reject*/
        if($result->type == 'reject') {
            $result->custom_order_id = get_post_meta($result->ID,'custom_order', true);
        }

        return $result;
    }

   /**
    * filter response
    *
    * @param object $response
    * @param object $request
    * @return object $response
    * @since 1.0
    * @package MicrojobEngine
    * @category void
    * @author JACK BUI
    */
    public function filter_message_response($response, $request){
        if( isset($request['type']) && $request['type'] == 'dispute'){
            $response['msg'] = __('Your report has been sent.', 'enginethemes');
        }

        if(isset($request['type']) && $request['type'] == 'offer') {
            $response['msg'] = __('Your offer has been sent.', 'enginethemes');
            $response['data']->custom_order_id = $request['custom_order_id'];
            $response['data']->label_offer_sent = '<div class="label-status order-color get-offer-color"><span>' . __('Offer sent', 'enginethemes') . '</span></div>';
        }

        return $response;
    }

    /**
     * Filter validate pending account when post message
     * @param object $result
     * @return object $request
     * @since 1.0.5
     * @package MicrojobEngine
     * @category void
     * @author Tat Thien
     */
    public function check_pending_account($result, $request) {
        $page = isset( $request['page'] ) ? $request['page'] :'';
        $type = isset( $request['type'] ) ? $request['type'] :'';
        if( $page == 'mjob_order' || $type == 'custom_order') {
            $result = array(
                'success' => true,
                'msg' => __("Successful.", 'enginethemes')
            );
        }

        return $result;
    }

    /**
     * Add first message in order if opening message in mJob not null
     * @param array $result
     * @param array $request
     * @return int $message_id
     * @since 1.1.1
     * @package MicrojobEngine
     * @category void
     * @author Dang Bui
     */
    public function add_opening_message($result, $request) {
        if( ! $result || ! $request)
           return;

        if( empty($request['opening_message']) )
            return;

        $data_post_meta = array(
            'from_user' => $result->mjob_author,
            'to_user' => $result->post_author,
            'type' => 'message',
            'parent_conversation_id' => $result->ID,
            'receiver_unread' => '1',
            'sender_unread' => ''
        );
        $data_post = array(
            'post_author' => $result->mjob_author,
            'post_content' => $request['opening_message'],
            'post_type' => 'ae_message',
            'post_status' => 'publish',
            'post_parent' => $result->ID,
            'post_title' => sprintf(__('First Message from mJob %s of %s', 'enginethemes'), $request['mjob_name'], $result->mjob_author_name)
        );
        $message_id = wp_insert_post($data_post);
        foreach ($data_post_meta as $k => $v) {
            add_post_meta($message_id, $k, $v);
        }

        return $message_id;
    }
}

$new_instance = MJE_Conversation_Action::get_instance();