<?php
if( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if( !function_exists( 'mje_stripe_get_template' ) ) {
    /**
     * Get template, passing attributes and including the file
     *
     * @param $template_name
     * @param array $args
     * @param string $template_path
     * @since 1.0
     * @author Tat Thien
     */
    function mje_stripe_get_template( $template_name, $args = array(), $template_path = '' ) {
        if( !empty( $args ) && is_array( $args ) ) {
            extract( $args );
        }

        if( ! $template_path ) {
            $template_path = MJE_STRIPE_PATH . '/templates/';
        }

        $located = $template_path . $template_name;

        if( ! file_exists( $located ) ) {
            return;
        }

        include( $located );
    }
}
