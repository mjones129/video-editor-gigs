<?php
class AE_Order extends ET_Order
{

    protected $payment_package;
    protected $order_name;

    public function __construct($order, $ship = array()) {

        if (is_array($order)) {
            $this->payment_package = empty($order['payment_plan']) ? '' : (string)$order['payment_plan'];
            $this->payment_plan =  $this->payment_package;

            $this->order_name = empty($order['order_name']) ? __("Post ad", 'enginethemes') : $order['order_name'];
            parent::__construct($order, $ship);
            $this->update_order();
        } else {
            parent::__construct($order, $ship);

            $this->_product_id = get_post_meta($order, 'et_order_product_id', true);
            $this->payment_plan = get_post_meta($order, 'et_order_plan_id', true);

            $this->payment_package = get_post_meta($order, 'et_order_plan_id', true);
            $this->update_discount($order);
        }
    }
    function update_discount($order_id){

        if( ! empty($this->_coupon_code) ){

            $is_count = get_post_meta($order_id,'is_count', true);
           // et_log('is_count: '.$is_count);
            if( ! $is_count ){
               // et_log('line 34');
                mje_update_time_used_discount($this->_coupon_code);
                update_post_meta($order_id, 'is_count', 1);
            }
        }

    }
    public function get_order_data() {
        return array(
            'ID' => $this->_ID,
            'payer' => $this->_payer,
            'product_id' => $this->_product_id,
            'created_date' => $this->_created_date,
            'status' => $this->_stat,
            'payment' => $this->_payment,
            'products' => $this->_products,

            'currency' => $this->_currency,
            'payment_code' => $this->_payment_code,
            'total' => $this->_total,
            'paid_date' => $this->_paid_date,
            'shipping' => $this->_shipping,
            'payment_package' => $this->payment_package,
            'payment_plan' => $this->payment_plan
        );
    }

    /**
     * Override parent class
     */
    function update_order() {
        parent::update_order();
        update_post_meta($this->_ID, 'et_order_plan_id', $this->payment_package);
    }

    public function generate_data_to_pay() {
        $return = parent::generate_data_to_pay();
        $return['payment_package'] = $this->payment_package;
        $return['order_name'] = $this->order_name;
        $return['product_id'] = $this->_product_id;
        return $return;
    }

    /**
     * get orders
     * @param array $args
     * @return object $order_query
     */
    public static function get_orders($args = array()) {
        $default_args = array(
            'payment' => 0,
            'paged' => 0,
            'post_status' => array(
                'pending',
                'publish',
                'draft'
            ) ,
            'post__in' => ''
        );
        $args = wp_parse_args($args, $default_args);

        $args['post_type'] = 'order';

        if ($args['payment']) {
            $args['meta_query'] = array(
                'relation' => 'AND',
                array(
                    'key' => 'et_order_gateway',
                    'value' => $args['payment']
                )
            );
        }
        unset($args['payment']);

        $order_query = new WP_Query($args);
        return $order_query;
    }

    public function set_payment_plan($plan_id) {
        $this->payment_plan = $plan_id;
    }

    public function add_product($product, $number = 1) {
        $this->_products[$product['ID']] = array(
            'ID' => $product['ID'],
            'NAME' => $product['post_title'],
            'AMT' => $this->calculate_vat_fee($product['et_price']),
            'QTY' => $number,
            'L_DESC' => $product['post_content'],
            'TYPE' => $product['post_type']
        );
        $et_price = (float) $product['et_price'];
        $product_price = number_format($et_price * $number, 2, '.', '');

        // $this->_total_before_discount += $et_price;// previous version 1.3.9.4
        $this->_total_before_discount = $product_price; // since verion 1.3.9.5
        $this->_total = number_format($this->calculate_discount($this->_total_before_discount) , 2, '.', '');
        $this->_total = number_format($this->calculate_vat_fee($this->_total) , 2, '.', '');

        $this->_product_id = $product['post_id'];
        $this->_product_type = $product['post_type'];
        $this->update_order();
    }
}

