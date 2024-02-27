<?php

/**
 * project review class
 */
class MJE_Review extends AE_Comments
{
    static $current_review;
    static $instance;

    /**
     * return class $instance
     */
    public static function get_instance($type = "mjob_review") {
        if (self::$instance == null) {

            self::$instance = new MJE_Review($type);
        }
        return self::$instance;
    }

    public function __construct($type = "mjob_review") {
        $this->comment_type = $type;
        $this->meta = array(
            'et_rate'
        );

        $this->post_arr = array();
        $this->author_arr = array();

        $this->duplicate = true;
        $this->limit_time = 120;
    }

    /**
     * The function retrieve employer rating score and review count
     * @param Integer $user_id The employer id
     * @since 1.0
     * @author JACK BUI
     */
    public static function user_rating_score($user_id) {
        global $wpdb;
        $sql = "SELECT AVG(M.meta_value)  as rate_point, COUNT(C.comment_ID) as count
                FROM $wpdb->posts as  p
                    join $wpdb->comments as C
                                ON p.ID = c.comment_post_ID
                    join $wpdb->commentmeta as M
                        ON C.comment_ID = M.comment_id
                WHERE
                    p.post_author = $user_id
                    AND p.post_status ='finished'
                    AND p.post_type ='mjob_order'
                    AND M.meta_key = 'et_rate'
                    AND C.comment_type ='mjob_review'
                    AND C.comment_approved = 1";

        $results = $wpdb->get_results($sql);
        if($results) {
            return array('rating_score' => $results[0]->rate_point , 'review_count' => $results[0]->count );
        }else {
            return array('rating_score' => 0 , 'review_count' => 0 );
        }
    }
}