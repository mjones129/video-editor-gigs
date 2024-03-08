<?php

/**
 * Class render order list in engine themes backend
 * - list order
 * - search order
 * - load more order
 * @since 1.0
 * @author Dakachi
 */
class AE_OrderList
{
    /**
     * construct a user container
     */
    function __construct($args = array() , $roles = '') {
        $this->args = $args;
        $this->roles = $roles;
        add_action('wp_ajax_ae-fetch-orders', array(
            $this,
            'fetch_orders'
        ));
        add_action('wp_ajax_ae-sync-order', array(
            $this,
            'sync_order'
        ));

        /**
         * Update package purchases data
         * update meta for order: et_order_product_type, et_invoice_no
         * @todo Remove in version 1.1.4
         */
        $this->update_package_purchages_data();
    }

    /**
     * Update package purchases data
     * update meta for order: et_product_type, et_invoice_no
     * @todo Remove in version 1.1.4
     */
    function update_package_purchages_data() {
        if(!get_option('mjob_update_package')) {
            $query = new WP_Query(array(
                'post_type' => 'mjob_post',
                'meta_key' => 'et_ad_order',
                'showposts' => - 1,
                'post_status' => array(
                    'publish',
                    'pending',
                    'draft',
                    'archive',
                    'reject'
                )
            ));

            while($query->have_posts()) {
                $query->the_post();
                global $post;

                $order_id = get_post_meta($post->ID, 'et_ad_order', true);

                if(!empty($order_id)) {
                    update_post_meta($post->ID, 'et_invoice_no', $order_id);
                    delete_post_meta($post->ID, 'et_ad_order');

                    update_post_meta($order_id, 'et_order_product_type', 'pack');
                }
            }

            update_option('mjob_update_package', true);
        }
    }

    /**
     * render list of orders
     */
    function render() {
        $support_gateway = apply_filters('ae_support_gateway', array(
            'cash' => __("Cash", 'enginethemes') ,
            'paypal' => __("Paypal", 'enginethemes') ,
            '2checkout' => __("2Checkout", 'enginethemes') ,
            'credit' => __("Credit", 'enginethemes'),
        ));

        $post_status = array(
            'publish' => __('Publish', 'enginethemes'),
            'pending'  => __('Pending', 'enginethemes'),
            'draft'   => __('Draft', 'enginethemes')
        );
        $orders = AE_Order::get_orders(array(
            'meta_key' => 'et_order_product_type',
            'meta_value' => 'pack'
        ));
        require_once('page-payments.php');
?>
	
	<script type="text/javascript">
		(function($){
			$(document).ready(function(){
				if (typeof AE.Views.OrderList !== 'undefined') {
		            var order_view = new AE.Views.OrderList({
		                el: $('.order-container'),
                        item_wrapper: '.list-payment',
		                pages : parseInt('<?php echo $orders->max_num_pages; ?>')
		            });
		        }
			});
		})(jQuery);
	</script>
    <?php
    }
    
    //TODO: block control order
    function fetch_orders() {
        if (!current_user_can('edit_others_posts')) return;
        
        $request = $_REQUEST;
        if (isset($request['search']) && $request['search'] != '') {
            /**
             * search post with keyword
             */

            $posts = new WP_Query(array(
                'post_type' => 'mjob_post',
                's' => $request['search'],
                'meta_key' => 'et_invoice_no',
                'showposts' => - 1,
                'post_status' => array(
                    'publish',
                    'pending',
                    'draft',
                    'archive',
                    'reject'
                )
            ));
            /**
             * build orders id param
             */
            $order_ids = array('0');
            while ($posts->have_posts()) {
                $posts->the_post();
                global $post;
                $order_id = get_post_meta($post->ID , 'et_invoice_no', true);
                if ($order_id) {
                    $order = get_post($order_id);
                    $order_ids = array_merge($order_ids, (array)$order->ID);
                }
            }

            // add args post__in to query order
            if (!empty($order_ids)) $request['post__in'] = $order_ids;
        }

        
        /**
         * get orders
         */

        $request['meta_key'] = 'et_order_product_type';
        $request['meta_value'] = 'pack';

        $orders = AE_Order::get_orders($request);
        $content = '';
        ob_start();
        while ($orders->have_posts()) {
            $orders->the_post();
            global $post;
            get_template_part('includes/modules/MJE_Admin/module/package-purchases/template/order', 'item');
        }
        $content = ob_get_clean();
        // Get pagination
        ob_start();
        ae_pagination($orders,$request['paged'],'page');
        $paginate = ob_get_clean();

        $response = array();
        $response['pages'] = $orders->max_num_pages;
        $response['page'] = $_REQUEST['paged'];
        $response['data'] = $content;
        $response['paginate'] = $paginate;
        
        if (!$orders->have_posts()) $response['msg'] = __("Sorry, no order found for your query.", 'enginethemes');

        wp_send_json($response);
    }
    
    /**
     * catch ajax callback ae-sync-order to update order status and send json to clien
     * @return null
     *
     * @since  1.2
     * @author  Dakachi
     */
    function sync_order() {
        if (!current_user_can('manage_options')) {
        	wp_send_json(array(
	            'success' => false
	        ));
        }

        $order_status = $_REQUEST['status'];
        $order_id = $_REQUEST['ID'];
        
        $order = get_post($order_id);

        if($order->post_parent){
            $post_id = wp_update_post(array(
                'ID' => $order->post_parent,
                'post_status' => $order_status
            ));    
        }

        // update order status
        $order_id = wp_update_post(array(
            'ID' => $order_id,
            'post_status' => $order_status
	    ));	

        if ($order_id) {
            wp_send_json(array(
                'success' => true,
                'msg' => __("Update order successfull.", 'enginethemes')
            ));
        } else {
            wp_send_json(array(
                'success' => false
            ));
        }
    }
}