<?php
class MJE_Notification_Action extends MJE_Post_Action
{
    // Custom post type
    public $post_type = 'mje_notification';

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
     * @since 1.3
     * @author Tat Thien
     */
    public function __construct()
    {
        parent::__construct($this->post_type);

        // Actions hook
        $this->add_action( 'wp_enqueue_scripts', 'add_scripts', 10 );
        $this->add_action( 'wp_footer', 'render_notify_container', 10 );

        // Filters hook
        $this->add_filter( 'mje_restrict_post_type_archive', 'restrict_post_type', 10 );
        $this->add_filter( 'mje_restrict_post_type_singular', 'restrict_post_type', 10 );

        // Actions hook for ajax
        $this->add_ajax( 'mje_sync_notification', 'sync', 10 );
        $this->add_ajax( 'mje_fetch_notifications', 'fetch', 10 );
        $this->add_ajax( 'mje_mark_read_notifications', 'mark_read', 10 );
    }

    /**
     * Add scripts
     * @param void
     * @since 1.3
     * @author Tat Thien
     */
    public function add_scripts()
    {
        if( is_user_logged_in() ) {
            wp_enqueue_script( 'notification', get_template_directory_uri() . '/assets/js/notification.js', array(
                'jquery',
                'underscore',
                'backbone',
                'appengine'
            ), ET_VERSION, true );
        }
    }

    /**
     * Render html for container of notification
     * @param void
     * @since 1.3
     * @author Tat Thien
     */
    public function render_notify_container()
    {
        if( is_user_logged_in() ) {
            mje_get_template( 'template/notification/container.php' );
            mje_get_template_part( 'template/notification/item', 'loop' );
        }
    }

    /**
     * Restrict post type from archive and singular
     *
     * @param array $post_types
     * @return array $post_types
     * @since 1.3
     * @author Tat Thien
     */
    public function restrict_post_type( $post_types ) {
        array_push( $post_types, $this->post_type );
        return $post_types;
    }

    /**
     * Create notification
     * @param string $notify_code
     * @param int|string $receiver
     * @since 1.3
     * @author Tat Thien
     */
    public function create( $notify_code, $receiver )
    {
        $args = apply_filters( 'mje_insert_notification_args', array(
            'post_type' => $this->post_type,
            'post_status' => 'unread',
            'post_author' => $receiver,
            'post_title' => $notify_code,
            'post_content' => $notify_code
        ) );
        $post = wp_insert_post( $args );
        return $post;
    }

    /**
     * Build notification query args
     *
     * @param array $query
     * @return array $args
     * @since 1.3
     * @author Tat Thien
     */
    public function build_query_args( $args = array() ) {
        global $user_ID;
        $default = array(
            'post_type' => $this->post_type,
            'post_status' => array( 'unread', 'read' ),
            'posts_per_page' => 10,
            'author' => $user_ID,
        );
        $args = wp_parse_args( $args, $default );
        return $args;
    }

    /**
     * Return a WP_Query object
     *
     * @param array $args
     * @return WP_Query $query
     * @since 1.3
     * @author Tat Thien
     */
    public function build_query( $args = array() ) {
        $query = new WP_Query( $this->build_query_args( $args ) );
        return $query;
    }

    /**
     * sync notification
     *
     * @since 1.3
     * @author Tat Thien
     */
    public function sync() {
        global $user_ID;
        $request = $_REQUEST;
        // validate author
        if( $user_ID != $request['post_author'] ) {
            wp_send_json( array(
                'success' => false
            ) );
        }
        // validate nonce
        if( ! wp_verify_nonce( $request['noti_nonce'], 'mje_sync_notification' ) ) {
            wp_send_json( array(
                'success' => false
            ) );
        }

        $action = $request['do_action'];
        switch ( $action ) {
            case 'hide':
                $post = wp_update_post( array(
                    'ID' => $request['ID'],
                    'post_status' => 'hide'
                ) );
                break;
            case 'undo':
                $post = wp_update_post( array(
                    'ID' => $request['ID'],
                    'post_status' => 'read'
                ) );
                break;
            default:
                $post = null;
        }

        if( ! is_wp_error( $post ) ) {
            wp_send_json( array(
                'success' => true
            ) );
        } else {
            wp_send_json( array(
                'success' => false
            ) );
        }
    }

    /**
     * Fetch notifications
     *
     * @since 1.3
     * @author Tat Thien
     */
    public function fetch() {
        $this->validate( $_REQUEST );
        $paged = ! empty( $_REQUEST['paged'] ) ?  $_REQUEST['paged'] : 1;

        $args = array(
            'paged' => $paged
        );

        $query = $this->build_query( $args );
        $posts = $this->get( $args );
        wp_send_json( array(
            'success' => true,
            'data' => $posts,
            'post_count' => $query->post_count,
            'max_num_pages' => $query->max_num_pages
        ) );
    }

    /**
     * Update status of notifications to read
     */
    public function mark_read() {
        $this->validate( $_REQUEST );

        $noti_ids = $_REQUEST['noti_ids'];
        if( ! empty( $noti_ids ) ) {
            foreach ($noti_ids as $id ) {
                wp_update_post( array(
                    'ID' => $id,
                    'post_status' => 'read'
                ) );
            }
        }
        wp_send_json( array(
            'success' => true,
            'data' => $noti_ids
        ) );
    }

    /**
     * Validate request
     *
     * @param $request
     * @since 1.3
     * @author Tat Thien
     */
    public function validate( $request ) {
        // Check user logged in
        if( ! is_user_logged_in() ) {
            wp_send_json( array(
                'success' => false,
                'msg' => __( 'Please login to see your notifications!', 'enginethemes' )
            ) );
        }

        // Check nonce
        if( ! wp_verify_nonce( $request['nonce'], 'mje_fetch_notifications') ) {
            wp_send_json( array(
                'success' => false,
                'msg' => __( 'Invalid action!', 'enginethemes' )
            ) );
        }
    }

    /**
     * Get notifications
     *
     * @param array $args
     * @return array $data
     * @since 1.3
     * @author Tat Thien
     */
    public function get( $args ) {
        $data = array();
        $query  = $this->build_query( $args );
        while( $query->have_posts() ) {
            $query->the_post();
            global $post;
			if(isset($post->post_status)){
				$post_converted = $this->convert( $post );
				if( ! empty( $post_converted->noti_content ) ) {
				   $data[] = $post_converted;
				}
			}
        }

        return $data;
    }

    /**
     * Add more specific data to single notification post
     *
     * @param object $post
     * @return object $post
     * @since 1.3
     * @author Tat Thien
     */
    public function convert( $post ) {

        // Previous version
        // $post_date = get_the_date( 'Y/m/d H:i:s', $post->ID );
        // $post->noti_time = sprintf( __( '%s ago', 'enginethemes' ), et_human_time_diff( strtotime( $post_date ) ) );

        /* fix noti time incorrect*/
        // Version 1.3.9.5
        $from               = strtotime($post->post_date_gmt); //2020-12-16 08:22:56
        $gmt_date           = gmdate("M d Y H:i:s"); // v1.3.9.5
        $to                 = strtotime($gmt_date);
        $post->noti_time    = sprintf(__('%s ago','enginethemes'),human_time_diff($from, $to ));

        /* End */

        $post->noti_nonce = wp_create_nonce( 'mje_sync_notification' );
        $post->noti_icon = '<i class="fa fa-info"></i>';
        $post->noti_link = '';
        $post = $this->build_notification( $post );
        return $post;
    }

    /**
     * Create notification content from its code
     *
     * @param object $post
     * @return object $post
     * @since 1.3
     * @author Tat Thien
     */
    public function build_notification( $post ) {
        $code = trim( $post->post_content );
        $code = str_ireplace( '&amp;', '&', $post->post_content );
        $code = strip_tags( $code );

        // Convert string to variables
        parse_str( $code ,$result);
        // version 1.3.6

        $type = isset($result['type']) ? $result['type']:'';
        $sender = isset($result['sender']) ? $result['sender']:'';
        $mjob_id = isset($result['mjob_id']) ? $result['mjob_id']:'';
        $post_id = isset($result['post_id']) ? $result['post_id']:'';
        $winner_id = isset($result['winner_id']) ? $result['winner_id']:'';
        $post_parent = isset($result['post_parent']) ? $result['post_parent']:'';
        $mjob_order_id = isset($result['mjob_order_id']) ? $result['mjob_order_id']:'';
        $amount = isset($result['amount']) ? $result['amount']:'';
        $review_id = isset($result['review_id']) ? $result['review_id']:'';
        $score = isset($result['score']) ? $result['score']:'';


        switch ( $type ) {
            case 'activated_user':
                $post->noti_content = __( 'Hooray! Your account has been activated. Now you can offer your stuff, buy things you need. Fast and easy!', 'enginethemes' );
                break;
            case 'approve_withdrawal':
                $post->noti_content = __( '<strong>Admin</strong> <span class="action-text">approved</span> your withdrawal request.', 'enginethemes' );
                $post->noti_link = et_get_page_link( 'revenues' ) . '#withdraw-history';
                break;
            case 'decline_withdrawal':
                $post->noti_content = __( '<strong>Admin</strong> <span class="action-text">rejected</span> your withdrawal request.', 'enginethemes' );
                $post->noti_link = et_get_page_link( 'revenues' ) . '#withdraw-history';
                break;
            case 'checkout_mjob_by_credit':
                $amount = mje_format_price( $amount );
                $post->noti_link = et_get_page_link( 'revenues' );
                $post->noti_content = sprintf( __( 'You\'ve <span class="action-text">spent</span> %s credit(s) on a service. ', 'enginethemes' ), $amount );
                break;
            case 'new_mjob_order':
                $post = $this->build_sender_info( $sender, $post );
                $post->noti_link = get_the_permalink( $post_id );
                $mjob = '<strong>' . get_the_title( $post_parent ) . '</strong>';
                $post->noti_content = sprintf( __( '%s <span class="action-text">ordered</span> your service %s.', 'enginethemes' ), $post->noti_sender, $mjob );
                break;
            case 'review_mjob_order':
                $post = $this->build_sender_info( $sender, $post );
                $post->noti_link = get_the_permalink( $mjob_id ) . '#review-' . $review_id;
                $mjob = '<strong>' . get_the_title( $mjob_id ) . '</strong>';
                $rating = '<span class="rate-it star" data-score="' . $score . '"></span>';
                $post->noti_content = sprintf( __( '%s <span class="action-text">accepted</span> and put %s for your delivery of %s.', 'enginethemes' ), $post->noti_sender, $rating, $mjob );
                break;
            case 'finish_mjob_order':
                $post = $this->build_sender_info( $sender, $post );
                $post->noti_link = get_the_permalink( $mjob_order_id );
                $mjob = '<strong>' . get_the_title( $mjob_id ) . '</strong>';
                $post->noti_content = sprintf( __( '%s <span class="action-text">accepted</span> your delivery for %s', 'enginethemes' ), $post->noti_sender, $mjob );
                break;
            case 'buyer_dispute_mjob_order':
                $post = $this->build_sender_info( $sender, $post );
                $post->noti_link = get_permalink( $mjob_order_id );
                $mjob = '<strong>' . get_the_title( $mjob_id ) . '</strong>';
                $post->noti_content = sprintf( __( '%s <span class="action-text action-text-dispute">disputed</span> your task for %s', 'enginethemes' ), $post->noti_sender, $mjob );
                break;
            case 'seller_dispute_mjob_order':
                $post = $this->build_sender_info( $sender, $post );
                $post->noti_link = get_permalink( $mjob_order_id );
                $mjob = '<strong>' . get_the_title( $mjob_id ) . '</strong>';
                $post->noti_content = sprintf( __( '%s <span class="action-text action-text-dispute">disputed</span> your order for %s', 'enginethemes' ), $post->noti_sender, $mjob );
                break;
            case 'resolve_mjob_order':
                global $user_ID;
                $mjob_order = mje_mjob_order_action()->get_mjob_order( $mjob_order_id );
                $post = $this->build_sender_info( $winner_id, $post );
                $post->noti_icon = '<i class="fa fa-info"></i>';
                $post->noti_link = $mjob_order->permalink;
                $mjob = '<strong>' . get_the_title( $mjob_id ) . '</strong>';

                if( $winner_id == $user_ID ) {
                    $winner_is = __( 'You are the winner.', 'enginethemes' );
                } else {
                    $winner_is = sprintf( __( 'The winner is %s.', 'enginethemes' ), $post->noti_sender );
                }

                if( $mjob_order->seller_id == $user_ID ) {
                    $post->noti_content = sprintf( __( 'Admin <span class="action-text">resolved</span> your disputed task for %s. %s', 'enginethemes' ), $mjob, $winner_is );
                } else {
                    $post->noti_content = sprintf( __( 'Admin <span class="action-text">resolved</span> your order for %s. %s', 'enginethemes' ), $mjob, $winner_is );
                }
                break;
            case 'start_mjob_order':
                $post = $this->build_sender_info( $sender, $post );
                $post->noti_link = get_the_permalink( $mjob_order_id );
                $mjob = '<strong>' . get_the_title( $mjob_id ) . '</strong>';
                $post->noti_content = sprintf( __( '%s has <span class="action-text">started</span> to work on your order for %s.', 'enginethemes' ), $post->noti_sender, $mjob );
                break;
            case 'delay_mjob_order':
                $post = $this->build_sender_info( $sender, $post );
                $post->noti_link = get_the_permalink( $mjob_order_id );
                $mjob = '<strong>' . get_the_title( $mjob_id ) . '</strong>';
                $post->noti_content = sprintf( __( '%s <span class="action-text">delayed</span> your order for %s.', 'enginethemes' ), $post->noti_sender, $mjob );
                break;
            case 'deliver_mjob_order':
                $post = $this->build_sender_info( $sender, $post );
                $post->noti_link = get_the_permalink( $mjob_order_id );
                $mjob = '<strong>' . get_the_title( $mjob_id ) . '</strong>';
                $post->noti_content = sprintf( __( '%s <span class="action-text">delivered</span> your order for %s. Only one step to close the order, view your delivery and write a review for the seller.', 'enginethemes' ), $post->noti_sender, $mjob );
                break;
            case 'admin_delete_mjob_order':
                global $user_ID;
                $mjob_order = mje_mjob_order_action()->get_mjob_order( $mjob_order_id );
                $mjob = '<strong>' . get_the_title( $mjob_id ) . '</strong>';

                $post->noti_content = sprintf( __( 'Admin <span class="action-text">deleted</span> your order for %s.', 'enginethemes' ), $mjob );
                if( $mjob_order->seller_id == $user_ID ) {
                    $post->noti_content = sprintf( __( 'Admin <span class="action-text">deleted</span> your task for %s.', 'enginethemes' ), $mjob );
                }
                break;
            case 'admin_restore_mjob_order':
                global $user_ID;
                $mjob_order = mje_mjob_order_action()->get_mjob_order( $mjob_order_id );
                $mjob = '<strong>' . get_the_title( $mjob_id ) . '</strong>';
                $post->noti_link = $mjob_order->permalink;
                $post->noti_content = sprintf( __( 'Admin <span class="action-text">restored</span> your deleted order for %s.', 'enginethemes'), $mjob );
                if( $mjob_order->seller_id == $user_ID ) {
                    $post->noti_content = sprintf( __( 'Admin <span class="action-text">restored</span> your deleted task for %s.', 'enginethemes'), $mjob );
                }
                break;
            case 'admin_pause_mjob':
                $post->noti_link = get_the_permalink( $mjob_id );
                $mjob = '<strong>' . get_the_title( $mjob_id ) . '</strong>';
                $post->noti_content = sprintf( __( 'Admin <span class="action-text">paused</span> your service %s', 'enginethemes'), $mjob );
                break;
            case 'admin_archive_mjob':
                $post->noti_link = get_the_permalink( $mjob_id );
                $mjob = '<strong>' . get_the_title( $mjob_id ) . '</strong>';
                $post->noti_content = sprintf( __( 'Admin <span class="action-text">archived</span> your service %s', 'enginethemes'), $mjob );
                break;
            case 'admin_approve_mjob':
                $post->noti_link = get_the_permalink( $mjob_id );
                $mjob = '<strong>' . get_the_title( $mjob_id ) . '</strong>';
                $post->noti_content = sprintf( __( 'Admin <span class="action-text">approved</span> your service %s', 'enginethemes'), $mjob );
                break;
            case 'admin_reject_mjob':
                $post->noti_link = get_the_permalink( $mjob_id );
                $mjob = '<strong>' . get_the_title( $mjob_id ) . '</strong>';
                $post->noti_content = sprintf( __( 'Admin <span class="action-text">rejected</span> your service %s', 'enginethemes'), $mjob );
                break;
            case 'admin_edit_mjob':
                $post->noti_link = get_the_permalink( $mjob_id );
                $mjob = '<strong>' . get_the_title( $mjob_id ) . '</strong>';
                $post->noti_content = sprintf( __( 'Admin <span class="action-text">edited</span> your service %s', 'enginethemes'), $mjob );
                break;
            case 'admin_delete_mjob':
                $mjob = '<strong>' . get_the_title( $mjob_id ) . '</strong>';
                $post->noti_content = sprintf( __( 'Admin <span class="action-text">deleted</span> your service %s. Please contact admin for further details.', 'enginethemes' ), $mjob );
                break;
            case 'admin_restore_mjob':
                $post->noti_link = get_the_permalink( $mjob_id );
                $mjob = '<strong>' . get_the_title( $mjob_id ) . '</strong>';
                $post->noti_content = sprintf( __( 'Admin <span class="action-text">restored</span> your deleted service %s.', 'enginethemes' ), $mjob );
                break;
            default:
               $post->noti_content = "";
			   /**
				 * Add action in case other type notification
				 *
				 * @since 1.3.1
				 * @author Tan Hoai
				 */

				do_action('mje_other_type_notification', $post);
        }

        /**
         * Filter for notification content.
         * hook into this filter to add another notification type
         *
         * @param object $post
         * @since 1.3
         * @author Tat Thien
         */
        return apply_filters( 'mje_build_notification', $post );
    }

    public function build_sender_info( $sender, $post ) {
        $user_data = get_userdata( $sender );
        $post->noti_icon = mje_avatar( $sender, 35 );
        if($user_data){
            $post->noti_sender = '<strong>' . $user_data->display_name . '</strong>';
        }
        return $post;
    }
}

new MJE_Notification_Action();
