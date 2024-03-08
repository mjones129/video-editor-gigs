<?php
global $post, $ae_post_factory, $user_ID;
/**
 * Created by PhpStorm.
 * User: Dang Bui
 * Date: 5/24/2016
 * Time: 11:17
 */
class customOrderAction extends AE_Base
{
    public function __construct()
    {
        $this->add_ajax('show-custom-order-detail', 'customOrderDetail');
    }

    /**
     * Load custom order detail before use click title mjob in list custom order
     */
    public function customOrderDetail() {
        global $user_ID;
        $request = $_REQUEST;
        $post = get_post($request['custom_order_id']);
        if( ! $post) {
            return wp_send_json(
                array(
                    'success'=> false,
                    'msg'=> __('Custom Order not exist!', 'enginethemes')
                )
            );
        }

        $post->post_content = wpautop($post->post_content);

        $post->post_modified = date(get_option('date_format'), strtotime($post->post_modified));

        $post->attach_file = mje_get_list_attach_files($post->ID);

        if ( $budget = get_post_meta($post->ID,'custom_order_budget', true) )
            $post->budget = mje_shorten_price($budget);

        if (  $deadline = (int)get_post_meta($post->ID, 'custom_order_deadline', true) )
            $post->deadline = $deadline;

        if( $mjob_id = get_post_meta($post->ID, 'custom_order_mjob', true))
            $post->mjob_name = '<a href="'. get_the_permalink($mjob_id) .'" target="_blank">'. get_the_title($mjob_id) .'</a>';

        $post->is_offer = false;

//        Get offer
        if($id_offer = get_post_meta($post->ID, 'custom_offer_id', true)) {
            $post->is_offer = true;
            $offer = get_post($id_offer);
            $post->offer_content = wpautop($offer->post_content);

            if( $offer_budget = get_post_meta($id_offer, 'custom_offer_budget', true))
                $post->offer_budget = mje_shorten_price($offer_budget);

            if( $offer_etd = (int)get_post_meta($id_offer, 'custom_offer_etd', true))
                $post->offer_etd = $offer_etd;

            if( $offer_status = get_post_meta($id_offer, 'custom_order_status', true))
                $post->order_status = $offer_status;

            $post->offer_attach_file = mje_get_list_attach_files($id_offer);
        }

        $post->custom_order_status = false;
        if($custom_order_status = get_post_meta($post->ID, 'custom_order_status', true)) {
            if(in_array($custom_order_status, array('decline', 'reject', 'checkout'))) {
                $arr_custom_order_status = array(
                    'decline' => '<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>' . __('This custom order has been declined.','enginethemes'),
                    'reject' =>'<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>' . __('This offer has been rejected', 'enginethemes'),
                );

                // Get order detail of custom order
                $args = array(
                    'post_type' => 'mjob_order',
                    'post_status' => array('publish', 'late', 'delivery', 'disputed', 'disputing', 'finished', 'pending'),
                    'meta_query' => array(
                        array(
                            'key' => 'custom_order_id',
                            'value' => $post->ID,
                            'compare' => '='
                        )
                    )
                );
                $order = get_posts($args);
                if($order) {
                    $order_link = get_the_permalink($order[0]->ID);
                    $seller_id = get_post_meta($order[0]->ID, 'seller_id', true);
                    if('pending' == $order[0]->post_status && $seller_id == $user_ID) {
                        $arr_custom_order_status['checkout'] = '<i class="fa fa-check-circle" aria-hidden="true"></i>' . __('This offer has been accepted. Payment is currently pending. Please wait.', 'enginethemes');
                    } else {
                        $arr_custom_order_status['checkout'] = '<i class="fa fa-check-circle" aria-hidden="true"></i>' . sprintf(__('This offer has been accepted. View order detail <a href="%s" target="_blank">here.</a>', 'enginethemes'), $order_link);
                    }
                }

                $post->custom_order_status = $custom_order_status;
                $post->custom_order_status_text = $arr_custom_order_status[$custom_order_status];
            }
        }

        $response = array(
            'success' => true,
            'data' => $post
        );

        return wp_send_json($response);
    }

}
new customOrderAction();