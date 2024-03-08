<?php
class MJE_Extra_Post_Type extends MJE_Post{
    public static $instance;
    /**
     * get_instance method
     *
     */
    public static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    /**
     * the constructor of this class
     *
     */
    public  function __construct($post_type = '', $taxs = array(), $meta_data = array(), $localize = array()){
        $this->post_type = 'mjob_extra';
        parent::__construct( $this->post_type, $taxs, $meta_data, $localize);
        $this->post_type_singular = 'Microjob Extra';
        $this->post_type_regular = 'Microjob Extras';
        $this->meta = array(
            'et_budget'
        );
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
                'name' => __("Microjob Extra", 'enginethemes'),
                'singular_name' => __('Microjob Extra', 'enginethemes'),
                'add_new' => __('Add New', 'enginethemes'),
                'add_new_item' => __('Add New Microjob Extra', 'enginethemes'),
                'edit_item' => __('Edit Microjob Extra', 'enginethemes'),
                'new_item' => __('New Microjob Extra', 'enginethemes'),
                'all_items' => __('All Microjob Extras', 'enginethemes'),
                'view_item' => __('View Microjob Extra', 'enginethemes'),
                'search_items' => __('Search Microjob Extras', 'enginethemes'),
                'not_found' => __('No Microjob Extras found', 'enginethemes'),
                'not_found_in_trash' => __('No Microjob Extras found in Trash', 'enginethemes'),
                'parent_item_colon' => '',
                'menu_name' => __('Microjob Extras', 'enginethemes')
            ),
            'show_in_menu' => false
        );
        $this->register_posttype($args);
    }
}

add_action( 'init', function() {
    $new_instance = MJE_Extra_Post_Type::get_instance();
    $new_instance->init();
} );