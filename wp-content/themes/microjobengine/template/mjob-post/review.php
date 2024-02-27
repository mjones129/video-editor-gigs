<div class="review-job">
    <h3 class="title">
        <?php printf(__('Review <span class="total-review">(%s total)</span>', 'enginethemes'), mje_get_total_reviews( $mjob_post->ID ) ); ?>
    </h3>
    <ul>
        <?php
        $reviews_per_page = 5;
        $total_args =  array(
            'type' => 'mjob_review',
            'post_id' => $mjob_post->ID ,
            'paginate' => 'load',
            'order' => 'DESC',
            'orderby' => 'date',
        );

        $query_args = wp_parse_args(array(
            'number' => $reviews_per_page,
            'page' => 1
        ), $total_args);

        // Get reviews
        $review_obj = MJE_Review::get_instance();
        $reviews = $review_obj->fetch($query_args);
        $reviews = $reviews['data'];
        $review_data = array();

        // Get total reviews
        $total_reviews = count(get_comments($total_args));
        // Get review pages
        $review_pages  =   ceil($total_reviews/$query_args['number']);
        $query_args['total'] = $review_pages;

        if(!empty($reviews)):
            foreach($reviews as $key => $value) {
                $review_data[] = $value;
                ?>
                <li id="review-<?php echo $value->comment_ID; ?>" class="review-item clearfix">
                    <div class="image-avatar">
                        <?php echo $value->avatar_user; ?>
                    </div>
                    <div class="profile-viewer">
                        <a href="<?php echo $value->author_data->author_url; ?>" class="name-author">
                            <?php echo $value->author_data->display_name; ?>
                        </a>
                        <p class="review-time"><?php echo $value->date_ago; ?></p>
                        <div class="rate-it star" data-score="<?php echo $value->et_rate; ?>"></div>
                        <div class="commnet-content"><?php echo $value->comment_content;  ?></div>
                    </div>
                </li>
                <?php
            }

        endif; ?>
    </ul>

    <div class="paginations-wrapper" >
        <?php
        if($review_pages > 1) {
            ae_comments_pagination($review_pages, $paged ='' ,$query_args);
        }
        ?>
    </div>
    <?php echo '<script type="json/data" class="review-data" > ' . json_encode($review_data) . '</script>'; ?>
</div>