<?php
/**
 * Template Name: Cancel Payment
 */
$session = et_read_session();
if( !empty( $session['order_id'] ) ) :
    get_header();
    ?>
    <!-- Page Blog -->
    <section id="blog-page">
        <div class="container page-container">
            <!-- block control  -->
            <div class="row block-page">
                <div class="blog-content info-payment-method">
                    <h1 class="title"><?php _e('Order Cancelled', 'enginethemes'); ?></h1>

                    <p class="sub-title"><?php _e('It seems that you are busy at this moment, so order it again when you’re free.', 'enginethemes'); ?></p>

                    <div class="link-detail-method">
                        <a href="" class="<?php mje_button_classes( array() ); ?>"><?php _e('Back to homepage', 'enginethemes'); ?></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php
    et_destroy_session();
    get_footer();
else :
    wp_redirect( get_home_url() );
endif;