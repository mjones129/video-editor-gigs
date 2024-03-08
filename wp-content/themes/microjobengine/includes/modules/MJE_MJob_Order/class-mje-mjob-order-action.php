<?php
class MJE_MJob_Order_Action extends MJE_Post_Action{
    public $mail;

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
    public  function __construct( $post_type = 'mjob_order' ){
        global $ae_post_factory;

        parent::__construct( $post_type );
        $this->add_ajax( 'ae-fetch-mjob_order', 'fetch_post' );
        $this->add_ajax( 'mjob_order_action', 'handle_actions' );
        $this->add_filter( 'ae_convert_mjob_order', 'convert' );
        $this->add_action( 'transition_post_status',  'transform_status', 10, 3 );
        $this->add_action( 'mje_after_process_payment', 'after_process_payment', 10, 2 );
        $this->add_filter( 'mjob_check_pending_account', 'check_user_pending', 10, 2 );
        $this->add_filter( 'after_update_post','notChangeStatus' );
        $this->ruler = array(
        );

        $this->mail = MJE_Mailing::get_instance();
    }

    /**
     * Functions handle all order actions
     * @param void
     * @return void
     * @since 1.0.5
     * @package MicrojobEngine
     * @category mJob Order
     * @author Tat Thien
     */
    public function handle_actions() {
        $request = $_REQUEST;
        $response = $this->validate_post( $request );

        if( !$response['success'] ){
            wp_send_json( $response );
            exit;
        }

        if(isset($request['do_action']) && !empty( $request ) ) {
            $action = $request['do_action'];
            switch( $action ) {
                case 'dispute':
                    $this->dispute_order( $request );
                    break;
                case 'admin_decide':
                    $this->resolve_dispute( $request );
                    break;
                case 'start-work':
                    $this->start_order();
                    break;
                case 'delay':
                    $this->delay_order( $request );
                    break;
                case 'finish':
                    $this->finish_order( $request );
                    break;
            }
        }
    }

    /**
     * Don't edit post_date if change status pending to public
     * @param array $args
     * @return array $args
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author Dang Bui
     */
    public function notChangeStatus( $args ) {
        if( $args['action'] == 'mjob-admin-order-sync' )
            $args['edit_date'] = true;
        return $args;
    }

    /**
     * sync Post function
     * @param void
     * @return array $response
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function sync( $request ){
        global $user_ID, $ae_post_factory;
        $order_obj = $ae_post_factory->get( 'mjob_order' );

        $response = $this->validate_post( $request );
        if( !$response['success'] ){
            wp_send_json( $response );
            exit;
        }

        if( !isset( $request['post_author']) && !$user_ID ){
            $request['post_author'] = mje_get_temp_user_id();
        }

        if( $user_ID ){
            $request['post_author'] = $user_ID;
        }

        $mjob = mje_mjob_action()->get_mjob( $request['post_parent'] );
        if ( !$mjob ) {
            $response = array(
                'success' => false,
                'msg' => __( "No mJob found for this order!", 'enginethemes' )
            );
            wp_send_json( $response );
        }

        $request['post_content'] = $mjob->post_content;

        /**
         * Sync order
         */
        $response = $this->sync_post( $request );
        if ( $response['success'] ) {
            $result = $response['data'];

            // If checkout for custom order, the total will be offer price
            $subtotal = 0;
            if( isset( $request['custom_offer_id'] ) ) {
                $subtotal = get_post_meta( $request['custom_offer_id'], 'custom_offer_budget', true );
            } else {
                $subtotal = $mjob->et_budget;
            }
            if ( ($request['method'] == 'create' || empty( $result->extra_info ) ) && !empty( $result->extra_ids ) ) {
                $arr_extras = array();
                foreach ( $result->extra_ids as $key => $value ) {
                    $extra = mje_extra_action()->get_extra_of_mjob( $value, $result->post_parent );
                    array_push( $arr_extras, $extra );
                    if ( $extra ) {
                        $subtotal += $extra->et_budget;
                    }
                }
                if( $request['method'] == 'create' ) {
                    update_post_meta( $result->ID, 'extra_info', $arr_extras );
                }
            }

            //$total = apply_filters('mje_apply_coupon_to_checkout', $subtotal, $request);


            $currency = mje_get_currency();
            if( $request['method'] == 'create' ) {

                update_post_meta( $result->ID, 'mjob_price', $mjob->et_budget );
                //update_post_meta( $result->ID, 'amount', mje_get_price_after_commission_for_buyer($subtotal) );

                update_post_meta( $result->ID, 'amount', mje_get_price_mjob_order_for_buyer($subtotal, $request) ); // has_discount  discount code here
                if( mje_enable_extra_fee() ){
                    $extra_fee_fixed = ae_get_option('extra_fee_fixed') ? (int) ae_get_option('extra_fee_fixed'): 0;
                    $extra_percent= ae_get_option('extra_fee_percentage') ? (int) ae_get_option('extra_fee_percentage'): 0;
                    if($extra_fee_fixed > 0)
                        update_post_meta( $result->ID, 'extra_fee_fixed', $extra_fee_fixed );
                    if($extra_percent > 0){
                        $fee = $subtotal*$extra_percent/100;
                        update_post_meta( $result->ID, 'extra_fee_percent', $extra_percent );
                        update_post_meta( $result->ID, 'extra_fee_percent_value', $fee );
                    }
                }
                // the fund buyer have to pay.
                // admin earn commision, seller earn real_amount;

                update_post_meta( $result->ID, 'real_amount', mje_get_price_after_commission( $subtotal ) ); // seller got this.
                update_post_meta( $result->ID, 'currency', $currency );
                update_post_meta( $result->ID, 'seller_id', $mjob->post_author );
                update_post_meta( $result->ID, 'fee_commission', ae_get_option('order_commission_buyer', 0) ); // %
                update_post_meta( $result->ID, 'buyer_id', $result->post_author );

                // Update for custom order
                if( isset( $request['custom_order_id'] ) )
                    update_post_meta( $result->ID, 'custom_order_id', $request['custom_order_id'] );

                // Update for custom offer
                if( isset($request['custom_offer_id'] ) )
                    update_post_meta( $result->ID, 'custom_offer_id', $request['custom_offer_id'] );
                do_action('after_insert_mjob_order', $result, $request);
            }
            $response['data'] = $order_obj->convert( $result );

            //$response = $this->setupPayment($response['data']);

            //Add first opening message in order process
            $conversation = new MJE_Conversation_Action();
            $conversation->add_opening_message( $result, $request );
        }

        return $response;
    }

    /**
     * Get converted mjob order by ID
     *
     * @param string|int $post_id
     * @return object $mjob_order
     * @since 1.2
     * @author Tat Thien
     */
    public function get_mjob_order( $post_id ) {
        global $ae_post_factory;
        $post_obj = $ae_post_factory->get( 'mjob_order' );
        $post = get_post( $post_id );
        $mjob_order = $post_obj->convert( $post );

        return $mjob_order;
    }

    /*
     * override checkPendingAccount
     *
     * @param object $result
     * @param array $request
     * @return object $result
     */
    public function check_user_pending($result, $request){
        if( isset($request['post_type']) && ($request['post_type'] == 'mjob_order' || $request['post_type'] == 'ae_message') ) {
            if( $request['post_type'] != 'ae_message'){
                return array(
                    'success' => true,
                    'msg' => __('Successful.', 'enginethemes')
                );
            }
            if( $request['post_type'] == 'ae_message' && (isset($request['type']) && $request['type'] == 'dispute') ) {
                return array(
                    'success' => true,
                    'msg' => __('Successful.', 'enginethemes')
                );
            }
        }
        return $result;
    }

    /**
     * convert post
     *
     * @param object $result
     * @return object $result after convert
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function convert($result){
        global $ae_post_factory, $user_ID;
        $result->author_name = get_the_author_meta('display_name', $result->post_author);
        $result->mjob_order_author_url = get_author_posts_url($result->post_author);
        $user_obj = new AE_Users();
        $result->avatar = $user_obj->get_avatar($result->post_author, 150);
        //Get mjob info of order
        $result->mjob_author = '';
        $result->mjob_author_name = '';
        $result->mjob_author_url = '';
        $result->mjob_content = '';
        $result->mjob_price_text = '';
        $result->mjob_id = '';
        $result->mjob_price = '';
        $result->mjob_time_delivery = '';

        $mjob = get_post($result->post_parent);
        $result->mjob_post_thumbnail = mje_get_thumbnail($result->ID);
        if($mjob) {
            $result->mjob_author = $mjob->post_author;

            $author = get_userdata($mjob->post_author);
            //Check if user is not deleted
            if($author)
                $result->mjob_author_name = $author->display_name;

            $result->mjob_author_url = get_author_posts_url($mjob->post_author);
            if("product name" != $result->unfiltered_content) {
                $result->mjob_content = $result->post_content;
            } else {
                $result->mjob_content = $mjob->post_content;
            }

            $result->mjob_price_text = mje_shorten_price($mjob->et_budget);
            $result->mjob_id = $mjob->ID;
            $result->mjob_price = $mjob->et_budget;
            $result->mjob_time_delivery = (int)$mjob->time_delivery;

            // mjob thumbnail
            $thumbnail_id = get_post_thumbnail_id( $result->mjob_id );
            $thumbnail = wp_get_attachment_image_src( $thumbnail_id, 'medium_post_thumbnail' );
            if( isset($thumbnail[0]) && $thumbnail[0] ){
                $result->mjob_post_thumbnail = $thumbnail[0];
            } else {
                $mjob_post_thumbnail = get_template_directory_uri() . '/assets/img/mjob_thumbnail.png';
                if ( ae_get_option('default_mjob') ) {
                    $default            = ae_get_option('default_mjob');
                    $defautl_thumb      = $default['medium_post_thumbnail'];
                    $mjob_post_thumbnail  = $defautl_thumb[0];
                }
                $result->mjob_post_thumbnail = $mjob_post_thumbnail;
            }
        }

        $post_date = get_the_date('U', $result->ID);

        $result->order_human_time = sprintf( __( 'On %s', 'enginethemes' ),  et_the_time($post_date));
        $result->amount_text = mje_format_price($result->amount);
        $date_format = get_option('date_format');
        $result->modified_date = the_modified_date( $date_format, '', '', false );
        if (current_user_can('manage_options') || $result->post_author == $user_ID || $result->mjob_author == $user_ID) {
            $children = get_children(array(
                'numberposts' => 15,
                'order' => 'ASC',
                'post_parent' => $result->ID,
                'post_type' => 'order_delivery'
            ));

            $order_delivery = $ae_post_factory->get('order_delivery');
            $result->order_delivery = array();
            foreach ($children as $key => $value) {
                $value = $order_delivery->convert($value);
                $value->post_content = mje_filter_message_content($value->post_content);
                $result->order_delivery[] = $value;
            }
        }
        switch($result->post_status){
            case 'publish':
                $result->status_text = __('Active', 'enginethemes');
                $result->status_class = 'active-color';
                $result->status_text_color = 'active-text';
                break;
            case 'pending':
                $result->status_text = __('Pending', 'enginethemes');
                $result->status_class = 'pending-color';
                $result->status_text_color = 'pending-text';
                break;
            case 'late':
                $result->status_text = __('Late', 'enginethemes');
                $result->status_class = 'late-color';
                $result->status_text_color = 'late-text';
                break;
            case 'delivery':
                $result->status_text = __('Delivered', 'enginethemes');
                $result->status_class = 'delivered-color';
                $result->status_text_color = 'delivered-text';
                break;
            case 'disputed':
                $result->status_text = __('Resolved', 'enginethemes');
                $result->status_class = 'disputed-color';
                $result->status_text_color = 'disputed-text';
                break;
            case 'disputing':
                $result->status_text = __('Disputing', 'enginethemes');
                $result->status_class = 'disputing-color';
                $result->status_text_color = 'disputing-text';
                break;
            case 'finished':
                $result->status_text = __('Finished', 'enginethemes');
                $result->status_class = 'finished-color';
                $result->status_text_color = 'finished-text';
                break;
            case 'draft':
                $result->status_text = __('Draft', 'enginethemes');
                $result->status_class = 'draft-color';
                $result->status_text_color = 'draft-text';
                break;
            default:
                $result->status_text = __('Unknown', 'enginethemes');
                $result->status_class = 'active-color';
                $result->status_text_color = 'active-text';
                break;
        }

        if(!isset($result->real_amount) || empty($result->real_amount)) {
            $result->real_amount = mje_get_price_after_commission($result->amount);
        }
        if( !isset($result->mjob_order_detail) || empty($result->mjob_order_detail) ){
            $result->mjob_order_detail = $result->mjob_content;
        }

        return $result;
    }

    /**
     * validate data
     *
     * @param array $data
     * @return array $result
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function validate_post( $data ){
        $result = array(
            'success'=> true,
            'msg'=> __( 'Successful!', 'enginethemes' )
        );

        // Check wpnonce
        if ( ! de_verify_nonce( $data['_wpnonce'], 'ae-mjob_post-sync' ) ) {
            $result = array(
                'success' => false,
                'msg' => __( "You can't not do this action!", 'enginethemes' )
            );
        }

        return $result;
    }

    /**
     * process payment action
     *
     * @param array $payment_return
     * @param object $data
     * @return array $payment_return
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function after_process_payment( $payment_return, $data ){
        et_track_payment('after_process_payment.');
        global $ae_post_factory;

        $order      = $payment_return['order'];
        $order_data = $order->get_order_data();

        if( $order_data['product_type'] == 'mjob_order' ) {
            $args = array(
                'post_type'     => 'mjob_order',
                'ID'            => $order_data['product_id'],
                'payment'       => $order_data['payment'],
                'post_status'   => 'pending'
            );

            // Get mjob order
            $mjob_order = $this->get_mjob_order( $order_data['product_id'] );

            // Get mjob post
            $mjob_post = get_post( $mjob_order->post_parent );

            /**
             * If payment status completed and payment type is not CASH
             * set mjob order status to publish
             */

            $payment_status = isset($payment_return['payment_status']) ? strtoupper($payment_return['payment_status']) : 0;

            if( $order_data['payment'] != 'cash' && $payment_status == 'COMPLETED' ) {
                et_track_payment('after_process_payment => COMPLETED.');
                $args['post_status'] = 'publish';

                /**
                 * Update working fund of seller
                 */
                if( $mjob_order && ( !isset( $mjob_order->paid ) || !$mjob_order->paid ) ){
                    $user_wallet = AE_WalletAction()->getUserWallet( $mjob_post->post_author, "working" );
                    $user_wallet->balance += $mjob_order->real_amount;
                    AE_WalletAction()->setUserWallet( $mjob_post->post_author, $user_wallet, "working" );
                    update_post_meta( $mjob_order->ID, 'paid', true );
                }
                $this->add_sales_order($mjob_order , 1);
            }
            else if( $payment_status == 'DRAFT' )
            {
                $args['post_status'] = 'draft';
            }

            // Update custom order
            $custom_order_id = get_post_meta( $mjob_order->ID, 'custom_order_id', true );
            if( !empty( $custom_order_id ) ) {
                update_post_meta( $custom_order_id, 'custom_order_status', 'checkout' );
            }


            if( $args['post_status'] == 'publish' ) {
                // Send email new order to seller
                $this->mail->notify_new_mjob_order( $mjob_order );
            }
            $payment = array(
                'payment_status' => $payment_status,
                'payment_gateway' => $order_data['payment'],
            );
            if( $payment_status == 'COMPLETED' || $payment_status == 'PENDING' ){
                et_track_payment('after_process_payment => notify_admin_new_mjob_order.');
                $this->mail->notify_admin_new_mjob_order( $mjob_order, $payment );// ver 1.3.9.6 add
            }
            /**
             * Update mjob order meta
             */
            update_post_meta( $mjob_order->ID, 'et_invoice_no', $order_data['ID'] );

            $mjob_order_id = wp_update_post( $args );

            et_track_payment('after_process_payment => notify_payment.');
            // Send email new payment
            $mjob_order->et_invoice_no = $order_data['ID'];
            $mjob_order->payment_type = $data['payment_type'];
            $this->mail->notify_payment( $mjob_order );


            /**
             * Action after updated mjob order
             *
             * @param int|string $mjob_order_id
             * @since 1.3
             * @author Tat Thien
             */
            do_action( 'mje_updated_mjob_order', $mjob_order_id );
        }

        return $payment_return;
    }
    public function add_sales_order($mjob_order , $status)
    {
        $sales = get_post_meta($mjob_order->post_parent, 'et_total_sales', true);
        $total_sales = (!empty($sales)) ? $sales : 0 ;
        if($status)
        {
            update_post_meta( $mjob_order->post_parent, 'et_total_sales', $total_sales + 1);
        }
        else
        {
            if($total_sales)
             update_post_meta( $mjob_order->post_parent, 'et_total_sales', $total_sales - 1);
        }
    }
    /**
     * update order
     *
     * @param string $new_status
     * @param string $old_status
     * @param object $post
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function transform_status( $new_status, $old_status, $post ){
        if( $post->post_type == 'mjob_order' ){
            global $ae_post_factory;
            $order_obj = $ae_post_factory->get( 'mjob_order' );
            $post = $order_obj->convert( $post );
            if( $new_status == 'publish' && $old_status == 'pending' ){
                $mjob = get_post($post->post_parent);
                if( $post ){
                    if( !$post->paid ) {
                        $user_wallet = AE_WalletAction()->getUserWallet( $mjob->post_author, "working" );
                        $user_wallet->balance += $post->real_amount;
                        AE_WalletAction()->setUserWallet( $mjob->post_author, $user_wallet, "working" );
                        update_post_meta( $post->ID, 'paid', true );
                    }
                }
                // Send email new order to seller
                if( 'CASH' == strtoupper( $post->payment_type ) ) {
                    $this->mail->notify_new_mjob_order( $post );
                }
                // add sales oder for mjob
                $this->add_sales_order($post , 1);
            } elseif( $new_status == 'trash' && ( $old_status == 'publish' || $old_status == 'late' || $old_status == 'delivery' || $old_status == 'disputing' ) ) {
                // When admin delete a mjob post
                $seller_id = $post->seller_id;
                $buyer_id = $post->post_author;

                // update balance for seller
                $seller_wallet =  AE_WalletAction()->getUserWallet( $seller_id, "working" );
                $seller_wallet->balance -= $post->real_amount;
                AE_WalletAction()->setUserWallet( $seller_id, $seller_wallet, "working" );

                if( $post->payment_type == "credit" ) {
                    // Update available fund of buyer
                    $buyer_wallet = AE_WalletAction()->getUserWallet( $buyer_id );
                    $buyer_wallet->balance += $post->amount;
                    AE_WalletAction()->setUserWallet( $buyer_id, $buyer_wallet );

                    // Update spent of buyer
                    $buyer_spent_wallet = AE_WalletAction()->getUserWallet( $buyer_id, "checkout" );
                    $buyer_spent_wallet->balance -= $post->amount;
                    AE_WalletAction()->setUserWallet( $buyer_id, $buyer_spent_wallet, "checkout" );
                }
                $this->add_sales_order($post , 0);
                // send email to seller and buyer
                $this->mail->delete_order( $post, $seller_id, $buyer_id );
            } elseif( $old_status == 'trash' && ( $new_status == 'publish' || $new_status == 'late' || $new_status == 'delivery' || $new_status == 'disputing' ) ) {
                $seller_id = $post->seller_id;
                $buyer_id = $post->post_author;

                // update balance for seller
                $seller_wallet =  AE_WalletAction()->getUserWallet( $seller_id, "working" );
                $seller_wallet->balance += $post->real_amount;
                AE_WalletAction()->setUserWallet( $seller_id, $seller_wallet, "working" );

                if( $post->payment_type == "credit" ) {
                    // Update available fund of buyer
                    $buyer_wallet = AE_WalletAction()->getUserWallet( $buyer_id );
                    $buyer_wallet->balance -= $post->amount;
                    AE_WalletAction()->setUserWallet( $buyer_id, $buyer_wallet );

                    // Update spent of buyer
                    $buyer_spent_wallet = AE_WalletAction()->getUserWallet( $buyer_id, "checkout" );
                    $buyer_spent_wallet->balance += $post->amount;
                    AE_WalletAction()->setUserWallet( $buyer_id, $buyer_spent_wallet, "checkout" );
                }
                $this->add_sales_order($post , 1);
                // send email to seller and buyer
                $this->mail->restore_order( $post, $seller_id, $buyer_id );
            }
        }
    }

    /**
     * override filter query args
     *
     * @param array $query_args
     * @return array $query_args
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function filter_query_args( $query_args )
    {
        global $user_ID;
        $query = $_REQUEST['query'];

        if ( isset( $query['meta_key'] ) ) {
            $query_args['meta_key'] = $query['meta_key'];
            if ( isset($query['meta_value'] ) ) {
                $query_args['meta_value'] = $query['meta_value'];
            }

            if ( isset( $query['meta_compare'] ) ) {
                $query_args['meta_compare'] = $query['meta_compare'];
            }
        }

        $query_args['post_type'] = 'mjob_order';

        if( !isset( $query['is_task']) || $query['is_task'] == false ) {
            $query_args['author'] = $user_ID;
        }


        if( !empty( $query['post_status'] ) ){
            $query_args['post_status'] = $query['post_status'];
        } else {
            $query_args['post_status'] = array(
                'pending',
                'publish',
                'late',
                'delivery',
                'disputing',
                'disputed',
                'finished'
            );
        }

        // If post_status is all
        if( isset($query['is_task'] ) && $query['is_task'] == true && empty( $query['post_status'] ) ) {
            $query_args['post_status'] = array(
                'publish',
                'late',
                'delivery',
                'disputing',
                'disputed',
                'finished'
            );
        }

        return $query_args;
    }

    /**
     * Dispute order
     * @param int $order_id
     * @return void
     * @since 1.0.5
     * @package MicrojobEngine
     * @category mJob Order
     * @author Tat Thien
     */
    public function dispute_order( $request ) {
        global $user_ID;
        $order_id = $request['ID'];
        $order = $this->get_mjob_order( $order_id );

        $order_status = $order->post_status;
        if( $order_status == "publish" || $order_status == "late" || $order_status == "delivery" ) {
            // Update order status to DISPUTING
            wp_update_post(array(
                'ID' => $order_id,
                'post_status' => 'disputing'
            ));

            // Send email to seller, buyer, admin
            update_post_meta($order_id, 'is_dispute', true);
            $this->mail->dispute_order($order);

            // Create change log
            mje_add_mjob_order_changelog( $order_id, $user_ID, 'dispute' );

            // Save current countdown
            $expire_date = get_post_meta( $order_id, 'et_order_expired_date', true );
            $countdown_delivery = get_post_meta( $order_id, 'order_countdown_delivery', true );
            if( $expire_date &&  empty( $countdown_delivery ) ) {
                update_post_meta( $order_id, 'order_countdown_delivery', $request['order_countdown_delivery'] );
            }

            // Checkout countdown expire
            $expire_time = strtotime( $expire_date );
            $current_time = get_option( 'timezone_string' ) ? current_time( 'timestamp', true ) : current_time( 'timestamp' );
            if( $current_time >= $expire_time ) {
                update_post_meta( $order_id, 'order_countdown_delivery', '' );
            }

            $success = true;
            $msg = __("Your report has been sent.", 'enginethemes');

            /**
             * Fire action when user dispute a mjob order
             *
             * @param array $request
             * @since 1.3
             * @author Tat Thien
             */
            do_action( 'mje_disputed_mjob_order', $request );
        } else {
            $success = false;
            $msg = __("You can not dispute.", 'enginethemes');
        }

        wp_send_json(array(
            'success' => $success,
            'msg' => $msg
        ));
    }

    /**
     * Admin decide dispute order
     *
     * @param array $request
     * @since 1.0.5
     * @package MicrojobEngine
     * @category mJob Order
     * @author Tat Thien
     */
    public function resolve_dispute( $request ) {
        global $user_ID;
        $order_id = $request['post_parent'];
        $order = $this->get_mjob_order( $order_id );

        if( !isset( $request['winner'] ) || empty( $request['winner'] ) ) {
            $success = false;
            $msg = __("Please choose the winner.", 'enginethemes');
            wp_send_json( array(
                'success' => $success,
                'msg' => $msg
            ) );
        }

        if( $order->post_status == 'disputing' ) {
            if(is_super_admin($user_ID)) {
                // Update order status to RESOLVED
                wp_update_post( array(
                    'ID' => $order_id,
                    'post_status' => 'disputed'
                ));

                // Create admin message
                $message_id = wp_insert_post( array(
                    'post_type' => 'ae_message',
                    'post_parent' => $order_id,
                    'post_author' => $user_ID,
                    'post_status' => 'publish',
                    'post_content' => $request['post_content']
                ) );
                if( !is_wp_error( $message_id ) ) {
                    update_post_meta( $message_id, 'type', 'message' );
                }

                sleep(2);

                // Create change log
                $changelog_id = mje_add_mjob_order_changelog( $order_id, $user_ID, 'admin_decide' );
                update_post_meta( $changelog_id, 'winner', $request['winner'] );
                //et_log('Refund Order ID:'.$order_id);

                $refund_buyer_fee = isset($request['check_for_refun_fee_check'][0]) ? 1 : 0;
                // Update balance
                $this->update_winner_revenue( $request['winner'], $order, $refund_buyer_fee );

				if( $refund_buyer_fee ){
					$buyer = get_post_meta($order_id,'buyer_id',true);
					if( $buyer == $request['winner'] ){

						// refund fee;
						$total_x      = get_post_meta($order_id,'amount',true);
						$fee_x        = get_post_meta($order_id,'fee_commission',true); // buyer commision fee
                        $fee_buyer    = $total_x*($fee_x*0.01)/(1+$fee_x*0.01);
                        //et_log("Fee refund: ".$fee_buyer);
						$wallet       = AE_WalletAction()->getUserWallet($buyer);
                        //et_log('Amount fee  refund to buyer: '.$fee_buyer);
						$wallet->balance+=$fee_buyer;
						AE_WalletAction()->setUserWallet($buyer, $wallet);
					}
                }


                // Send decision email
                $this->mail->dispute_decision( $order, $request['winner'] );

                update_post_meta( $order_id, 'winner_id', $request['winner'] );

                $success = true;
                $msg = __( "You report has been sent.", 'enginethemes' );

                /**
                 * Fire action after admin resolved mjob order
                 *
                 * @param $request
                 * @since 1.3
                 * @author Tat Thien
                 */
                do_action( 'mje_resolved_mjob_order', $request );
            } else {
                $success = false;
                $msg = __( "Permission denied.", 'enginethemes' );
            }
        } else {
            $success = false;
            $msg = __( "Order status must be Disputing.", 'enginethemes' );
        }

        wp_send_json( array(
            'success' => $success,
            'msg' => $msg
        ) );
    }

    /**
     * update balance after admin decide the winner
     *
     * @param integer $winner
     * @param object $order
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function update_winner_revenue( $winner, $order, $refund_buyer_fee = 0 ) {
        if ( $order ) {
            if ( $winner != $order->post_author && $winner != $order->mjob_author ) {
                return false;
            } else {
                // Check if order is transferred or not
                $is_transferred = get_post_meta( $order->ID, "is_transferred", true );
                if ( !$is_transferred ) {
                    if ( $winner == $order->seller_id ) { // Seller win
                        // Transfer working fund to available fund of seller
                        AE_WalletAction()->transferWorkingToAvailable( $winner, $order->ID, $order->real_amount );
                    } elseif ( $winner == $order->post_author ) { // Buyer win
                        // Get buyer and seller wallet
                        $winner_wallet      = AE_WalletAction()->getUserWallet( $winner );
                        $loser_wallet_wf    = AE_WalletAction()->getUserWallet( $order->mjob_author, "working" );
                        $loser_wallet_af    = AE_WalletAction()->getUserWallet( $order->mjob_author );

                        // Get order amount with commission

                        $real_amount = $order->real_amount;// old version

                        $mjob_price  = $amout_refund  = $order->mjob_price; // version 1.3.9.5



                        if( $order->extra_info ){
                            foreach ($order->extra_info as $extra) {
                                $extra_budget = (float) $extra->et_budget;
                                $amout_refund = $amout_refund + $extra_budget; // amout refund to buyer.

                            }
                        }
                        $winner_wallet->balance += $amout_refund;
                        //et_log('Amout refund: '.$amout_refund);

                        // end 1.3.9.5

                        // If working fund of seller greater than price
                        if ( $loser_wallet_wf->balance >= $real_amount ) {
                            $loser_wallet_wf->balance -= $real_amount;
                        } else {
                            $loser_wallet_af->balance -= ( $real_amount - $loser_wallet_wf->balance );
                            $loser_wallet_wf->balance = 0;
                        }

                        // Update available fund of buyer
                        AE_WalletAction()->setUserWallet( $winner, $winner_wallet );

                        // Update spent if buyer used credit
                        if( $order->payment_type == 'credit' ) {
                            $buyer_wallet_spent = AE_WalletAction()->getUserWallet( $winner, 'checkout' );
                            //$buyer_wallet_spent->balance -= $order->real_amount; // amount with commission - Old version

                            // version 1.3.9.5
                            if( $refund_buyer_fee ){
                                $buyer_wallet_spent->balance -= $order->amount ;
                            } else {
                                $buyer_wallet_spent->balance -= $amout_refund ; // version 1.3.9.5
                            }
                            // end   version 1.3.9.5
                            AE_WalletAction()->setUserWallet( $winner, $buyer_wallet_spent, 'checkout' );
                        }

                        // Update working fund of seller
                        AE_WalletAction()->setUserWallet( $order->mjob_author, $loser_wallet_wf, "working" );
                        AE_WalletAction()->setUserWallet( $order->mjob_author, $loser_wallet_af );

                        // Update order transferred
                        update_post_meta( $order->ID, 'is_transferred', true );
                    }
                }
            }
            update_post_meta( $order->ID, "is_transferred", true );
        }
    }

    /**
     * Start work
     * @param void
     * @return void
     * @author Dang Bui
     */
    public function start_order() {
        $id_order = $_POST['id_order'];
        $seller_id = $_POST['seller_id'];
        $time_delivery = $_POST['mjob_time_delivery'];
        $current_time = current_time( 'timestamp' );
        if( get_option( 'timezone_string' ) ) {
            $current_time = current_time( 'timestamp', true );
        }

        $expired_date = strtotime( "+$time_delivery day", $current_time );
        $expired_date = date( 'Y-m-d\TH:i', $expired_date );

        if( $time_delivery <= 0 ) {
            $data = array(
                'status' => false,
                'msg' => __( 'Expected time of delivery must be greater than 0','enginethemes' )
            );
            wp_send_json( $data );
        }

        if( add_post_meta( $id_order, 'et_order_expired_date', $expired_date, true ) ) {
            add_post_meta( $id_order,'mjob_order_delivery', $time_delivery, true );
            // Create change log
            mje_add_mjob_order_changelog( $id_order, $seller_id, 'start_work' );

            $data = array( 'status' => true );

            /**
             * Fire action when seller start mjob order
             *
             * @param array $_POST    request data
             * @since 1.3
             * @author Tat Thien
             */
            do_action( 'mje_started_mjob_order', $_POST );
        } else {
            $data = array( 'status' => false, 'msg' => __( 'Order is started', 'enginethemes' ) );
        }
        wp_send_json( $data );
    }

    /**
     * Delay an order
     * @param void
     * @return void
     * @since MicrojobEngine 1.1.4
     * @author Tat Thien
     */
    public function delay_order( $request ) {
        global $user_ID, $wpdb, $ae_post_factory;
        $post = get_post( $request['ID'] );
        if( $post ) {
            $mjob_post_obj = $ae_post_factory->get( 'mjob_order' );
            $mjob_order = $mjob_post_obj->convert( $post );

            if( $mjob_order->mjob_author == $user_ID && $mjob_order->post_status == 'publish' ) {
                $wpdb->update(
                    $wpdb->posts,
                    array( 'post_status' => 'late' ),
                    array( 'ID' => $request['ID'] ),
                    array( '%s' ),
                    array( '%d' )
                );

                $mjob_order->post_status = 'late';
                $mjob_order->status_text = __('LATE', 'enginethemes');
                $mjob_order->status_class = 'late-color';
                $mjob_order->status_text_color = 'late-text';

                // Create change log
                mje_add_mjob_order_changelog( $request['ID'], $user_ID, 'late' );

                /**
                 * Fire action when seller delayed mjob order
                 *
                 * @param array $request   request data
                 * @since 1.3
                 * @author Tat Thien
                 */
                do_action( 'mje_delayed_mjob_order', $request );

                wp_send_json( array(
                    'success' => true,
                    'msg' => __("Successful update!", 'enginethemes'),
                    'data'=> $mjob_order
                ) );
            } else {
                wp_send_json( array(
                    'success' => false,
                    'msg' => __( "You can't delay this other!", 'enginethemes' ),
                ) );
            }
        }
    }

    /**
     * Complete an order
     * @param void
     * @return void
     * @since MicrojobEngine 1.1.4
     * @author Tat Thien
     */
    public function finish_order( $request ) {
        global $user_ID;
        $post_id = wp_update_post( array(
            'ID' => $request['ID'],
            'post_status' => 'finished'
        ) );

        if( ! is_wp_error( $post_id ) ) {
            $order = get_post($request['ID']);

            // Send email finish to seller
            $this->mail->accept_order($order, "");

            // Transfer money
            AE_WalletAction()->transferWorkingToAvailable( $request['seller_id'], $request['ID'], $request['real_amount'] );

            // Create changelog
            mje_add_mjob_order_changelog( $request['ID'], $user_ID, 'accept' );

            /**
             * Fire action when finished mjob order
             *
             * @param array $request
             * @since 1.3
             * @author Tat Thien
             */
            do_action( 'mje_finished_mjob_order', $request );

            wp_send_json( array(
                'success' => true
            ) );
        } else {
            wp_send_json( array(
                'success' => false,
                'msg' => __( 'An error while updating order!', 'enginethemes' )
            ) );
        }
    }

}
function mje_get_mjob_order( $id ) {
    global $ae_post_factory;
    $post_obj = $ae_post_factory->get( 'mjob_order' );
    $post = get_post( $id );
    $mjob_order = $post_obj->convert( $post );

    return $mjob_order;
}

$order_action_instance = MJE_MJob_Order_Action::get_instance();