<?php

function filter_query_advance($query_args)
{
    global $wpdb;

	$query = $_REQUEST['query'];
    $advance = array();

    if (isset($query['et_budget']) && !empty($query['et_budget'])) {
        $budget = $query['et_budget'];
        $budget = explode(",", $budget);
        $min = !empty($budget[0]) ? (int)$budget[0]: 0;
        $max = !empty($budget[1]) ? (int)$budget[1]: PHP_INT_MAX;

        $advance['meta_query'][] = array(
            'key' => 'et_budget',
            'value' => array(
              	$min,
                $max
            ) ,
            'type' => 'numeric',
            'compare' => 'BETWEEN'
        );
    }
    if(isset($query['language']) && !empty($query['language'])) {
        $language = implode(',', $query['language']);
        global $wpdb;
        $sql = "SELECT DISTINCT post_author FROM `$wpdb->posts` as wp JOIN $wpdb->term_relationships AS wtr ON wp.ID = wtr.object_id WHERE post_type = 'mjob_profile' AND wp.post_status = 'publish' AND wtr.term_taxonomy_id IN ($language)";
        $results = $wpdb->get_results($sql);
        if (!empty($results)) {
            $args = array();
            foreach ($results as $key => $value) {
                array_push($args, $value->post_author);
            }
        }
        $advance['author__in'] = ($args) ? $args : 'null';
    }
    if( !empty($advance) )
        $query_args  = wp_parse_args( $query_args, $advance );

    return $query_args;
}
add_filter( 'mje_mjob_filter_query_args', 'filter_query_advance' );
add_filter( 'mje_mjob_param_filter_query', 'filter_param_search' );
function filter_param_search($query)
{
    $filter = array('is_search'=> is_search());
    if(isset($query['skill_ids']) && !empty($query['skill_ids']))
    {
        $filter['skill'] = explode(',', $query['skill_ids'] );
    }
    if(isset($query['language_ids']) && !empty($query['language_ids']))
    {
        $filter['language'] = explode(',', $query['language_ids'] );
    }
    if (isset($query['price_min']) || isset($query['price_max']) ) {
        $query['price_min'] = isset($query['price_min']) ? $query['price_min'] : '' ;
        $query['price_max'] = isset($query['price_max']) ? $query['price_max'] : '' ;
        $filter['et_budget'] = $query['price_min'] . ',' . $query['price_max'] ;
    }
    if(isset($query['verified']) && $query['verified'] == "true" )
    {
        $filter['verified'] = $query['verified'];
    }
    return  $filter;
}
?>