<?php
require_once dirname(__FILE__) . '/aecore/index.php';
if(!class_exists('AE_Base')) return;
require_once dirname(__FILE__) . '/alias-function.php';
require_once dirname(__FILE__) . '/theme.php';
require_once dirname(__FILE__) . '/modules/MJE_Mailing/index.php';
require_once dirname(__FILE__) . '/class-mje-post.php';
require_once dirname(__FILE__) . '/class-mje-post-factory.php';
require_once dirname(__FILE__) . '/class-mje-post-action.php';
require_once dirname(__FILE__) . '/class-mje-search.php';
require_once dirname(__FILE__) . '/class-mje-revenue.php';
require_once dirname(__FILE__) . '/class-mje-invoices.php';
require_once dirname(__FILE__) . '/modules/index.php';
require_once dirname(__FILE__) . '/widgets.php';
require_once dirname(__FILE__) . '/libs/index.php';
require_once dirname( __FILE__ ) . '/class-mje-order.php';
require_once dirname( __FILE__ ) . '/class-mje-checkout.php';
require_once dirname( __FILE__ ) . '/class-mje-heartbeat.php';
require_once dirname(__FILE__) . '/class-mje-update-data.php';
require_once dirname(__FILE__) . '/tool_data/index.php';
require_once dirname(__FILE__) . '/functions.php';
require_once dirname(__FILE__) . '/shortcode.php';
require_once dirname(__FILE__) . '/elementor/index.php';
require_once dirname(__FILE__) . '/generate_pages.php';
require_once dirname(__FILE__) . '/add_user_capabilities.php';
require_once dirname(__FILE__) . '/dashboard.php';

/**
 * Check plugin is active or not
 */
function et_is_plugin_active($plugin) {
    include_once (ABSPATH . 'wp-admin/includes/plugin.php');
    return is_plugin_active($plugin);
}