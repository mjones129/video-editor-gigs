<?php
/**
 * Created by PhpStorm.
 * User: Jack Bui
 * Date: 1/21/2016
 * Time: 2:23 PM
 */
class MJE_Order extends ET_Order{
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
    public  function __construct($order = array(), $ship = array()){
        parent::__construct($order, $ship);
        if( is_numeric($order) && $order > 0  ){
            $this->update_discount($order);
        }
    }
    function update_discount($order_id){

        if( ! empty($this->_coupon_code) ){
            $is_count = get_post_meta($order_id,'is_count', true);

            if( ! $is_count ){
                mje_update_time_used_discount($this->_coupon_code);
                update_post_meta($order_id, 'is_count', 1);
            }
        }

    }
    public function get_total() {
        return parent::get_total();
    }
    static function register_order_post_type(){

    }
    /**
     * add product
     *
     * @param object $product
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function add_product($product, $number = 1) {

        $this->_products[$product->ID] = array(
            'ID' => $product->ID,
            'NAME' => $product->post_title,
            'AMT' => $this->calculate_vat_fee($product->amount),
            'QTY' => $number,
            'L_DESC' => $product->post_content,
            'TYPE' => $product->post_type
        );


        $total_before_discount = (float) $product->amount*$number;

        // $this->_total_before_discount += number_format( $total_before_discount, 2, '.', ''); // 1/3/9/3
        $this->_total_before_discount = number_format( $total_before_discount, 2, '.', '');// 1.3.9.4 php8
        $this->_total = number_format($this->calculate_discount($this->_total_before_discount) , 2, '.', '');
        $this->_total = number_format($this->calculate_vat_fee($this->_total) , 2, '.', '');

        $this->_product_id = $product->ID;
        $this->_product_type = $product->post_type;
        $this->_payment = $product->payment_type;

        $this->update_order();
    }
    /**
     * Override parent class
     */
    function update_order() {
        parent::update_order();
    }

    /**
     * generate data
     *
     * @param void
     * @return array $return
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function generate_data_to_pay() {
        $return = parent::generate_data_to_pay();
        $return['order_name'] = $this->_payment;
        $return['product_id'] = $this->_product_id;
        $return['ID'] = $this->_ID;
        return $return;
    }
    /**
     * get order data
     *
     * @param void
     * @return array $order_data
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function get_order_data() {
        return array(
            'ID' => $this->_ID,
            'payer' => $this->_payer,
            'product_id' => $this->_product_id,
            'created_date' => $this->_created_date,
            'status' => $this->_stat,
            'payment' => $this->_payment,
            'products' => $this->_products,
            'product_type' => $this->_product_type,
            'currency' => $this->_currency,
            'payment_code' => $this->_payment_code,
            'total' => $this->_total,
            'total_before_discount' => $this->_total_before_discount,
            'discount_rate' => $this->_discount_rate,
            'discount_method' => $this->_discount_method,
            'paid_date' => $this->_paid_date,
            'shipping' => $this->_shipping
        );
    }

}