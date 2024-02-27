<?php
class AE_WithdrawHistory extends AE_Posts
{
    public static $instance;

    /**
     * get_instance method
     *
     */
    public static function getInstance()
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
        parent::__construct('ae_withdraw_history', $taxs, $meta_data, $localize);
    }
    /**
     * init for this class
     *
     * @param void
     * @return void
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    public function init()
    {
        $this->ae_withdraw_register_post_type();
    }
    /**
     * register post type
     *
     * @param void
     * @return void
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    public function  ae_withdraw_register_post_type(){

        register_post_type('ae_withdraw_history', array(
            'labels' => array(
                'name' => __('Credit History', 'enginethemes') ,
                'singular_name' => __('Credit History', 'enginethemes') ,
                'add_new' => __('Add New', 'enginethemes') ,
                'add_new_item' => __('Add New Credit history', 'enginethemes') ,
                'edit_item' => __('Edit Credit history', 'enginethemes') ,
                'new_item' => __('New Credit history', 'enginethemes') ,
                'all_items' => __('All Credit Histories', 'enginethemes') ,
                'view_item' => __('View Credit history', 'enginethemes') ,
                'search_items' => __('Search Credit histories', 'enginethemes') ,
                'not_found' => __('No Credit history found', 'enginethemes') ,
                'not_found_in_trash' => __('No Credit histories found in Trash', 'enginethemes') ,
                'parent_item_colon' => '',
                'menu_name' => __('Credit Histories', 'enginethemes')
            ) ,
            'public' => false,
            'publicly_queryable' => true,
            'show_ui' => false,
            'show_in_menu' => false,
            'query_var' => true,
            'rewrite' => true,

            'capability_type' => 'post',
            // 'capabilities' => array(
            //     'manage_options'
            // ) ,
            'has_archive' => 'packs',
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => array(
                'title',
                'editor',
                'author',
                'custom-fields'
            )
        ));
        global $ae_post_factory;
        $tax = array();
        $meta = array(
            'history_type',
            'history_status',
            'amount',
            'currency',
            'history_time',
            'user_balance',
        );
        $ae_post_factory->set('ae_withdraw_history', new AE_Posts('ae_withdraw_history', $tax, $meta));
    }
    /**
      * save History
      *
      * @param array $args
      * @return integer $history_id
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function saveHistory($args){
        global $user_ID;
        $history_post = array(
            'post_type' => 'ae_withdraw_history',
            'post_status' => 'publish',
            'post_author' => $user_ID,
            'post_title' => __('credit history', 'enginethemes'),
            'post_content' => 'charge ' . $args['amount']
        );
        $default = array(
            "destination" => '',
            "source_transaction" => '',
            "statement_descriptor" => __("Freelance escrow", 'enginethemes')
        );
        $args = wp_parse_args($args, $default);
        if( isset( $args['post_title'] ) ){
            $history_post['post_title']= $args['post_title'];
        }
        $history_id = wp_insert_post($history_post);
        update_post_meta($history_id, 'history_type', $args['history_type']);
        update_post_meta($history_id, 'history_status', $args['status']);
        update_post_meta($history_id, 'amount', $args['amount']);
        update_post_meta($history_id, 'currency', $args['currency']);
        update_post_meta($history_id, 'destination', $args['destination']);
        update_post_meta($history_id, 'source_transaction', $args['source_transaction']);
        update_post_meta($history_id, 'statement_descriptor', $args['statement_descriptor']);
        update_post_meta($history_id, 'user_balance', ae_price_format(AE_WalletAction()->getUserWallet($user_ID)->balance));
        update_post_meta($history_id, 'payment_method', $args['payment_method']);
        return $history_id;
    }
    /**
      * retrieve charge information
      *
      * @param integer $id
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public function retrieveHistory($id){
        global $ae_post_factory;
        $history_obj = $ae_post_factory->get('ae_withdraw_history');
        $post = get_post($id);
        $history = $history_obj->convert($post);
        return $history;
    }
}
class AE_WithdrawHistoryAction extends AE_PostAction
{
    function __construct($post_type = 'ae_withdraw_history')
    {
        $this->post_type = 'ae_withdraw_history';
        // add action fetch profile
        $this->add_filter('ae_convert_ae_withdraw_history', 'ae_withdraw_convert_history');
        $this->add_ajax('fre-fetch-history', 'fetch_post');
    }
    /**
      * convert history object
      *
      * @param object $result
      * @return void
      * @since 1.0
      * @package FREELANCEENGINE
      * @category FRE CREDIT
      * @author Jack Bui
      */
    public  function ae_withdraw_convert_history($result){
        global $user_ID;

        $payment_method_txt_arr = mje_render_payment_name();

        $history_status_txt_arr = array(
            'completed' => __('Successful', 'enginethemes'),
            'cancelled' => __('Rejected', 'enginethemes'),
            'pending' => __('Pending', 'enginethemes')
        );

        $post_date = get_post_time('Y-m-d h:i:s', true, $result->ID);
        $result->history_time = sprintf( _x( '%s ago', '%s = human-readable time difference', 'enginethemes' ), human_time_diff( strtotime($post_date), time() ));
		$result->date_text=date(get_option('date_format').' '.get_option('time_format'),strtotime($post_date)).' '.mje_text_timezone();
        $result->amount_text = ae_price_format($result->amount);
        // History status text
        $result->history_status_text = $history_status_txt_arr[$result->history_status];

        // Payment method text
        $payment_method = get_post_meta($result->ID, 'payment_method', true);
        $result->payment_method_text = $payment_method_txt_arr[$payment_method];
        return $result;
    }
    /**
     * filter query
     *
     * @param array $query_args
     * @return array $query_args after filter
     * @since 1.0
     * @package FREELANCEENGINE
     * @category FRE CREDIT
     * @author Jack Bui
     */
    public function filter_query_args($query_args){
        if( isset($_REQUEST['query']['history_type']) && !empty($_REQUEST['query']['history_type']) ){
            $query_args['meta_query'] = array(
                array(
                'key'=> 'history_type',
                'value'=> $_REQUEST['query']['history_type'] )
                );
        }
        $date_format = get_option( 'date_format' );
        $from = date($date_format);
        $to = date($date_format);
        $flag = false;
        if( isset($_REQUEST['query']['ae_withdraw_from']) && !empty($_REQUEST['query']['ae_withdraw_from']) ){
            $from = date($date_format, strtotime($_REQUEST['query']['ae_withdraw_from'] ));
            $flag = true;
        }
        if( isset($_REQUEST['query']['ae_withdraw_to']) && !empty($_REQUEST['query']['ae_withdraw_to']) ){
            $to = date($date_format, strtotime($_REQUEST['query']['ae_withdraw_to']) ) ;
            $flag = true;
        }
        if( $flag ) {
            $query_args['date_query'] = array(
                'after' => $from,
                'before' => $to,
            );
        }
        return $query_args;
    }
}
new AE_WithdrawHistoryAction();