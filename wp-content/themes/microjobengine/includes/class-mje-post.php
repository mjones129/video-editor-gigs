<?php
class MJE_Post extends AE_Posts
{
    public static $instance;
    public $post_type;
    public $post_type_singular;
    public $post_type_regular;

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
     * @author Jack Bui
     */
    public function __construct($post_type = '', $taxs = array(), $meta_data = array(), $localize = array())
    {

        parent::__construct( $post_type, $taxs, $meta_data, $localize );

    }
    /**
     * register post type
     *
     * @param array $argss
     * @return void
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    public function register_posttype($args = array())
    {
        $default = array(
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array(
                'slug' => ae_get_option($this->post_type.'_slug', $this->post_type)
            ) ,
            'capability_type' => 'post',
            // 'capabilities' => array(
            //     'manage_options'
            // ) ,
            'has_archive' => ae_get_option($this->post_type.'_archive', $this->post_type),
            'hierarchical' => true,
            'menu_position' => null,
            'supports' => array(
                'title',
                'editor',
                'author',
                'custom-fields',
                'thumbnail',
                'excerpt',
                'comments'
            )
        );
        $args = wp_parse_args($args, $default);
        if( in_array( $this->post_type, array('ae_message')  ) ){
            $args['capabilities'] = array('manage_options');
        }

        register_post_type($this->post_type, $args);
        flush_rewrite_rules();
        global $ae_post_factory;
        $ae_post_factory->set( $this->post_type, new MJE_Post( $this->post_type, $this->taxs, $this->meta ) );
    }
    /**
     * Register taxonomy for post
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function register_taxonomy($tax = '', $post_type = array(), $labels = array(), $args = array()){
        $args = wp_parse_args( $args, array(
            'labels' => $labels,
            'public' => true,
            'show_in_nav_menus' => true,
            'show_admin_column' => true,
            'hierarchical' => true,
            'show_tagcloud' => true,
            'show_ui' => true,
            'query_var' => true,
            'rewrite' => array(
                'slug' => ae_get_option($tax.'_slug', $tax) ,
                'hierarchical' => ae_get_option($tax.'_hierarchical', false)
            ) ,
            'capabilities' => array(
                'manage_terms',
                'edit_terms',
                'delete_terms',
                'assign_terms'
            )
        ));
        register_taxonomy($tax, $post_type , $args);
    }

    /**
     * Insert a post
     *
     * @param array $args
     * @return object $result
     * @since 1.1.4
     * @author Tat Thien
     */
    public function insert($args) {
        global $current_user, $user_ID;

        // strip tags
        foreach ($args as $key => $value) {
            if ((in_array($key, $this->meta) || in_array($key, $this->convert)) && is_string($args[$key]) && $key != 'post_content') {
                $args[$key] = strip_tags($args[$key]);
            }
        }

        // pre filter filter post args
        $args = apply_filters('ae_pre_insert_' . $this->post_type, $args);
        if (is_wp_error($args)) return $args;

        $args = wp_parse_args($args, array(
            'post_type' => $this->post_type
        ));

        // Only use for mjob_post
        if( $this->post_type == 'mjob_post' ) {
            /*if admin disable plan set status to pending or publish*/
            $pending = ae_get_option('use_pending', false);
            $pending = apply_filters( 'use_pending', $pending, $this->post_type );
            $disable_plan = ae_get_option('disable_plan', false);

            /*if admin disable plan set status to pending or publish*/
            if ($disable_plan) {
                // Change Status Publish places that posted by Admin
                if(is_super_admin()){
                    // Publish post
                    $args['post_status'] = 'publish';
                } else {
                    // disable plan
                    if ($pending) {
                        // pending post
                        $args['post_status'] = 'pending';
                    } else {
                        // disable pending post
                        $args['post_status'] = 'publish';
                    }
                }
            }
        }

        if (!isset($args['post_status'])) {
            $args['post_status'] = 'draft';
        }

        // could not create with an ID
        if (isset($args['ID'])) {
            return new WP_Error('invalid_data', __("The ID already existed!", 'enginethemes'));
        }

        if (!isset($args['post_author']) || empty($args['post_author'])) $args['post_author'] = $current_user->ID;

        if ( empty( $args['post_author'] ) ) return new WP_Error('missing_author', __('You must login to submit listing.', 'enginethemes'));

        // filter tax_input
        $args = $this->_filter_tax_input($args);
        if(isset($args['post_content'])){
            // filter post content strip invalid tag
            $args['post_content'] = $this->filter_content($args['post_content']);
        }
        /**
         * insert post by wordpress function
         */
        $result = wp_insert_post($args, true);

        /**
         * update custom field and tax
         */
        if ($result != false && !is_wp_error($result)) {
            $this->update_custom_field($result, $args);
            $args['ID'] = $result;
            $args['id'] = $result;

            /**
             * do action ae_insert_{$this->post_type}
             * @param Int $result Inserted post ID
             * @param Array $args The array of post data
             */
            do_action('ae_insert_' . $this->post_type, $result, $args);

            $result = (object)$args;

            /**
             * do action ae_insert_post
             * @param object $args The object of post data
             */
            do_action('ae_insert_post', $result);

            // localize text for js
            if (!empty($this->localize)) {
                foreach ($this->localize as $key => $localize) {
                    $a = array();
                    foreach ($localize['data'] as $loc) {
                        array_push($a, $result->$loc);
                    }

                    $result->$key = vsprintf($localize['text'], $a);
                }
            }
            $result->permalink = get_permalink($result->ID);

            if (current_user_can('manage_options') || $result->post_author == $user_ID) {

                /**
                 * featured image not null and should be in carousels array data
                 */
                if (isset($args['featured_image'])) {
                    set_post_thumbnail($result->ID, $args['featured_image']);
                }
            }
        }

        $result =  apply_filters('ae_convert_after_insert_' . $this->post_type, $result);
        return $result;
    }
}
