<?php
/**
 * @since: version 1.3.9.3
 * @author: danng
 * @package : mje_elementor
*/
require_once dirname(__FILE__) . '/widgets.php';
require_once dirname(__FILE__) . '/widget_block_search.php';
require_once dirname(__FILE__) . '/widget_latest_mjob.php';
require_once dirname(__FILE__) . '/widget_categories.php';
require_once dirname(__FILE__) . '/replace_js.php';

// register list widget to buld homepage.
function mje_home_widgets() {
    register_widget( 'Mje_Block_LatestMjob' );
    register_widget( 'Mje_Block_Categories' );
    register_widget( 'Mje_Block_Search' );
}
add_action( 'widgets_init', 'mje_home_widgets', 99 );