<?php
    global $user_ID, $ae_post_factory;
    $post_obj = $ae_post_factory->get('mjob_post');
?>

<div class="list-job">
    <?php
    $args = array(
        'post_type' => 'mjob_post',
        'author' => $user_ID,
        'posts_per_page' => 5,
        'orderby' => 'date',
        'post_status'=> array(
            'pending',
            'publish',
            'reject',
            'archive',
            'pause',
            'unpause',
            'draft'
        ),
    );
    $mjob_posts = new WP_Query($args);
    $postdata = array();
    if($mjob_posts->have_posts()) {
        ?>
        <ul class="mjob-list mjob-list--horizontal">
            <?php
            while($mjob_posts->have_posts()) :
                $mjob_posts->the_post();
                global $post;
                $convert = $post_obj->convert($post);
                $postdata[] = $convert;
            ?>
            <li>
                <?php mje_get_template( 'template/mjob-item.php', array( 'current' => $convert ) ); ?>
            </li>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
        </ul>

        <div class="view-all float-center">
            <a href="<?php echo et_get_page_link('my-listing-jobs'); ?>"><?php _e('View all', 'enginethemes'); ?></a>
        </div>
    <?php } else { ?>
        <p class="no-items"><?php _e('There are no mJobs found!', 'enginethemes'); ?></p>
    <?php } ?>
</div>