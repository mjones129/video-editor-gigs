<?php
class MJE_Mailing_Action extends AE_Base
{
    public static $instance;
    public $mail;

    static function get_instance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    function __construct() {
        $this->mail = MJE_Mailing::get_instance();
        $this->add_action('ae_reject_post', 'reject_post', 9, 1);
        //$this->add_action('mjob_update_order_author', 'mJobMailUpdateNewOrder', 10, 1);
        $this->add_action('ae_approve_withdraw', 'approve_withdraw');
        $this->add_action('ae_decline_withdraw', 'decline_withdraw');
        $this->add_action('mjob_decline_order', 'decline_mjob_order');
        $this->add_filter('ae_filter_receipt_mail_template', 'filter_receipt_content', 10, 3);
        $this->add_filter( 'ae_inbox_mail_headers', 'filter_inbox_mail_header' );
        // 1.3.7.6 later
        $this->add_action('transition_post_status','approve_mjob', 10 , 3);
    }

    public function reject_post($args) {
        if($args['post_type'] == 'mjob_post') {
            $this->mail->reject_post($args);
        }

        global $et_appengine;
        remove_action('ae_reject_post', array($et_appengine, 'reject_post'));
    }

    public function filter_receipt_content($content, $order, $data) {
        if($order['payment'] == 'cash') {
            $content = ae_get_option('pay_package_by_cash');
        }

        $post = get_post($data['ad_id']);
        if(!empty($post)) {
            $link = '<a href="'. get_permalink($post->ID) .'">'. get_permalink($post->ID) .'</a>';
            $detail = "";
            switch($post->post_type) {
                case 'mjob_post':
                    $detail = sprintf(__('Submit a mJob, visit here: %s', 'enginethemes'), $link);
                    break;
                default:
                    $detail = __('Submit post', 'enginethemes');

            }

            $content = str_ireplace('[detail]', $detail, $content);
        }
        return $content;
    }

    public function approve_withdraw($withdraw) {
        $this->mail->approve_withdraw($withdraw->post_author);
    }

    public function decline_withdraw($withdraw) {
        $this->mail->decline_withdraw($withdraw);
    }

    public function decline_mjob_order($mjob_order) {
        $this->mail->decline_mjob_order($mjob_order);
    }

    public function filter_inbox_mail_header( $headers ) {
        return '';
    }
    function approve_mjob($new_status, $old_status, $post ){

        if ( $old_status != 'publish' &&  $new_status == 'publish' && 'mjob_post' === $post->post_type ) {
            $this->mail->approve_mjob($post);
        }

    }
}
$new_instance = MJE_Mailing_Action::get_instance();