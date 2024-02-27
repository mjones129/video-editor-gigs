<?php
class MJE_Search extends AE_PostAction
{
    public static $instance;

    /**
     * get instance method
     */
    static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    function __construct() {
        $this->add_filter('pre_get_posts', 'filter_search_results');
    }

    /**
     * Filter search results
     * @param $query
     * @return $query
     * @since 1.0
     * @package MicrojobEngine
     * @category Search
     * @author Tat Thien
     */
    public function filter_search_results($query) {
        global $wp_query;
        global $wpdb;
        $search_var = esc_html( get_search_query( 's' ) );
        $wp_query->set('s', $search_var);

        if(!is_admin() && $query->is_main_query()) {
            if($query->is_search()) {
                $query->is_tax = false;
                $query->is_archive = false;

                // Save keyword session
                global $wp_session;
                $keyword = get_query_var('s');
                setcookie('mjob_search_keyword', $keyword, time()+3600);

                $query->set('post_type', array('mjob_post'));
                // Role visitor
                $query->set('post_status', array('publish', 'unpause'));
                if( is_super_admin() ){
                    // Role: admin
                    $query->set('post_status', array('pending', 'publish', 'unpause'));
                }

                // Filter by category
                if(isset($_GET['mjob_category']) && !empty($_GET['mjob_category'])) {
                    $categoryID = $_GET['mjob_category'];
                    if(isset($_GET['skill_ids']) && !empty($_GET['skill_ids'])) {
                        $skill = explode(",", $_GET['skill_ids']);
                        $taxquery = array(
                            'relation' => 'AND',
                            array(
                                'taxonomy' => 'skill',
                                'field' => 'term_id',
                                'terms' => $skill,
                                'include_children' => true,
                                'operator' => 'IN'
                            ),
                            array(
                                'taxonomy' => 'mjob_category',
                                'field' => 'term_id',
                                'terms' => array($categoryID)
                            )
                        );
                        $query->set( 'tax_query', $taxquery );
                        unset($query->query_vars['mjob_category']);
                    }
                    else {
                         $query->query_vars['tax_query'] = array(
                            'relation' => 'OR',
                            array(
                                'taxonomy' => 'mjob_category',
                                'field' => 'term_id',
                                'terms' =>  array($categoryID)
                            )
                        );
                    }
                }
                else if(isset($_GET['skill_ids']) && !empty($_GET['skill_ids'])) {
                    // Filter by skill only
                    $skill = explode(",", $_GET['skill_ids']);
                    $query->query_vars['tax_query'] = array(
                        'relation' => 'OR',
                        array(
                            'taxonomy' => 'skill',
                            'field' => 'term_id',
                            'terms' => $skill
                        ),
                    );
                }
                if(isset($_GET['orderby']) && !empty($_GET['orderby'])) {
                    if($_GET['orderby'] == 'date')
                    {
                        $query->query_vars['orderby'] = 'date';
                    }
                    else
                    {
                        $query->query_vars['meta_key'] = $_GET['orderby'];
                        $query->query_vars['orderby'] = 'meta_value_num date';
                    }
                }
                if (isset($_GET['price_min']) || isset($_GET['price_max']) ) {
                    $min = !empty($_GET['price_min']) ? (int)$_GET['price_min']: 0;
                    $max = !empty($_GET['price_max']) ? (int)$_GET['price_max']: PHP_INT_MAX;

                    $query->query_vars['meta_query'][] = array(
                        'key' => 'et_budget',
                        'value' => array(
                            $min,
                            $max
                        ) ,
                        'type' => 'numeric',
                        'compare' => 'BETWEEN'
                    );
                }
                if(isset($_GET['language_ids']) && !empty($_GET['language_ids'])) {
                    $language =  $_GET['language_ids'];
                    global $wpdb;
                    $sql = "SELECT DISTINCT post_author FROM `$wpdb->posts` as wp JOIN $wpdb->term_relationships AS wtr ON wp.ID = wtr.object_id WHERE post_type = 'mjob_profile' AND wtr.term_taxonomy_id IN ($language)";
                    $results = $wpdb->get_results($sql);
                    $args = array();
                    if (!empty($results)) {
                        foreach ($results as $key => $value) {
                            array_push($args, $value->post_author);
                        }
                    }
                    $query->query_vars['author__in'] = ($args) ? $args : 'null';
                }
                if(isset($_GET['sort']) && !empty($_GET['sort'])) {
                     $query->query_vars['order'] = $_GET['sort'];
                }
                $query = apply_filters( 'mje_mjob_search_in_url', $query );
            }
        }
        return $query;
    }
}
$new_instance = MJE_Search::get_instance();