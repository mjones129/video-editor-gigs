<?php
class MJE_Package_Post_Type extends MJE_Post {
	public static $instance;
	/**
	 * Get instance method
	 */
	public static function get_instance() {
		if(!self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	/**
	 * The constructor of this class
	 */
	public function __construct($post_type = '', $taxs = array(), $meta_data = array(), $localize = array()) {
		$this->post_type = 'pack';
		parent::__construct($this->post_type, $taxs, $meta_data, $localize);
		$this->post_type_singular = 'Pack';
		$this->post_type_regular = 'Packs';
		$this->meta = array('et_price');
	}
	/**
	 * init function
	 *
	 * @param void
	 * @return void
	 * @since 1.0
	 * @package MicrojobEngine
	 * @category void
	 * @author JACK BUI
	 */
	public function init(){
		$args = array(
			'labels' => array(
				'name' => __("Pack", 'enginethemes'),
				'singular_name' => __('Pack', 'enginethemes'),
				'add_new' => __('Add New', 'enginethemes'),
				'add_new_item' => __('Add New Pack', 'enginethemes'),
				'edit_item' => __('Edit Pack', 'enginethemes'),
				'new_item' => __('New Pack', 'enginethemes'),
				'all_items' => __('All Packs', 'enginethemes'),
				'view_item' => __('View Pack', 'enginethemes'),
				'search_items' => __('Search Packs', 'enginethemes'),
				'not_found' => __('No Packs found', 'enginethemes'),
				'not_found_in_trash' => __('No Packs found in Trash', 'enginethemes'),
				'parent_item_colon' => '',
				'menu_name' => __('Packs', 'enginethemes')
			),
		);
		$this->register_posttype($args);
	}
}

add_action('init', 'init_package');
function init_package() {
	$new_instance = MJE_Package_Post_Type::get_instance();
	$new_instance->init();

	// New AE_Package
	$package = new AE_Package('pack',
		array(
			'sku',
			'et_price',
			'et_number_posts',
			'et_duration',
			'et_featured',
			'et_permanent'
		),
		array(
			'backend_text' => array(
				'text' => __('%s for %d day', 'enginethemes'),
				'data' => array(
					'et_price',
					'et_number_posts'
				)
			)
		)
	);
	$pack_action = new AE_PackAction($package);

	global $ae_post_factory;
	$ae_post_factory->set('pack', $package);
}

/**
 * Filter backend text
 * @param object $result
 * @return object $result
 * @since 1.0
 * @package MicrojobEngine
 * @category Authentication
 * @author Tat Thien
 */
if(!function_exists('mje_filter_package')) {
	function mje_filter_package($result) {
	    $result->et_price = absint($result->et_price);
        $result->et_number_posts = absint($result->et_number_posts);
        $result->et_duration = absint($result->et_duration);

        $result->package_price = mje_format_price($result->et_price);
        
        if($result->et_duration >= 0) {
			$result->package_duration = sprintf(__('%s days', 'enginethemes'), $result->et_duration);
		}
		if(isset($result->et_permanent) && $result->et_permanent == "1") {
			$result->package_duration = __('permanent sell', 'enginethemes');
		}

		return $result;
	}

	add_filter('ae_convert_pack', 'mje_filter_package');
	add_filter('ae_convert_after_insert_pack', 'mje_filter_package');
}