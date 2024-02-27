<?php
class MJE_Review_Action extends AE_Base
{
    public function __construct() {
        $this->mail = MJE_Mailing::get_instance();

        //$this->add_action('preprocess_comment', 'process_review');

        // $this->add_action( 'comment_post' , 'update_rating');
        $this->init_ajax();
        $this->add_filter('ae_convert_comment', 'filter_comment');
    }

    /**
     * init ajax action
     * @since 1.0
     * @author Dakachi
     */
    function init_ajax() {
        $this->add_ajax('mjob-user-review', 'user_review_action', true, false);
        $this->add_ajax('mjob-fetch-review', 'fetch_reviews');
    }

    /**
     * Fetch review
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Review
     * @author Tat Thien
     */
    public function fetch_reviews() {
        $request = $_REQUEST;
        $page = $request['page'];
        $query_args = $request['query'];
        $query_args['page'] = $page;

        $review_obj = MJE_Review::get_instance();
        $reviews = $review_obj->fetch($query_args);
        $reviews = $reviews['data'];

        if(!empty($reviews)) {
            wp_send_json(array(
                'success' => true,
                'data' => $reviews,
                'max_num_pages' => $query_args['total']
            ));
        } else {
            wp_send_json(array(
                'success' => false
            ));
        }
    }

    /**
     * Filter review data
     * @param $comment
     * @return $comment
     * @since 1.0
     * @package MicrojobEngine
     * @category Review
     * @author Tat Thien
     */
    public function filter_comment($comment) {
        $avatar = mje_avatar($comment->user_id, 75);
        $comment->avatar_user = $avatar;
        ob_start();
        comment_text($comment->ID);
        $comment->comment_content = ob_get_clean();
        return $comment;
    }

    /*
     * add review by freelancer.
    */
    function user_review_action() {
        global $user_ID, $ae_post_factory, $current_user;
        $args = $_POST;
        $order_id = $args['order_id'];
        $status = get_post_status($order_id);
        $order_obj = $ae_post_factory->get('mjob_order');
        $order = get_post($order_id);
        $order = $order_obj->convert($order);
        $mjob_id = $order->mjob_id;
        $author_order = get_post_field('post_author', $order_id);
        // Review class
        $review = MJE_Review::get_instance("mjob_review");
        /*
         * validate data
        */
        if (!isset($args['score']) || empty($args['score'])) {
            $result = array(
                'succes' => false,
                'msg' => __('You have to rate this mJob!', 'enginethemes')
            );
            wp_send_json($result);
        }
        if (!isset($args['comment_content']) || empty($args['comment_content'])) {
            $result = array(
                'succes' => false,
                'msg' => __('Please write a review for this mJob!', 'enginethemes')
            );
            wp_send_json($result);
        }

        /*
         * check permission
        */
        if ($user_ID != $author_order || !$user_ID) {
            wp_send_json(array(
                'succes' => false,
                'msg' => __('You have to be the owner of this mJob to review!', 'enginethemes')
            ));
        }

        /*
         *  check status of project
        */
        if ($status != 'delivery') {
            wp_send_json(array(
                'succes' => false,
                'msg' => __('Wait until the order is delivered to review!', 'enginethemes')
            ));
        }
        /**
         * check user reviewed project owner or not
         */
        if( $this->check_user_reviewed( $mjob_id, $order_id ) ) {
            wp_send_json(array(
                'succes' => false,
                'msg' => __('You have already reviewed this mJob!', 'enginethemes')
            ));
        }

        // add review
        $args['comment_post_ID'] = $mjob_id;
        $args['comment_approved'] = 1;
        $this->comment_type = 'mjob_reivew';
        $comment = $review->insert($args);
        if (!is_wp_error($comment)) {

            /**
             * Fire action after buyer review seller based on mjob order
             *
             * @param int $int mjob id
             * @param array $args submit args (rating score, comment)
             * @since 1.3
             * @author Tat Thien
             */
            do_action('mje_user_reviewed_mjob', $mjob_id, $comment, $args);

            // update project, bid, bid author, project author after review
            $this->after_review($mjob_id, $comment, $args);

            // Transfer working fund to available fund
            // Check if order is transferred or not
            AE_WalletAction()->transferWorkingToAvailable($order->seller_id, $order->ID, $order->real_amount);

            // Send email to seller
            $this->mail->accept_order($order, $args['comment_content']);

            wp_send_json(array(
                'success' => true,
                'msg' => __("Your review has been submitted.", 'enginethemes')
            ));
        } else {

            // revert bid status
            wp_update_post(array(
                'ID' => $order_id,
                'post_status' => 'Delivery'
            ));

            wp_send_json(array(
                'success' => false,
                'msg' => $comment->get_error_message()
            ));
        }
    }
    /**
     * Description
     *
     * @param integer $mjob_id
     * @param integer $comment_id
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    function after_review($mjob_id, $comment_id, $args) {
        global $wpdb;

        // Update comment meta
        update_comment_meta($comment_id, 'order_id', $args['order_id']);

        if (isset($_POST['score']) && $_POST['score']) {
            $rate = (float)$_POST['score'];
            if ($rate > 5) $rate = 5;
            update_comment_meta($comment_id, 'et_rate', $rate);
            update_post_meta($mjob_id, 'rating_score', $rate);
        }
        $user_id = (int)get_post_field('post_author', $mjob_id);
        $sql = "SELECT AVG(M.meta_value)  as rate_point, COUNT(C.comment_ID) as count
                FROM $wpdb->posts as  p
                    join $wpdb->comments as C
                                ON p.ID = C.comment_post_ID
                    join $wpdb->commentmeta as M
                        ON C.comment_ID = M.comment_id
                WHERE
                    p.post_author = $user_id
                    AND p.post_type ='mjob_post'
                    AND M.meta_key = 'et_rate'
                    AND C.comment_type ='mjob_review'
                    AND C.comment_approved = 1";
        $results = $wpdb->get_results($sql);
        if ($results) {
            wp_cache_set("reviews-{$user_id}", $results[0]->count);

            // update post rating score
            update_post_meta($mjob_id, 'rating_score', $results[0]->rate_point);
        } else {
            update_post_meta($mjob_id, 'rating_score', $rate);
        }
        // send mail to employer.
        //$this->mail->review_mjob_email($mjob_id);
    }

    /**
     * fetch comment
     */
    function fetch_comments() {

        global $ae_post_factory;
        $review_object = $ae_post_factory->get('de_review');

        // get review object

        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 2;
        $query = $_REQUEST['query'];

        $map = array(
            'status' => 'approve',
            'meta_key' => 'et_rate',
            'type' => 'review',
            'post_type' => 'place',
            'number' => '4',
            'total' => '10'
        );

        $query['page'] = $page;

        $data = $review_object->fetch($query);
        if (!empty($data)) {
            $data['success'] = true;
            wp_send_json($data);
        } else {
            wp_send_json(array(
                'success' => false,
                'data' => $data
            ));
        }
    }

    /**
     * display form for freelancer review employer  after complete project.
     * @since  1.0
     * @author Dan
     */
    function mjob_review_form() {
        wp_reset_query();
        global $user_ID;
        $status = get_post_status(get_the_ID());
        $bid_accepted = get_post_field('accepted', get_the_ID());
        $freelan_id = (int)get_post_field('post_author', $bid_accepted);
        $comment = get_comments(array(
            'status' => 'approve',
            'post_id' => get_the_ID() ,
            'type' => 'mjob_review'
        ));
        $review = isset($_GET['review']) ? (int)$_GET['review'] : 0;
        $status = get_post_status(get_the_ID());

        if (empty($comment) && $user_ID == $freelan_id && $review && $status == 'complete') { ?>
            <script type="text/javascript">
                (function($, Views, Models, Collections) {
                    $(document).ready(function(){
                        this.modal_review       = new AE.Views.Modal_Review();
                        this.modal_review.openModal();
                    });
                })(jQuery, AE.Views, AE.Models, AE.Collections);
            </script>

            <?php
        }
    }

    /**
     * Check if current user reviewed mjob order or not
     *
     * @param int $mjob_id
     * @param int $order_id
     * @return bool
     * @since 1.3
     * @author Tat Thien
     */
    public function check_user_reviewed( $mjob_id, $order_id ) {
        global $current_user;
        $comment = get_comments( array(
            'status' => 'approve',
            'type' => 'mjob_review',
            'post_id' => $mjob_id,
            'author_email' => $current_user->user_email,
            'meta_key' => 'order_id',
            'meta_value' => $order_id
        ) );

        if ( ! empty( $comment ) ) {
            return true;
        }
        return false;
    }
}
?>