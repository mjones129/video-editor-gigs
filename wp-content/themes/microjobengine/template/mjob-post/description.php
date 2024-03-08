<?php
    global $user_ID;
?>
<h3 class="title"><?php _e( 'Description', 'enginethemes' ) ;?></h3>
<div class="post-detail description">
    <div class="blog-content">
        <div class="post-content mjob_post_content">
            <?php the_content(); ?>
        </div>
    </div>
</div>
<div class="clearfix">
    <div class="tags">
        <?php mje_list_tax_of_mjob( $mjob_post->ID, '', 'skill' ) ?>
    </div>
    <?php if( $user_ID != $mjob_post->post_author ): ?>
        <?php mje_render_order_button( $mjob_post ); ?>
    <?php endif; ?>
</div>
<?php do_action('mjob_post_after_description', $mjob_post);?>
