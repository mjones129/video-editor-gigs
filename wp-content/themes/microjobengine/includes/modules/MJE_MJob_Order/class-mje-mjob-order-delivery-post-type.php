<?php
class MJE_MJob_Order_Delivery_Post_Type extends MJE_Post{
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
        $this->post_type = 'order_delivery';
        parent::__construct( $this->post_type, $taxs, $meta_data, $localize);
        $this->meta = array(
            'et_carousels'
        );
        $this->post_type_singular = 'Order Delivery';
        $this->post_type_regular = 'Order Deliveries';
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
                'name' => __("Order Delivery", 'enginethemes'),
                'singular_name' => __('Order Delivery', 'enginethemes'),
                'add_new' => __('Add New', 'enginethemes'),
                'add_new_item' => __('Add New Order Delivery', 'enginethemes'),
                'edit_item' => __('Edit Order Delivery', 'enginethemes'),
                'new_item' => __('New Order Delivery', 'enginethemes'),
                'all_items' => __('All Order Deliveries', 'enginethemes'),
                'view_item' => __('View Order Delivery', 'enginethemes'),
                'search_items' => __('Search Order Deliveries', 'enginethemes'),
                'not_found' => __('No Order Deliveries found', 'enginethemes'),
                'not_found_in_trash' => __('No Order Deliveries found in Trash', 'enginethemes'),
                'parent_item_colon' => '',
                'menu_name' => __('Order Deliveries', 'enginethemes')
            ),
            'hierarchical' => false,
            'show_in_menu' => false
            );
        $this->register_posttype($args);
    }
    /**
      * override convert function
      *
      * @param object $post_data
      * @param string $thumbnail
      * @param boolean $excerpt
      * @param boolean $singular
      * @return object $data
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function convert( $post_data, $thumbnail = 'thumbnail', $excerpt = TRUE, $singular = FALSE ) {
        $data = parent::convert($post_data, $thumbnail, $excerpt, $singular);
        return $data;
    }
}

add_action('init', 'init_mjob_order_delivery_post_type');
function init_mjob_order_delivery_post_type() {
    $new_instance = MJE_MJob_Order_Delivery_Post_Type::get_instance();
    $new_instance->init();
}
?>