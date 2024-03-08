<?php
/**
 * Template Name: Revenues
 */
global $current_user, $ae_post_factory;
get_header();
?>
    <div id="content">
        <div class="container mjob-revenues-page dashboard withdraw">
            <div class="row title-top-pages">
                <p class="block-title"><?php _e('Revenues', 'enginethemes'); ?></p>
                <p><a href="<?php echo et_get_page_link('dashboard'); ?>" class="btn-back"><i class="fa fa-angle-left"></i><?php _e('Back to dashboard', 'enginethemes'); ?></a></p>
            </div>
            <div class="row profile">
                <div class="col-lg-3 col-md-3 col-sm-12 col-sx-12 profile">
                    <?php get_sidebar('my-profile'); ?>
                </div>

                <div class="col-lg-9 col-md-9 col-sm-12 col-sx-12 outer-revenues">
                    <div class="box-shadow withdraw-revenues">
                        <!-- revenues report -->
                        <?php get_template_part('template/dashboard', 'revenues'); ?>
                        <div class="withdraw-history-wrapper">
							<?php echo apply_filters('show_template_revenue_withdraw_form',$content="");  ?>
                            <?php get_template_part('template/revenue', 'withdraw-history'); ?>
                        </div><!-- /.withdraw-history-wrapper -->
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
get_footer();
?>