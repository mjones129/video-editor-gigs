<?php
class MJE_Notification_Post_Type extends MJE_Post
{
    public static $instance;

    public static function get_instance()
    {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor of class
     * @param string $post_type
     * @param array $taxs
     * @param array $meta_data
     * @param array $localize
     */
    public function __construct( $post_type = '', $taxs = array(), $meta_data = array(), $localize = array() )
    {
        $this->post_type = 'mje_notification';
        parent::__construct( $this->post_type, $taxs, $meta_data, $localize );
    }

    /**
     * Init post type
     * @param void
     * @return void
     * @since 1.3
     * @author Tat Thien
     */
    public function init_post_type ()
    {
        $args = array(
            'labels' => array(
                'name' => __( 'Notification', 'enginethemes'),
                'singular_name' => __( 'Notifications', 'enginethemes' ),
                'add_new' => __( 'Add New', 'enginethemes' ),
                'add_new_item' => __( 'Add New Notification', 'enginethemes' ),
                'edit_item' => __( 'Edit Notification', 'enginethemes' ),
                'new_item' => __( 'New Notification', 'enginethemes' ),
                'all_items' => __( 'All Notifications', 'enginethemes' ),
                'view_item' => __( 'View Notification', 'enginethemes' ),
                'search_items' => __( 'Search Notifications', 'enginethemes' ),
                'not_found' => __( 'No Notifications found', 'enginethemes' ),
                'not_found_in_trash' => __( 'No Notifications found in Trash', 'enginethemes' ),
                'parent_item_colon' => '',
                'menu_name' => __( 'Notifications', 'enginethemes' )
            ),
            'show_ui' => false,
            'show_in_menu' => false,
            'menu_icon' => 'dashicons-megaphone'
        );
        $this->register_posttype($args);

        // Post status: Unread
        register_post_status( 'unread', array(
            'label'                     => __( 'Unread', 'enginethemes' ),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'Unread <span class="count">(%s)</span>', 'Unread <span class="count">(%s)</span>' ),
        ) );

        // Post status: Read
        register_post_status( 'read', array(
            'label'                     => __( 'Read', 'enginethemes' ),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'Read <span class="count">(%s)</span>', 'Read <span class="count">(%s)</span>' ),
        ) );

        // Post status: Hide
        register_post_status( 'hide', array(
            'label'                     => __( 'Hide', 'enginethemes' ),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop( 'Hide <span class="count">(%s)</span>', 'Hide <span class="count">(%s)</span>' ),
        ) );
    }
}

add_action( 'init', function() {
    $new_instance = MJE_Notification_Post_Type::get_instance();
    $new_instance->init_post_type();
} );