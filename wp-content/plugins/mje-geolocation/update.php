<?php

//require_once dirname(__FILE__) . '/inc/inc.update.php';
if (!class_exists('MJE_Geolocation_Update') && class_exists('AE_Plugin_Updater') ){
	class MJE_Geolocation_Update extends AE_Plugin_Updater{
		const VERSION = MJE_GEOLOCATION_VER;
		// setting up updater
		public function __construct(){
			$this->product_slug 	= plugin_basename( dirname(__FILE__) . '/mje-geolocation.php' );
			$this->slug 			= 'mje-geolocation';
			$this->license_key 		= get_option('et_license_key', '');
			$this->current_version 	= self::VERSION;
			$this->update_path 		= 'http://update.enginethemes.com/?do=product-update&product=mje-geolocation&type=plugin';
			parent::__construct();
		}
	}
	new MJE_Geolocation_Update();
}


?>