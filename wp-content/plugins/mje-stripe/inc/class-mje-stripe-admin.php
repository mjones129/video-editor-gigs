<?php
class MJE_Stripe_Admin extends AE_Base
{
    public static $instance;

    public static function get_instance() {
        if( self::get_instance() == null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        $this->add_filter( 'mje_payment_gateway_setting_sections', 'add_sections' );
    }

    /**
     * Add settings page for Stripe
     *
     * @param array $pages
     * @return array $pages
     * @since 1.0.0
     * @author Tat Thien
     */
    public function add_sections( $sections ) {
        $sections['mje-stripe'] = $this->get_general_section();
        return $sections;
    }

    /**
     * Generate general settings section
     *
     * @param void
     * @return array $sections
     * @since 1.1.4
     * @author Tat Thien
     */
    public function get_general_section () {
        $sections = array(
            'args' => array(
                'title' => __( 'Stripe', 'mje_stripe' ),
                'id' => 'mje-stripe',
                'class' => '',
                'icon' => '',
            ),
            'groups' => array(
                array(
                    'args' => array(
                        'title' => __( 'Stripe', 'mje_stripe' ),
                        'id' => '',
                        'class' => '',
                        'desc' => ''
                    ),
                    'fields' => array(
                        array(
                            'id' => 'mje-stripe-enable',
                            'class' => '',
                            'type' => 'switch',
                            'title' => __( 'Using Stripe', 'mje_stripe' ),
                            'desc' => __( 'Enabling this will activate Stripe payment gateway.', 'mje_stripe'),
                            'name' => 'mje_stripe_enable',
                        ),
                        /* API key */
                        array(
                            'id' => 'mje-stripe-api',
                            'class' => '',
                            'type' => 'combine',
                            'title' => __( 'Stripe API', 'mje_stripe' ),
                            'desc' => __( 'The Stripe API by providing one of your API keys in the request. Get API <a href="https://dashboard.stripe.com/account/apikeys" target="_blank">here</a>.', 'mje_stripe'),
                            'name' => '',
                            'children' => array(
                                array(
                                    'id' => 'mje-stripe-test-secret-key',
                                    'class' => 'test-key',
                                    'type' => 'text',
                                    'title' => __( 'Test secret key', 'mje_stripe' ),
                                    'desc' => '',
                                    'name' => 'mje_stripe_test_secret_key',
                                ),
                                array(
                                    'id' => 'mje-stripe-test-pub-key',
                                    'class' => 'test-key',
                                    'type' => 'text',
                                    'title' => __( 'Test publishable key', 'mje_stripe' ),
                                    'desc' => '',
                                    'name' => 'mje_stripe_test_pub_key',
                                ),
                                array(
                                    'id' => 'mje-stripe-live-secret-key',
                                    'class' => 'live-key',
                                    'type' => 'text',
                                    'title' => __( 'Live secret key', 'mje_stripe' ),
                                    'desc' => '',
                                    'name' => 'mje_stripe_live_secret_key',
                                ),
                                array(
                                    'id' => 'mje-stripe-live-pub-key',
                                    'class' => 'live-key',
                                    'type' => 'text',
                                    'title' => __( 'Live publishable key', 'mje_stripe' ),
                                    'desc' => '',
                                    'name' => 'mje_stripe_live_pub_key',
                                )
                            )
                        ),
                        array(
                            'id' => 'mje-stripe-production-mode',
                            'class' => '',
                            'type' => 'switch',
                            'title' => __( 'Production Mode', 'mje_stripe' ),
                            'desc' => __( 'Enabling this will allow you to use the minify version of CSS or Javascript.', 'mje_stripe'),
                            'name' => 'mje_stripe_production_mode',
                        )
                    )
                ),
            )
        );

        return $sections;
    }
}

new MJE_Stripe_Admin();