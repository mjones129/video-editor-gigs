<?php
class MJE_Stripe_Options
{
    public static $instance;
    public $options;

    public static function get_instance () {
        if( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct () {
        $this->options = get_option( 'et_options' );
    }

    /**
     * Get Stripe API key based on test mode or live mode
     *
     * @param void
     * @return array $stripe_api
     * @since 1.0.0
     * @author Tat Thien
     */
    public function get_api_key() {
        $stripe_api = array();
        $mode = 'live';
        if( $this->options['test_mode'] ) {
            $mode = 'test';
        }

        $stripe_api['pub_key'] = ! empty( $this->options['mje_stripe_' . $mode . '_pub_key'] ) ? $this->options['mje_stripe_' . $mode . '_pub_key'] : '';
        $stripe_api['secret_key'] = ! empty( $this->options['mje_stripe_' . $mode . '_secret_key'] ) ? $this->options['mje_stripe_' . $mode . '_secret_key'] : '';

        return $stripe_api;
    }
}