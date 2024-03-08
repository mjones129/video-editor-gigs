<?php
/**
 * Template Name: Dashboard
 */
global $wp_query, $ae_post_factory, $post, $current_user, $user_ID;
$user = mJobUser::getInstance();
$user_data = $user->convert($current_user->data);

// Convert profile
$profile_obj = $ae_post_factory->get('mjob_profile');
$profile_id = get_user_meta($user_ID, 'user_profile_id', true);
if($profile_id) {
    $post = get_post($profile_id);
    if($post && !is_wp_error($post)) {
        $profile = $profile_obj->convert($post);
    }
}

// mjob post object
$post_obj = $ae_post_factory->get('mjob_post');

get_header();
?>
    <div id="content">
        <div class="block-page">
            <?php if(!mje_is_user_active($user_ID)): ?>
                <div class="active-account">
                    <p><?php _e('Your account is not activated yet! Lost the activation link?', 'enginethemes'); ?> <a href="" class="resend-email-confirm"><?php _e('Resend it.', 'enginethemes'); ?></a></p>
                </div>
            <?php endif; ?>

            <div class="container dashboard main-dashboard">
                <div class="title-top-pages">
                    <p class="block-title"><?php _e('Dashboard', 'enginethemes'); ?></p>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 block-items-detail">
                        <?php get_sidebar('public-profile'); ?>
                    </div>

                    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                        <?php get_template_part('template/dashboard', 'revenues'); ?>
                        <div id="analytics" class="chart box-shadow clearfix">
                            <!--Add chart in here-->
                            <div class="title"><?php _e('Order Statistics', 'enginethemes'); ?></div>
                            <div class="line">
                                <span class="line-distance"></span>
                            </div>
                            <div class="chart-padding">
                                <div class="chart-inner">
                                    <canvas id="dashboard-chart" width="690" height="222"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="information-items-detail box-shadow">
                            <div class="tabs-information">
                                <div class="view-all view-all-head"><a href="<?php echo et_get_page_link('my-list-order'); ?>"><?php _e('View all', 'enginethemes'); ?></a></div>
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active"><a href="#order" aria-controls="order" role="tab" data-toggle="tab"><?php _e('My orders', 'enginethemes'); ?></a></li>
                                    <li role="presentation"><a href="#task" aria-controls="task" role="tab" data-toggle="tab"><?php _e('Tasks', 'enginethemes'); ?></a></li>
                                </ul>
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane active" id="order">
                                        <?php
                                            get_template_part('template/dashboard', 'list-orders');
                                        ?>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="task">
                                        <?php
                                            get_template_part('template/dashboard', 'list-tasks');
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="information-items-detail box-shadow">
                            <div class="tabs-information">
                                <div class="link-post-job">
                                    <?php
                                    $absolute_url = mje_get_full_url( $_SERVER );
                                    if( is_mje_submit_page() ){
                                        $post_link = '#';
                                    }
                                    else {
                                        $post_link = et_get_page_link('post-service') . '?return_url=' . $absolute_url;
                                    }
                                    ?>
                                    <a href="<?php echo $post_link ?>" class="post-job-button"><span class="text-post"><?php _e('Post a mjob', 'enginethemes'); ?></span>
                                        <div class="plus-circle"><i class="fa fa-plus"></i></div>
                                    </a>
                                </div>

                                <div class="view-all view-all-head"><a href="<?php echo et_get_page_link('my-listing-jobs'); ?>"><?php _e('View all', 'enginethemes'); ?></a></div>

                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active"><a href="#job" aria-controls="job" role="tab" data-toggle="tab"><?php _e('My jobs', 'enginethemes'); ?></a></li>
                                </ul>
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane active" id="job">
                                        <?php
                                            get_template_part('template/dashboard', 'list-mjobs');
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
get_footer();
?>