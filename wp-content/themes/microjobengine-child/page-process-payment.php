<?php
/**
 *	Template Name: Process Payment
 */
$session = et_read_session();
global $ad , $payment_return, $order_id, $user_ID;

/**
 * If seller reuse purchased packages
 */
if(isset($_GET['payment-type']) && $_GET['payment-type'] == 'usePackage') {
    $payment_return = ae_process_payment($_GET['payment-type'], $session);
    // If success, return to mjob detail
    if($payment_return['ACK']) {
        $mjob_url = get_the_permalink($session['ad_id']);

        // Destroy session for order data
        et_destroy_session();

        // Redirect to mjob detail
        wp_redirect($mjob_url);
        exit;
    }
}


/**
 * Get order data
 */
$order_id = isset($_GET['order-id']) ? $_GET['order-id'] : '';
// global $wpdb;
// $sql = "Select m.meta_key, m.meta_value from $wpdb->postmeta m where post_id = $order_id";
// $quey = $wpdb->get_results($sql);
// echo '<pre>';
// var_dump($quey);
// echo '</pre>';

if( isset($session['process_type']) && $session['process_type'] == 'buy' ){
    $order = new MJE_Order($order_id);
} else {
    $order = new AE_Order($order_id);
}
$order_data = $order->get_order_data();

/**
 * Allow current user is payer and administrator can view the order
 */
if( $order_id && ($user_ID == $order_data['payer'] || is_super_admin($user_ID)) ) {
    // Get product data
    $ad = get_post($order_data['product_id']);

    $payment_type = $order_data['payment'];

    get_header(); ?>
    <!-- Page Blog -->
    <section id="blog-page">
        <div class="container page-container">
            <!-- block control  -->
            <div class="row block-page">
                <div class="blog-content info-payment-method">
                    <h1 class="title"><?php _e('Order Received', 'enginethemes'); ?></h1>

                    <p class="sub-title"><?php _e('Thank you. Your order has been received and is now being processed!', 'enginethemes'); ?></p>

                    <div class="invoice-detail">
                        <div class="mje-table">
                            <div class="mje-table-row">
                                <div class="mje-table-col">
                                    <span class="table-invoice-title"><?php _e('Invoice no', 'enginethemes') ?></span>
                                    <?php echo $order_data['ID']; ?>
                                </div>
                                <div class="mje-table-col">
                                    <span class="table-invoice-title"><?php _e('Date', 'enginethemes'); ?></span>
                                    <?php echo get_the_date(get_option('date_format'), $order_id); ?>
                                </div>
                                <div class="mje-table-col">
                                    <span class="table-invoice-title"><?php _e('Payment type', 'enginethemes') ?></span>

                                    <?php
                                        $payment_method_txt_arr = mje_render_payment_name();
                                        $payment_method = $order_data['payment'];
                                    ?>
                                    <?php echo $payment_method_txt_arr[$payment_method]; ?>

                                </div>
                                <div class="mje-table-col">
                                    <span class="table-invoice-title"><?php _e('Total', 'enginethemes') ?></span>
                                    <?php echo mje_format_price($order_data['total']); ?>
                                </div>
                            </div>
                        </div>

                        <?php if($order_data['payment'] == 'cash') : ?>
                            <?php
                            $cash_options = ae_get_option('cash');
                            $cash_message = $cash_options['cash_message'];
                            ?>
                            <div class="invoice-note">
                                <p class="type-cash"><?php _e('CASH NOTE', 'enginethemes'); ?></p>
                                <?php echo $cash_message; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="link-detail-method">
                        <?php if($ad->post_type == 'mjob_post') : ?>
                            <a href="<?php echo get_the_permalink($ad->ID) ?>" class="<?php mje_button_classes( array( ) ); ?>"><?php _e('View Your Purchase', 'enginethemes'); ?><i class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
                        <?php else :
								echo apply_filters('show_text_button_process_payment',$content="", $ad);
						 	  endif
						 ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php
    // Make sure that process payment just happen one time

    if($order_id ) {

        $process_type = isset($session['process_type']) ? $session['process_type'] : '';
        et_track_payment('Call Process_Payment in page_process-payment.php. process_type ='.$process_type);
        if( $process_type == 'buy' ){
            $payment_return = MJE_Checkout::process_payment( $payment_type, $session ); // buy 1 service
        } else {
            $payment_return = ae_process_payment($payment_type, $session);
        }
        update_post_meta($order_id, 'et_order_is_process_payment', true);
        et_destroy_session();

    }
    get_footer();
} else {
    wp_redirect(get_home_url());
    exit;
}
