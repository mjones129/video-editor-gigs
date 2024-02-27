<?php
/**
 * Update receiver id
 * @param int $user_id
 * @param int $receiver_id
 * @return void
 * @since 1.0
 * @package MicrojobEngine
 * @category Conversation
 * @author Tat Thien
 */
if(!function_exists('mje_update_receiver_id')) {
    function mje_update_receiver_id($user_id, $receiver_id) {
        // Get array of receiver id
        $receiver_id_arr = mje_get_receiver_id($user_id);
        array_push($receiver_id_arr, $receiver_id);
        update_user_meta($user_id, 'receiver_id', $receiver_id_arr);
    }
}

/**
 * Get array of receivers id of specific user
 * @param int $user_id
 * @return array $receiver_id
 * @since 1.0
 * @package MicrojobEngine
 * @category Conversation
 * @author Tat Thien
 */
if(!function_exists('mje_get_receiver_id')) {
    function mje_get_receiver_id($user_id) {
        $receiver_id = get_user_meta($user_id, 'receiver_id', true);
        if(empty($receiver_id)) {
            return array();
        } else {
            return $receiver_id;
        }
    }
}

/**
 * Check if  two users have conversation
 * @param int $user_id
 * @param int $receiver_id
 * @return boolean
 * @since 1.0
 * @package MicrojobEngine
 * @category Conversation
 * @author Tat Thien
 */
if(!function_exists('mje_is_has_conversation')){
    function mje_is_has_conversation($user_id, $receiver_id) {
        $conversation = mje_get_conversation($user_id, $receiver_id);
        if(!empty($conversation)) {
            return true;
        } else {
            return false;
        }
    }
}

/**
 * Get conversation of two user
 * @param int $first_user    ID of user
 * @param int $second_user   ID of user
 * @return object $result
 * @since 1.0
 * @package MicrojobEngine
 * @category Conversation
 * @author Tat Thien
 */
if(!function_exists('mje_get_conversation')) {
    function mje_get_conversation($first_user, $second_user) {
        $args = array(
            'post_type' => 'ae_message',
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'ASC',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'relation' => 'OR',
                    array(
                        'relation' => 'AND',
                        array(
                            'key' => 'to_user',
                            'value' => $first_user,
                        ),
                        array(
                            'key' => 'from_user',
                            'value' => $second_user,
                        )
                    ),
                    array(
                        'relation' => 'AND',
                        array(
                            'key' => 'to_user',
                            'value' => $second_user,
                        ),
                        array(
                            'key' => 'from_user',
                            'value' => $first_user,
                        )
                    )
                ),
                array(
                    'key' => 'is_conversation',
                    'value' => '1',
                )
            )
        );

        $result = get_posts($args);
        return $result;
    }
}

/**
 * Get conversation of an user
 * @param int $user_id
 * @return object $result
 * @since 1.0
 * @package MicrojobEngine
 * @category Conversation
 * @author Tat Thien
 */
if(!function_exists('mje_get_conversation_by_user')) {
    function mje_get_conversation_by_user($user_id) {
        $args = array(
            'post_type' => 'ae_message',
            'post_status' => 'publish',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'relation' => 'OR',
                    array(
                        'key' => 'to_user',
                        'value' => $user_id,
                    ),
                    array(
                        'key' => 'from_user',
                        'value' => $user_id,
                    )
                ),
                array(
                    'key' => 'is_conversation',
                    'value' => '1',
                )
            )
        );

        $result = get_posts($args);
        return $result;
    }
}

/**
 * Get conversation page link
 * @param int $first_user    ID of user
 * @param int $second_user   ID of user
 * @return string $link
 * @since 1.0
 * @package MicrojobEngine
 * @category Conversation
 * @author Tat Thien
 */
if(!function_exists('mje_get_conversation_link')) {
    function mje_get_conversation_link($first_user, $second_user) {
        $posts = mje_get_conversation($first_user, $second_user);
        $link = get_permalink($posts[0]->ID);
        return $link;
    }
}

/**
 * Render class for message item
 * @param int $post_author
 * @return string
 * @since 1.0
 * @package MicrojobEngine
 * @category Conversation
 * @author Tat Thien
 */
if(!function_exists('mje_get_message_class')) {
    function mje_get_message_class($post_author) {
        global $user_ID;
        if( $user_ID != $post_author)
           return "guest-message";
        if(is_super_admin($post_author)) {
            return "admin-message";
        } elseif($user_ID == $post_author) {
            return "private-message";
        }
    }
}

/**
 * Filter message content
 * @param string $content
 * @return string $content
 * @since 1.0
 * @package MicrojobEngine
 * @category Conversation
 * @author Tat Thien
 */
if(!function_exists('mje_filter_message_content')) {
    function mje_filter_message_content($content) {
        // Get bad words
        $bad_words = (ae_get_option('filter_bad_words'))?ae_get_option('filter_bad_words'):'';
		//$bad_words = ($bad_words && !is_array($bad_words))?trim($bad_words):'';
        $bad_words = preg_replace('/\s+/', '', $bad_words);

        $content = apply_filters('mjob_before_filter_message_content', $content);
        if(!empty($bad_words)) {
            // Get bad words replace
            $bad_words_replace = ae_get_option('bad_word_replace');
            if(empty($bad_words_replace)) {
                $bad_words_replace = "[bad word]";
            }
            $bad_words_arr = explode(",", $bad_words);
            foreach($bad_words_arr as $bad_word) {
                if(!empty($bad_word)) {
                    $content = str_ireplace($bad_word, $bad_words_replace, $content);
                }
            }
        }

        $content = apply_filters('mjob_after_filter_message_content', $content);

        return $content;
    }
}

/**
 * Return default arguments to get conversations of a user;
 * @param void
 * @return void
 * @since 1.0
 * @package MicrojobEngine
 * @category Conversation
 * @author Tat Thien
 */
if(!function_exists('mje_get_default_conversation_query_args')) {
    function mje_get_default_conversation_query_args($receiver_id = 0) {
        global $user_ID;
        $user_id = $user_ID;
        if($receiver_id)
            $user_id = $receiver_id;
        $default = array(
            'post_type' => 'ae_message',
            'post_status' => 'publish',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'relation' => 'OR',
                    array(
                        'key' => 'to_user',
                        'value' => $user_id,
                    ),
                    array(
                        'key' => 'from_user',
                        'value' => $user_id,
                    )
                ),
            ),
            'orderby' => 'date',
            'order' => 'DESC'
        );
        return $default;
    }
}


/**
 * Get unread messages of a conversation
 * @param object $conversation
 * @return int $count
 * @since 1.0
 * @package MicrojobEngine
 * @category Conversation
 * @author Tat Thien
 */
if(!function_exists('mje_get_unread_message')) {
    function mje_get_unread_message($conversation) {
        global $user_ID;
        $count = 0;
        if($user_ID == $conversation->to_user && $conversation->receiver_unread == true) {
            $count = 1;
        }

        $messages = get_posts(array(
            'post_type' => 'ae_message',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'post_parent' => $conversation->ID,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'to_user',
                    'value' => $user_ID,
                ),
                array(
                    'key' => 'receiver_unread',
                    'value' => '1'
                )
            )
        ));

       return $messages;
    }
}

/**
 * Get amount of unread messages
 * @param object $conversation
 * @return int $count
 * @since 1.0
 * @package MicrojobEngine
 * @category Conversation
 * @author Tat Thien
 */
if(!function_exists('mje_get_unread_message_count')) {
    function mje_get_unread_message_count($conversation) {
        global $ae_post_factory;
        if( is_numeric( $conversation ) ) {
            $post_object= $ae_post_factory->get('ae_message');
            $conversation = $post_object->convert( get_post( $conversation ) );
        }
        $messages = mje_get_unread_message($conversation);
        $count = count($messages);
        return $count;
    }
}

/**
 * Get unread conversation
 * @param void
 * @return void
 * @since 1.0
 * @package MicrojobEngine
 * @category void
 * @author Tat Thien
 */
if(!function_exists('mje_get_unread_conversation')) {
    function mje_get_unread_conversation( $query_args = array() ) {
        global $user_ID;
        $default = mje_get_default_conversation_query_args();
        $args = wp_parse_args(array(
            'posts_per_page' => -1,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => $user_ID . '_conversation_status',
                    'value' => 'unread',
                ),
                array(
                    'key' => 'is_conversation',
                    'value' => '1'
                )
            )
        ), $default);

        if( ! empty( $query_args ) ) {
            $args = wp_parse_args( $query_args, $args );
        }

        $conversation = get_posts($args);
        return $conversation;
    }
}

/**
 * Get amount of unread conversation
 * @param void
 * @return int $count
 * @since 1.0
 * @package MicrojobEngine
 * @category Conversation
 * @author Tat Thien
 */
if(!function_exists('mje_get_unread_conversation_count')) {
    function mje_get_unread_conversation_count() {
        $conversation = mje_get_unread_conversation();
        $count = count($conversation);
        return $count;
    }
}

function mje_get_user_dropdown_conversation() {
    $default = mje_get_default_conversation_query_args();

    $args = wp_parse_args(array(
        'posts_per_page' => 5,
        'orderby' => 'meta_value',
        'meta_key' => 'latest_reply_timestamp',
    ), $default);

    //Query if type is conversation
    array_push($args['meta_query'], array(
        'key' => 'is_conversation',
        'value' => '1',
    ));

    $conversations_query = new WP_Query($args);
    while($conversations_query->have_posts()) :
        $conversations_query->the_post();
        get_template_part('template/conversation-dropdown', 'item');

    endwhile;
    wp_reset_postdata();
}