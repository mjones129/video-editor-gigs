<?php

/**
 * Class render order list in engine themes backend
 * - list order
 * - search order
 * - load more order
 * @since 1.0
 * @author Dakachi
 */
class AE_WithdrawList
{

    /**
     * construct a user container
     */
    function __construct($args = array(), $roles = '')
    {
        $this->args = $args;
        $this->roles = $roles;
    }

    /**
     * render list of withdraws list
     */
    function render()
    {
        $withdraws = get_withdraws();
        ?>
        <div class="et-main-content order-container fre-credit-withdraw-container" id="">
            <div class="et-main-main">
                <div class="group-wrapper">
                    <div class="group-fields">
                        <div class="search-box et-member-search">
                            <p class="title-search"><?php _e("All Withdraws", 'enginethemes'); ?></p>
                            <div class="function-filter">
                                <form action="">
                                 <span class="et-search-role">
                                    <select name="post_status" id="" class="et-input">
                                        <option value=""><?php _e("All status", 'enginethemes'); ?></option>
                                        <option value="publish"><?php _e('Publish', 'enginethemes'); ?></option>
                                        <option value="pending"><?php _e('Pending', 'enginethemes'); ?></option>
                                        <option value="draft"><?php _e('Draft', 'enginethemes'); ?></option>
                                    </select>
                                 </span>
                                    <span class="et-search-input">
                                    <input type="text" class="et-input order-search search" name="s" placeholder="<?php
                                    _e("Search", 'enginethemes'); ?>">
                                    <i class="fa fa-search" aria-hidden="true"></i>
                                 </span>
                                </form>
                            </div>
                        </div>
                        <div class="et-main-main no-margin clearfix overview list fre-credit-withdraw-list-wrapper">
                            <div  class="list-payment-package">
                                <!-- order list  -->
                                <ul class="row title-list">
                                    <li class="col-md-2 col-sm-2">
                                     <span class="sort-link sort-price">
                                        <a href="" class="orderby" data-sort="amount" data-order="desc"><?php _e('Amount', 'enginethemes'); ?><i class="fa fa-sort" aria-hidden="true"></i></a>
                                     </span>
                                    </li>
                                    <li class="col-md-3 col-sm-3"><?php _e('Withdraw info', 'enginethemes'); ?></li>
                                    <li class="col-md-1 col-sm-1"></li>
                                    <li class="col-md-4 col-sm-4 time-request">
                                    <span class="sort-link sort-price">
                                        <a href="" class="orderby" data-sort="time_request" data-order="asc"><?php _e('Time requested', 'enginethemes'); ?><i class="fa fa-sort" aria-hidden="true"></i></a>
                                     </span>
                                    </li>
                                    <li class="col-md-2 payment-type"><?php _e('Transfer type', 'enginethemes'); ?></li>
                                </ul>
                                <ul class="list-inner list-payment list-withdraws users-list"
                                >
                                    <?php
                                    $withdraw_data = array();
                                    if ($withdraws->have_posts()) {
                                        global $post, $ae_post_factory;
                                        $withdraw_obj = $ae_post_factory->get('ae_credit_withdraw');
                                        while ($withdraws->have_posts()) {
                                            $withdraws->the_post();
                                            $convert = $withdraw_obj->convert($post);
                                            $withdraw_data[] = $convert;
                                            include dirname(__FILE__) . '/admin-template/withdraw-item.php';
                                        }
                                    } else {
                                        _e('<p class="no-items">There are no payments yet.</p>', 'enginethemes');
                                    } ?>
                                </ul>
                                <div class="col-md-12">
                                    <div class="paginations-wrapper">
                                        <?php
                                        ae_pagination($withdraws, get_query_var('paged'), 'page');
                                        wp_reset_query();
                                        ?>
                                    </div>
                                </div>
                                <?php echo '<script type="data/json" class="fre_credit_withdraw_dta" >' . json_encode($withdraw_data) . '</script>'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php }
}
class Fre_Credit_WithdrawAction extends AE_PostAction
{
    function __construct($post_type = 'ae_credit_withdraw')
    {
        $this->post_type = 'ae_credit_withdraw';
        // add action fetch profile
        $this->add_ajax('fre-admin-fetch-withdraw', 'fetch_post');
        $this->add_filter('ae_convert_ae_credit_withdraw', 'fre_credit_convert_withdraw');
        $this->add_ajax('fre-admin-withdraw-sync', 'sync_withdraw');
        $this->add_filter('ae_admin_globals', 'fre_credit_decline_msg');

        // @todo Remove in version 1.1.3
        $this->update_withdraw_data();
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
        $request_query = $_REQUEST['query'];
        $query_args['post_status'] = array('pending', 'publish', 'draft');
        if( isset($request_query['post_status']) ){
            $query_args['post_status'] = $request_query['post_status'];
        }

        if(isset($request_query['orderby'])) {
            $orderby = $request_query['orderby'];
            switch ($orderby) {
                case 'amount':
                    $query_args['meta_key'] = $orderby;
                    $query_args['orderby'] = 'meta_value_num date';
                    $query_args['order'] = $request_query['order'] ? $request_query['order'] : 'DESC';
                    break;
                case 'time_request':
                    $query_args['meta_key'] = $orderby;
                    $query_args['orderby'] = 'meta_value_num';
                    $query_args['order'] = $request_query['order'] ? $request_query['order'] : 'DESC';
                    break;
                default:
                    $query_args['orderby'] = 'date';
                    $query_args['order'] = $request_query['order'] ? $request_query['order'] : 'DESC';
            }
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
    public function  fre_credit_convert_withdraw($result){
        $result->edit_link = get_edit_post_link($result->ID);
        $result->author_url = get_author_posts_url($result->post_author, $author_nicename = '');
        $result->author_avatar = mje_avatar($result->post_author, 58);
        $result->author_name = get_the_author_meta('display_name',$result->post_author);
        $result->time_request_formated = str_ireplace("On ", "", et_the_time($result->time_request));
		$result->date_text = date(get_option('date_format').' '.get_option('time_format'),$result->time_request).' '.mje_text_timezone();
        $result->amount_formated = ae_price_format($result->amount);

        $payment_method_txt_arr = mje_render_payment_name();
        $payment_method = get_post_meta($result->charge_id, 'payment_method', true);
        $result->payment_method_text = $payment_method_txt_arr[$payment_method];

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
    public function sync_withdraw(){
        global $ae_post_factory, $user_ID;
        $request = $_REQUEST;
        $withdraw = $ae_post_factory->get('ae_credit_withdraw');
        if(isset($request['publish']) && $request['publish'] == 1 ){
            $request['post_status'] = 'publish';
        }
        if( isset($request['archive']) && $request['archive'] == 1 ){
            $request['post_status'] = 'draft';
            unset($request['archive']);
        }
        // sync notify
        if( is_super_admin() ) {
            $result = $withdraw->sync($request);
            if( $result ) {
                $charge = AE_WithdrawHistory()->retrieveHistory($result->charge_id);
                if( $charge ){
                    $user_id = $charge->post_author;
                    $user_freezable_wallet = AE_WalletAction()->getUserWallet($user_id, 'freezable');
                    $user_withdrew_wallet = AE_WalletAction()->getUserWallet($user_id, 'withdrew');
                    $wallet = new AE_VirtualWallet($charge->amount, $charge->currency);
                    $number = AE_WalletAction()->checkBalance($user_id, $wallet, 'freezable');
                    if( $number >= 0 ){
                        if( $result->post_status == 'publish' || $result->post_status == 'draft') {
                            $user_freezable_wallet->balance = $number;
                            AE_WalletAction()->setUserWallet($user_id, $user_freezable_wallet, 'freezable');
                        }
                        if( $result->post_status == 'draft' ){
                            $user_wallet = AE_WalletAction()->getUserWallet($user_id);
                            $user_wallet->balance += $charge->amount;
                            AE_WalletAction()->setUserWallet($user_id, $user_wallet);
                            update_post_meta($charge->ID, 'history_status', 'cancelled');

                            /**
                             * Decline withdraw
                             * @since MicrojobEngine 1.0
                             */
                            do_action('ae_decline_withdraw', $result);
                        }
                        if( $result->post_status == 'publish' ){
                            /**
                             * Update withdrew
                             * @since MicrojobEngine 1.0
                             */
                            $user_withdrew_wallet->balance += $charge->amount;
                            AE_WalletAction()->setUserWallet($user_id, $user_withdrew_wallet, 'withdrew');

                            update_post_meta($charge->ID, 'history_status', 'completed');

                            /**
                             * Approve withdraw
                             * @since MicrojobEngine 1.0
                             */
                            do_action('ae_approve_withdraw', $result);
                        }
                        update_post_meta($charge->id, 'user_balance', ae_price_format(AE_WalletAction()->getUserWallet($charge->post_author)->balance));
                        $response = array(
                            'success' => true,
                            'data' => $result,
                            'msg' => __("Update withdraw successful!", 'enginethemes')
                        );
                    }
                    else{
                        $response = array(
                            'success' => false,
                            'msg' => __("There isn't enough money in your wallet!", 'enginethemes')
                        );
                    }
                }
                else{
                    $response = array(
                        'success' => false,
                        'msg' => __("There isn't any charge for this withdraw request!", 'enginethemes')
                    );
                }
            }
            else{
                $response = array(
                    'success'=>false,
                    'msg'=> __('Update failed!', 'enginethemes')
                );
            }
        }
        else{
            $response = array(
                'success'=>false,
                'msg'=> __('Please login to your administrator to update withdraw!', 'enginethemes')
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
    public function fre_credit_decline_msg($vars){
        $vars['confirm_message'] = __('Are you sure to decline this request?', 'enginethemes');
        return $vars;
    }

    /**
     * Update time request.
     * @todo Maybe removed in version 1.1.3
     */
    public function update_withdraw_data() {
        if(!get_option('is_update_withdraw_data')) {
            $withdraws = get_posts(array(
                'post_type' => 'ae_credit_withdraw',
                'post_status' => 'any',
                'posts_per_page' => -1
            ));

            foreach ($withdraws as $withdraw) {
                $history_id = get_post_meta($withdraw->ID, 'charge_id', true);
                $history_time = get_the_time('U', $history_id);
                if($history_time) {
                    update_post_meta($withdraw->ID, 'time_request', $history_time);
                } else {
                    // Just care about the missing data
                    // Do not meet in fact
                    update_post_meta($withdraw->ID, 'time_request', get_the_time('U', $withdraw->ID));
                }
            }

            update_option('is_update_withdraw_data', true);
        }
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
function fre_credit_admin_footer_function() {
    include_once dirname(__FILE__) . '/admin-template/withdraw-item-js.php';
}
add_action('admin_footer', 'fre_credit_admin_footer_function');
/**
 * get withdraws list
 *
 * @param array $args
 * @return WP_QUERY $withdraw_query
 * @since 1.0
 * @package FREELANCEENGINE
 * @category FRE CREDIT
 * @author Jack Bui
 */
function get_withdraws($args = array()){
    $default_args = array(
        'paged' => 1,
        'post_status' => array(
            'pending',
            'publish',
            'draft'
        ),
        'meta_key' => 'time_request',
        'orderby' => 'meta_value_num',
        'order' => 'DESC'
    );
    $args = wp_parse_args($args, $default_args);
    $args['post_type'] = 'ae_credit_withdraw';
    $withdraw_query = new WP_Query($args);
    return $withdraw_query;
}

new Fre_Credit_WithdrawAction();