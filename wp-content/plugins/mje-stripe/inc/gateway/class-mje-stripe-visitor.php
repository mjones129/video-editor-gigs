<?php
class MJE_Stripe_Visitor extends ET_PaymentVisitor
{
    protected $_payment_type = 'stripe';

    function setup_checkout( ET_Order $order )
    {
        global $user_email;
        $response = array();
        $return_url = $this->_settings['return'];

        \Stripe\Stripe::setApiKey( MJE_Stripe::$stripe_api['secret_key'] );
        $token = $_REQUEST['token'];
        $order_pay = $order->generate_data_to_pay();
        try {
            /**
             * Check zero-decimal currencies
             * do not multiple with 100 if currency is a zero-decimal type
             */
            $amount = $order_pay['total'];
            if( ! in_array( $order_pay['currencyCodeType'], MJE_Stripe::get_zero_decimal_currencies() ) ) {
                $amount *= 100;
            }
            $charge = \Stripe\Charge::create( array(
                'amount' => ( float ) $amount,
                'currency' => $order_pay['currencyCodeType'],
                'source' => $token,
                'description' => sprintf( __( 'Charge by user: %s', 'mje_stripe' ), $user_email ),
                'metadata' => array( 'order_id' => $order_pay['ID'] )
            ) );

            $charge_id = $charge->id;
            $return_token = md5( $charge_id );

            // Update order
            $order->set_payment_code( $return_token );
            $order->set_payer_id( $charge_id );
            $order->update_order();

            // Response data
            $return_url .= '&token=' . $return_token;
            $response = array(
                'success' => true,
                'ACK' => true,
                'msg' => __( 'Congrats. Your payment is successful!', 'mje_stripe' ),
                'url' => $return_url
            );
        } catch( Exception $e ) {
            // Force delete order and mjob order
            wp_delete_post( $order_pay['ID'], true );
            wp_delete_post( $order_pay['product_id'], true );

            // Destroy session
            et_destroy_session();

            $response = array(
                'success' => false,
                'ACK' => false,
                'msg' => $e->getMessage()
            );
        }

        return $response;
    }

    function do_checkout( ET_Order $order )
    {
        $payment_code = $order->get_payment_code();
        $payment_return = array();
        if( ! empty( $_REQUEST['token'] ) && $_REQUEST['token'] == $payment_code ) {
            $order->set_status( 'publish' );
            $order->update_order();

            $payment_return = array(
                'ACK' => true,
                'payment' => 'stripe',
                'payment_status' => 'Completed'
            );
        } else {
            $order->set_status( 'pending' );
            $order->update_order();

            $payment_return = array(
                'ACK' => false,
                'payment' => 'stripe',
                'payment_status' => 'Pending',
            );
        }

        return $payment_return;
    }
}