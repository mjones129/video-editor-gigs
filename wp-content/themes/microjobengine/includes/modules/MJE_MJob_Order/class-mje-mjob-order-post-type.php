<?php
class MJE_Mjob_Order_Post_Type extends MJE_Post{
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
        $this->post_type = 'mjob_order';
        parent::__construct( $this->post_type, $taxs, $meta_data, $localize);
        $this->meta = array(
            'extra_ids',
            'amount',
            'real_amount',
            'currency',
            'payment_type',
            'paid',
            'order_delivery',
            'seller_id',
            'buyer_id',
            'reject_message',
            'order_delivery_day',
            'mjob_order_detail',
            'extra_info',
            'fee_commission',
            'et_order_currency',
            'et_invoice_no'
        );
        $this->post_type_singular = 'Microjob Order';
        $this->post_type_regular = 'Microjob Orders';
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
                'name' => __("Microjob Order", 'enginethemes'),
                'singular_name' => __('Microjob Order', 'enginethemes'),
                'add_new' => __('Add New', 'enginethemes'),
                'add_new_item' => __('Add New Microjob Order', 'enginethemes'),
                'edit_item' => __('Edit Microjob Order', 'enginethemes'),
                'new_item' => __('New Microjob Order', 'enginethemes'),
                'all_items' => __('All Microjob Orders', 'enginethemes'),
                'view_item' => __('View Microjob Order', 'enginethemes'),
                'search_items' => __('Search Microjob Orders', 'enginethemes'),
                'not_found' => __('No Microjob Orders found', 'enginethemes'),
                'not_found_in_trash' => __('No Microjob Orders found in Trash', 'enginethemes'),
                'parent_item_colon' => '',
                'menu_name' => __('Microjob Orders', 'enginethemes')
            ),
            'hierarchical' => false,
            'show_in_menu' => false,
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

add_action('init', 'init_mjob_order_post_type');
function init_mjob_order_post_type() {
    $new_instance = MJE_Mjob_Order_Post_Type::get_instance();
    $new_instance->init();
}