<?php
class ET_Microjobengine extends AE_Base
{
    function __construct() {
        $timezone = get_option('timezone_string');
        if(!empty($timezone)) {
            // Set default timezone
            date_default_timezone_set($timezone);
        }

        // disable admin bar if user can not manage options
        if (!current_user_can('manage_options') || et_load_mobile()) {
            show_admin_bar(false);
        };
        $this->add_action('init', 'theme_init');
        register_nav_menu('et_header_standard', __("Standard Header menu", 'enginethemes'));
        register_nav_menu('et_footer_social', __("Social menu in Footer", 'enginethemes'));

        $this->add_action( 'widgets_init', 'register_sidebar_widget' );

        /**
         * add query vars
         */
        $this->add_filter('query_vars', 'add_query_vars');

        /**
         * enqueue front end scripts
         */
        $this->add_action('wp_enqueue_scripts', 'on_add_scripts', 9);
        $this->add_action('admin_enqueue_scripts', 'mJobAdminScripts');

        /**
         * enqueue front end styles
         */
        $this->add_action('wp_print_styles', 'on_add_styles', 10);

        /**
         * Filer query pre get post.
         */
        $this->add_action('pre_get_posts', 'pre_get_posts', 10);

        $this->add_filter('posts_orderby', 'order_by_post_status', 10, 2);

        /**
         * call new classes in footer
         */
        $this->add_action('wp_footer', 'script_in_footer', 100);
        $this->add_action('wp_footer', 'mJobOverrideValidatorError', 102);

        /**
         * add return url for user after register
         */
        $this->add_filter('ae_after_insert_user', 'filter_link_redirect_register');

        /**
         * add return url for user after login
         */
        $this->add_filter('ae_after_login_user', 'filter_link_redirect_login');

        /**
         * check role for user when register
         */
        $this->add_filter('ae_convert_post', 'add_new_post_fields');

        /**
         * add users custom fields
         */
        $this->add_filter('ae_define_user_meta', 'add_user_meta_fields');

        /**
         * restrict pages
         */
        $this->add_action('template_redirect', 'restrict_pages');

        /**
         * redirect user to home after logout
         */
        $this->add_filter('logout_url', 'logout_home', 10, 2);

        /**
         * filter profile link and replace by author post link
         */
        $this->add_filter('post_type_link', 'post_link', 10, 2);

        /**
         * add comment type filter dropdow
         */
        $this->add_filter('admin_comment_types_dropdown', 'admin_comment_types_dropdown');

        /**
         * add action admin menu prevent seller enter admin area
         */
        $this->add_action('admin_menu', 'redirect_seller');
        //$this->add_action('login_init', 'redirect_login'); // disable from verson 1.3.5.1
         /**
         * Add action to check view count
         */
        $this->add_action("before_single_place", "may_increase_view_count");
        /**
         * add theme support
         */
        add_theme_support('automatic-feed-links');

        $this->add_filter('ae_globals', 'mJobGlobals');
        $this->add_filter('use_pending', 'mjob_user_pending', 10, 2);

        /**
         * allow user to upload a video file
         * @author tam
         */
        $this->add_filter('upload_mimes', 'mjob_add_mime_types');
        $this->add_filter('et_upload_file_upload_mimes', 'mjob_add_mime_types');
        $this->add_filter('ae_is_mobile', 'disableMobileVersion');

        /**
         * Filter price decimal
         */
        $this->add_filter( 'ae_price_decimal', 'filter_price_decimal' );

        new MJE_Review_Action();

        /**
         * init place meta post
         */
        new AE_Schedule('mjob_post');

        /**
         * init microjob order schedule
         */
        new mJobOrderSchedule(30);

        /**
         * Add image size
         */
        add_image_size("medium_post_thumbnail", 265, 160, true);
        add_image_size("mjob_detail_slider", 768, 435, true);
        add_image_size("mje_category_thumbnail", 255, 255, true);

        /**
         * Redirect to Welcome page after activate theme
         */
        global $pagenow;
        if ( is_admin() && 'themes.php' == $pagenow && isset( $_GET['activated'] ) ) {
            wp_redirect(admin_url("admin.php?page=et-welcome"));
        }
        $this->add_ajax('ae_update_subscriber','ae_update_subscriber');
        $this->add_ajax( 'fre_get_skills', 'fre_get_skills' ); //v1.3.9.8
    }

    /**
     * init theme
     * @since 1.0
     * @author Dakachi
     */
    function theme_init() {
        // register a post status: Reject (use when a project was rejected)
         register_post_status('reject', array(
             'label' => __('Reject', 'enginethemes') ,
             'private' => true,
             'public' => false,
             'exclude_from_search' => false,
             'show_in_admin_all_list' => true,
             'show_in_admin_status_list' => true,
             'label_count' => _n_noop('Reject <span class="count">(%s)</span>', 'Reject <span class="count">(%s)</span>') ,
         ));

        /* a project after expired date will be changed to archive */
         register_post_status('archive', array(
             'label' => __('Archive', 'enginethemes') ,
             'private' => false,
             'public' => true,
             'exclude_from_search' => true,
             'show_in_admin_all_list' => true,
             'show_in_admin_status_list' => true,
             'label_count' => _n_noop('Archive <span class="count">(%s)</span>', 'Archive <span class="count">(%s)</span>') ,
         ));

        /* after finish a project, project and accepted bid will be changed to complete */
         register_post_status('finished', array(
             'label' => _x('finished', 'post') ,
             'public' => true,
             'exclude_from_search' => false,
             'show_in_admin_all_list' => true,
             'show_in_admin_status_list' => true,
             'label_count' => _n_noop('Finished <span class="count">(%s)</span>', 'Finished <span class="count">(%s)</span>') ,
         ));


        /**
         * when a project was accept a bid, it will be change to close
         */
         register_post_status('close', array(
             'label' => _x('close', 'post') ,
             'public' => true,
             'exclude_from_search' => false,
             'show_in_admin_all_list' => true,
             'show_in_admin_status_list' => true,
             'label_count' => _n_noop('Close <span class="count">(%s)</span>', 'Close <span class="count">(%s)</span>') ,
         ));

        /**
         * when employer close project or freelancer quit a project, it change to disputing
         */
         register_post_status('disputing', array(
             'label' => _x('disputing', 'post') ,
             'public' => true,
             'exclude_from_search' => false,
             'show_in_admin_all_list' => true,
             'show_in_admin_status_list' => true,
             'label_count' => _n_noop('Disputing <span class="count">(%s)</span>', 'Disputing <span class="count">(%s)</span>') ,
         ));

        /**
         * when admin resolve a disputing project, it's status change to disputed
         */
         register_post_status('disputed', array(
             'label' => _x('disputed', 'post') ,
             'public' => true,
             'exclude_from_search' => false,
             'show_in_admin_all_list' => true,
             'show_in_admin_status_list' => true,
             'label_count' => _n_noop('Resolved <span class="count">(%s)</span>', 'Resolved <span class="count">(%s)</span>') ,
         ));

        /**
         * when a user dont want employer hide/contact him,
         * he can change his profile to hide, so no one can contact him
         */
        register_post_status('pause', array(
            'label' => __('Pause', 'enginethemes') ,
            'private' => false,
            'public' => true,
            'exclude_from_search' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Pause <span class="count">(%s)</span>', 'Pause <span class="count">(%s)</span>') ,
        ));
        /**
         * when a user dont want employer hide/contact him,
         * he can change his profile to hide, so no one can contact him
         */
        register_post_status('unpause', array(
            'label' => __('Active', 'enginethemes') ,
            'private' => false,
             'public' => true,
             'exclude_from_search' => false,
             'show_in_admin_all_list' => true,
             'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Active <span class="count">(%s)</span>', 'Active <span class="count">(%s)</span>') ,
        ));
        /**
         * when a user dont want employer hide/contact him,
         * he can change his profile to hide, so no one can contact him
         */
        register_post_status('late', array(
            'label' => __('Late', 'enginethemes') ,
            'private'=> false,
            'public' => true,
            'exclude_from_search' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Late <span class="count">(%s)</span>', 'Late <span class="count">(%s)</span>') ,
        ));
        register_post_status('delivery', array(
            'label' => __('Delivered', 'enginethemes') ,
            'private'=> false,
            'public' => true,
            'exclude_from_search' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Delivered <span class="count">(%s)</span>', 'Delivered <span class="count">(%s)</span>') ,
        ));
        register_post_status('finish', array(
            'label' => __('Finished', 'enginethemes') ,
            'private'=> false,
            'public' => true,
            'exclude_from_search' => true,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Finished <span class="count">(%s)</span>', 'Finished <span class="count">(%s)</span>') ,
        ));
        /**
         * set up social login
         */
        if(function_exists('init_social_login')){
            init_social_login();
        };
        /**
         * override author link
         */
        global $wp_rewrite;
        if ($wp_rewrite->using_permalinks()) {
            $wp_rewrite->author_base = ae_get_option('author_base', 'author');
            $wp_rewrite->author_structure = '/' . $wp_rewrite->author_base . '/%author%';
        }

        // Remove action
        global $et_appengine;
        remove_action('et_cash_checkout', 'ae_cash_message', 10);
        remove_action('ae_process_payment_action', array($et_appengine, 'notify_admin'), 10);
    }

    /**
     * filter redirect link after logout
     * @param string $logouturl
     * @param string $redir
     * @return string
     * @since 1.0
     * @author ThaiNt
     */
    public function logout_home($logouturl, $redir) {
        $redir = get_option('siteurl');
        return $logouturl . '&amp;redirect_to=' . urlencode($redir);
    }
    public function fre_get_skills() {
        $terms = get_terms( 'skill', array(
            'hide_empty' => 0,
            'fields'     => 'names'
        ) );
        wp_send_json( $terms );
    }
    /**
     * add query var
     */
    function restrict_pages() {
        global $current_user, $user_ID;

        /**
         * Filters the restrict post type singular,
         * redirect to 404 page if user visit the restrict post type singular
         *
         * @since 1.3
         * @params array $restrict_post_type_singulars
         */
        $restrict_post_type_singulars = apply_filters( 'mje_restrict_post_type_singular', array(
            'mjob_profile',
            'mjob_extra',
            'order_delivery',
            'pack'
        ) );
        foreach( $restrict_post_type_singulars as $type ) {
            if( is_singular( $type ) ) {
                wp_redirect( home_url( '404' ) );
                exit();
            }
        }

        /**
         * Filters the restrict post type archive,
         * redirect to 404 page if user visit the restrict post type archive
         *
         * @since 1.3
         * @params array $restrict_post_types
         */
        $restrict_post_types = apply_filters( 'mje_restrict_post_type_archive', array(
            'mjob_order',
            'order_delivery',
            'mjob_profile',
            'mjob_extra',
            'pack',
            'ae_message'
        ) );
        foreach($restrict_post_types as $post_type) {
            if(is_post_type_archive($post_type)) {
                wp_redirect(home_url('404'));
                exit();
            }
        }

        if( !$user_ID ){
            $restrict_pages = array(
                'page-my-list-jobs.php',
                'page-my-list-order.php',
                'page-my-listing-jobs.php',
                'page-my-list-messages.php',
                'page-profile.php',
                'page-payment-method.php',
                'page-change-password.php',
                'page-revenues.php',
                'page-dashboard.php',
                'page-user-default.php',
                'page-settings.php',
            );

            foreach($restrict_pages as $slug) {
                if( is_page_template( $slug ) ){
                    // get redirect url
                    $page_name = str_ireplace( '.php', '', $slug );
                    $page_name = str_ireplace( 'page-', '', $page_name );
                    $redirect_url = et_get_page_link( $page_name );
                    wp_redirect( et_get_page_link( 'sign-in' ) . '?redirect_to=' . urlencode( $redirect_url ) );
                    exit();
                }
            }
        }
        if( is_singular('mjob_order') ){
            global $post;
            if( $post->post_status == 'draft' ){
                wp_redirect(home_url('404'));
                exit();
            }
        }

        if(is_singular('mjob_post')) {
            global $user_ID, $post;
            if(current_user_can('manage_options')) {
                return;
            }elseif($user_ID != $post->post_author && in_array($post->post_status, array('archive', 'reject'))) {
                wp_redirect(home_url('404'));
                exit();
            }
        }

        // If is attachment
        if(is_attachment()) {
            wp_redirect(home_url('404'));
            exit();
        }


        if( is_user_logged_in()  ) {
            $is_banned = (int) get_user_meta($user_ID,'is_banned', true) ;
            if( $is_banned && !is_404() ){
                wp_redirect(home_url('404/?reason=ban'));
                exit();
            }
        }
    }
    /**
     * filter profile link and change it to author posts link
     * @param String $url The post url
     * @param Object $post current post object
     * @return string
     */
    public function post_link($url, $post) {
        return $url;
    }
    /**
     * hook to filter comment type dropdown and add review favorite to filter comment
     * @param Array $comment_types
     * @return Array $comment_types
    */
    function admin_comment_types_dropdown($comment_types) {
        return $comment_types;
    }
    /**
     * redirect wp
     */
    function redirect_seller() {
        if (!(current_user_can('manage_options') || current_user_can('editor'))) {
            wp_redirect(home_url());
            exit;
        }
    }
    function redirect_login() {
        if (ae_get_option('login_init') && !is_user_logged_in()) {
            wp_redirect(home_url());
            exit;
        }
    }

    function register_sidebar_widget () {
        /**
        * Creates a sidebar blog
        * @param string|array  Builds Sidebar based off of 'name' and 'id' values.
        */
        $args = array(
            'name'          => __( 'Blog Sidebar', 'enginethemes' ),
            'id'            => 'sidebar-blog',
            'description'   => '',
            'class'         => '',
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget'  => '</aside>',
            'before_title'  => '<h2 class="widgettitle">',
            'after_title'   => '</h2>'
        );

        register_sidebar( $args );
    }
    /**
     * add query var
     */
    function add_query_vars($vars) {
        array_push($vars, 'payment-type');
        return $vars;
    }
    //add new return custom fields for posts
    function add_new_post_fields($result) {

         //author name field
         if (!isset($result->author_name)) {
             $author = get_user_by('id', $result->post_author);
             $result->author_name = isset($author->display_name) ? $author->display_name : __('Unnamed', 'enginethemes');
         }

         //comments field
         if (!isset($result->comment_number)) {
             $num_comments = get_comments_number($result->ID);
             if (et_load_mobile()) {
                 $result->comment_number = $num_comments ? $num_comments : 0;
             } else {
                 if (comments_open($result->ID)) {
                     if ($num_comments == 0) {
                         $comments = __('No Comments', 'enginethemes');
                     } elseif ($num_comments > 1) {
                         $comments = $num_comments . __(' Comments', 'enginethemes');
                     } else {
                         $comments = __('1 Comment', 'enginethemes');
                     }
                     $write_comments = '<a href="' . get_comments_link() . '">' . $comments . '</a>';
                 } else {
                     $write_comments = __('Comments are off for this post.', 'enginethemes');
                 }
                 $result->comment_number = $write_comments;
             }
         }

         //post excerpt field
         if ($result->post_excerpt) {
             ob_start();
             echo apply_filters('the_excerpt', $result->post_excerpt);
             $post_excerpt = ob_get_clean();
             $result->post_excerpt = $post_excerpt;
         }

         //category field
         $categories = get_the_category();
         $separator = ' - ';
         $output = '';
         if ($categories) {
             foreach ($categories as $category) {
                 $output.= '<a href="' . get_category_link($category->term_id) . '" title="' . esc_attr(sprintf(__("View all posts in %s", 'enginethemes') , $category->name)) . '">' . $category->cat_name . '</a>' . $separator;
             }
             $result->category_name = trim($output, $separator);
         }
         //avatar field
         //if(!isset($result->avatar)) {
             $result->avatar = get_avatar($result->post_author, 65);
         //}
        return $result;
    }
    //redirect user to url after login
    function filter_link_redirect_login($result) {
        return $result;
    }
    //redirect user to url after register
    function filter_link_redirect_register($result) {
        return $result;
    }
    //add custom fields for user
    function add_user_meta_fields($default) {
         $default = wp_parse_args(array(
             'user_hour_rate',
             'user_profile_id',
             'user_currency',
             'user_skills',
             'user_available'
         ) , $default);
        return $default;
    }

    function on_add_scripts() {

        global $user_ID;

        $this->add_existed_script('jquery');
        $this->add_existed_script('underscore');
        $this->add_existed_script('backbone');
        $this->add_existed_script('plupload');
        $this->add_existed_script('appengine');

        $this->add_script('raty-js', get_template_directory_uri() . '/assets/js/lib/raty.js', array(
            'jquery'), ET_VERSION, true);

        // add script validator
        $this->add_existed_script('jquery-validator');
        $this->add_existed_script('bootstrap');
        /**
         * bootstrap slider for search form
         */
        $this->add_existed_script('slider-bt');

        // Notification lib
        $this->add_script('toastr-js', get_template_directory_uri() . '/assets/js/lib/toastr.min.js', array(
            'jquery',
            'underscore',
            'backbone',
            'appengine'), ET_VERSION, true);


       /* $this->add_script(' plyr-js', get_template_directory_uri() . '/assets/js/lib/plyr.js', array(
            'jquery'), ET_VERSION, true);*/

        $this->add_script('chosen-js', get_template_directory_uri() . '/assets/js/lib/chosen.jquery.min.js', array(
            'jquery',
            'underscore',
            'backbone',
            'appengine'), ET_VERSION, true);

        wp_localize_script('chosen-js', 'raty', array(
            'hint' => array(
                __('bad', 'enginethemes') ,
                __('poor', 'enginethemes') ,
                __('nice', 'enginethemes') ,
                __('good', 'enginethemes') ,
                __('gorgeous', 'enginethemes')
            )
        ));

        $this->add_script('front', get_template_directory_uri() . '/assets/js/front.js', array(
            'jquery',
            'underscore',
            'backbone',
            'appengine'), ET_VERSION, true);

        $this->add_script('waves', get_template_directory_uri() . '/assets/js/lib/waves.js', array(
            'jquery',
            'underscore',
            'backbone',
            'appengine'), ET_VERSION, true);

        $this->add_script('dot', get_template_directory_uri() . '/assets/js/lib/jquery.dot.js', array(
            'jquery',
            'underscore',
            'backbone',
            'appengine'), ET_VERSION, true);

        $this->add_script('wow-animate', get_template_directory_uri() . '/assets/js/lib/wow.min.js', array(
            'jquery',
            'underscore',
            'backbone',
            'appengine'), ET_VERSION, true);


        $this->add_script('scrollbar', get_template_directory_uri() . '/assets/js/lib/customscrollbar.min.js', array(
            'jquery',
            'underscore',
            'backbone',
            'appengine'), ET_VERSION, true);

        $this->add_script('jquery-detect-timezone', get_template_directory_uri() . '/assets/js/lib/jquerydetecttimezone.js', array(), ET_VERSION, false);

        $this->add_script('textarea-auto-resize', get_template_directory_uri() . '/assets/js/lib/autosize.min.js', array(
            'jquery',
            'underscore',
            'backbone',
            'appengine',
            'front'), ET_VERSION, true);

        if( is_mje_submit_page()) {
            $this->add_script('post-service', get_template_directory_uri() . '/assets/js/post-service.js', array(
                'jquery',
                'underscore',
                'backbone',
                'appengine',
                'front'), ET_VERSION, true);
        }
        if(is_page_template('page-profile.php')) {
            // Cropper library
            $this->add_script('cropper-js', get_template_directory_uri() . '/assets/js/lib/cropper.min.js', array(
                'jquery',
                'underscore',
                'backbone',
                'appengine',
                'front'), ET_VERSION, true);

            $this->add_script('profile', get_template_directory_uri() . '/assets/js/profile.js', array(
                'jquery',
                'underscore',
                'backbone',
                'appengine',
                'front'), ET_VERSION, true);

        }

        if(is_page_template('page-dashboard.php')) {
            $this->add_script('chart', get_template_directory_uri() . '/assets/js/lib/chart.min.js', array(
                'jquery',
                'underscore',
                'backbone',
                'appengine',
                'front'), ET_VERSION, true);

            $this->add_script('dashboard', get_template_directory_uri() . '/assets/js/dashboard.js', array(
                'jquery',
                'underscore',
                'backbone',
                'appengine',
                'front'), ET_VERSION, true);
        }
        if(is_author()) {
            $this->add_script('profile', get_template_directory_uri() . '/assets/js/author.js', array(
                'jquery',
                'underscore',
                'backbone',
                'appengine',
                'front'), ET_VERSION, true);
        }
        if(is_page_template('page-payment-method.php')) {
            $this->add_script('payment-method', get_template_directory_uri() . '/assets/js/payment-method.js', array(
                'jquery',
                'underscore',
                'backbone',
                'appengine',
                'front'), ET_VERSION, true);
        }
        if(is_page_template('page-revenues.php')) {
            $this->add_script('revenues', get_template_directory_uri() . '/assets/js/revenues.js', array(
                'jquery',
                'underscore',
                'backbone',
                'appengine',
                'front'), ET_VERSION, true);
        }
        if( is_singular('mjob_post') || is_page_template('page-process-payment.php')) {
            $this->add_script('single-mjob', get_template_directory_uri() . '/assets/js/single-mjob.js', array(
                'jquery',
                'underscore',
                'backbone',
                'appengine',
                'front'), ET_VERSION, true);
            $this->add_script('addthis-script', '//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-4ed5eb280d19b26b', array() , ET_VERSION, true);
        }

        if( is_page_template('page-order.php')) {
            $this->add_script('checkout-handle', get_template_directory_uri() . '/assets/js/checkout-handle.js', array(
                'jquery',
                'underscore',
                'backbone',
                'appengine',
                'front'), ET_VERSION, true);
        }

        if( is_singular('mjob_post') || is_singular('ae_message') ) {
            $this->add_script('custom-order', get_template_directory_uri(). '/assets/js/custom-order.js', array(
                'jquery',
                'underscore',
                'backbone',
                'appengine',
                'front',
                'ae-message-js'
            ), 'enginethemes', true);
        }

        if( is_page_template('page-order.php') || is_page_template('page-process-payment.php')){
            $this->add_script('order-mjob', get_template_directory_uri() . '/assets/js/payment.js', array(
                'jquery',
                'underscore',
                'backbone',
                'appengine',
                'front',
            ), ET_VERSION, true);
        }

        if( is_page_template('page-order.php') || is_mje_submit_page()){
            $this->add_script('credit-checkout', get_template_directory_uri() . '/assets/js/checkout-credit.js', array(
                'jquery',
                'underscore',
                'backbone',
                'appengine',
                'front',
            ), ET_VERSION, true);
        }
        if( is_singular('mjob_order') ) {
            $this->add_script('jquery-countdown', get_template_directory_uri() . '/assets/js/lib/jquery.countdown.min.js', array(
                'jquery',
                'underscore',
                'backbone',
                'appengine',
                'front',
            ), ET_VERSION, false);

        }
        if( is_page_template('page-my-list-order.php') || is_singular('mjob_order') ){
            $this->add_script('order-list', get_template_directory_uri() . '/assets/js/order-list.js', array(
                'jquery',
                'underscore',
                'backbone',
                'appengine',
                'front',
                'ae-message-js'
            ), ET_VERSION, true);
        }

        if( is_page_template('page-my-invoices.php') ){
            $this->add_script('invoices', get_template_directory_uri() . '/assets/js/invoices.js', array(
                'jquery',
                'underscore',
                'backbone',
                'appengine',
                'front',
            ), ET_VERSION, true);
        }
        if( is_search() ||  is_tax('mjob_category') || is_post_type_archive('mjob_post') ) {
            $this->add_script('search-advance', get_template_directory_uri() . '/assets/js/search-advance.js', array(
                'jquery',
                'underscore',
                'backbone',
                'appengine',
            ), ET_VERSION, true);
        }
        // Add style css for mobile version.
        if (et_load_mobile()) {
            return;
        }
    }

    /**
     * Add scrips and styles to admin
     * @param void
     * @return void
     * @since 1.1.4
     * @author Tat Thien
    */
    public function mJobAdminScripts() {
        $this->add_existed_script('bootstrap');
    }

    function on_add_styles() {
        //$this->add_existed_style('bootstrap');
        wp_deregister_style('bootstrap');

        $this->add_style('font-awesome', get_template_directory_uri() . '/assets/css/font-awesome.css', array() , ET_VERSION);

        // CSS Vendor
        $this->add_style('vendor', get_template_directory_uri() . '/assets/css/vendor.css', array() , ET_VERSION);

        $this->add_style('main-style-css', get_template_directory_uri() . '/assets/css/main.css', ET_VERSION);
        $this->add_style('custom-css', get_template_directory_uri() . '/assets/css/custom.css', ET_VERSION);
        $this->add_style('plyr-css', get_template_directory_uri() . '/assets/css/plyr.css', ET_VERSION);

    }
    /*
     * custom query prev query post
    */
    function pre_get_posts($query) {
        if (!is_admin() && (is_post_type_archive('mjob_post') || is_tax('mjob_category') ) || is_tax('skill') ) {
            if (!$query->is_main_query()) return $query;
            if (current_user_can('manage_options')) {
                // Role: admin
                $query->set('post_status', array(
                    'pending',
                    'publish',
                    'unpause'
                ));
                //$query->set ('orderby', 'post_status');
            } else {
                // Role: author
                if( is_page_template('page-my-list-jobs.php') ){
                    $query->set('is_author', true);
                    $query->set('post_status', array('publish', 'pause', 'reject', 'unpause') );
                }else {
                    // Role: vistor
                    $query->set('post_status', array('publish', 'unpause'));
                }
            }
        }
        return $query;
    }
    /*
     * custom order when admin view page-archive-projects
    */
    function order_by_post_status($orderby, $object) {
         global $user_ID, $mjob_is_author;
         if(!isset($mjob_is_author)) {
            //if ((is_post_type_archive('mjob_post') || is_tax('mjob_category') || is_tax('skill') ) && !is_admin() && current_user_can('edit_others_posts')) {// previous 1.3.8
            if ( is_post_type_archive('mjob_post') && ! is_admin() && current_user_can( 'edit_others_posts' ) ) { // update from 1.3.8
                return self::order_by_post_pending($orderby, $object);
             }

             if (isset($object->query_vars['post_status']) && is_array($object->query_vars['post_status'] )
                 && isset($object->query_vars['author']) && $user_ID == $object->query_vars['author'] && $object->query_vars['post_type']=="mjob_post" && $user_ID) {
                 return self::order_by_post_pending($orderby, $object);
             }
         }
        return $orderby;
    }
    static function order_by_post_pending($orderby, $object) {
         global $wpdb;
         $orderby = " case {$wpdb->posts}.post_status
                             when 'pending' then 0
                             when 'disputing' then 1
                             when 'reject' then 2
                             when 'publish' then 3
                             when 'pause' then 4
                             when 'unpause' then 5
                             when 'close' then 6
                             when 'complete' then 7
                             when 'draft' then 8
                             when 'archive' then 9
                             end,
                         {$wpdb->posts}.post_date DESC";
        return $orderby;
    }

    function script_in_footer() {
        do_action('ae_before_render_script');
        ?>
            <script type="text/javascript" id="frontend_scripts">
            (function ($ , Views, Models, AE) {
                $(document).ready(function(){
                    var currentUser;
                    if($('#user_id').length > 0 ) {
                      currentUser = new Models.User( JSON.parse($('#user_id').html()) );
                      //currentUser.fetch();
                    } else {
                      currentUser = new Models.User();
                    }
                    // init view front
                    if(typeof Views.Front !== 'undefined') {
                      AE.App = new Views.Front({model : currentUser});
                    }
                    AE.App.user = currentUser;
                    if(typeof Views.PostSevice !== 'undefined' && $('.mjob-post-service').length > 0) {
                      AE.PostService = new Views.PostSevice({
                          el: '.mjob-post-service',
                          user_login: currentUser.get('id'),
                          free_plan_used: 0,
                          limit_free_plan: false,
                          step: 2
                      });
                    }
                });
            })(jQuery, AE.Views, AE.Models, window.AE);

            </script>
        <?php
        do_action('ae_after_render_script');

        $this->mJobIncludeTemplate();
    }
    /**
     * Check if need increase view count
     * @param null $post_id
     * @return bool|mixed $is_increased
     */
    function may_increase_view_count($post_id = null)
    {
        if(!is_singular('mjob_post')){
            return;
        }

        if($post_id == null)
        {
            global $post;
            $post_id = $post->ID;
        }

        $viewed_mjob = array();
        $is_increased = false;
        if (isset($_COOKIE['viewed_mjob'])) {
            $viewed_mjob = explode("|", $_COOKIE['viewed_mjob']);

            if (!in_array($post->ID, $viewed_mjob)) {
                /**
                 * User had not view this mjob
                 */
                $is_increased = $this->increase_view_count($post_id);
                $viewed_mjob[] = $post_id;
            }
        } else {
            /**
             * User had not view this mjob
             */
            $is_increased = $this->increase_view_count($post_id);
            $viewed_mjob[]=$post_id;
        }

        /**
         * update cookie
         */
        $viewed_mjob = implode("|",$viewed_mjob);
        $secure = ( 'https' === parse_url( site_url(), PHP_URL_SCHEME ) && 'https' === parse_url( home_url(), PHP_URL_SCHEME ) );

        setcookie( 'viewed_mjob', $viewed_mjob, 0, COOKIEPATH, COOKIE_DOMAIN, $secure );
        if ( SITECOOKIEPATH != COOKIEPATH )
        {
            setcookie( 'viewed_mjob', $viewed_mjob, 0, SITECOOKIEPATH, COOKIE_DOMAIN, $secure );
        }

        return $is_increased;
    }
    /**
     * Increase post view count
     *
     * @param $post_id
     * @return mixed
     */
    function increase_view_count($post_id){
        $current_view_count = get_post_meta( $post_id, 'view_count', true );
        if($current_view_count)
        {
            $current_view_count = $current_view_count +1;
        }
        else{
            $current_view_count = 1;
        }
        return update_post_meta($post_id, 'view_count',$current_view_count);
    }
    /**
     * Override jquery validator error
     * @param void
     * @return void
     * @since 1.1
     * @package MicrojobEngine
     * @author Tat Thien
     */
    function mJobOverrideValidatorError() {
        ?>
        <!-- localize validator -->
        <script type="text/javascript">
            (function ($) {
                if(typeof $.validator !== 'undefined' ) {
                    $.extend($.validator.messages, {
                        rangelength: $.validator.format("<?php _e("Value between {0} and {1} characters long.", 'enginethemes'); ?>"),
                        range: $.validator.format("<?php _e("Value between {0} and {1}.", 'enginethemes'); ?>"),
                        min : $.validator.format("<?php _e("Value greater than or equal to {0}.", 'enginethemes'); ?>"),
                        max : $.validator.format("<?php _e("Value less than or equal to {0}.", 'enginethemes'); ?>")
                    });
                }
            })(jQuery);

            plupload.addI18n({
                'Select files' : '<?php _e('Select files', 'enginethemes'); ?>',
                'Add files to the upload queue and click the start button.' : '<?php _e('Add files to the upload queue and click the start button.', 'enginethemes'); ?>',
                'Filename' : '<?php _e('Filename', 'enginethemes'); ?>',
                'Status' : '<?php _e('Status', 'enginethemes'); ?>',
                'Size' : '<?php _e('Size', 'enginethemes'); ?>',
                'Add files' : '<?php _e('Add files', 'enginethemes'); ?>',
                'Stop current upload' : '<?php _e('Stop current upload', 'enginethemes'); ?>',
                'Start uploading queue' : '<?php _e('Start uploading queue', 'enginethemes'); ?>',
                'Uploaded %d/%d files': '<?php _e('Uploaded %d/%d files', 'enginethemes'); ?>',
                'N/A' : '<?php _e('N/A', 'enginethemes'); ?>',
                'Drag files here.' : '<?php _e('Drag files here.', 'enginethemes'); ?>',
                'File extension error.': '<?php _e('File extension error.', 'enginethemes'); ?>',
                'File size error.': '<?php _e('File size error.', 'enginethemes'); ?>',
                'Init error.': '<?php _e('Init error.', 'enginethemes'); ?>',
                'HTTP Error.': '<?php _e('HTTP Error.', 'enginethemes'); ?>',
                'Security error.': '<?php _e('Security error.', 'enginethemes'); ?>',
                'Generic error.': '<?php _e('Generic error.', 'enginethemes'); ?>',
                'IO error.': '<?php _e('IO error.', 'enginethemes'); ?>',
                'Stop Upload': '<?php _e('Stop Upload', 'enginethemes'); ?>',
                'Add Files': '<?php _e('Add Files', 'enginethemes'); ?>',
                'Start Upload': '<?php _e('Start Upload', 'enginethemes'); ?>',
                '%d files queued': '<?php _e('%d files queued', 'enginethemes'); ?>',
                'The width of the image must be greater than ': '<?php _e('The width of the image must be greater than ', 'enginethemes'); ?>',
                'The height of the image must be greater than ': '<?php _e('The height of the image must be greater than ', 'enginethemes'); ?>',
                'The height of the image must be less than ': '<?php _e('The height of the image must be less than ', 'enginethemes'); ?>',
                'The width of the image must be less than ': '<?php _e('The width of the image must be less than ', 'enginethemes'); ?>'
            });
        </script>
        <?php
    }

    /**
     * include template
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function mJobIncludeTemplate(){
        /*
         * include skill item template
         */
        get_template_part('template-js/skill' , 'item');
        /*
         * Include carousel item template
         */
        get_template_part('template-js/carousel-file', 'item');
        get_template_part('template-js/carousel', 'item');

        /**
         * Include user header template
         */
        get_template_part('template-js/my-account', 'item');
        /*
         * Include extra item
         *
         */
        get_template_part('template-js/extra', 'item');
        get_template_part('template-js/edit-extra', 'item');
        /*
         * Include mjob item
         *
         */
        if( defined('MJE_REQUEST') && is_post_type_archive(MJE_REQUEST) ) {
            request_item_js_template();
        } else {
            mje_get_template_part('template-js/mjob', 'item');
        }



        if(is_author()) {
            get_template_part('template-js/mjob-list', 'item');
        }
        /*
         * include modal reject mjob
         */
        get_template_part('template-js/modal', 'reject');
        if( is_page_template('page-my-list-order.php') || is_singular('mjob_order') ){
            get_template_part('template-js/order', 'item');
            get_template_part('template-js/task', 'item');
            get_template_part('template-js/modal-delivery', 'order');
        }

        get_template_part('template/modal', 'conversation');

        /**
         * Include history item
         */
        get_template_part('template-js/history', 'item');
        if( is_singular('mjob_order')){
            get_template_part('template-js/modal', 'review');
        }
        /**
         * Include conversation item
         */
        get_template_part('template-js/conversation', 'item');
        get_template_part('template-js/message', 'item');
        get_template_part('template-js/post', 'item');

        if(is_singular('mjob_post') || is_page_template('page-order.php') || is_page_template('page-process-payment.php')) {
            get_template_part('template-js/review', 'item');
        }

        if( is_page_template( 'page-my-invoices.php' ) ) {
            get_template_part( 'template-js/invoice', 'item' );
        }
    }
    /**
     * add more global variable
     *
     * @param array $vars
     * @return array $vars
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function mJobGlobals($vars){
        global $user_ID;
        $vars['user_ID'] = $user_ID;
        $vars['is_admin'] = false;
        if( is_super_admin() ){
            $vars['is_admin'] = true;
        }
        $vars['is_search'] = is_search();
        $vars['is_tax_mjob_category'] = is_tax('mjob_category') ? (int) get_queried_object()->term_id : 0;
        $vars['is_archive_mjob'] = is_post_type_archive('mjob_post');
        $vars['is_archive_mjob_link'] = get_post_type_archive_link('mjob_post');

        $vars['term_link'] = is_tax('mjob_category') ? get_term_link($vars['is_tax_mjob_category']) : '';

        $vars['is_tax_skill'] = is_tax('skill');
        $vars['mJobDefaultGalleryImage'] = TEMPLATEURL. '/assets/img/image-avatar.jpg';
        $vars['mjob_image_directory'] = TEMPLATEURL. '/assets/img';
        $number_format = mje_get_number_format_settings();
        $vars['decimal'] = $number_format['decimal'];;
        $vars['decimal_point'] = $number_format['decimal_point'];
        $vars['thousand_sep'] = $number_format['thousand_sep'];
        $currency = ae_get_option('currency', array(
            'align' => 'left',
            'code' => 'USD',
            'icon' => '$'
        ));
        $vars['mjob_currency'] = $currency;
        $vars['order_link'] = et_get_page_link('order');
        $vars['profile_empty_text'] = __('There is no content', 'enginethemes');
        $vars['no_services'] = sprintf(__('<div class="not-found">This search matches 0 results! <p class="not-found-sub-text"><label for="input-search" class="new-search-link">New search</label> or <a href="%s">back to home page</a></p></div>', 'enginethemes'), get_site_url());
        //$vars['no_services']
        $vars['no_mjobs'] = __('<div class="not-found">There are no mJobs found!</div>', 'enginethemes');
        $vars['no_orders'] = __('<p class="no-items" >There are no orders found!</p>', 'enginethemes');
        $vars['min_images'] = ae_get_option('min_carousel', 1);
        $vars['min_images_notification'] = __('You must have at least one picture!', 'enginethemes');
        $vars['delivery_status'] = __('DELIVERED', 'enginethemes');
        $vars['disputing_status'] = __('DISPUTING', 'enginethemes');
        $file_types = ae_get_option('file_types', 'pdf,doc,docx,jpg,png');
        $file_types = preg_replace('/\s+/', '', $file_types);
        $vars['file_types'] = $file_types;

        $max_file_size = ae_get_option('max_file_size');
        if(empty($max_file_size) || !is_numeric($max_file_size)) {
            $max_file_size = wp_max_upload_size() / (1024 * 1024) . 'mb';
        } else {
            $max_file_size = $max_file_size . 'mb';
        }
        $vars['plupload_config']['max_file_size'] = $max_file_size;
        $vars['progress_bar_3'] = mje_render_progress_bar(3, false);
        $vars['progress_bar_4'] = mje_render_progress_bar(4, false);

        $date_format = 'M j';
        $current_date = date('M j Y', time());
        $last_week = date('M j Y', strtotime('-1 week'));
        $vars['date_range'] = mje_get_range_of_date($last_week, $current_date, '+1 day', $date_format);
        $vars['data_chart'] = mje_get_mjob_order_chart();

        $vars['show_bio_text'] = __('Show more', 'enginethemes');
        $vars['hide_bio_text'] = __('Show less', 'enginethemes');

        $vars['pending_account_error_txt'] = __("Your account is pending. You have to activate your account to continue this step.", 'enginethemes');
        $vars['disableNotification'] = __('This mJob was paused by the seller.', 'enginethemes');
        $vars['priceMinNoti'] = __('Please enter a number greater than 0.', 'enginethemes');
        $vars['requiredField'] = __('This field is required!', 'enginethemes');
        $vars['uploadSuccess'] = __('Job slider updated successfully!', 'enginethemes');
        $vars['user_confirm'] = ae_get_option('user_confirm');
        $vars['permalink_structure'] = get_option('permalink_structure');
        $vars['notice_expired_date'] = __('This order was expected to be delivered at ','enginethemes');
        $primary_color = get_theme_mod( MJE_Skin_Action::get_skin_name() . '_primary_color');
        $primary_chart_color = get_theme_mod( MJE_Skin_Action::get_skin_name() . '_primary_chart_shadow' );
        $vars['primary_color'] = !empty($primary_color) ? $primary_color : 'rgba(16,162,239,1)';
        $vars['primary_chart_color'] = !empty($primary_chart_color) ? $primary_chart_color : 'rgba(16,162,239,0.1)';
        //Title decline and reject custom order
        $vars['custom_order_decline'] = __('Decline Custom Order', 'enginethemes');
        $vars['custom_order_reject'] = __('Offer Rejected', 'enginethemes');

        // For credit usage
        $vars['credit_balance_not_enough'] = __('Your balance is not enough to use this checkout method.', 'enginethemes');

        $vars['min_text'] = __('minutes', 'enginethemes');
        $vars['hour_text'] = __('hours', 'enginethemes');
        $vars['sec_text'] = __('seconds', 'enginethemes');
        $vars['min_withdraw'] = MIN_WITHDRAW;
        $vars['min_withdraw_error'] = sprintf(__('Minimum money to withdraw is %s', 'enginethemes'), mje_format_price(MIN_WITHDRAW, ""));
        $vars['title_popover_opening_message'] = __('This message will be shown in the direct chat box.', 'enginethemes');
        $vars['result'] = __('mJob Available', 'enginethemes');
        $vars['results'] = __('mJobs Available', 'enginethemes');
        $vars['skin_assets_path'] = MJE_Skin_Action::get_skin_assets_path();
        $vars['skin_name'] = MJE_Skin_Action::get_skin_name();

        // Check tablet
        $detector = new AE_MobileDetect();
        $vars['is_tablet'] = $detector->isTablet();
        $vars['is_phone'] = $detector->isMobile();
        $vars['fee_order_buyer'] = ae_get_option('order_commission_buyer', 0);
        $vars['is_home'] = ( is_home() || is_front_page() ) ? true : 0;
        // chosen text
       // $vars['no_result_text'] = __('0 items','enginethemes');
        return $vars;
    }
    /**
     * user pending option
     *
     * @param boolean $pending
     * @param string $post_type
     * @return boolean $pending
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function mjob_user_pending($pending, $post_type){
        if( $post_type != 'mjob_post' ){
            return false;
        }
        return $pending;
    }
    /**
     * allow user upload file type
     *
     * @param array $mimes
     * @return array
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function mjob_add_mime_types($mimes) {
        /**
         * admin can add more file extension
         */
        if (current_user_can('manage_options')) {
            return array_merge($mimes, array(
                'ac3' => 'audio/ac3',
                'mpa' => 'audio/MPA',
                'flv' => 'video/x-flv',
                'svg' => 'image/svg+xml',
                'mp4' => 'video/MP4',
                'doc' =>   'application/msword',
                'docx'=>   'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'pdf' => 'application/pdf',
                'psd' => 'application/psd',
                'zip' => 'multipart/x-zip'
            ));
        }
        // if user is normal user
        $mimes = array_merge($mimes, array(
            'doc' =>   'application/msword',
            'docx'=>   'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'pdf' => 'application/pdf',
            'psd' => 'application/psd',
            'zip' => 'multipart/x-zip'
        ));
        return $mimes;
    }
    /**
     * disable mobile version
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function disableMobileVersion(){
        return false;
    }

    /**
     * Filter price decimal
     *
     * @param string|int $decimal
     * @return int
     * @author Tat Thien
     * @since 1.1.4
     */
    public function filter_price_decimal( $decimal ) {
        $decimal = 2;
        return $decimal;
    }

    public function ae_update_subscriber(){
        global $user_ID;

        $value = ($_REQUEST['value'] == false || $_REQUEST['value'] == 'false') ? 2 : 1;

        update_user_meta($user_ID,'et_subscriber', $value);

        wp_send_json( array('success' => true, 'msg' => __('Your setting is update','enginethemes' ) ) );
    }
}

global $et_mjob;
add_action('after_setup_theme', 'et_setup_theme');
function et_setup_theme() {
    global $et_mjob;
    $et_mjob = new ET_Microjobengine();
    if (is_admin() || current_user_can('manage_options')) {
        new ET_Admin();
    }
}

/**
 * Set default user roles for social login
 * @author JACK BUI
 */
if( !function_exists( 'mJobDefaultUserRoles' ) ){
    function mJobDefaultUserRoles( $default_role ){
        $default_role = array('author');
        return $default_role;
    }

    add_filter( 'ae_social_login_user_roles_default', 'mJobDefaultUserRoles' );
}

if(!function_exists('mJobRemoveLinkedIn')) {
    /**
     * Filter remove social login with LinkedIn
     * @param void
     * @return boolean
     * @since 1.0
     * @package Microjobengine
     * @category Filter Hook
     * @author Tat Thien
     */
    function mJobRemoveLinkedIn() {
        return false;
    }

    add_filter('ae_enable_social_linkedin', 'mJobRemoveLinkedIn');
}


if(!function_exists('mJobPackageScripts')) {
    /**
     * Add scripts for package
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Action Hook
     * @author Tat Thien
     */
    function mJobPackageScripts() {
        /**
         * Change checkbox to radio for most popular mJob categories tab
         */
        ?>
        <script>
            (function($) {
                $('#mjob_category-pop').find('li').each(function() {
                   $(this).find('input[type="checkbox"]').attr('type', 'radio');
                   $(this).find('input[type="radio"]').attr('name', 'tax_input[mjob_category][]');
                });
            })(jQuery)
        </script>
        <?php

        if(isset($_GET['page']) && $_GET['page'] == 'et-settings') {
            ?>
            <script>
                (function($) {
                    $('body').delegate('input[name="et_permanent"]', 'click', function() {
                        var disabled = $(this).attr('data-disable');
                        if($(this).is(':checked')) {
                            $('input[name='+disabled+']').attr('disabled', true);
                            $('label[for='+disabled+']').remove();
                        } else {
                            $('input[name='+disabled+']').removeAttr('disabled');
                        }
                    });
                })(jQuery)
            </script>
            <?php
        }

        // Include template part for admin
        if(is_admin() && ( isset( $_GET['page'] ) && $_GET['page'] == 'et-wizard' )) {
            ?>
            <div class="modal fade" id="preview-skin-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-body">

                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    }

    add_filter('admin_footer', 'mJobPackageScripts');
}

if( !function_exists( 'mJobFilterCategoryChecklistArgs' ) ) {
     /**
     * Filter category checklist args for mJob
     *
     * @param array $args
     * @return array $args
     * @since 1.1.4
     * @package MicrojobEngine
     * @author Tat Thien
     */
    function mJobFilterCategoryChecklistArgs( $args, $post_id ) {
        if( !class_exists( 'mJob_Walker_Category_Checklist' ) ) {
            /**
             * Class Walker for mJob Category
             * change checkbox to raido
             * @since 1.1.4
             * @author Tat Thien
             */
            class mJob_Walker_Category_Checklist extends Walker_Category_Checklist
            {
                /**
                 * Start the element output.
                 *
                 * @see Walker::start_el()
                 *
                 * @since 2.5.1
                 *
                 * @param string $output   Passed by reference. Used to append additional content.
                 * @param object $category The current term object.
                 * @param int    $depth    Depth of the term in reference to parents. Default 0.
                 * @param array  $args     An array of arguments. @see wp_terms_checklist()
                 * @param int    $id       ID of the current term.
                 */
                public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
                    if ( empty( $args['taxonomy'] ) ) {
                        $taxonomy = 'category';
                    } else {
                        $taxonomy = $args['taxonomy'];
                    }

                    if ( $taxonomy == 'category' ) {
                        $name = 'post_category';
                    } else {
                        $name = 'tax_input[' . $taxonomy . ']';
                    }

                    $args['popular_cats'] = empty( $args['popular_cats'] ) ? array() : $args['popular_cats'];
                    $class = in_array( $category->term_id, $args['popular_cats'] ) ? ' class="popular-category"' : '';

                    $args['selected_cats'] = empty( $args['selected_cats'] ) ? array() : $args['selected_cats'];

                    if ( ! empty( $args['list_only'] ) ) {
                        $aria_cheched = 'false';
                        $inner_class = 'category';

                        if ( in_array( $category->term_id, $args['selected_cats'] ) ) {
                            $inner_class .= ' selected';
                            $aria_cheched = 'true';
                        }

                        /** This filter is documented in wp-includes/category-template.php */
                        $output .= "\n" . '<li' . $class . '>' .
                            '<div class="' . $inner_class . '" data-term-id=' . $category->term_id .
                            ' tabindex="0" role="checkbox" aria-checked="' . $aria_cheched . '">' .
                            esc_html( apply_filters( 'the_category', $category->name ) ) . '</div>';
                    } else {
                        /** This filter is documented in wp-includes/category-template.php */
                        $output .= "\n<li id='{$taxonomy}-{$category->term_id}'$class>" .
                            '<label class="selectit"><input value="' . $category->term_id . '" type="radio" name="'.$name.'[]" id="in-'.$taxonomy.'-' . $category->term_id . '"' .
                            checked( in_array( $category->term_id, $args['selected_cats'] ), true, false ) .
                            disabled( empty( $args['disabled'] ), false, false ) . ' /> ' .
                            esc_html( apply_filters( 'the_category', $category->name ) ) . '</label>';
                    }
                }
            } // end class

            if( $args['taxonomy'] == 'mjob_category' ) {
                $args['walker'] = new mJob_Walker_Category_Checklist;
            }
        }

        return $args;
    }

    // Filter mjob category checklist args
    add_filter('wp_terms_checklist_args', 'mJobFilterCategoryChecklistArgs', 10, 2);
}

/**
 * Init WP_List_Table
 *
 * @since 1.4
 * @author Thien Nguyen
 */
if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . '/wp-admin/includes/class-wp-list-table.php' );
}