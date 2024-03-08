<?php

/**
 * Class render order list in engine themes backend
 * - list order
 * - search order
 * - load more order
 * @since 1.0
 * @author Dakachi
 */
class MJE_MJob_Order_Container {

	/**
	 * construct a user container
	 */
	function __construct($args = array(), $roles = '') {
		$this->args = $args;
		$this->roles = $roles;
	}

	/**
	 * render list of withdraws list
	 */
	function render() {
		$mjobOrders = get_mjobOrders();
		$post_status = array(
			'' => __('All status', 'enginethemes'),
			'publish' => __('Publish', 'enginethemes'),
			'pending' => __('Pending', 'enginethemes'),
			'draft' => __('Draft', 'enginethemes'),
			'late' => __('Late', 'enginethemes'),
			'delivery' => __('Delivery', 'enginethemes'),
			'disputing' => __('Disputing', 'enginethemes'),
			'disputed' => __('Resolved', 'enginethemes'),
			'finished' => __('Finished', 'enginethemes'),
		);

		$payment_list = mje_get_payment_list(array('2checkout'));

		$sort_time = array(
			'desc' => __('Latest', 'enginethemes'),
			'asc' => __('Oldest', 'enginethemes'),
		)
		?>

        <div class="et-main-content order-container mjob-order-container" id="">
            <div class="et-main-main">
                <div class="group-wrapper">
                	<form action="<?php echo admin_url('?page=et-mjob-order');?>" method="get" id="#export-filters">
                    <div class="group-fields">
                        <div class="search-box et-member-search">
                            <p class="title-search"><?php _e("All Orders", 'enginethemes');?></p>
                            <div class="function-filter">
                                <span class="et-search-role">
                                    <select name="payment_type" id="" class="et-input">
                                         <option value="" ><?php _e("All payment types", 'enginethemes');?></option>
                                        <?php foreach ($payment_list as $key => $value) {?>
                                            <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                        <?php }?>
                                    </select>
                                </span>
                                <span class="et-search-role">
                                    <select name="post_status" id="" class="et-input">
                                        <?php foreach ($post_status as $k => $v) {?>
                                            <option value="<?php echo $k ?>"><?php echo $v ?></option>
                                        <?php }?>
                                    </select>
                                </span>
                                <span class="et-search-input">
                                    <input type="text" class="et-input order-search search" name="s" placeholder="<?php
_e("Search order name", 'enginethemes');?>">
                                       <i class="fa fa-search" aria-hidden="true"></i>
                                </span>
                            </div> <!-- function-filter !-->
                        </div>
                        <!-- // user search box -->

                        <div class="et-main-main no-margin clearfix overview list mjob-order-list-wrapper">
                            <!-- order list  -->
                            <div  class="list-payment-package class-mje-mjob-order-container.php">
                                <ul class="row title-list">
                                    <li class="col-md-5 col-sm-3"><?php _e('Order info', 'enginethemes');?></li>
                                    <li class="col-md-2 col-sm-2"></li>
                                    <li class="col-md-3 col-sm-4 time-purchase">
                                    <span class="sort-link sort-time">
                                        <a href="" class="orderby" data-sort="date" data-order="asc"><?php _e('Date purchased', 'enginethemes');?><i class="fa fa-sort" aria-hidden="true"></i></a>
                                     </span>
                                    </li>
                                    <li class="col-md-2 col-sm-3 payment-type">
                                        <span><?php _e('Payment type', 'enginethemes');?></span>
                                    </li>
                                </ul>
                                <ul class="list-inner list-payment list-mjob-orders users-list">
                                    <?php
                                    $btnDownload = 'hide';
                                    $mjoborder_data = array();
									if ($mjobOrders->have_posts()) {
										$btnDownload = '';
										global $post, $ae_post_factory;
										$mjoborder_obj = $ae_post_factory->get('mjob_order');

										while ($mjobOrders->have_posts()) {
											$mjobOrders->the_post();
											$convert = $mjoborder_obj->convert($post);
											$mjoborder_data[] = $convert;
											include TEMPLATEPATH . '/includes/modules/MJE_MJob_Order/template/mjob-order-item.php';
										}
									} else {
										echo '<li style="text-align: center;">';
										_e('There are no payments yet.', 'enginethemes');
										echo '</li>';
									}?>


                                </ul>
                                <button type="submit" name="submit" class="hide <?php echo $btnDownload;?>" value="download"><?php _e('Download','enginethemes');?> &nbsp; <i class="fa fa-download"></i>
                                </button>
                                <select name="download" class="btnExportOrder <?php echo $btnDownload;?>">
                                	<option value="0"><?php _e('Download','enginethemes');?></option>
                                	<option value="xml"><?php _e('Download XML','enginethemes');?></option>
                                	<option value="xls"><?php _e('Download Excel xls','enginethemes');?></option>

                                </select>
                                <div class="col-md-12">
                                    <div class="paginations-wrapper">
                                        <?php
                                        ae_pagination($mjobOrders, PAGINATION_START, 'page');
										wp_reset_query();
										?>
                                    </div>
                                </div>
                                <?php echo '<script type="data/json" class="mjob_order_data" >' . json_encode($mjoborder_data) . '</script>'; ?>
                            </div>
                        </div>

                    </div> <!-- end group field !-->
                </form>  <!-- end form !-->
                </div>
            </div>
            <!-- //user list -->
        </div>
        <style type="text/css">
        	.btnExportOrder{
        		background-color: transparent !important;
        		text-decoration1s: underline;
        		text-align: center;
        		float: right;
        		margin-right: 20px;
        		padding: 10px;
        		margin-top: 15px;
        	}
        	.no-items{
        		padding-top: 25px;
        	}
        	button.hide{
        		display: none;
        		opacity: 0;
        		z-index: -11;
        	}
        </style>
    <?php }
}

class MJE_MJob_Order_Admin_Action extends AE_PostAction {
	function __construct($post_type = 'mjob_order') {
		parent::__construct($post_type);

		$this->post_type = 'mjob_order';
		// add action fetch profile
		$this->add_ajax('mjob-admin-fetch-order', 'fetch_post');
		$this->add_filter('ae_convert_mjob_order', 'mjob_convert_order');
		$this->add_ajax('mjob-admin-order-sync', 'sync_order');
		$this->add_filter('ae_admin_globals', 'mjob_decline_msg');
		$this->gateway_order = array(
			'cash' => __("<p class='cash'>Cash</p>", 'enginethemes'),
			'paypal' => __("<p class='paypal'>Paypal</p>", 'enginethemes'),
			'2checkout' => __("<p class='checkout'>2Checkout</p>", 'enginethemes'),
			'credit' => __("<p class='credit'>Credit</p>", 'enginethemes'),
		);

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
	public function filter_query_args($query_args) {
		$query_args['orderby'] = 'post_date';
		$query_args['post_status'] = array('pending', 'publish', 'draft');
		if (isset($_REQUEST['query']['order'])) {
			$query_args['order'] = $_REQUEST['query']['order'];
		}
		if (isset($_REQUEST['query']['post_status'])) {
			$query_args['post_status'] = $_REQUEST['query']['post_status'];
		}

		if (isset($_REQUEST['query']['payment_type'])) {
			$query_args['meta_key'] = 'payment_type';
			$query_args['meta_value'] = $_REQUEST['query']['payment_type'];
		}

		return $query_args;
	}
	/**
	 * description
	 *
	 * @param object $result
	 * @return object $result;
	 * @since 1.0
	 * @package FREELANCEENGINE
	 * @category FRE CREDIT
	 * @author Jack Bui
	 */
	public function mjob_convert_order($result) {
		$result->mjob_order_edit_link = get_edit_post_link($result->ID);
		$result->mjob_order_link = get_the_permalink($result->ID);
		$result->mjob_order_author_url = get_author_posts_url($result->post_author, $author_nicename = '');
		$result->mjob_order_author_name = get_the_author_meta('display_name', $result->post_author);
		$gateway_order = mje_render_payment_name();
		if (array_key_exists((string) $result->payment_type, $gateway_order)) {
			$result->icon_gateway = $gateway_order[$result->payment_type];
		} else {
			$result->icon_gateway = $result->payment_type;
		}
		return $result;
	}
	/**
	 * sync withdraw
	 *
	 * @param void
	 * @return void
	 * @since 1.0
	 * @package FREELANCEENGINE
	 * @category FRE CREDIT
	 * @author Jack Bui
	 */
	public function sync_order() {
		global $ae_post_factory, $user_ID;
		$request = $_REQUEST;
		$mjob_order = $ae_post_factory->get('mjob_order');
		$invoice_status = 'pending';
		if (isset($request['publish']) && $request['publish'] == 1) {
			$request['post_status'] = 'publish';
			$invoice_status = 'publish';
		}
		if (isset($request['archive']) && $request['archive'] == 1) {
			$request['post_status'] = 'draft';
			$invoice_status = 'draft';
			unset($request['archive']);
		}
		// sync notify
		if (is_super_admin()) {
			$result = $mjob_order->sync($request);
			if ($result) {
				// Update invoice
				$invoice_id = get_post_meta($request['ID'], 'et_invoice_no', true);
				$invoice_id = wp_update_post(array(
					'ID' => $invoice_id,
					'post_status' => $invoice_status,
				));

				if ($result->post_status == 'draft') {
					do_action('mjob_decline_order', $result);
				}
				$response = array(
					'success' => true,
					'msg' => __('Update success!', 'enginethemes'),
					'data' => $result,
				);
			} else {
				$response = array(
					'success' => false,
					'msg' => __('Update failed!', 'enginethemes'),
				);
			}
		} else {
			$response = array(
				'success' => false,
				'msg' => __('Please login to your administrator to update withdraw!', 'enginethemes'),
			);
		}
		wp_send_json($response);
	}
	/**
	 * decline msg
	 *
	 * @param array $vars
	 * @return array $vars
	 * @since 1.0
	 * @package FREELANCEENGINE
	 * @category FRE CREDIT
	 * @author Jack Bui
	 */
	public function mjob_decline_msg($vars) {
		$vars['confirm_message'] = __('Are you sure to decline this request?', 'enginethemes');
		return $vars;
	}
}

/**
 * add footer template
 *
 * @param void
 * @return void
 * @since 1.0
 * @package FREELANCEENGINE
 * @category FRE CREDIT
 * @author Jack Bui
 */
function mjob_footer_function() {
	include_once TEMPLATEPATH . '/includes/modules/MJE_MJob_Order/template/mjob-order-item-js.php';
}
add_action('admin_footer', 'mjob_footer_function');
/**
 * script
 *
 * @param void
 * @return void
 * @since 1.0
 * @package MicrojobEngine
 * @category void
 * @author JACK BUI
 */
function mjob_admin_enqueue_script($hook) {
	if (is_super_admin() && !is_customize_preview()) {
		$js_url = get_template_directory_uri() . '/includes/modules/MJE_MJob_Order/assets/mjob_admin_pluginjs.js';
		$args  =  array( 'underscore', 'backbone', 'appengine' );
		if( defined('ELEMENTOR_VERSION') ){
			$args  =  array('underscore','backbone','backbone-marionette','appengine' );
		}
		wp_enqueue_script('mjob_admin_js',$js_url , $args, ET_VERSION, true );
	}
}
add_action('admin_enqueue_scripts', 'mjob_admin_enqueue_script', 5);
/**
 * get withdraws list
 *
 * @param array $args
 * @return WP_QUERY $mjob_order_query
 * @since 1.0
 * @package FREELANCEENGINE
 * @category FRE CREDIT
 * @author Jack Bui
 */
function get_mjobOrders($args = array()) {
	$default_args = array(
		'paged' => 1,
		'post_status' => array(
			'publish',
			'late',
			'draft',
			'pending',
			'delivery',
			'disputing',
			'disputed',
			'finished',
		),
	);
	$args = wp_parse_args($args, $default_args);
	$args['post_type'] = 'mjob_order';
	$mjob_order_query = new WP_Query($args);
	return $mjob_order_query;
}
new MJE_MJob_Order_Admin_Action();