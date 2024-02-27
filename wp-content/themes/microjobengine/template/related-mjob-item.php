<?php
global $wp_query, $ae_post_factory, $post;
$post_object    = $ae_post_factory->get( 'mjob_post' );
$current        = $post_object->current_post;
?>
<li class="col-lg-4 col-md-4 col-sm-4 col-xs-12 mjob-item">
    <div class="inner clearfix">
        <div class="item-header">
            <div class="vote">
                <div class="rate-it star" data-score="<?php echo $current->rating_score; ?>"></div>
            </div>
        </div>

        <div class="item-image">
            <a href="<?php echo $current->permalink; ?>"><img src="<?php echo $current->the_post_thumbnail; ?>" alt=""></a>
        </div>

        <div class="item-title">
            <h2 class="name-job"><a href="<?php echo $current->permalink; ?>"><?php echo $current->post_title; ?></a></h2>
        </div>

        <div class="item-data">
            <div class="author">
                <p><span class="by-author"><?php _e('by ', 'enginethemes'); ?></span> <?php echo $current->author_name;?></p>
            </div>
            <div class="price">
                <span><?php echo $current->et_budget_text ?></span>
            </div>
        </div>
    </div>
</li>