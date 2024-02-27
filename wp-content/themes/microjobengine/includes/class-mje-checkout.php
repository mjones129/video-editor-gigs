<?php
class MJE_Checkout extends AE_Base
{
    public static $instance;
    public $accept_product_types;

    public static function get_instance () {
        if( is_null( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct()
    {
        $this->add_ajax( 'mje_checkout_product', 'do_checkout');
        $this->build_product_types();
    }

    /**
     * Handle checkout ajax
     *
     * @param void
     * @return void
     * @since 1.2
     * @author Tat Thien
     */
    public function do_checkout() {
        $request = $_REQUEST;

        /* Validate checkout action */
        if( ! $this->validate_nonce() ) {
            wp_send_json( array(
                'success' => false,
                'msg' => __( 'Invalid action!', 'enginethemes' )
            ) );
        }

        if( ! $this->validate_product_type( $request['p_type'] ) ) {
            wp_send_json( array(
                'success' => false,
                'msg' => __( 'Invalid type of product!', 'enginethemes' )
            ) );
        }

        if( ! $this->validate_total( $request['p_total'] ) ) {
            wp_send_json( array(
                'success' => false,
                'msg' => __( 'Invalid total price!', 'enginethemes' )
            ) );
        }

        $response = array();

        switch ( $request['p_type'] ) {
            case 'mjob_order':
                $mjob_order_action =  MJE_MJob_Order_Action::get_instance();
                $response = $mjob_order_action->sync( $request['p_data'] );

                break;
            default:
                $response = apply_filters( 'mje_checkout_response_data', $request );
        }

        $payment_resp = $this->setup_payment( $request, (object) $response['data'] );

        // Send response to client
        wp_send_json( $payment_resp );
    }

    /**
     * setup payment after save draft order
     *
     * @param array $checkout
     * @param object $product
     * MJE_Stripe_Visitor@return array $response
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function setup_payment( $checkout, $product ){
        et_track_payment('setup_payment');
        global $user_ID;

        $response = array(
            'success'=> false,
            'msg'=> __('Payment failed!', 'enginethemes')
        );

        $arg = apply_filters('ae_payment_links', array(
            'return' => et_get_page_link('process-payment') ,
            'cancel' => et_get_page_link('process-payment')
        ));
        et_track_payment('checkout:');
        et_track_payment($checkout);
        $payment_type = $checkout['p_payment'];

        /**
         * factory create payment visitor
         */
        $coupon_code = isset($checkout['coupon_code']) ? $checkout['coupon_code']: '';
        $order_data = array(
            'payer'         => $user_ID,
            'total'         => '',
            'status'        => 'draft',
            'payment'       => $payment_type,
            'paid_date'     => '',
            'post_parent'   => $product->ID,
            'amount'        => $product->amount,
            'coupon_code'   => $coupon_code,
        );

        $product->payment_type = $payment_type;
        et_track_payment('order_data:');
        et_track_payment($order_data);

        $order = new MJE_Order( $order_data );
        $order->add_product( $product );
        $order_data = $order->generate_data_to_pay();
        $is_v2 = is_order_v2($payment_type);
        if( !$is_v2 ){
        // write session
            et_track_payment('SAVE order_id to section: v1');
            et_write_session('order_id', $order_data['ID']);
            et_write_session('process_type', 'buy');
        } else {
            et_track_payment('Is v2 version.');
        }
        et_track_payment('Create visitor Object of payment_type : '.$payment_type);
        $visitor = AE_Payment_Factory::createPaymentVisitor( strtoupper($payment_type), $order, $payment_type );

        // setup visitor setting
        $visitor->set_settings( $arg );

        // accept visitor process payment
        et_track_payment('Call order->accept()');
        $nvp = $order->accept( $visitor );
        et_track_payment('result of accept == nvp: ');


        if ($nvp['ACK']) {
            $response = array(
                'success' => $nvp['ACK'],
                'data' => $nvp,
                'paymentType' => $payment_type
            );
        } else {
            $response = array(
                'success' => false,
                'paymentType' => $payment_type,
                'msg' => ! empty( $nvp['msg'] ) ? $nvp['msg'] : __("Invalid payment gateway!", 'enginethemes')
            );
        }

        /**
         * filter $response send to client after process payment
         *
         * @param Array $response
         * @param String $paymentType  The payment gateway user select
         * @param Array $order The order data
         *
         * @package  AE Payment
         * @category payment
         *
         * @since  1.0
         * @author  Dakachi
         */
        $response = apply_filters('mje_setup_payment', $response, $payment_type, $order);

        return $response;
    }

    /**
     * ae_process_payment function process payment return to check payment amount, update order
     * @use AE_Order , ET_NOPAYOrder, AE_Payment_Factory
     * @param string $payment_type the string of payment type such as paypal, 2checkout , stripe
     * @param object $data
     *  -args $order_id : current order_id on process
     *  -args $ad_id : current ad id user submit
     * @return array $payment_return
     *
     * @package AE Payment
     * @category payment
     *
     * @since 1.0
     * @author  Dakachi
     *
     */
    public static function process_payment( $payment_type, $data ) {
        $payment_return = array(
            'ACK' => false
        );
        if ( $payment_type ) {
            // check order id
            if ( isset($data['order_id'] ) ) {
                $order = new MJE_Order( $data['order_id'] );
            } else {
                $order = new ET_NOPAYOrder();
            }


            // call a visitor process order base on payment type
            et_track_payment('payment_type:'.$payment_type);
            $visitor = AE_Payment_Factory::createPaymentVisitor( strtoupper($payment_type), $order, $payment_type );
            $payment_return = $visitor->do_checkout( $order );

            $data['order'] = $order;
            $data['payment_type'] = $payment_type;

            /**
             * filter payment return
             * @param array $payment_return
             * @param array $data -order : Order data, payment_type ...
             * @since 1.0
             */
            $payment_return = apply_filters( 'mje_process_payment', $payment_return, $data );
            if($payment_return){
                $payment_return['order'] = $data['order'];

                /**
                 * do an action after payment
                 * @param array $payment_return
                 * @param array $data -order : Order data, payment_type ...
                 * @since 1.0
                 */
                et_track_payment('call mje_after_process_payment hook');
                do_action( 'mje_after_process_payment', $payment_return, $data );
            }
        }
        return $payment_return;
    }

    /**
     * Validate nonce
     *
     * @param void
     * @return boolean
     * @since 1.2
     * @author Tat Thien
     */
    public function validate_nonce() {
        if( ! de_check_ajax_referer( 'mje_checkout_action', 'p_nonce', false ) ) {
            return false;
        }
        return true;
    }

    /**
     * Validate total price of checkout
     *
     * @param float|string $total
     * @return boolean
     * @since 1.2
     * @author Tat Thien
     */
    public function validate_total( $total ) {
        if( $total <= 0 ) {
            return false;
        }
        return true;
    }

    /**
     * Validate product type based on accepted product types
     *
     * @param string $product_type
     * @return boolean
     * @since 1.2
     * @author Tat Thien
     */
    public function validate_product_type( $product_type ) {
        if( ! in_array( $product_type, $this->accept_product_types ) ) {
            return false;
        }
        return true;
    }

    /**
     * Build accepted product types
     *
     * @param void
     * @return void
     * @since 1.2
     * @author Tat Thien
     */
    public function build_product_types() {
        $this->accept_product_types = apply_filters( 'mje_checkout_product_types',  array( 'mjob_order', 'mje_claims' ) );
    }
}

$instance  = MJE_Checkout::get_instance();