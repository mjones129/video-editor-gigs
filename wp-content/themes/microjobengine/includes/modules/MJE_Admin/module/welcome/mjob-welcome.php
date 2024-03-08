<?php
if(!defined('ABSPATH')) {
    exit(1);
}
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 8/2/2016
 * Time: 17:04
 */
class mJob_Welcome extends AE_Page {
    public function __construct()
    {
        $this->add_action('admin_print_styles','load_style');
    }

    private static function get_url() {
        return TEMPLATEURL.'/includes/modules/MJE_Admin/';
    }

    public function render()
    {
       get_template_part('includes/modules/MJE_Admin/module/welcome/page-welcome');
    }

    public function load_style() {
    }
}