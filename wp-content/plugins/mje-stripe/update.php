<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if( class_exists( 'AE_Plugin_Updater') ) {
    class MJE_Stripe_Updater extends AE_Plugin_Updater
    {
        const VERSION = '1.1.1';
        public function __construct(){
            $this->product_slug 	= plugin_basename( dirname(__FILE__) . '/mje-stripe.php' );
            $this->slug 			= 'mje_stripe';
            $this->license_key 		= get_option( 'et_license_key', '' );
            $this->current_version 	= self::VERSION;
            $this->update_path 		= 'http://update.enginethemes.com/?do=product-update&product=mje_stripe&type=plugin';
            parent::__construct();
        }
    }

    new MJE_Stripe_Updater();
}