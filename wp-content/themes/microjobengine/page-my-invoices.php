<?php
/**
 * Template Name: My Invoices
 */
global $user_ID, $wp_query;

// Get invoice id
if( ! empty( $wp_query->query_vars['invoice_id'] ) ) {
    $invoice_id = $wp_query->query_vars['invoice_id'];
    if( ! is_numeric( $invoice_id ) ) {
        wp_redirect( et_get_page_link( 'my-invoices') );
    }
} else {
    $invoice_id = '';
}

if ( empty( $user_ID ) ) {
    wp_redirect( et_get_page_link( 'sign-in' ) . '?redirect_to=' . mje_get_full_url( $_SERVER ) );
}
get_header(); ?>
    <div id="content" class="my-list-order">
        <div class="block-page">
            <div id="invoices-container" class="container dashboard withdraw">
                <div class="row title-top-pages">
                    <p class="block-title"><?php _e('My Invoices', 'enginethemes'); ?></p>
                    <p><a href="<?php echo et_get_page_link('dashboard'); ?>" class="btn-back"><i class="fa fa-angle-left"></i><?php _e('Back to dashboard', 'enginethemes'); ?></a></p>
                </div>
                <div class="row profile">
                    <div class="col-lg-3 col-md-3 col-sm-12 col-sx-12 profile">
                        <?php get_sidebar('my-profile'); ?>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-sx-12">
                        <div class="information-items-detail box-shadow">
                            <?php if( empty( $invoice_id ) ) : ?>
                                <?php mje_get_template_part( 'template/invoices/list' ); ?>
                            <?php else : ?>
                                <?php mje_get_template( 'template/invoices/detail.php', array( 'invoice_id' => $invoice_id ) ); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
get_footer();
