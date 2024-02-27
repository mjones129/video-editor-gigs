<?php
    global $user_ID, $ae_post_factory, $wp_query;
    $post_obj = $ae_post_factory->get('ae_withdraw_history');
?>
<div id="withdraw-history" class="block-history mjob-withdraw-history-container">
    <div class="payment-method">
        <p class="choose-payment"><?php _e('Withdrawn history', 'enginethemes'); ?></p>
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <td class="title-head"><?php _e('Payment method', 'enginethemes'); ?></td>
                    <td class="title-head"><?php _e('Date', 'enginethemes'); ?></td>
                    <td class="title-head"><?php _e('Amount', 'enginethemes'); ?></td>
                    <td class="title-head"><?php _e('Status', 'enginethemes'); ?></td>
                </tr>
                </thead>
                <tbody class="list-histories">
                    <?php
                        $args = array(
                            'post_type' => 'ae_withdraw_history',
                            'post_status' => 'publish',
                            'meta_query' => array(
                                'key' => 'history_type',
                                'value' => 'withdraw',
                                'compare' => '='
                            ),
                            'author' => $user_ID
                        );
                        $postdata = array();
                        query_posts($args);
                        $is_have_post = false;
                        if(have_posts()):
                            $is_have_post = true;
                            while(have_posts()):
                                the_post();
                                $convert = $post_obj->convert($post);
                                $postdata[] = $convert;
                                get_template_part('template/revenue', 'withdraw-history-item');
                            endwhile;
                        endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
        echo '<div class="paginations-wrapper float-center">';
        ae_pagination($wp_query, get_query_var('paged'), 'load');
        echo '</div>';
        wp_reset_query();
    ?>
</div>

<?php
echo '<script type="data/json" class="withdraw_history_postdata" >'.json_encode($postdata).'</script>';
?>