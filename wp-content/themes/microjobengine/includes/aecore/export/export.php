<?php

function download_mje_order_admin() {

    if( isset($_REQUEST['submit']) && $_REQUEST['submit'] == 'download' ){
        require_once dirname( __FILE__ ) . '/class-ae-exporter.php';
        require_once dirname( __FILE__ ) . '/class-ae-excel.php';

        $status = isset($_REQUEST['post_status']) ? $_REQUEST['post_status'] : '';
        $type   = isset($_REQUEST['download']) ? $_REQUEST['download'] : 'xml';

        if( $type == 'xml'){
            $xml_args = $defaults = array(
                'content'    => 'mjob_order',
                'author'     => false,
                'category'   => false,
                'start_date' => false,
                'end_date'   => false,
            );
            if( $status )
                $xml_args['status'] = $status;

            if ( isset($_REQUEST['payment_type']) ) {
                $xml_args['meta_key'] = 'payment_type';
                $xml_args['meta_value'] = $_REQUEST['payment_type'];
            }
            mje_export($args);

        } else {
            $args = array(
                'post_type'         => 'mjob_order',
                'posts_per_page'    => -1,
            );
            if( empty($status) )
                $args['post_status'] = $status;

            if ( isset($_REQUEST['payment_type']) ) {
                $args['meta_key'] = 'payment_type';
                $args['meta_value'] = $_REQUEST['payment_type'];
            }

            $ae_excel   = new AE_Export_Excel($type);
            $ae_excel->download($args);
        }
    }

}
add_action( 'admin_init', 'download_mje_order_admin', 1 );