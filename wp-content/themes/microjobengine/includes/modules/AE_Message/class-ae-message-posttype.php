<?php
/**
 * AE message class
 */
class AE_AE_Message_Posttype extends MJE_Post
{
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
     * The constructor
     *
     * @param string $post_type
     * @param array $taxs
     * @param array $meta_data
     * @param array $localize
     * @return void void
     *
     * @since 1.0
     * @author Tambh
     */
    public  function __construct($post_type = '', $taxs = array(), $meta_data = array(), $localize = array()){
        $this->post_type = 'ae_message';

        parent::__construct( $this->post_type, $taxs, $meta_data, $localize);

        $this->meta = array(
            'et_carousels',
            'from_user',
            'to_user',
            'last_sender',
            'send_date',
            'last_date',
            'is_conversation',
            'post_id',
            'post_name',
            'conversation_status',
            'archive_on_sender',
            'archive_on_receiver',
            'receiver_latest_reply',
            'sender_latest_reply',
            'latest_reply',
            'receiver_unread',
            'sender_unread',
            'type',
            'action_type',
            'level'
        );
        $this->post_type_singular = 'Message';
        $this->post_type_regular = 'Messages';
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
                'name' => __("Message", 'enginethemes'),
                'singular_name' => __('Message', 'enginethemes'),
                'add_new' => __('Add New', 'enginethemes'),
                'add_new_item' => __('Add New Message', 'enginethemes'),
                'edit_item' => __('Edit Message', 'enginethemes'),
                'new_item' => __('New Message', 'enginethemes'),
                'all_items' => __('All Messages', 'enginethemes'),
                'view_item' => __('View Message', 'enginethemes'),
                'search_items' => __('Search Messages', 'enginethemes'),
                'not_found' => __('No Messages found', 'enginethemes'),
                'not_found_in_trash' => __('No Messages found in Trash', 'enginethemes'),
                'parent_item_colon' => '',
                'menu_name' => __('Messages', 'enginethemes')
            ),
            'hierarchical' => false,
            'menu_icon' => 'dashicons-testimonial',

            'exclude_from_search' => true,
            'capability_type' => 'ae_message',
            //'capabilities' => 'manage_options',
        );
        $this->register_posttype($args);
    }
}

add_action('init', 'initMessagePostType');
function initMessagePostType() {
    $instance = AE_AE_Message_Posttype::get_instance();
    $instance->init();
}