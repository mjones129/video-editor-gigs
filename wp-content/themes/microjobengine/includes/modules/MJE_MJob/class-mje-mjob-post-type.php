<?php
class MJE_MJob_Post_Type extends MJE_Post {
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
        $this->post_type = 'mjob_post';
        parent::__construct( $this->post_type, $taxs, $meta_data, $localize);
        $meta_fields = array(
            'time_delivery',
            'et_payment_package',
            'et_price',
            'et_budget',
            'rating_score',
            'et_carousels',
            'modified_date',
            'opening_message',
            'et_total_sales',
            'view_count',
			      'video_meta'
        );
        /**
         * @since  1.3.10(mje_geolocation);
        */
        $this->meta = apply_filters('mjob_post_meta_fields', $meta_fields);

        $this->post_type_singular = 'Microjob';
        $this->post_type_regular = 'Microjobs';


        $this->taxs = array(
                'mjob_category',
                'skill'
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
        /**
         * Register micrjobs post type
         */
        $args = array(
            'labels' => array(
                'name' => __("Microjob", 'enginethemes'),
                'singular_name' => __('Microjobs', 'enginethemes'),
                'add_new' => __('Add New', 'enginethemes'),
                'add_new_item' => __('Add New Microjob', 'enginethemes'),
                'edit_item' => __('Edit Microjob', 'enginethemes'),
                'new_item' => __('New Microjob', 'enginethemes'),
                'all_items' => __('All Microjobs', 'enginethemes'),
                'view_item' => __('View Microjob', 'enginethemes'),
                'search_items' => __('Search Microjobs', 'enginethemes'),
                'not_found' => __('No Microjobs found', 'enginethemes'),
                'not_found_in_trash' => __('No Microjobs found in Trash', 'enginethemes'),
                'parent_item_colon' => '',
                'menu_name' => __('Microjobs', 'enginethemes')
            ),
            'menu_icon' => 'dashicons-carrot'
        );
        $this->register_posttype($args);

        /**
         * Register mjob category
         */
        $tax = 'mjob_category';
        $mjob_category_labels = array(
            'name' => __('Microjob Categories', 'enginethemes') ,
            'singular_name' => __('Microjob Category', 'enginethemes') ,
            'search_items' => __('Search Microjob Categories', 'enginethemes') ,
            'popular_items' => __('Popular Microjob Categories', 'enginethemes') ,
            'all_items' => __('All Microjob Categories', 'enginethemes') ,
            'parent_item' => __('Parent Microjob Category', 'enginethemes') ,
            'parent_item_colon' => __('Parent Microjob Category', 'enginethemes') ,
            'edit_item' => __('Edit Microjob Category', 'enginethemes') ,
            'update_item' => __('Update Microjob Category', 'enginethemes') ,
            'add_new_item' => __('Add New Microjob Category', 'enginethemes') ,
            'new_item_name' => __('New Microjob Category Name', 'enginethemes'),
            'add_or_remove_items' => __('Add or remove Microjob Categories', 'enginethemes'),
            'choose_from_most_used' => __('Choose from most used enginetheme', 'enginethemes') ,
            'menu_name' => __('Categories', 'enginethemes') ,
        );
        $this->register_taxonomy($tax, array($this->post_type), $mjob_category_labels);

        /**
         * Register mjob tag
         */
        $status = false;
        $switch_skill = ae_get_option('switch_skill');
        if($switch_skill){
            $status = true;
        }
        $tax = 'skill';
        $mjob_tag_labels = array(
            'name' => __('Tags', 'enginethemes') ,
            'singular_name' => __('Tag', 'enginethemes') ,
            'search_items' => __('Search Tags', 'enginethemes') ,
            'popular_items' => __('Popular Tags', 'enginethemes') ,
            'all_items' => __('All Tags', 'enginethemes') ,
            'parent_item' => __('Parent Tag', 'enginethemes') ,
            'parent_item_colon' => __('Parent Tag', 'enginethemes') ,
            'edit_item' => __('Edit Tag', 'enginethemes') ,
            'update_item' => __('Update Tag', 'enginethemes') ,
            'add_new_item' => __('Add New Tag', 'enginethemes') ,
            'new_item_name' => __('New Tag Name', 'enginethemes'),
            'add_or_remove_items' => __('Add or remove Tags', 'enginethemes'),
            'choose_from_most_used' => __('Choose from most used enginetheme', 'enginethemes') ,
            'menu_name' => __('Tags', 'enginethemes') ,
        );
        $args = array(
            'hierarchical'=> $status,
        );
        $this->register_taxonomy($tax, array( $this->post_type ), $mjob_tag_labels, $args);

        // add since 1.3.10
        $custom_tax = (object) apply_filters('custom_new_tax', array('has_new_custom_tax'=> false,'taxt'=>'', 'label' =>'','args' => array() ) );;

        if($custom_tax->has_new_custom_tax){
          $this->register_taxonomy($custom_tax->tax, array( $this->post_type ), $custom_tax->label, $custom_tax->args);
        }
    }
    /**
      * override convert function
      *
      * @param object $post_data
     * @param string $thumbnail
     * @param boolean $excerpt
     * @param boolean $singular
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function convert( $post_data, $thumbnail = 'thumbnail', $excerpt = TRUE, $singular = FALSE ) {
        $data = parent::convert($post_data, $thumbnail, $excerpt, $singular);
        $data->post_content = $data->unfiltered_content;
        return $data;
    }
}

add_action('init', function() {
    $new_instance = MJE_MJob_Post_Type::get_instance();
    $new_instance->init();
});