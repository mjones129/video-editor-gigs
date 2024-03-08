<?php
class MJE_Credit_Visitor extends ET_PaymentVisitor
{
    protected $_payment_type = 'credit';
    function setup_checkout (ET_Order $order) {
        return array(
            'success' => true,
            'ACK' => true,
            'url' => $this->_settings['return']
        );
    }

    function do_checkout ( ET_Order $order ) {
        $order_pay = clone $order;
        $order = $order_pay->generate_data_to_pay();

        if(isset($order['ID'])) {
            $order_pay->set_status('publish');
            $order_pay->update_order();

            return array (
                'ACK'		=> true,
                'payment'	=> 	'credit',
                'response'	=>	array (
                    'S_MESSAGE'		=>	"",
                    'L_MESSAAGE' 	=>  "",
                ),
                'payment_status'	=>  'Completed'
            );
        } else {
            return array(
                'ACK' => false,
                'payment' => 'credit',
                'response' => array(
                    'S_MESSAGE' => __("Invalid order ID", 'enginethemes'),
                    'L_MESSAAGE' => __("Invalid order ID", 'enginethemes'),
                ),
                'payment_status' => 'error'
            );
        }
    }
}

class MJE_Revenue extends AE_Base
{
    public static $instance;

    static function get_instance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    function __construct() {
        $min_withdraw = (int)ae_get_option('minimum_withdraw', 15);
        define('MIN_WITHDRAW', $min_withdraw);

        $this->mail = MJE_Mailing::get_instance();
        $this->secure_code_request_time = 2; //
        $this->add_ajax('mjob_revenue_sync', 'sync_revenue');
        $this->add_ajax('mjob_withdraw_sync', 'sync_withdraw');

        $this->add_action('mje_after_process_payment', 'update_revenue_after_checkout_mjob', 10, 2);
        $this->add_action('ae_member_process_order', 'update_revenue_after_checkout_package', 15, 3);

        $this->add_filter('et_build_payment_visitor', 'build_payment_visitor', 10, 3);
        $this->add_filter('ae_convert_user', 'add_user_revenue');
    }

    /**
     * All revenue actions
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Revenues
     * @author Tat Thien
     */
    public function sync_revenue() {
        global $user_ID;
        $request = $_REQUEST;

        if(empty($user_ID)) {
            wp_send_json(array(
                'success' => false,
                'msg' => __('Invalid user.', 'enginethemes'),
            ));
        }

        if(!de_verify_nonce($request['_wpnonce'], 'withdraw_action')) {
            wp_send_json(array(
                'success' => false,
                'msg' => __('Don\'t try to hack.', 'enginethemes'),
            ));
        }

        if(isset($request['do_action'])) {
            switch($request['do_action']) {
                case 'request_secure_code':
                    $resp = $this->mje_request_secure_code($user_ID);
                    break;
                case 'validate_checkout':
                    $resp = $this->validate_credit_checkout($user_ID, $request);
                    break;
            }

            wp_send_json($resp);
        }
    }

    /**
     * Do withdraw
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Revenues
     * @author Tat Thien
     */
    public function sync_withdraw() {
        global $user_ID, $current_user;
        $request = $_REQUEST;
        $default = array(
            'account_type'=> '',
            'amount'=> '',
            'secure_code'=> '',
            '_wpnonce' => ''
        );
        $request = wp_parse_args($request, $default);

        // Check user
        if(empty($user_ID)) {
            wp_send_json(array(
                'success' => false,
                'msg' => __('Invalid user.', 'enginethemes'),
            ));
        }

        // Check user active
        if(!mje_is_user_active($user_ID)) {
            wp_send_json(array(
                'success' => false,
                'msg' => __('Your account is pending. You have to activate your account to continue this step!', 'enginethemes')
            ));
        }

        // Check nonce
        if(!de_verify_nonce($request['_wpnonce'], 'withdraw_action')) {
            wp_send_json(array(
                'success' => false,
                'msg' => __('Don\'t try to hack.', 'enginethemes'),
            ));
        }

        // Check balance
        $wallet = ae_credit_convert_wallet($request['amount']);
        $result = AE_WalletAction()->checkBalance($user_ID, $wallet );
        if( $result < 0 ){
            wp_send_json(array(
                'success'=> false,
                'msg'=> __("You don't have enough money in your wallet!", 'enginethemes')
            ));
        }

        // Check valid secure code
        $secure_code = AE_WalletAction()->getSecureCode($user_ID);
        if(empty($secure_code)) {
            wp_send_json(array(
                'success'=> false,
                'msg'=> __('You don\'t have a secure code. Please request one!', 'enginethemes')
            ));
        }

        // Check user payment
        $payment_info = mJobUserAction()->mJobCheckPaymentInfo($user_ID, $request['account_type']);
        if($payment_info == false) {
            $resp = array(
                'success'=> false,
            );
            // Check setup PayPal account
            if($request['account_type'] == 'paypal') {
                $resp['msg'] = sprintf(__('Please set up your PayPal account <a href="%s" style="text-decoration: underline;">here</a>!', 'enginethemes'), et_get_page_link('payment-method'));
                wp_send_json($resp);
            } else { // Check setup bank account
                $resp['msg'] = sprintf(__('Please set up your bank account <a href="%s" style="text-decoration: underline;">here</a>!', 'enginethemes'), et_get_page_link('payment-method'));
                wp_send_json($resp);
            }
        }

        $result = AE_WalletAction()->checkSecureCode($user_ID, $request['secure_code']);
        if(!$result){
            wp_send_json(array(
                'success'=> false,
                'msg'=> __('Please enter a valid secure code!', 'enginethemes')
            ));
        }

        // Check empty amount
        if(empty($request['amount'])){
            wp_send_json(array(
                'success'=> false,
                'msg'=> __('Please enter a valid number!', 'enginethemes')
            ));
        }

        // Check minimum amount
        if( (float)$request['amount'] < (float)MIN_WITHDRAW ){
            wp_send_json(array(
                'success'=> false,
                'msg'=> __('Please enter a number greater than minimum withdrawal!', 'enginethemes')
            ));
        }

        $user_wallet = AE_WalletAction()->getUserWallet($user_ID);
        $charge_obj = array(
            'amount' => (float)$request['amount'],
            'currency' => $user_wallet->currency,
            'customer' => $user_ID,
            'status'=> 'pending',
            'post_title'=> __('withdrew','enginethemes'),
            'history_type'=> 'withdraw',
            'payment_method' => $request['account_type']
        );
        $charge = AE_WalletAction()->charge($charge_obj);
        if( !$charge['success'] ){
            wp_send_json($charge);
        }

        // Insert withdraw
        $post_title = sprintf(__('%s sent a request to withdraw %s ', 'enginethemes'), $current_user->data->display_name, ae_price_format($request['amount']));

        $payment_info = mJobUserAction()->mJobGetPaymentInfo($user_ID);

        if($request['account_type'] == 'paypal') { // Account is PayPal
            $content = __('<h2>PayPal Infomation: </h2>');
            $content .= sprintf(__('User name: <a href="%s">%s</a> <br>', 'enginethemes'), get_author_posts_url($user_ID), $current_user->data->display_name);
            $content .= sprintf(__('Email address: %s', 'enginethemes'), $payment_info['paypal']);
        } else { // Account is bank
            $content = __('<h2>Bank Infomation: </h2>');
            $content .= sprintf(__('User name: <a href="%s">%s</a> <br>', 'enginethemes'), get_author_posts_url($user_ID), $current_user->data->display_name);
            $content .= sprintf(__('First name: %s <br>', 'enginethemes'), $payment_info['bank']['first_name']);
            $content .= sprintf(__('Middle name: %s <br>', 'enginethemes'), $payment_info['bank']['middle_name']);
            $content .= sprintf(__('Last name: %s <br>', 'enginethemes'), $payment_info['bank']['last_name']);
			$content .= sprintf(__('Bank name: %s<br>' , 'enginethemes'), $payment_info['bank']['name']);
            $content .= sprintf(__('SWIFT code: %s <br>', 'enginethemes'), $payment_info['bank']['swift_code']);
            $content .= sprintf(__('Account number: %s <br>', 'enginethemes'), $payment_info['bank']['account_no']);
        }


        $withdraw = array(
            'post_title'=> $post_title,
            'post_type'=> 'ae_credit_withdraw',
            'post_status'=> 'pending',
            'post_content'=> $content,
            'post_author'=> $user_ID
        );

        $post = wp_insert_post($withdraw);
        if($post){
            update_post_meta($post, 'amount', $request['amount']);
            update_post_meta($post, 'currency', $user_wallet->currency);
            update_post_meta($post, 'charge_id', $charge['id']);
            update_post_meta($post, 'time_request', get_the_time('U', $charge['id']));

            // Send email to admin
            $this->mail->request_withdraw($post);

            wp_send_json(array(
                'success'=> true,
                'msg'=> __('Request sent!', 'enginethemes'),
                'data'=> ae_credit_balance_info($user_ID)
            ));
        }
        else{
            wp_send_json(array(
                'success'=> false,
                'msg'=> __('Failed request!', 'enginethemes')
            ));
        }
    }

    /**
     * Request a secure code
     * @param int $user_id
     * @return array $resp
     * @since 1.0
     * @package MicrojobEngine
     * @category Revenue
     * @author Tat Thien
     */
    public function mje_request_secure_code($user_id) {
        // Check request time
        $request_time = get_user_meta($user_id, 'secure_code_request_time', true);
        if(!empty($request_time)) {
            $duration = (time() - $request_time) / 60;
            if((int)$duration < $this->secure_code_request_time) {
                return array(
                    'success' => false,
                    'msg' => sprintf(__('You need to wait for %s minutes to request a new secure code!', 'enginethemes'), $this->secure_code_request_time)
                );
            }
        }

        // Generate secure code
        $secure_code = AE_WalletAction()->generateSecureCode();
        // Save secure code to user
        AE_WalletAction()->setSecureCode($user_id, $secure_code);
        // Update request time
        update_user_meta($user_id, 'secure_code_request_time', time());
        // Send email
        $this->mail->send_secure_code($user_id, $secure_code);

        $resp = array(
            'success' => true,
            'msg' => __('Secure code has been sent to your email.', 'enginethemes')
        );

        return $resp;
    }

    /**
     * Validate checkout with credit
     * @param int $user_id
     * @return array $resp
     * @since 1.1
     * @package MicrojobEngine
     * @category Revenue
     * @author Tat Thien
     */
    public function validate_credit_checkout( $user_id, $request ) {
        $resp = array(
            'success' => true
        );

        // Check secure code
        $secure_code = isset($request['secure_code']) ? $request['secure_code'] :0;
        $result = AE_WalletAction()->checkSecureCode( $user_id, $secure_code );
        if(!$result){
            return array(
                'success'=> false,
                'msg'=> __('Please enter a valid secure code!', 'enginethemes')
            );
        }

        // Check balance
        $amount = 0;
        if( $request['checkout_type'] == 'checkout_order' ) {
            $price = 0;
            $extra_price = 0;
            // Get mjob price
            if(isset( $request['mjob_id']) && !empty($request['mjob_id'] ) ) {
                $price = ( float ) get_post_meta( $request['mjob_id'], 'et_budget', true );
            } elseif( isset( $request['custom_offer_id']) && !empty( $request['custom_offer_id'] ) ) {
                // Get offer price
                $price = ( float ) get_post_meta( $request['custom_offer_id'], 'custom_offer_budget', true );
            } else {
                return array(
                    'success'=> false,
                    'msg'=> __( 'Invalid mJob!', 'enginethemes' )
                );
            }

            // Get extra price
            if( isset( $request['extra_ids'] ) && !empty($request['extra_ids'] ) ) {
                foreach( $request['extra_ids'] as $extra_id ) {
                    $extra_price += get_post_meta( $extra_id, 'et_budget', true );
                }
            }
            $amount = $price + $extra_price;
        } elseif( $request['checkout_type'] == 'checkout_package' ) {
            $payment = new MJE_Payment();
            $packs = $payment->get_plans();
            foreach( $packs as $pack ) {
                if( $pack->sku == $request['package_id'] ) {
                    $amount = $pack->et_price;
                    break;
                }
            }
        }
        $wallet = ae_credit_convert_wallet( $amount );
        $result = AE_WalletAction()->checkBalance( $user_id, $wallet );
        if( $result < 0 ){
            return array(
                'success'=> false,
                'msg'=> __("You don't have enough money in your wallet to process checkout!", 'enginethemes')
            );
        }

        return $resp;
    }

    /**
     * Add payment visitor for CREDIT
     * @param object $class
     * @param string $paymentType
     * @param object $order
     * @return object $class
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author Tat Thien
     */
    public function build_payment_visitor($class , $paymentType, $order) {
        if($paymentType == 'CREDIT') {
            $class = new MJE_Credit_Visitor( $order );
        }
        return $class;
    }

    /**
     * Update revenue after checkout mjob
     * @param object $payment_return
     * @return object $data
     * @since 1.1
     * @package MicrojobEngine
     * @category Revenue
     * @author Tat Thien
     */
    public function update_revenue_after_checkout_mjob($payment_return, $data) {
        global $user_ID;

        if($data['payment_type'] == 'credit') {
            $order = $data['order'];
            $order_data = $order->get_order_data();

            $this->update_revenue_after_checkout($user_ID, $order_data['total']);

            /**
             * Fire an action when update user revenue after checkout
             *
             * @param int|string $user_ID    buyer id
             * @param int|float $order_data['total']    total of checkout
             * @since 1.3
             * @author Tat Thien
             */
            do_action( 'mje_updated_revenue_after_checkout_mjob', $user_ID, $order_data['total'] );
        }
        return $payment_return;
    }

    /**
     * Update revenue after checkout package
     * @param int $user_id
     * @param object $order
     * @param object $data
     * @return void
     * @since 1.1
     * @package MicrojobEngine
     * @category Revenue
     * @author Tat Thien
     */
    public function update_revenue_after_checkout_package($user_id, $order, $data) {
        if($order['payment'] == 'credit') {
            $this->update_revenue_after_checkout($user_id, $order['total']);
        }
    }

    /**
     * Update available fund and checkout fund
     * @param int $user_id
     * @param float $total
     * @return void
     * @since 1.1
     * @package MicrojobEngine
     * @category Revenue
     * @author Tat Thien
     */
    public function update_revenue_after_checkout($user_id, $total) {
        // Update available fund
        $available_wallet = AE_WalletAction()->getUserWallet($user_id);
        $available_wallet->balance -= $total;
        AE_WalletAction()->setUserWallet($user_id, $available_wallet);

        // Update checkout fund
        $checkout_wallet = AE_WalletAction()->getUserWallet($user_id, "checkout");
        $checkout_wallet->balance += $total;
        AE_WalletAction()->setUserWallet($user_id, $checkout_wallet, "checkout");
    }

    /**
     * Filter user data
     * @param object $user
     * @return object $user
     * @since 1.1
     * @package MicrojobEngine
     * @category Revenue
     * @author Tat Thien
     */
    public function add_user_revenue($user) {
        $available_wallet = AE_WalletAction()->getUserWallet($user->ID);
        $available_balance = $available_wallet->balance;
        if(!empty($available_balance)) {
            $user->available_fund = $available_balance;
        } else {
            $user->available_fund = 0;
        }

        $user->withdraw_nonce = de_create_nonce('withdraw_action');
        return $user;
    }
}

$new_instance = MJE_Revenue::get_instance();