<?php
/**
 * Template Name: Page order
 *
 * @param void
 * @return void
 * @since 1.0
 * @package MicrojobEngine
 * @category void
 * @author JACK BUI
 */

global $user_ID;

// Check is user logged in
if( !is_user_logged_in() ) {
    wp_redirect(et_get_page_link('sign-in') . '?redirect_to=' . mje_get_full_url($_SERVER));
}

$product_id = ! empty( $_GET['pid'] ) ? strip_tags( $_GET['pid'] ) : '';
if( empty( $product_id ) || ! is_numeric( $product_id ) ) {
    wp_redirect( get_home_url() );
}
get_header();

$product = get_post( $product_id );
?>
<div id="content" class="mjob-order-page">
    <div class="block-page">
        <div class="container dashboard withdraw">
            <?php if( mje_is_user_active( $user_ID ) ): ?>
                <?php
                if( $product ) {
                    global $ae_post_factory;
                    $object = $ae_post_factory->get( $product->post_type );
                    $product = $object->convert( $product );
					switch ( $product->post_type ) {
                        case 'mjob_post':
                            mje_get_template( 'template/checkout/checkout-mjob.php', array( 'product' => $product ) );
                            break;
                        case 'ae_message':
                            mje_get_template( 'template/checkout/checkout-custom-order.php', array( 'product' => $product ) );
                            break;                        default:
							/**
							 * Add action in case orther postype
							 *
							 * @since 1.3.1
							 * @author Tan Hoai
							 */
							do_action('mje_checkout_custom_product',$product);
                    }
                }
                ?>
                <input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo de_create_nonce('mje_checkout_action') ?>">
            <?php else: ?>
                <div class="error-block">
                    <p><?php _e('Your account is pending. You have to activate your account to continue this step.', 'enginethemes'); ?></p>
                </div>
            <?php endif; ?>
        </div>
        <?php mje_get_template( 'template/checkout/gateway.php' ); ?>
    </div>
</div>
<?php
get_template_part('template/modal', 'secure-code');
get_footer();
?>
