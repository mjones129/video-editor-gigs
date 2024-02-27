<?php
/**
 * Template Name: Blog
 * Template for displaying posts
 * @package WordPress
 * @subpackage MicrojobEnginee
 * @since MicrojobEngine 1.1.4
 */
get_header();
?>

    <div id="content">
        <div class="container dashboard withdraw">
            <!-- block control  -->
            <div class="row title-top-pages">
                <p class="block-title"><?php the_title(); ?></p>
            </div>
            <div class="row block-posts blog-pages" id="post-control">
                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 col-sm-12 col-xs-12">
                    <div class="menu-left">
                        <p class="title-menu"><?php _e('Categories', 'enginethemes'); ?></p>
                        <?php mje_show_filter_categories('category', array('parent' => 0)); ?>
                        <?php get_sidebar('blog'); ?>
                    </div>
                </div>
                <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 posts-container" id="posts_control">
                    <?php
                    $args = array(
                        'post_type' => 'post'
                    );
                    query_posts( $args );
                    if(have_posts()){
                        get_template_part( 'template/list', 'posts' );
                    } else {
                        echo '<h5>'.__( 'There is no posts yet', 'enginethemes' ).'</h5>';
                    }
                    ?>
                </div><!-- RIGHT CONTENT -->
            </div>
            <!--// block control  -->
        </div>
    </div>
<?php
get_footer();
?>
