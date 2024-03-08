<?php
class MJE_Notication_Hook extends AE_Base
{
    public static $instance;
    public $notification_action;
    public static function get_instance()
    {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->notification_action = MJE_Notification_Action::get_instance();
        /* authentication related hooks */
        $this->add_action( 'ae_after_confirm_user', 'confirmed_user', 10, 2 );

        /* withdrawal related hooks */
        $this->add_action( 'ae_approve_withdraw', 'approve_withdrawal', 10);
        $this->add_action( 'ae_decline_withdraw', 'decline_withdrawal', 10);
        $this->add_action( 'mje_updated_revenue_after_checkout_mjob', 'checkout_mjob_by_credit', 10, 2 );

        /* mjob order related hooks */
        $this->add_action( 'mje_updated_mjob_order', 'updated_mjob_order' );
        $this->add_action( 'transition_post_status', 'transform_status', 10, 3 );
        $this->add_action( 'mje_user_reviewed_mjob', 'review_mjob_order', 10, 3 );
        $this->add_action( 'mje_finished_mjob_order', 'finish_mjob_order', 10, 1 );
        $this->add_action( 'mje_disputed_mjob_order', 'dispute_mjob_order', 10, 1 );
        $this->add_action( 'mje_resolved_mjob_order', 'resolve_mjob_order', 10, 1 );
        $this->add_action( 'mje_started_mjob_order', 'start_mjob_order', 10, 1 );
        $this->add_action( 'mje_delayed_mjob_order', 'delay_mjob_order', 10, 1 );
        $this->add_action( 'mje_delivered_mjob_order', 'deliver_mjob_order', 10, 1 );

        /* mjob related hooks */
        $this->add_action( 'mje_paused_mjob', 'pause_mjob', 10, 1 );
        $this->add_action( 'mje_archived_mjob', 'archive_mjob', 10, 1 );
        $this->add_action( 'mje_approved_mjob', 'approve_mjob', 10, 1 );
        $this->add_action( 'mje_rejected_mjob', 'reject_mjob', 10, 1 );
        $this->add_action( 'mje_edited_mjob', 'edit_mjob', 10, 1 );
    }

    /**
     * Action after confirmed user
     *
     * @param int|string $user_id
     * @param string $key
     * @since 1.3
     * @author Tat Thien
     */
    public function confirmed_user( $user_id, $key )
    {
        $this->notification_action->create( MJE_NOTIFY_ACTIVATED_USER, $user_id );
    }

    /**
     * Action after approved withdrawal
     *
     * @param object $result
     * @since 1.3
     * @author Tat Thien
     */
    public function approve_withdrawal( $result ) {
        $this->notification_action->create( MJE_NOTIFY_APPROVE_WITHDRAWAL , $result->post_author );
    }

    /**
     * Action after declined withdrawal
     *
     * @param object $result
     * @since 1.3
     * @author Tat Thien
     */
    public function decline_withdrawal( $result ) {
        $this->notification_action->create( MJE_NOTIFY_DECLINE_WITHDRAWAL , $result->post_author );
    }

    /**
     * Add notification when user use credit to checkout
     *
     * @param int $user_id
     * @param int|float $total
     * @since 1.3
     * @author Tat Thien
     */
    public function checkout_mjob_by_credit( $user_id, $total ) {
        $code = 'type=checkout_mjob_by_credit';
        $code .= '&amount=' . $total;
        $this->notification_action->create( $code, $user_id );
    }

    /**
     * Add notification for seller when they get a new mjob order
     *
     * @param int|string $mjob_order_id
     * @since 1.3
     * @author Tat Thien
     */
    public function updated_mjob_order( $mjob_order_id ) {
        $mjob_order = mje_mjob_order_action()->get_mjob_order( $mjob_order_id );
        if( 'publish' == $mjob_order->post_status ) {
            $code = 'type=new_mjob_order';
            $code .= '&post_id=' . $mjob_order_id;
            $code .= '&post_parent=' . $mjob_order->post_parent;
            $code .= '&sender=' . $mjob_order->post_author;
            $this->notification_action->create( $code, $mjob_order->seller_id );
        }
    }

    /**
     * Add notification for seller when they get a new mjob order
     * when admin approve orders
     *
     * @param int|string $mjob_order_id
     * @since 1.3
     * @author Tat Thien
     */
    public function transform_status( $new_status, $old_status, $post ) {
        if( 'mjob_order' == $post->post_type ) {
            $mjob_order = mje_mjob_order_action()->get_mjob_order( $post->ID );
            if( 'publish' == $new_status && 'pending' == $old_status ) {
                $code = 'type=new_mjob_order';
                $code .= '&post_id=' . $post->ID;
                $code .= '&post_parent=' . $mjob_order->post_parent;
                $code .= '&sender=' . $mjob_order->post_author;
                $this->notification_action->create( $code, $mjob_order->seller_id );
            } else if( 'trash' == $new_status && is_super_admin() ) {
                // admin delete order
                $code = 'type=admin_delete_mjob_order';
                $code .= '&mjob_id=' . $mjob_order->mjob_id;
                $code .= '&mjob_order_id=' . $mjob_order->ID;
                $this->notification_action->create( $code, $mjob_order->buyer_id );
                $this->notification_action->create( $code, $mjob_order->seller_id );
            } else if( 'trash' == $old_status && is_super_admin() ) {
                // admin restore order
                $code = 'type=admin_restore_mjob_order';
                $code .= '&mjob_id=' . $mjob_order->mjob_id;
                $code .= '&mjob_order_id=' . $mjob_order->ID;
                $this->notification_action->create( $code, $mjob_order->buyer_id );
                $this->notification_action->create( $code, $mjob_order->seller_id );
            }
        }

        // Transform mjob post status
        if( 'mjob_post' == $post->post_type && is_super_admin() ) {
            if( 'trash' == $new_status ) {
                // Admin delete mjob in backend
                $code = 'type=admin_delete_mjob';
                $code .= '&mjob_id=' . $post->ID;
                $this->notification_action->create( $code, $post->post_author );
            } else if( 'trash' == $old_status ) {
                // Admin restore mjob in backend
                $code = 'type=admin_restore_mjob';
                $code .= '&mjob_id=' . $post->ID;
                $this->notification_action->create( $code, $post->post_author );
            }
        }
    }

    /**
     * Add notification when buyer review seller based on mjob order
     *
     * @param int $mjob_id
     * @param array $args
     * @since 1.3
     * @author Tat Thien
     */
    public function review_mjob_order( $mjob_id, $review_id, $args ) {
        $mjob_order = mje_mjob_order_action()->get_mjob_order( $args['order_id'] );
        $code = 'type=review_mjob_order';
        $code .= '&mjob_id=' . $mjob_id;
        $code .= '&review_id=' . $review_id;
        $code .= '&score=' . $args['score'];
        $code .= '&sender=' . $mjob_order->post_author;
        $this->notification_action->create( $code, $mjob_order->seller_id );
    }

    /**
     * Add notification when buyer accept the delivery without review
     *
     * @param array $request
     * @since 1.3
     * @author Tat Thien
     */
    public function finish_mjob_order( $request ) {
        $review_action = new MJE_Review_Action();
        if( ! $review_action->check_user_reviewed( $request['mjob_id'], $request['ID'] ) ) {
            $code = 'type=finish_mjob_order';
            $code .= '&mjob_id=' . $request['mjob_id'];
            $code .= '&mjob_order_id=' . $request['ID'];
            $code .= '&sender=' . $request['post_author'];
            $this->notification_action->create( $code, $request['seller_id'] );
        }
    }

    /**
     * Add notification when buyer or seller dispute mjob order
     *
     * @param array $request
     * @since 1.3
     * @author Tat Thien
     */
    public function dispute_mjob_order( $request ) {
        global $user_ID;
        $code = 'mjob_id=' . $request['mjob_id'];
        $code .= '&mjob_order_id=' . $request['ID'];
        if( $user_ID == $request['buyer_id'] ) {
            $code .= '&type=buyer_dispute_mjob_order';
            $code .= '&sender=' . $user_ID;
            $this->notification_action->create( $code, $request['seller_id'] );
        } else if( $user_ID == $request['seller_id'] ) {
            $code .= '&type=seller_dispute_mjob_order';
            $code .= '&sender=' . $user_ID;
            $this->notification_action->create( $code, $request['buyer_id'] );
        }
    }

    /**
     * Add notification for both seller and buyer when admin resolve the disputing mjob order
     *
     * @param array $request
     * @since 1.3
     * @author Tat Thien
     */
    public function resolve_mjob_order( $request ) {
        $mjob_order = mje_mjob_order_action()->get_mjob_order( $request['post_parent' ] );
        $code = 'type=resolve_mjob_order';
        $code .= '&mjob_id=' . $mjob_order->mjob_id;
        $code .= '&mjob_order_id=' . $mjob_order->ID;
        $code .= '&winner_id=' . $request['winner'];
        // Send notification for both seller and buyer
        $this->notification_action->create( $code, $mjob_order->seller_id );
        $this->notification_action->create( $code, $mjob_order->buyer_id );
    }

    /**
     * Add notification for buyer when seller start mjob order
     *
     * @param array $data
     * @since 1.3
     * @author Tat Thien
     */
    public function start_mjob_order( $data ) {
        $mjob_order = mje_mjob_order_action()->get_mjob_order( $data['id_order'] );
        $code = 'type=start_mjob_order';
        $code .= '&mjob_id=' . $mjob_order->mjob_id;
        $code .= '&mjob_order_id=' . $mjob_order->ID;
        $code .= '&sender=' . $mjob_order->seller_id;
        $this->notification_action->create( $code, $mjob_order->buyer_id );
    }

    /**
     * Add notification for buyer when seller delayed mjob order
     *
     * @param array $request
     * @since 1.3
     * @author Tat Thien
     */
    public function delay_mjob_order ( $request ) {
        $mjob_order = mje_mjob_order_action()->get_mjob_order( $request['ID'] );
        $code = 'type=delay_mjob_order';
        $code .= '&mjob_id=' . $mjob_order->mjob_id;
        $code .= '&mjob_order_id=' . $mjob_order->ID;
        $code .= '&sender=' . $mjob_order->seller_id;
        $this->notification_action->create( $code, $mjob_order->buyer_id );
    }

    /**
     * Add notification for buyer when seller delivered mjob order
     *
     * @param int|string $mjob_order_id
     * @since 1.3
     * @author Tat Thien
     */
    public function deliver_mjob_order( $mjob_order_id ) {
        $mjob_order = mje_mjob_order_action()->get_mjob_order( $mjob_order_id );
        $code = 'type=deliver_mjob_order';
        $code .= '&mjob_id=' . $mjob_order->mjob_id;
        $code .= '&mjob_order_id=' . $mjob_order->ID;
        $code .= '&sender=' . $mjob_order->seller_id;
        $this->notification_action->create( $code, $mjob_order->buyer_id );
    }

    public function pause_mjob( $result ) {
        if( is_super_admin() ) {
            $code = 'type=admin_pause_mjob';
            $code .= '&mjob_id=' . $result->ID;
            $this->notification_action->create( $code, $result->post_author );
        }
    }

    public function archive_mjob( $result ) {
        if( is_super_admin() ) {
            $code = 'type=admin_archive_mjob';
            $code .= '&mjob_id=' . $result->ID;
            $this->notification_action->create( $code, $result->post_author );
        }
    }

    public function approve_mjob( $result ) {
        // et_log('admin approve job action');
        if( is_super_admin() ) {
            $code = 'type=admin_approve_mjob';
            $code .= '&mjob_id=' . $result->ID;
            $this->notification_action->create( $code, $result->post_author );
        }
    }

    public function reject_mjob( $result ) {
        if( is_super_admin() ) {
            $code = 'type=admin_reject_mjob';
            $code .= '&mjob_id=' . $result->ID;
            $this->notification_action->create( $code, $result->post_author );
        }
    }

    public function edit_mjob( $result ) {
        if( is_super_admin() ) {
            $code = 'type=admin_edit_mjob';
            $code .= '&mjob_id=' . $result->ID;
            $this->notification_action->create( $code, $result->post_author );
        }
    }
}

new MJE_Notication_Hook();