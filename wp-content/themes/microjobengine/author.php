<?php
/**
 * Template Name: Page Author
 */
global $wp_query, $ae_post_factory, $post, $current_user, $user_ID;

$author_id 	= get_query_var('author');
$author = mJobUser::getInstance();
$author_data = $author->get($author_id);

// Convert profile
$profile_obj = $ae_post_factory->get('mjob_profile');
$profile_id = get_user_meta($author_id, 'user_profile_id', true);
if($profile_id) {
    $post = get_post($profile_id);
    if($post && !is_wp_error($post)) {
        $profile = $profile_obj->convert($post);
    }
}

get_header();
?>
<div id="content">
    <div class="container mjob-profile-page mjob-author-page">
        <div class="title-top-pages">
            <p class="block-title"><?php printf(__('%s\'s profile', 'enginethemes'), $author_data->display_name); ?></p>
        </div>
        <div class="row profile user-public-profile">
            <div class="col-lg-4 col-md-4 col-sm-12 col-sx-12 block-items-detail profile">
                <?php get_sidebar('public-profile'); ?>
            </div>

            <div class="col-lg-8 col-md-8 col-sm-12 col-sx-12">
                <?php
                global $mjob_is_author;
                $mjob_is_author = true;
                ?>
                <div class="row">
                    <?php
                    $args = array(
                        'post_type' => 'mjob_post',
                        'author' => $author_id,
                        'post_status' => array( 'publish', 'unpause' )
                    );
                    $mjob_posts = new WP_Query( $args );
                    $postdata = array();
                    $post_obj = $ae_post_factory->get( 'mjob_post' );
                    if( $mjob_posts->have_posts() ) :
                    ?>
                    <ul class="mjob-list mjob-list--horizontal">
                        <?php
                        while ( $mjob_posts->have_posts() ) :
                            $mjob_posts->the_post();
                            $convert = $post_obj->convert( $post );
                            $postdata[] = $convert;
                        ?>
                        <li class="col-lg-12">
                            <?php mje_get_template( 'template/mjob-item.php', array( 'current' => $convert ) ); ?>
                        </li>
                        <?php endwhile; ?>
                        <?php wp_reset_postdata();  ?>
                    </ul>
                    <?php else: ?>
                        <p class="no-items"><?php _e('There are no mJobs found!', 'enginethemes'); ?></p>
                    <?php endif; ?>
                    <?php
                        echo '<div class="paginations-wrapper float-center">';
                        ae_pagination($mjob_posts, get_query_var('paged'), 'load');
                        echo '</div>';
                        /**
                         * render post data for js
                         */
                        echo '<script type="data/json" class="mjob_postdata" >' . json_encode($postdata) . '</script>';
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
get_footer();
?>