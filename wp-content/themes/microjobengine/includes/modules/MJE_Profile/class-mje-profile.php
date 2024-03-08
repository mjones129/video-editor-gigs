<?php
class MJE_Profile extends MJE_Post{
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
        $this->post_type = 'mjob_profile';
        parent::__construct( $this->post_type, $taxs, $meta_data, $localize);
        $this->meta = array(
            'rating_score',
            'payment_info',
            'billing_full_name',
            'billing_full_address',
            'billing_country',
            'billing_vat',
            'status',
            'time_delivery',
            'profile_description'
        );
        $this->post_type_singular = 'Profile';
        $this->post_type_regular = 'Profiles';

        $this->taxs = array(
            'country',
            'language'
        );
    }
    /**
     * init function
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Profile
     * @author Tat Thien
     */
    public function init(){
        $args = array(
            'labels' => array(
                'name' => __("Profile", 'enginethemes'),
                'singular_name' => __('Profile', 'enginethemes'),
                'add_new' => __('Add New', 'enginethemes'),
                'add_new_item' => __('Add New Profile', 'enginethemes'),
                'edit_item' => __('Edit Profile', 'enginethemes'),
                'new_item' => __('New Profile', 'enginethemes'),
                'all_items' => __('All Profiles', 'enginethemes'),
                'view_item' => __('View Profile', 'enginethemes'),
                'search_items' => __('Search Profiles', 'enginethemes'),
                'not_found' => __('No Profiles found', 'enginethemes'),
                'not_found_in_trash' => __('No Profiles found in Trash', 'enginethemes'),
                'parent_item_colon' => '',
                'menu_name' => __('Profiles', 'enginethemes')
            ),
            'menu_icon' => 'dashicons-id-alt',
            'supports' => array('title','editor'),
        );
        $this->register_posttype($args);

        /**
         * Language taxonomy
         */
        $tax = 'language';
        $language_labels = array(
            'name' => __('Languages', 'enginethemes') ,
            'singular_name' => __('Language', 'enginethemes') ,
            'search_items' => __('Search Languages', 'enginethemes') ,
            'popular_items' => __('Popular Languages', 'enginethemes') ,
            'all_items' => __('All Languages', 'enginethemes') ,
            'parent_item' => __('Parent Language', 'enginethemes') ,
            'parent_item_colon' => __('Parent Language', 'enginethemes') ,
            'edit_item' => __('Edit Language', 'enginethemes') ,
            'update_item' => __('Update Language', 'enginethemes') ,
            'add_new_item' => __('Add New Language', 'enginethemes') ,
            'new_item_name' => __('New Language Name', 'enginethemes'),
            'add_or_remove_items' => __('Add or remove Languages', 'enginethemes'),
            'choose_from_most_used' => __('Choose from most used enginetheme', 'enginethemes') ,
            'menu_name' => __('Languages', 'enginethemes') ,
        );
        $args = array('hierarchical'=> true);
        $this->register_taxonomy($tax, array( $this->post_type ), $language_labels, $args);

        // Country
        $tax = 'country';
        $country_labels = array(
            'name' => __('Countries', 'enginethemes') ,
            'singular_name' => __('Country', 'enginethemes') ,
            'search_items' => __('Search Countries', 'enginethemes') ,
            'popular_items' => __('Popular Countries', 'enginethemes') ,
            'all_items' => __('All Countries', 'enginethemes') ,
            'parent_item' => __('Parent Country', 'enginethemes') ,
            'parent_item_colon' => __('Parent Country', 'enginethemes') ,
            'edit_item' => __('Edit Country', 'enginethemes') ,
            'update_item' => __('Update Country', 'enginethemes') ,
            'add_new_item' => __('Add New Country', 'enginethemes') ,
            'new_item_name' => __('New Country Name', 'enginethemes'),
            'add_or_remove_items' => __('Add or remove Countries', 'enginethemes'),
            'choose_from_most_used' => __('Choose from most used enginetheme', 'enginethemes') ,
            'menu_name' => __('Countries', 'enginethemes') ,
        );
        $args = array('hierarchical'=> true);
        $this->register_taxonomy($tax, array( $this->post_type ), $country_labels, $args);

    }
    /**
     * override convert function
     *
     * @param object $post_data
     * @param string $thumbnail
     * @param boolean $excerpt
     * @param boolean $singular
     * @return object
     * @since 1.0
     * @package MicrojobEngine
     * @category Profile
     * @author Tat Thien
     */
    public function convert( $post_data, $thumbnail = 'thumbnail', $excerpt = TRUE, $singular = FALSE ) {
        $data = parent::convert($post_data, $thumbnail, $excerpt, $singular);
        $data->post_content = $data->unfiltered_content;
        return $data;
    }
}

$new_instance = MJE_Profile::get_instance();
$new_instance->init();