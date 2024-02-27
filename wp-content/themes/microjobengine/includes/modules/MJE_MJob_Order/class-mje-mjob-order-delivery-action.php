<?php
class MJE_MJob_Order_Delivery_Action extends MJE_Post_Action{
    public static $instance;
    public $mail = '';
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
    public  function __construct($post_type = 'order_delivery'){
        parent::__construct($post_type);
        $this->add_ajax('ae-fetch-order_delivery', 'fetch_post');
        $this->add_ajax('ae-order_delivery-sync', 'sync_delivery');
        $this->add_filter('ae_convert_order_delivery', 'convert');
        $this->ruler = array(
            'post_content',
            'post_parent'
        );

        $this->mail = MJE_Mailing::get_instance();
    }
    /**
     * sync Post function
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function sync_delivery(){
        global $user_ID, $ae_post_factory;
        $request = $_POST;

        $response = $this->validatePost( $request );
        if( !$response['success'] ){
            wp_send_json( $response );
            exit;
        }
        $request['post_title'] = sprintf( __( 'Delivery for: %s', 'enginethemes' ), $response['order_title'] );
        $order = mje_mjob_action()->get_mjob( $request['post_parent'] );
        $request['post_status'] = 'publish';
        $response = $this->sync_post( $request );
        if( $response['success'] ){
            //Update total delivery order user total
            if( $data = $response['data'] ) {
                $this->add_user_number_delivery_order( $data->post_author );
            }

            $my_post = array(
                'ID' => $response['data']->post_parent,
                'post_status'=> 'delivery',
            );
            wp_update_post( $my_post );
            $post_date = get_the_time( 'Y-m-d H:i:s', $response['data']->ID );
            update_post_meta( $response['data']->post_parent, 'order_delivery_day', $post_date );
            update_post_meta( $response['data']->post_parent, 'order_countdown_delivery', $request['order_countdown_delivery'] );

            // Create change log
            $change_log_id = mje_add_mjob_order_changelog( $response['data']->post_parent, $response['data']->post_author, 'delivery' );

            // Send email order delivery
            if($response['data']->post_status == 'publish') {
                $this->mail->delivery_order($response['data'], $post_date);
            }

            /**
             * Fire action when seller deliver mjob order
             *
             * @param int|string mjob order id
             * @since 1.3
             * @author Tat Thien
             */
            do_action( 'mje_delivered_mjob_order', $response['data']->post_parent );
        }
        wp_send_json($response);
    }

    /**
     * Add number delivery order
     * @param $user_id
     * @author: Dang Bui
     */
    private function add_user_number_delivery_order($user_id) {
        $mjob_delivery = (int) get_user_meta($user_id,'mjob_delivery_order', true);
        $mjob_delivery = $mjob_delivery + 1;
        return update_user_meta($user_id, 'mjob_delivery_order', $mjob_delivery);
    }

    /**
     * convert post
     *
     * @param object $result
     * @return object $result after convert
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function convert( $result ){
        global $user_ID;
        $result->author_name = get_the_author_meta( 'display_name', $result->post_author );
        $order = get_post( $result->post_parent );
        $result->order_author = '';
        if( $order ){
            $result->order_author = $order->post_author;
        }
        if (current_user_can('manage_options') || $result->post_author == $user_ID || $result->order_author == $user_ID) {
            $children = get_children(array(
                'numberposts' => 15,
                'order' => 'ASC',
                'post_parent' => $result->ID,
                'post_type' => 'attachment'
            ));

            $result->et_carousels = array();

            foreach ($children as $key => $value) {
                $result->et_carousels[] = $value;
            }
        }
        return $result;
    }
    /**
     * validate data
     *
     * @param array $data
     * @return array $result
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function validatePost($data){
        $result = array(
            'success'=> false,
            'msg'=> __('Failed!', 'enginethemes')
        );
        if( isset($data['post_parent']) ){
            $order = get_post($data['post_parent']);
            if( $order ){
                $args = array(
                    'post_type'=> 'order_delivery',
                    'post_parent'=> $data['post_parent']
                );
                $q = new WP_Query($args);
                if( $q->found_posts == 0 ){
                    return array(
                        'success'=> true,
                        'msg'=> __('Success!', 'enginethemes'),
                        'order_title'=> $order->post_title
                    );
                }
            }
        }
        return $result;
    }
    /**
     * override filter query args
     *
     * @param array $query_args
     * @return array $query_args
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function filter_query_args($query_args)
    {
        global $user_ID;
        return $query_args;
    }
}
new MJE_MJob_Order_Delivery_Action();