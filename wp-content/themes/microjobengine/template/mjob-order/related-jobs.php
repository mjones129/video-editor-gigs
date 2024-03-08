<?php global $user_ID; ?>
<?php if($mjob_order->seller_id != $user_ID): ?>
    <div class="block-items related">
        <?php
        $author = get_userdata( ($mjob_order->seller_id ) );
        $author_name = '';
        if( $author ){
            $author_name = $author->display_name;
        }
        ?>
        <p class="text-dispute"><?php echo sprintf( __( "Other %s's jobs", 'enginethemes' ), $author_name ); ?></p>
        <?php
        global $user_ID, $ae_post_factory;
        $post_object = $ae_post_factory->get( 'mjob_post' );
        $args = array(
            'post_type'=> 'mjob_post',
            'post_status'=> array( 'publish', 'unpause' ),
            'showposts'=> 3,
            'author'=> $mjob_order->seller_id
        );
        $related_query = new WP_Query( $args );
        ?>
        <ul class="row list-mjobs realated-job">
        <?php if( $related_query->have_posts() ): ?>
            <?php while( $related_query->have_posts() ) : ?>
                <?php
                $related_query->the_post();
                global $post;
                $convert = $post_object->convert( $post );
                ?>
                <li class="col-lg-4 col-md-4 col-sm-6 col-xs-6 col-mobile-12">
                <?php mje_get_template( 'template/mjob-item.php', array( 'current' => $convert ) ); ?>
                </li>
            <?php endwhile;  ?>
        <?php else: ?>
            <h3><?php _e( 'There are no mJobs found! ', 'enginethemes' ) ?></h3>
        <?php endif; ?>
        </ul>
        <?php
        wp_reset_postdata();
        ?>
    </div>
<?php endif; ?>