<?php
/**
 * Enqueue scripts for wp-admin
 * @param void
 * @return void
 * @since 1.1.1
 * @package MicrojobEngine
 * @category Admin
 * @author Tat Thien
 */
function mje_add_admin_scripts() {
    if(isset($_GET['page']) && strpos($_GET['page'], 'et-') !== false) {
        if( is_admin() && !is_customize_preview()) {
            wp_enqueue_style('mjob-admin-style', get_template_directory_uri() . '/assets/css/admin/mjob-admin.css');
            wp_enqueue_style('font-awesome-style', get_template_directory_uri() . '/assets/css/font-awesome.css');
            wp_enqueue_script('mjob-admin', get_template_directory_uri() . '/includes/modules/MJE_Admin/assets/mjob-admin.js', array(
                'jquery',
                'backbone',
                'underscore',
                'appengine'
            ), '', true);

            wp_localize_script('mjob-admin', 'mJobAdmin', array(
                'min_price_error' => __('Value must be less than maximum price.', 'enginethemes'),
                'max_price_error' => __('Value must be greater than minimum price.', 'enginethemes'),
            ));
        }
    }

    if(isset($_GET['page']) && $_GET['page'] == 'et-welcome') {
        if( is_admin() && !is_customize_preview()) {
            wp_enqueue_style('mjob-admin-welcome-style', get_template_directory_uri() . '/assets/css/admin/admin-welcome.css');
            wp_enqueue_style('font-awesome-style', get_template_directory_uri() . '/assets/css/font-awesome.css');
        }
    }
}

add_action('admin_enqueue_scripts', 'mje_add_admin_scripts');

/**
 * Filter global variable for js
 */
add_filter('ae_admin_globals', 'mje_admin_filter_globals');
function mje_admin_filter_globals($vars) {
    $vars['mjob_order_not_found_text'] = __('Sorry, no order found for your query.', 'enginethemes');
    $vars['withdraw_not_found_text'] = __('Sorry, no withdraw request found for your query.', 'enginethemes');
    $vars['alert_confirm_approve'] = __('Are you sure you want to approve this item?', 'enginethemes');
    $vars['alert_confirm_decline'] = __('Are you sure you want to decline this item?', 'enginethemes');
    $vars['confirm_user'] = __('Are you sure you want to confirm this member?', 'enginethemes');
    $vars['disable_plan_notice'] = __('This feature is only available when you disable the "Free to submit mJob" section', 'enginethemes');
    $vars['number_format_settings'] = mje_get_number_format_settings();
    return $vars;
}

/**
 * Hook scripts to admin head
 *
 * @since 1.3.2
 * @author Tat Thien
 */
add_action( 'admin_head', 'mje_admin_header_scripts' );
function mje_admin_header_scripts () {
    if( isset( $_GET['page'] ) && stripos( $_GET['page'], 'et-payment-gateways' ) !== false ) :
        ?>
        <style type="text/css">
            <?php if( ae_get_option( 'test_mode' ) ) : ?>
            .field-combine-live-key{
                display: none;
            }
            <?php else : ?>
            .field-combine-test-key {
                display: none;
            }
            <?php endif; ?>
        </style>
        <?php
    endif;
}