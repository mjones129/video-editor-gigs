<?php
if(!defined('ABSPATH')) {
    exit;
}

if(class_exists('WP_Customize_Control')) {
    class WP_Customize_Group_Control extends WP_Customize_Control
    {

        protected function render() {
            if($this->type == 'open_group_control') {
                echo '<div>';
            } else if($this->type == 'close_group_control') {
                echo '</div>';
            }
        }
    }
}