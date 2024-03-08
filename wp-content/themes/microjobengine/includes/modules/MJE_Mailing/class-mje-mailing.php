<?php
class MJE_Mailing extends AE_Mailing
{
    public static $instance;

    static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    function __construct() {
    }

    /**
     * Send email to admin when have a new post
     * @param int $postID
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author Tat Thien
     */
    public function notify_new_mjob_post($postID) {
        $subject = sprintf(__('A new mJob submitted on your site', 'enginethemes'));
        $message = ae_get_option('new_mjob_mail_template');
        $message = $this->filter_post_placeholder($message, $postID);
        $post_link = '<a href="' . get_permalink($postID) . '" >'. __('here', 'enginethemes') .'</a>';
        $message = str_ireplace('[here]', $post_link, $message);
        // Mail to admin
        $this->wp_mail(get_option('admin_email'), $subject, $message, array(
            'post' => $postID
        ));
    }

    /**
     * Email notification when mJob has changed status
     * @param string $newStatus
     * @param string $oldStatus
     * @param object $post
     * @return string $newStatus
     * @since 1.0
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author Tat Thien
     */
    public function change_post_status($newStatus, $oldStatus, $post, $rejectMessage) {
        if ( $newStatus != $oldStatus && mje_is_subscriber($post->post_author) ) { // 1.3.72 add subscriber condition
            $authorID = $post->post_author;
            $user = get_userdata($authorID);
            $userEmail = $user->user_email;

            switch ($newStatus) {
                case 'publish':
                    // publish post mail
                    $subject = sprintf(__("Your post '%s' has been approved.", 'enginethemes') , get_the_title($post->ID));
                    $message = ae_get_option('approve_mjob_mail_template');
                    //send mail
                    $this->wp_mail($userEmail, $subject, $message, array(
                        'user_id' => $authorID,
                        'post' => $post->ID
                    ) , '');
                    break;

                case 'archive':
                    // archive post mail
                    $subject = sprintf(__('Your post "%s" has been archived', 'enginethemes') , get_the_title($post->ID));
                    $message = ae_get_option('archived_mjob_mail_template');
                    $message = str_ireplace('[reject_message]', $rejectMessage, $message);
                    $dashboardLink = '<a href="'. et_get_page_link('dashboard') .'">'. __('dashboard') .'</a>';
                    $message = str_ireplace('[dashboard]', $dashboardLink, $message);
                    // send mail
                    $this->wp_mail($userEmail, $subject, $message, array(
                        'user_id' => $authorID,
                        'post' => $post->ID
                    ) , '');
                    break;
                default:

                    //code
                    break;
            }
        }
        return $newStatus;
    }

    /**
     * Send email to admin when reject a post
     * @param object $data
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author Tat Thien
     */
    public function reject_post($data) {

        if( ! mje_is_subscriber($data['post_author']) ){
            return false;
        }
        // get post author
        $user = get_user_by('id', $data['post_author']);
        $user_email = $user->user_email;

        // mail title
        $subject = sprintf(__("Your post '%s' has been rejected.", 'enginethemes') , get_the_title($data['ID']));

        // get reject mail template
        $message = ae_get_option('reject_mail_template');

        // filter reject message
        $message = str_replace('[reject_message]', $data['reject_message'], $message);

        // filter dashboard link
        $dashboardLink = '<a href="'. et_get_page_link('dashboard') .'">'. __('dashboard') .'</a>';
        $message = str_ireplace('[dashboard]', $dashboardLink, $message);

        // send reject mail
        $this->wp_mail($user_email, $subject, $message, array(
            'user_id' => $data['post_author'],
            'post' => $data['ID']
        ) , '');
    }

    /**
     * Send email to author when someone order
     * @param object $order
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author Tat Thien
     */
    public function notify_new_mjob_order($order) {

        $message = ae_get_option('new_order');
        $post = get_post($order->post_parent);

        if( ! mje_is_subscriber( $post->post_author ) ){ // 1.3.72 condition add
            return false;
        }
        $author = get_userdata($post->post_author);

        $subject = sprintf(__('Your post "%s" has a new order', 'enginethemes'), $post->post_title);

        //Filter order placeholder
        $message = $this->filter_order_placeholder($message, $order);
        $this->wp_mail($author->user_email, $subject, $message, array(
            'user_id' => $post->post_author,
            'post' => $post->ID
        ));
        do_action('after_send_noti_new_mjob_order_to_seller', $order);
    }
    /**
     * Send email to admin after a new order in an mjob.
     * @param object $order
     * @return void
     * @since 1.3.9.6
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author danng
     */
    public function notify_admin_new_mjob_order($order, $payment = array()) {

        $message = ae_get_option('new_order_admin');
        $post    = get_post($order->post_parent);
        $subject = sprintf(__('Has a new order in mjob: %s', 'enginethemes'), $post->post_title);
        $admin_email = get_option('admin_email', true);
        //Filter order placeholder
        $message = str_ireplace('[payment_gateway]', $payment['payment_gateway'], $message);
        $message = str_ireplace('[payment_status]',$payment['payment_status'], $message);
        $message = $this->filter_order_placeholder($message, $order);

        $this->wp_mail($admin_email, $subject, $message, array(
            'user_id' => $post->post_author,
            'post' => $post->ID
        ));
    }

    /**
     * Send secure code to user
     * @param object $order
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author Tat Thien
     */
    public function send_secure_code($user_id, $secure_code) {
        $user = get_userdata($user_id);
        $user_email = $user->user_email;
        $subject = sprintf(__('%s has sent you a secure code', 'enginethemes'), get_option('blogname'));
        $message = ae_get_option('secure_code_mail');
        $message = str_ireplace('[secure_code]', $secure_code, $message);
        $this->wp_mail($user_email, $subject, $message, array(
            'user_id' => $user_id
        ));
    }

    /**
     * Send email to buyer when his order is delivered
     * @param object $order_delivery
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author Tat Thien
     */
    public function delivery_order($order_delivery, $delivery_date) {
        $message = ae_get_option('delivery_order');
        $mjob_order = get_post($order_delivery->post_parent);

        if( ! mje_is_subscriber( $mjob_order->post_author ) ){
            return false;
        }

        $mjob = get_post($mjob_order->post_parent);


        $user = get_userdata($mjob_order->post_author);
        $message = $this->filter_order_placeholder($message, $mjob_order);
        $message = str_ireplace('[note]', $order_delivery->post_content, $message);

        // Get the time of auto finishing
        $auto_finish_duration = ae_get_option('mjob_order_finish_duration', 7);
        $deliver_timestamp = strtotime($delivery_date);
        $date_finish_timestamp = strtotime("+" . $auto_finish_duration . "day", $deliver_timestamp);
        $date_finish = date(get_option('date_format') . ' ' . get_option('time_format'), $date_finish_timestamp);
        $message = str_ireplace('[finish_time]', $date_finish, $message);

        $subject = __('Your order has been delivered', 'enginethemes');

        $this->wp_mail($user->user_email, $subject, $message, array(
            'post' => $mjob,
            'user_id' => $mjob_order->post_author
        ));
    }

    /**
     * Send email to seller when his order is accepted
     * @param object $order
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author Tat Thien
     */
    public function accept_order($order, $review) {
        $is_accepted = get_post_meta($order->ID, 'is_accepted', true);
        if(!$is_accepted) {
            $mjob = get_post($order->post_parent);

            if( ! mje_is_subscriber($mjob->post_author) ){
                return false;
            }

            $user = get_userdata($mjob->post_author);
            $buyer = get_userdata($order->post_author);
            $message = ae_get_option('accepted_order');
            $message = $this->filter_order_placeholder($message, $order);

            if ("" == $review) {
                $review = __("Review skipped", 'enginethemes');
            }

            $message = str_ireplace('[note]', $review, $message);
            $subject = sprintf(__("Your order delivery for %s has been accepted by %s", 'enginethemes'), $buyer->display_name, $buyer->display_name);

            $this->wp_mail($user->user_email, $subject, $message, array(
                'post' => $mjob,
                'user_id' => $order->seller_id
            ));

            // Commission notification to admin
            $this->notify_commission($order);

            // update meta
            update_post_meta($order->ID, 'is_accepted', true);
        }
    }

    /**
     * Send commission notification to admin when an order has been finished
     * @param object $order
     * @return void
     * @since 1.1.2
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author Tat Thien
     */
    public function notify_commission($order) {
        $admin_email = get_option('admin_email');
        $message = ae_get_option('finished_order_commission');
        $subject = __('Notification: New Completed Order', 'enginethemes');
        $commission = abs($order->amount - $order->real_amount);
        $message = str_ireplace('[commission_amount]', mje_format_price($commission), $message);
        $message = $this->filter_order_placeholder($message, $order);

        $this->wp_mail($admin_email, $subject, $message);
    }

    /**
     * Send mail when order dispute
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author Tat Thien
     */
    public function dispute_order($order) {
        global $user_ID;
        // Get user data
        $seller_id = $order->seller_id;
        $seller = get_userdata($seller_id);
        $buyer_id = $order->post_author;
        $buyer = get_userdata($order->post_author);

        // Get admin email
        $admin_email = get_option('admin_email');
        // Get message content
        $admin_message = ae_get_option('dispute_order');
        $user_message = ae_get_option('dispute_order_user');
        // Email subject
        $subject = __('One of your order has been reported.', 'enginethemes');

        if( $user_ID == $seller_id ) {
            // Send to admin
            $this->wp_mail($admin_email, __('There was an order has been reported.', 'enginethemes'), $admin_message, array(
                'post' => $order
            ));
            // Send to buyer
            if( mje_is_subscriber($buyer->ID) ){
                $this->wp_mail($buyer->user_email, $subject, $user_message, array(
                    'post' => $order,
                    'user_id' => $buyer_id
                ));
            }
        } elseif($user_ID == $buyer_id) {
            // Send to admin
            $this->wp_mail($admin_email, __('There was an order has been reported.', 'enginethemes'), $admin_message, array(
                'post' => $order
            ));
            // Send to seller
             if( mje_is_subscriber($seller->ID) ){
                $this->wp_mail($seller->user_email, $subject, $user_message, array(
                    'post' => $order,
                    'user_id' => $seller_id
                ));
            }
        } else {
            // Send to buyer
            if( mje_is_subscriber($buyer->ID) ){
                $this->wp_mail($buyer->user_email, $subject, $user_message, array(
                    'post' => $order,
                    'user_id' => $buyer_id
                ));
            }
            // Send to seller
            if( mje_is_subscriber($seller->ID) ){
                $this->wp_mail($seller->user_email, $subject, $user_message, array(
                    'post' => $order,
                    'user_id' => $seller_id
                ));
            }
        }
    }

    /**
     * Dispute decision mail
     * @param $order
     * @param $winner
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author Tat Thien
     */
    public function dispute_decision($order, $winner) {
        // Get user data
        $seller_id = $order->seller_id;
        $seller = get_userdata($seller_id);
        $buyer_id = $order->post_author;
        $buyer = get_userdata($order->post_author);

        $message = ae_get_option('dispute_seller_win');
        $subject = __('Your disputed order has been processed by admin', 'enginethemes');

        // If winner is buyer
        if($winner == $order->post_author) {
            $message = ae_get_option('dispute_buyer_win');
        }
        if( mje_is_subscriber($buyer->ID )){
            $this->wp_mail($buyer->user_email, $subject, $message, array(
                'post' => $order,
                'user_id' => $buyer_id
            ));
        }
        if( mje_is_subscriber($seller->ID )){
            $this->wp_mail($seller->user_email, $subject, $message, array(
                'post' => $order,
                'user_id' => $seller_id
            ));
        }

        // Commission notification to admin
        $this->notify_commission( $order );
    }

    /**
     * Send email to admin when someone request withdraw
     * @param int $withdraw_id
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author Tat Thien
     */
    public function request_withdraw($withdraw_id) {
        $withdraw = get_post($withdraw_id);
        $message = ae_get_option('new_withdraw');

        $admin_email = get_option('admin_email');
        $amount = get_post_meta($withdraw->ID, 'amount', true);
        $user = get_userdata($withdraw->post_author);
        $subject = __('You\'ve got a new withdrawal request.', 'enginethemes');

        $message = str_ireplace('[user_name]', $user->display_name, $message);
        $message = str_ireplace('[total]', mje_format_price($amount), $message);
        $message = str_ireplace('[withdraw_info]', $withdraw->post_content, $message);

        $user_link = '<a href="'. get_author_posts_url($withdraw->post_author) .'">'. get_author_posts_url($withdraw->post_author) .'</a>';
        $message = str_ireplace('[user_link]', $user_link, $message);

        $this->wp_mail($admin_email, $subject, $message, array());
    }

    /**
     * Send email to user when admin approve his withdraw request
     * @param int $user_id
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author Tat Thien
     */
    public function approve_withdraw($user_id) {
        if( ! mje_is_subscriber($user_id) )
            return false;

        $user = get_userdata($user_id);
        $user_email = $user->user_email;
        $message = ae_get_option('approve_withdraw');
        $subject = __('Your withdrawal request has been approved', 'enginethemes');

        $available = AE_WalletAction()->getUserWallet($user_id)->balance;
        $working = AE_WalletAction()->getUserWallet($user_id, "working")->balance;
        $pending = AE_WalletAction()->getUserWallet($user_id, "freezable")->balance;
        $balance = $available + $working + $pending;
        $message = str_ireplace('[balance]', mje_format_price($balance), $message);

        $this->wp_mail($user_email, $subject, $message, array(
           'user_id' => $user_id
        ));
    }

    /**
     * Send email to user when admin decline his withdraw request
     * @param object $withdraw
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author Tat Thien
     */
    public function decline_withdraw($withdraw) {
        if( ! mje_is_subscriber($withdraw->post_author) )
            return false;


        $user = get_userdata($withdraw->post_author);
        $user_id = $user->ID;
        $user_email = $user->user_email;
        $message = ae_get_option('decline_withdraw');
        $subject = __('Your withdrawal request has been declined', 'enginethemes');

        $available = AE_WalletAction()->getUserWallet($user_id)->balance;
        $working = AE_WalletAction()->getUserWallet($user_id, "working")->balance;
        $pending = AE_WalletAction()->getUserWallet($user_id, "freezable")->balance;
        $balance = $available + $working + $pending;
        $message = str_ireplace('[balance]', mje_format_price($balance), $message);
        $message = str_ireplace('[note]', $withdraw->reject_message, $message);

        $this->wp_mail($user_email, $subject, $message, array(
            'user_id' => $user->ID
        ));
    }
    /**
     * Send email to user when admin decline his withdraw request
     * @param object $mjob_order
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author Tat Thien
     */
    public function decline_mjob_order($mjob_order) {
        if( ! mje_is_subscriber($mjob_order->post_author) )
            return false;

        $user = get_userdata($mjob_order->post_author);
        $user_email = $user->user_email;
        $message = ae_get_option('decline_mjob_order');
        $subject = __('Your microjob order request has been declined', 'enginethemes');

        $balance = AE_WalletAction()->getUserWallet($user->ID)->balance;
        $message = str_ireplace('[balance]', mje_format_price($balance), $message);
        $message = str_ireplace('[note]', $mjob_order->reject_message, $message);
        $this->wp_mail($user_email, $subject, $message, array(
            'user_id' => $user->ID
        ));
    }

     /**
     * Send email to user when admin approve his mjob post
     * @param object $mjob
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author Danng
     */
    public function approve_mjob($mjob) {

        $user       = get_userdata($mjob->post_author);
        $user_email = $user->user_email;
        $subject    = __("Your mjob has been approved.", 'enginethemes');
        $message    = ae_get_option('approve_mjob_mail_template');
        //send mail
        $this->wp_mail($user_email, $subject, $message, array(
            'user_id'   => $user->ID,
            'post'      => $mjob->ID
        ) , '');
    }

    /**
     * Admin delete order in backend
     * @param object $order
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author Tat Thien
     */
    public function delete_order($order, $seller_id, $buyer_id) {
        $seller = get_userdata($seller_id);
        $buyer = get_userdata($buyer_id);
        $message = ae_get_option('admin_delete_order_mail_template');
        $subject = __('Your order has been removed', 'enginethemes');

        // Send email to seller
        if(  mje_is_subscriber($seller->ID) ){

            $this->wp_mail($seller->user_email, $subject, $message, array(
                'user_id' => $seller_id,
                'post' => $order->ID
            ));
        }
        // Send email to buyer
        if(  mje_is_subscriber($buyer->ID) ){
            $this->wp_mail($buyer->user_email, $subject, $message, array(
                'user_id' => $buyer_id,
                'post' => $order->ID
            ));
        }
    }

    /**
     * Admin restore order in backend
     * @param object $order
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author Tat Thien
     */
    public function restore_order($order, $seller_id, $buyer_id) {
        $seller = get_userdata($seller_id);
        $buyer = get_userdata($buyer_id);
        $message = ae_get_option('admin_restore_order_mail_template');
        $subject = __('Your order has been restored', 'enginethemes');

        // Send to seller
        if(  mje_is_subscriber($seller->ID) ){
            $this->wp_mail($seller->user_email, $subject, $message, array(
                'user_id' => $seller_id,
                'post' => $order->ID
            ));
        }
        // Send to buyer
        if(  mje_is_subscriber($buyer->ID) ){
            $this->wp_mail($buyer->user_email, $subject, $message, array(
                'user_id' => $buyer_id,
                'post' => $order->ID
            ));
        }
    }

    /**
     * Send email when user have a new message
     * @param $object $message
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author Tat Thien
     */
    public function new_message($message) {
        if(  ! mje_is_subscriber($message->from_user) ){
            return false;
        }

        $from_user = get_userdata($message->from_user);
        $to_user = $message->to_user;

        $email_msg = ae_get_option('new_message_alert');
        $email_msg = str_ireplace('[content]', $message->post_content, $email_msg);
        $subject = sprintf(__('% has sent you a new message on %s', 'enginethemes'), $from_user->display_name, get_option('blogname'));

        $this->wp_mail($to_user, $subject, $email_msg, array(
            'user_id' => $to_user
        ));
    }

    public function notify_payment($order) {
        if(  ! mje_is_subscriber($order->post_author) && $order->payment_type !== 'cash'  ){
            return false;
            // alway send mail if payment_type == cash.
        }

        $subject = __('Thank you for your payment!', 'enginethemes');
        $user = get_userdata($order->post_author);
        $content = ae_get_option('ae_receipt_mail');
        if($order->payment_type == 'cash') {
            $subject = __('Follow these steps to complete your payment.', 'enginethemes');
            $content = ae_get_option('pay_package_by_cash');
        }
        $link = '<a href="' . get_permalink($order->ID) . '">' . get_the_title($order->ID) . '</a>';
        $content = str_ireplace('[link]', $link, $content);
        $content = str_ireplace('[display_name]', $user->display_name, $content);
        $content = str_ireplace('[payment]', ucfirst($order->payment_type), $content);
        $content = str_ireplace('[invoice_id]', $order->et_invoice_no, $content);
        $content = str_ireplace('[date]', date(get_option('date_format') , time()) , $content);
        $content = str_ireplace('[total]', mje_format_price( $order->amount ), $content);
        $content = str_ireplace('[currency]', $order->et_order_currency, $content);

        $detail = sprintf(__('Order a mJob, visit here: %s', 'enginethemes'), $link);

        // Email for checkout a custom offer
        $custom_order_id = get_post_meta($order->ID, 'custom_order_id', true);
        if(!empty($custom_order_id)) {
            $detail = sprintf(__('Checkout a custom offer , visit here: %s', 'enginethemes'), $link);
        }

        $content = str_ireplace('[detail]', $detail, $content);
        $this->wp_mail($user->user_email, $subject, $content, array(
            'user_id' => $order->post_author
        ));
    }


    /**
     * Send email to user and admin when Order Finished Automatically notification
     * @param order array
     * @return void
     * @since 1.0.5
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author Bui Cong Dang
     */
    public function auto_finish_order($post = null) {
        global $ae_post_factory;

        if( ! $post) return false;
        $order_obj = $ae_post_factory->get('mjob_order');
        $order = $order_obj->convert($post);

        $message = ae_get_option('finished_automatically_order');
        $order_guid = '';
        $mjob_guid = '';
        $subject = '';

        //Get info order and mjob
        $buyer_id = $post->post_author;
        $order_id = $post->ID;
        $mjob_id = $post->post_parent;
        $order_guid = $post->guid;
        $message = str_ireplace('[order_link]', $order_guid , $message);

        if( $buyer = get_userdata($buyer_id) ) {
            $email_buyer = $buyer->user_email;
            $display_name_buyer = $buyer->display_name;
            $subject = sprintf(__("Your order for %s has been Finished.", 'enginethemes'), $display_name_buyer);
        }

        if( $mjob = get_post($mjob_id) ) {
            if( mje_is_subscriber($mjob->post_author) ){
                $mjob_guid = $mjob->guid;
                $message = str_ireplace('[link]', $mjob_guid, $message);
                $seller_id = $mjob->post_author;
                if( $seller = get_userdata($seller_id)) {
                    $email_seller = $seller->user_email;
                    $this->wp_mail($email_seller,$subject, $message, array(
                        'user_id' => $seller_id
                    ));
                }
            }
        }
        if( mje_is_subscriber($buyer_id) ){
            $this->wp_mail($email_buyer, $subject, $message, array(
                'user_id' => $buyer_id,
            ));
        }

        // Commission notification to admin
        $this->notify_commission($order);
    }

    /**
     * Inform buyers when they receive an Offer from seller for their Custom order
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author Tat Thien
     */
    public function notify_new_offer($buyer_id, $seller_id, $conversation_id, $mjob_id) {
        if(! mje_is_subscriber($buyer_id) ){
            return false;
        }
        $seller = get_userdata($seller_id);
        $buyer = get_userdata($buyer_id);
        $link = '<a href="' . get_permalink($conversation_id) . '">' . __('Offer detail', 'enginethemes') . '</a>';
        $title = '<a href="' . get_permalink($mjob_id) . '">' . get_the_title($mjob_id) . '</a>';
        $message = ae_get_option('new_offer_mail_template');
        $subject = __('You have received an offer for your custom order.', 'enginethemes');
        $message = str_ireplace('[seller_display_name]', $seller->display_name, $message);
        $message = str_ireplace('[title]', $title, $message);
        $message = str_ireplace('[link]', $link, $message);
        $this->wp_mail($buyer->user_email, $subject, $message, array(
            'user_id' => $buyer_id
        ));
    }

    /**
     * Send email to seller when buyer send custom order
     * @param order array
     * @return void
     * @since 1.0.5
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author Bui Cong Dang
     */
    public function notify_new_custom_order($post = null) {
        if( ! $post) return false;
        if(! mje_is_subscriber( $post->to_user ) ){
            return false;
        }

        $message = ae_get_option('custom_order_send');
        $subject = 'You have received a custom order';
        $seller_id = $post->to_user;
        $buyer_id = $post->from_user;
        if($post->post_parent != '0') {
            $link = get_the_guid($post->post_parent);
        } else
            $link = get_the_guid($post->ID);

//        Get mjob name
        $mjob_id = get_post_meta($post->ID, 'custom_order_mjob', true);
        $mjob_name = get_the_title($mjob_id);

        $seller_data = get_userdata($seller_id);
        $seller_name = $seller_data->display_name;
        $seller_email = $seller_data->user_email;
        $buyer_data = get_userdata($buyer_id);
        $buyer_name = $buyer_data->display_name;


        $message = str_ireplace('[display_name]', $seller_name , $message);
        $message = str_ireplace('[buyer_display_name]', $buyer_name , $message);
        $message = str_ireplace('[title]', $mjob_name , $message);
        $message = str_ireplace('[link]', $link , $message);

        $this->wp_mail($seller_email, $subject, $message);
    }

    /**
     * Seller decline custom order
     * @param order array
     * @return void
     * @since 1.0.5
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author Bui Cong Dang
     */
    public function decline_custom_order($post = null) {
        if( ! $post) return false;

        if(! mje_is_subscriber( $post['to_user'] ) ){
            return false;
        }

        $message = ae_get_option('decline_custom_order');
        $subject = 'Your custom order has been declined';
        if(isset($post['from_user']) && $post['from_user'] != '') {
            $seller_data = get_userdata($post['from_user']);
            $seller_name = $seller_data->display_name;
            $message = str_ireplace('[seller_display_name]', $seller_name, $message);
        }
        if(isset($post['to_user']) && $post['to_user'] != '') {
            $buyer_data = get_userdata($post['to_user']);
            $buyer_name = $buyer_data->display_name;
            $buyer_mail = $buyer_data->user_email;
            $message = str_ireplace('[display_name]', $buyer_name, $message);
        }



        if(isset($post['why_decline']) && $post['why_decline'] != '') {
            $why = __('Here is the message from the seller:', 'enginethemes').'<br />'.$post['why_decline'];
            $message = str_ireplace('[decline_msg]', $why, $message);
        } else {
            $message = str_ireplace('[decline_msg]', '', $message);
        }


//        Get url conversation
        if(isset($post['post_parent']) && $post['post_parent'] != '') {
            $message = str_ireplace('[link]', get_the_guid($post['post_parent']), $message);
        }
//        Get mjob name
        if(isset($post['custom_order_id']) && $post['custom_order_id'] != '') {
            $mjob_id = get_post_meta($post['custom_order_id'],'custom_order_mjob', true);
            $mjob_name = get_the_title($mjob_id);
            $message = str_ireplace('[title]', $mjob_name,  $message);
        }

        $this->wp_mail($buyer_mail, $subject, $message);
    }


    /**
     * Buyer reject custom order
     * @param order array
     * @return void
     * @since 1.0.5
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author Bui Cong Dang
     */
    public function reject_custom_order($post = null) {
        if( ! $post) return false;

        if( ! mje_is_subscriber( $post['to_user'] ) ){
            return false;
        }

        $message = ae_get_option('reject_custom_order');
        $subject = 'Your offer for a custom order has been rejected';
        if(isset($post['from_user']) && $post['from_user'] != '') {
            $buyer_data = get_userdata($post['from_user']);
            $buyer_name = $buyer_data->display_name;
            $message = str_ireplace('[buyer_display_name]', $buyer_name, $message);
        }
        if(isset($post['to_user']) && $post['to_user'] != '') {
            $seller_data = get_userdata($post['to_user']);
            $seller_name = $seller_data->display_name;
            $seller_mail = $seller_data->user_email;
            $message = str_ireplace('[display_name]', $seller_name, $message);
        }

        if(isset($post['why_reject']) && $post['why_reject'] != '') {
            $why = __('Here is the message from the buyer:', 'enginethemes').'<br />'.$post['why_reject'];
            $message = str_ireplace('[reject_msg]', $why, $message);
        } else {
            $message = str_ireplace('[reject_msg]', '', $message);
        }

//        Get url conversation
        if(isset($post['post_parent']) && $post['post_parent'] != '') {
            $message = str_ireplace('[link]', get_the_guid($post['post_parent']), $message);
        }
//        Get mjob name
        if(isset($post['custom_order_id']) && $post['custom_order_id'] != '') {
            $mjob_id = get_post_meta($post['custom_order_id'],'custom_order_mjob', true);
            $mjob_name = get_the_title($mjob_id);
            $message = str_ireplace('[title]', $mjob_name,  $message);
        }

        $this->wp_mail($seller_mail, $subject, $message);
    }

    /**
     * @override get_mail_header of class AE_Mailing
     * return mail header template
     */
    public function get_mail_header() {
        ob_start();
        get_template_part('template/email', 'header');
        $mail_header = ob_get_clean();
        return $mail_header;
    }

    /**
     * @override get_mail_footer of class AE_Mailing
     * return mail footer html template
     */
    function get_mail_footer() {
        ob_start();
        get_template_part('template/email', 'footer');
        $mail_footer = ob_get_clean();
        return $mail_footer;
    }

    /**
     * Filter order data
     * @param string $message
     * @return object $order
     * @since 1.0
     * @package MicrojobEngine
     * @category mJob Mailing
     * @author Tat Thien
     */
    public function filter_order_placeholder($message, $order) {
        $buyer = get_userdata($order->post_author);

        // Buyer name
        $message = str_ireplace('[buyer_name]', $buyer->display_name, $message);

        // Total order
        $total = mje_format_price($order->amount);
        $extra =  mje_get_extra_after_fee_for_buyer($order->fee_commission , $order->mjob_price , $order->amount);
        $message = str_ireplace('[total]', $total, $message);
        $message = str_ireplace('[order_price]', $total, $message);
        $message = str_ireplace('[mjob_price_fee_buyer]', $order->fee_commission . '%', $message);

        $mjob_price = $order->mjob_price;

        // Filter price for custom offer
        $custom_offer_id = get_post_meta($order->ID, 'custom_offer_id', true);
        if(!empty($custom_offer_id)) {
            $offer_price = get_post_meta($custom_offer_id, 'custom_offer_budget', true);
            $extra =  mje_get_extra_after_fee_for_buyer($order->fee_commission , $offer_price , $order->amount);
            $message = str_ireplace('[mjob_price]', mje_format_price($offer_price) . ' ' .__('(Custom offer)', 'enginethemes'), $message);
            $message = str_ireplace('[extra_price]', mje_format_price($extra), $message);
        } else {
            // Filter for normal order
            $message = str_ireplace('[mjob_price]', mje_format_price($mjob_price), $message);
            $message = str_ireplace('[extra_price]', mje_format_price($extra), $message);
        }


        // Commission
        $commission = ae_get_option('order_commission', 10);
        $message = str_ireplace('[commission]', $commission . "%", $message);

        // Order link
        $order_permalink = get_the_permalink($order->ID);
        $order_link = '<a href="'. $order_permalink .'">'. $order_permalink .'</a>';
        $message = str_ireplace('[order_link]', $order_link, $message);

        return apply_filters('mje_order_placeholder', $message, $order);
    }

    /**
     * @param object $author
     * @param object $message_data
     */
    function inbox_mail($author, $message_data) {
        global $current_user;

        if( ! mje_is_subscriber( $author->ID) ){
            return false;
        }
        $headers = 'From: ' . $current_user->display_name . ' <' . $current_user->user_email . '>' . "\r\n";
        $headers .= "Reply-To: $current_user->user_email " . "\r\n";

        /**
         * Filter inbox mail header
         * @param string $headers
         */
        $headers = apply_filters('ae_inbox_mail_headers', $headers);

        //$inbox_message = $message_data->post_content;
        $inbox_message = mje_filter_message_content($message_data->unfiltered_content);

        $subject = sprintf(__('[%s]New Private Message From %s', 'enginethemes') , get_bloginfo('blogname') , $current_user->display_name);
        $message = ae_get_option('inbox_mail_template');
        $inbox_message = stripslashes(str_replace("\n", "<br>", $inbox_message));

        $sender_link = get_the_permalink( $message_data->conversation_id );

        /**
         * replace holder receive
         */
        $message = str_ireplace('[display_name]', $author->display_name, $message);
        $message = str_ireplace('[sender_link]', $sender_link, $message);
        $message = str_ireplace('[sender]', $current_user->display_name, $message);
        $message = str_ireplace('[message]', $inbox_message, $message);
        $message = str_ireplace('[blogname]', get_bloginfo('blogname') , $message);
        $this->wp_mail( $author->user_email, $subject, $message, array(), $headers);
    }

}