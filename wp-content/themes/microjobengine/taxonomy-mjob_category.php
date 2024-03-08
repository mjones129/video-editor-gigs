<?php
global $wp_query, $ae_post_factory, $post;
$post_object = $ae_post_factory->get('mjob_post');

$term = get_queried_object();
$term_id = $term->term_id;
get_header();
?>
    <div id="content" class="microjobengine\taxonomy.php">
        <?php get_template_part('template/content', 'page');?>
        <div class="block-page mjob-container-control search-result">
            <div class="container">
                <h2 class="block-title">
                    <span class="block-title-text" data-prefix="<?php _e('in', 'enginethemes'); ?>">
                        <?php
                        // Get term name
                        $term_name = (isset($term->name) && is_tax('mjob_category')) ? sprintf(__('<span class="term-name">in %s</span>', 'enginethemes'), $term->name) : '<span class="term-name"></span>';
                        // Get search result
                        $search_result = $wp_query->found_posts;

                        if($search_result == 1) {
                            printf(__('<span class="search-result-count">%s</span> <span class="search-text-result">MJOB AVAILABLE', 'enginethemes'), $search_result);
                        } else {
                            printf(__('<span class="search-result-count">%s</span> <span class="search-text-result">MJOBS AVAILABLE', 'enginethemes'), $search_result);
                        }
                        ?>
                    </span>

                    <div class="visible-lg visible-md">
                        <?php get_template_part('template/sort', 'template'); ?>
                    </div>
                    <div class="show-filter-wrap hidden-lg hidden-md">
                        <a class="filter-open-btn" href=""><?php _e('FILTER MJOB', 'enginethemes'); ?> <i class="fa fa-chevron-right" aria-hidden="true"></i></a>
                    </div>

                </h2>
                <div class="row">


                    <div class="col-lg-3 col-md-3 col-sm-12 col-sx-12">
                        <div class="mje-col-left-wrap">
                            <div class="mje-col-left">
                                <div class="header-filter-wrap hidden-lg hidden-md">
                                    <a class="filter-close-btn" href=""><i class="fa fa-chevron-left" aria-hidden="true"></i><?php _e('BACK', 'enginethemes'); ?></a>
                                </div>
                                <div class="hidden-lg hidden-md">
                                     <a class="clear-filter-btn" href= "<?php echo get_post_type_archive_link('mjob_post');?> "><?php _e('CLEAR ALL FILTER', 'enginethemes'); ?></a>
                                </div>
                                <div class="hidden-lg hidden-md">
                                    <?php get_template_part('template/sort', 'template'); ?>
                                </div>
                                <div class="menu-left">
                                    <p class="title-menu"><?php _e('Categories', 'enginethemes'); ?></p>
                                    <?php
                                        mje_show_filter_categories( 'mjob_category', array('parent' => 0), $term_id);
                                    ?>
                                </div>
                                <?php get_sidebar('filter');?>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-9 col-md-9 col-sm-12 col-sx-12">
                        <div class="block-items no-margin mjob-list-container">
                            <?php
                            get_template_part('template/list', 'mjobs');
                            $wp_query->query = array_merge(  $wp_query->query ,array('is_archive_mjob_post' => is_post_type_archive('mjob_post') ) ) ;
                            echo '<div class="paginations-wrapper">';
                            ae_pagination($wp_query, get_query_var('paged'));
                            echo '</div>';
                            wp_reset_query();
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