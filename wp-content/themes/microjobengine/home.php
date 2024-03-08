<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme and one
 * of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query,
 * e.g., it puts together the home page when no home.php file exists.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage MicrojobEngine
 * @since MicrojobEngine 1.0
 *
 * Template Name: Home Page
 */


$website = "http://example.com";
get_header();
// Get heading title and sub title
$heading_title  = get_theme_mod('home_heading_title') ? get_theme_mod('home_heading_title') : __('Get your stuffs done from $5', 'enginethemes');
$sub_title      = get_theme_mod('home_sub_title') ? get_theme_mod('home_sub_title') : __('Browse through millions of micro jobs. Choose one you trust. Pay as you go.', 'enginethemes');

$img_url        = ae_get_option('search_background');
$img_theme_mod  = get_theme_mod('search_background');
if (!empty($img_url)) {
	$img_url = $img_url['full']['0'];
} elseif (false === $img_theme_mod) {
	$img_url = get_template_directory_uri() . '/assets/img/bg-slider.jpg';
} else {
	$img_url = "";
}
$has_geo_ext = apply_filters('has_geo_extension','');

?>
    <div class="slider <?php echo $has_geo_ext;?>">
            <?php
            if ( ! is_acti_mje_geo() ){
               mje_search_form($heading_title, $sub_title);
            } else {
                do_action('mje_geo_search_form', $heading_title, $sub_title);
            } ?>
        <div class="background-image">
            <div class="backgound-under" style="background: url(<?php echo $img_url; ?>); background-repeat: no-repeat; background-size: cover;"></div>
            <!-- <img src="<?php //echo $img_url; ?>" alt="" class="wow fadeIn"> -->
        </div>
        <div class="statistic-job-number">
            <p class="link-last-job"><?php echo sprintf(__('There are %s microjobs more', 'enginethemes'), mje_get_mjob_count()); ?> <div class="bounce"><i class="fa fa-angle-down"></i></div></p>
        </div>
    </div>
    <div id="content">
        <div class="block-hot-items">
            <div class="container inner-hot-items wow fadeInUpBig">
                <?php
                $cat_title = get_theme_mod('mje_other_title_category') ? get_theme_mod('mje_other_title_category') : __('FIND WHAT YOU NEED', 'enginethemes');
                ?>
                <p class="block-title"><?php echo $cat_title; ?></p>
                <?php
                $terms = get_terms(
                    'mjob_category',
                    array(
                        'orderby'=> 'count',
                        'order' => 'DESC',
                        'hide_empty'=> false,
                        'offset' => 0, // begin this item possition.
                        'number' => 8,
                        'meta_key' => 'featured-tax',
                        'meta_value' => array('1','true',1, true),
                    )
                );?>
                <ul class="row">
                    <?php

                    if ( !empty( $terms ) && !is_wp_error( $terms ) ):

                    	foreach ($terms as $key => $term) {
                            $img_url = get_template_directory_uri() . '/assets/img/icon-1.png';
                            $meta = get_term_meta($term->term_id,'featured-tax', true);
                       		$img = get_term_meta($term->term_id, 'mjob_category_image', true);
                            $link = get_term_link($term->term_id, 'mjob_category');

                    		if ( !empty( $img ) ) {
                    			$img_url = esc_url(wp_get_attachment_image_url($img, 'full'));
                    		}?>

                            <li class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
                                <a href="<?php echo $link; ?>">
                                    <div class="hvr-float-shadow">
                                        <div class="avatar">
                                            <img src="<?php echo $img_url; ?>" alt="">
                                            <div class="line"><span class="line-distance"></span></div>
                                        </div>
                                        <h2 class="name-items"> <?php echo $term->name ?>  </h2>
                                    </div>
                                </a>
                            </li>
                            <?php
                    	}

                    endif;?>
                </ul>
            </div>
        </div>

        <div class="block-items">
            <div class="container">
                <?php
                    $mjob_title = get_theme_mod('mje_other_title_service') ? get_theme_mod('mje_other_title_service') : __('LATEST MICROJOBS', 'enginethemes');
                    ?>
                    <p class="block-title float-center"><?php echo $mjob_title; ?></p>
                    <?php
                    global $count_featured;
                    $count_featured = 0;
                    $showposts = 8;
                    $featured_html = '';

                    if( function_exists('html_mjob_featured_home') ){
                        $featured_html = html_mjob_featured_home();
                        $showposts = 12 - $count_featured;
                    }

                    $args = array(
                        'post_type' => 'mjob_post',
                        'post_status' => array(
                            'publish',
                            'unpause',
                        ),
                        'showposts' => $showposts,
                        'orderby' => 'date',
                        'order' => 'DESC',
                    );

                   if( function_exists('html_mjob_featured_home') ){
                        //$args['meta_key'] = 'et_featured';
                        $args['meta_query'] = array(
                            'relation' => 'OR',
                            array(
                                //check to see if et_featured has been filled out
                                'key' => 'et_featured',
                                'compare' => 'IN',
                                'value' =>  array('', '0'),
                            ) ,
                            array(
                                //if no et_featured has been added show these posts too
                                'key' => 'et_featured',
                                'compare' => 'NOT EXISTS'
                            )
                        );
                    }
                    $home_query = new WP_Query($args);
                    global $ae_post_factory;
                    $post_object = $ae_post_factory->get('mjob_post');

                    ?>
                    <ul class="row mjob-list">
                        <?php
                        if( ! empty( $featured_html ) )
                            echo $featured_html;
                        ?>

                        <?php while ($home_query->have_posts()):

                            $home_query->the_post();
                            global $post;
                            $convert = $post_object->convert($post);?>

                            <li class="<?php echo mje_home_loop_item_css($convert);?>">
                                <?php

                                mje_get_template('template/mjob-item.php', array('current' => $convert)); ?>
                            </li>
                        <?php endwhile;?>
                    </ul>

                    <?php wp_reset_postdata();?>
            </div>
            <div class="view-all-jobs-wrap mje-home-default">
                <a class="btn-order waves-effect waves-light btn-submit mjob-order-action" href="<?php echo get_post_type_archive_link('mjob_post'); ?>">
                    <?php _e('View all jobs', 'enginethemes');?>
                </a>
            </div>
        </div>


        <!-- show job request here !-->
        <?php
            if( function_exists('_mjobrecruit_load_plugin')){
               echo do_shortcode('[mjob_recruitments]');
            }
        ?>
        <!-- end block job request !-->
        <?php get_template_part('template/about', 'block');?>
    </div>
<?php
get_footer();
?>
