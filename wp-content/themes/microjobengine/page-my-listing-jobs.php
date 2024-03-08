<?php
/**
 * Template Name: My jobs listing
 *
 * @param void
 * @return void
 * @since 1.0
 * @package MicrojobEngine
 * @category void
 * @author JACK BUI
 */
get_header();
?>
<div id="content">
    <div class="block-page">
        <div class="container mjob-container-control my-list-mjobs dashboard withdraw">
            <div class="row title-top-pages">
                <p class="block-title"><?php _e('MY JOBS', 'enginethemes'); ?></p>
                <div class="filter"><?php get_template_part('template/filter', 'template');?></div>
                <p><a href="<?php echo et_get_page_link('dashboard'); ?>" class="btn-back"><i class="fa fa-angle-left"></i><?php _e('Back to dashboard', 'enginethemes'); ?></a></p>
            </div>
            <div class="row profile">
                <div class="col-lg-3 col-md-3 col-sm-12 col-sx-12 profile">
                    <?php get_sidebar('my-profile'); ?>
                </div>
                <div class="col-lg-9 col-md-9 col-sm-12 col-sx-12">
                    <div class="block-items no-margin">
                        <?php
                        global $wp_query, $user_ID;
                        $args = array(
                            'post_type'=> 'mjob_post',
                            'author'=> $user_ID,
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
                        $query = new WP_Query( $args );
                        global $ae_post_factory;
                        $post_object = $ae_post_factory->get( 'mjob_post' );
                        $post_data = array();
                        ?>
                        <?php if( $query->have_posts() ) : ?>
                            <ul class="row mjob-list list-mjobs">
                            <?php while( $query->have_posts() ) : ?>
                                <?php
                                $query->the_post();
                                global $post;
                                $convert = $post_object->convert( $post );
                                $post_data[] = $convert;
                                ?>
                                <li class="col-lg-4 col-md-4 col-sm-6 col-xs-6 col-mobile-12 item_js_handle">
                                    <?php mje_get_template( 'template/mjob-item.php', array( 'current' => $convert ) ); ?>
                                </li>
                            <?php endwhile; ?>
                            </ul>
                        <?php else : ?>
                            <div class="not-found"><?php _e('There are no mJobs found!', 'enginethemes'); ?></div>
                        <?php endif; ?>

                        <?php
                        $query->query = array_merge(  $query->query ,array(
                            'is_author' => true,
                            'page_template' => 'page-my-listing-jobs'
                        ) ) ;
                        ?>

                        <div class="paginations-wrapper float-center">
                        <?php ae_pagination( $query, get_query_var( 'paged' ), 'load' ); ?>
                        </div>

                        <script type="data/json" class="mJob_postdata" ><?php echo json_encode( $post_data ); ?></script>

                        <?php wp_reset_postdata(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php get_footer() ; ?>
