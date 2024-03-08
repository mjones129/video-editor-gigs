<?php
/*
Plugin Name: MjE Stripe
Plugin URI: http://enginethemes.com/
Description: Integrates the Stripe payment gateway to your MicrojobEngine site.
Version: 1.2
Author: EngineThemes
Author URI: http://enginethemes.com/
License: A "Slug" license name e.g. GPL2
Text Domain: mje_stripe
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

define( 'MJE_STRIPE_URL', plugin_dir_url( __FILE__ ) );
define( 'MJE_STRIPE_PATH', plugin_dir_path( __FILE__ ) );
define( 'MJE_STRIPE_VERSION', '1.2'.rand());

if( !function_exists( 'mje_stripe_require_plugin_files' ) ) {
    /**
     * Require necessary files
     *
     * @param void
     * @return void
     * @since 1.0.0.0
     * @author Tat Thien
     */
    function mje_stripe_require_plugin_files() {
        require_once('vendor/autoload.php');
        require_once dirname( __FILE__ ) . '/update.php';
        require_once dirname( __FILE__ ) . '/functions.php';
        require_once dirname( __FILE__ ) . '/template.php';
        require_once dirname( __FILE__ ) . '/inc/index.php';
    }
    add_action( 'after_setup_theme', 'mje_stripe_require_plugin_files' );
}

if( !function_exists( 'mje_stripe_enqueue_scripts' ) ) {
    /**
     * Enqueue Stripe scripts and styles
     * @param void
     * @return void
     * @since 1.0
     * @author Tat Thien
     */
    function mje_stripe_enqueue_scripts() {
        $prefix = '';
        if( ae_get_option( 'mje_stripe_production_mode') ) {
            $prefix = '.min';
        }
        wp_enqueue_style( 'mje-stripe', MJE_STRIPE_URL . 'assets/css/mje-stripe' . $prefix . '.css', array(), MJE_STRIPE_VERSION );

        wp_enqueue_script( 'stripe-v2', 'https://js.stripe.com/v2/' );
        wp_enqueue_script( 'stripe-v3', 'https://js.stripe.com/v3/' );
        wp_enqueue_script( 'mje-stripe-js', MJE_STRIPE_URL . 'assets/js/mje-stripe' . $prefix . '.js', array(
            'underscore',
            'backbone',
            'appengine'
        ), MJE_STRIPE_VERSION, true);

        // Generate localize script
        $stripe_options = MJE_Stripe_Options::get_instance();
        $api_key = $stripe_options->get_api_key();
        wp_localize_script( 'mje-stripe-js', 'mje_stripe', array(
            'publishable_key' => $api_key['pub_key'],
            'is_has_api_key' => MJE_Stripe::is_has_api_key(),
            'is_postMjob' => is_page_template('page-post-service.php')  ? 1: FALSE,
        ) );
    }

    add_action( 'wp_enqueue_scripts', 'mje_stripe_enqueue_scripts' );
}

if( !function_exists( 'mje_stripe_header_scripts' ) ) {
    /**
     * Hook scripts to head
     *
     * @param void
     * @return void
     * @since 1.0.0
     * @author Tat Thien
     */
    function mje_stripe_header_scripts() {
        // Add customize css
        $primary_color = MJE_Skin_Action::get_skin_name() . '_primary_color';
        ?>
        <style type="text/css" id="mje-stripe-customize">
            <?php
                AE_Customizer::ae_customize_generate_css(
                '#stripe_checkout_modal .card-expiration .form-label.active'
                , 'border-bottom-color', $primary_color );
            ?>
        </style>
        <?php
    }

    add_action( 'wp_head', 'mje_stripe_header_scripts' );
}

if( ! function_exists( 'mje_stripe_admin_header_scripts' ) ) {
    /**
     * Hook scripts to admin head
     *
     * @param void
     * @return void
     * @since 1.0.0
     * @author Tat Thien
     */
    function mje_stripe_admin_header_scripts () {
        if( isset( $_GET['page'] ) && stripos( $_GET['page'], 'et-mje-stripe' ) !== false ) :
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

    add_action( 'admin_head', 'mje_stripe_admin_header_scripts' );
}

if( ! function_exists( 'mje_stripe_load_translations' ) ) {
    function mje_stripe_load_translations() {
        load_plugin_textdomain( 'mje_stripe', false,  dirname( plugin_basename( __FILE__ ) ). '/languages' );
    }
    add_action( 'init', 'mje_stripe_load_translations' );
}