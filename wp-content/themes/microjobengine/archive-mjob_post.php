<?php
global $wp_query, $ae_post_factory, $post;
$post_object = $ae_post_factory->get('mjob_post');
get_header();
?>
    <div id="content" class="search.php">
        <?php get_template_part('template/content', 'page');?>
        <div class="block-page mjob-container-control search-result">
            <div class="container">
                <h2 class="block-title">
                    <p class="block-title-text" data-prefix="<?php _e('in', 'enginethemes'); ?>">
                        <?php

                        // Get search result
                        $search_result = $wp_query->found_posts;

                        if($search_result == 1) {
                            printf(__('<span class="search-result-count">%s</span> <span class="search-text-result">MJOB AVAILABLE</span>', 'enginethemes'), $search_result);
                        } else {
                            printf(__('<span class="search-result-count">%s</span> <span class="search-text-result">MJOBS AVAILABLE</span>', 'enginethemes'), $search_result);
                        }

                        ?>
                    </p>
                    <div class="visible-lg visible-md">
                        <?php get_template_part('template/sort', 'template'); ?>
                    </div>
                    <div class="show-filter-wrap hidden-lg hidden-md">
                        <a class="filter-open-btn" href=""><?php _e('FILTER MJOB', 'enginethemes'); ?> <i class="fa fa-chevron-right" aria-hidden="true"></i></a>
                    </div>
                </h2>
                <div class="row ">
                    <div class="col-lg-3 col-md-3 col-sm-12 col-sx-12">
                        <div class="mje-col-left-wrap">
                            <div class="mje-col-left">
                                <div class="header-filter-wrap hidden-lg hidden-md">
                                    <a class="filter-close-btn" href=""><i class="fa fa-chevron-left" aria-hidden="true"></i><?php _e('BACK', 'enginethemes'); ?></a>
                                </div>
                                <div class="hidden-lg hidden-md">
                                    <a class="clear-filter-btn" href= "<?php echo get_post_type_archive_link('mjob_post') . ''?> "><?php _e('CLEAR ALL FILTER', 'enginethemes'); ?></a>
                                </div>
                                <div class="hidden-lg hidden-md">
                                    <?php get_template_part('template/sort', 'template'); ?>
                                </div>
                                <div class="menu-left">
                                    <p class="title-menu"><?php _e('Categories', 'enginethemes'); ?></p>
                                    <?php
                                        mje_show_filter_categories( 'mjob_category', array('parent' => 0));
                                    ?>
                                </div>
                                <?php get_sidebar('filter');?>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-9 col-sm-12 col-sx-12">
                        <div class="block-items no-margin mjob-list-container">
                            <?php
                            get_template_part('template/list', 'mjobs-search');
                            $filter = apply_filters( 'mje_mjob_param_filter_query', $_GET );
                            $wp_query->query = array_merge( $wp_query->query, $filter);

                            echo '<div class="paginations-wrapper">';
                            ae_pagination($wp_query, get_query_var('paged'));
                            echo '</div>';
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
get_footer();
?>