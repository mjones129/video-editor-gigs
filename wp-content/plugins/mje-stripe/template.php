<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

//if( !function_exists( 'mje_stripe_render_button' ) ) {
    /**
     * Render Stripe button in payment gateways list
     *
     * @param void
     * @return void
     * @since 1.0
     * @author Tat Thien
     */
    function mje_stripe_render_button() {

        if( MJE_Stripe::is_active() ) :
            $disable_class = '';
            $tooltip = '';
            $checkout_type = 'checkout_order';
            if( is_page_template('page-post-service.php') )
                $checkout_type = 'checkout_package'; // add in version 1.2

            if( ! MJE_Stripe::is_has_api_key() ) {
                $disable_class = 'disable-gateway';
                $tooltip = 'data-toggle="tooltip" data-placement="top" data-original-title="' . __( 'You can not use this checkout method because of missing API key.', 'mje_stripe' ) . '"';
            }
            ?>
            <li>
                <div class="outer-payment-items hvr-underline-from-left <?php echo $disable_class; ?>" <?php echo $tooltip; ?>>
                    <a href="#" id="stripe-gateway" class="btn-submit-price-plan  " data-checkout-type="<?php echo $checkout_type;?>" data-type="stripe" >
                        <img src="<?php echo MJE_STRIPE_URL . 'assets/img/card-stripe.svg'; ?>" alt="Stripe logo">
                        <p class="text-cash"><?php _e( 'Stripe', 'mje_stripe' ); ?></p>
                    </a>
                </div>
            </li>
            <?php
        endif;
    }
    add_action( 'mje_after_payment_list', 'mje_stripe_render_button' ); // 1.0.1
    //add_action( 'after_payment_list', 'mje_stripe_render_button' ); // latest version 1.1

//}

if( !function_exists( 'mje_stripe_render_template_in_footer' ) ) {
    /**
     * Render html in footer
     *
     * @param void
     * @return void
     * @since 1.0
     * @author Tat Thien
    */
    function mje_stripe_render_template_in_footer() {
        mje_stripe_get_template( 'payment-modal.php' );
    }
    add_action( 'wp_footer', 'mje_stripe_render_template_in_footer' );
}

if( !function_exists( 'mje_stripe_render_payment_name' ) ) {
    /**
     * Filter payment name
     *
     * @param array $payment_name
     * @return array $payment_name
     * @since 1.0.0
     * @author Tat Thien
     */
    function mje_stripe_render_payment_name( $payment_name ) {
        $value = '<p class="payment-name stripe" title="' . __( 'Stripe', 'mje_stripe' ) . '"><img src="'. MJE_STRIPE_URL .'/assets/img/card-stripe.svg" /><span>' . __( 'Stripe', 'mje_stripe' ) . '</span></p>';
        $payment_name = wp_parse_args( $payment_name, array( 'stripe' => $value ) );
        return $payment_name;
    }
    add_filter( 'mje_render_payment_name',  'mje_stripe_render_payment_name' );
}