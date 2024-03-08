<?php
/**
 * Template Name: Change Password
 */
global $current_user;
get_header();
?>
    <div id="content">
        <div class="container dashboard withdraw">
            <div class="row title-top-pages">
                <p class="block-title"><?php _e('Change password', 'enginethemes'); ?></p>
                <p><a href="<?php echo et_get_page_link('dashboard'); ?>" class="btn-back"><i class="fa fa-angle-left"></i><?php _e('Back to dashboard', 'enginethemes'); ?></a></p>
            </div>
            <div class="row profile">
                <div class="col-lg-3 col-md-3 col-sm-12 col-sx-12 profile">
                    <?php get_sidebar('my-profile'); ?>
                </div>

                <div class="col-lg-9 col-md-9 col-sm-12 col-sx-12 box-shadow change-password-dashboard">
                    <?php
                    mJobChangePasswordForm();
                    ?>
                </div>
            </div>
        </div>
    </div>
<?php
get_footer();
?>