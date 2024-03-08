<?php
class MJE_Invoices extends AE_Base
{
    public static $instance;
    public $page_invoice = '';

    public static function get_instance() {
        if( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        $this->page_invoice = $this->get_page();
        $this->page_invoice = $this->page_invoice ? $this->page_invoice[0] : '';

        $this->add_ajax( 'mje_fetch_invoices', 'fetch' );
        $this->add_action( 'init', 'custom_invoice_detail_rewrite_rule' );
    }

    /**
     * Get page invoice data
     *
     * @param void
     * @return object $post
     * @since 1.2
     * @author Tat Thien
     */
    public function get_page() {
        $post = get_posts( array(
            'post_type' => 'page',
            'post_status' => 'publish',
            'showposts' => 1,
            'name' => 'my-invoices'
        ) );

        return $post;
    }

    /**
     * Get an invoice data and convert it
     *
     * @param int $invoice_id
     * @return object $post
     * @since 1.2
     * @author Tat Thien
     */
    public static function get_single_invoice( $invoice_id ) {
        $post = get_post( $invoice_id );
        $post = self::convert_invoice( $post );
        return $post;
    }

    /**
     * Get invoice query args, used for WP_Query
     *
     * @param array $args
     * @return array $args  filtered
     * @since 1.2
     * @author Tat Thien
     */
    public static function get_query_args( $args = array() ) {
        global $user_ID;
        $default = array(
            'post_type' => 'order',
            'post_status' => 'any',
            'author' => $user_ID,
            'showposts' => 10,
            'meta_key' => 'et_order_created_time',
            'orderby' => 'meta_value_num',
            'meta_query' => array()
        );
        $args = wp_parse_args( $args, $default );
        return apply_filters( 'mje_invoices_query_args', $args );
    }

    /**
     * Convert post data with meta fields included
     *
     * @param object $post
     * @return object $post
     * @since 1.2
     * @author Tat Thien
     */
    public static function convert_invoice( $post ) {
        $payment_name = mje_render_payment_name();
        $post->payment_type = get_post_meta( $post->ID, 'et_order_gateway', true );
        $post->payment_text = isset( $payment_name[$post->payment_type] ) ? $payment_name[$post->payment_type] : "";
        $post->total = mje_format_price( get_post_meta( $post->ID, 'et_order_total', true ) );
        $post->post_title = '#' . $post->ID;
        $post->fee_commission = get_post_meta( $post->ID, 'et_order_fee_buyer', true );
        $created_time =  ( int ) get_post_meta( $post->ID, 'et_order_created_time', true );
        $post->date = date( get_option( 'date_format' ), $created_time );
        $post->detail_url = et_get_page_link( 'my-invoices' ) . $post->ID;

        switch ($post->post_status) {
            case 'publish':
                $post->status = '<span class="st-item st-completed">'. __( 'Completed', 'enginethemes' ) .'</span>';
                break;
            case 'pending':
                $post->status = '<span class="st-item st-pending">'. __( 'Pending', 'enginethemes' ) .'</span>';
                break;
            case 'draft':
                $post->status = '<span class="st-item st-cancelled">'. __( 'Cancelled', 'enginethemes' ) .'</span>';
                break;
            default:
                $post->status = '<span class="st-item st-unknown">'. __( 'Unknown', 'enginethemes' ) .'</span>';
        }

        return $post;
    }

    /**
     * Get invoices data by ajax request
     *
     * @param void
     * @return void
     * @since 1.2
     * @author Tat Thien
     */
    public function fetch() {
        $request = $_REQUEST;
        $paged = $request['page'];
        $query_args = $this->get_query_args( array(
            'paged' => $paged
        ) );

        $query_args = $this->filter_query( $request['query'], $query_args );

        // Create query
        $invoice_query = new WP_Query( $query_args );
        if( $invoice_query->have_posts() ) :
            while( $invoice_query->have_posts() ) :
                $invoice_query->the_post();
                global $post;
                $invoice_data[] = MJE_Invoices::convert_invoice( $post );
            endwhile;
        endif;

        // Get pagination
        $paginate = $request['paginate'];
        $text = $request['text'];
        ob_start();
        ae_pagination( $invoice_query, $paged, $paginate, $text );
        $pagination = ob_get_clean();

        // Response data
        if( !empty( $invoice_data ) ) {
            wp_send_json(array(
                'data' => $invoice_data,
                'paginate' => $pagination,
                'msg' => __( "Success", 'enginethemes' ) ,
                'success' => true,
                'max_num_pages' => $invoice_query->max_num_pages,
                'total' => $invoice_query->found_posts,
            ));
        } else {
            wp_send_json(array(
                'success' => false,
                'data' => array()
            ));
        }
    }

    /**
     * Filter query args
     *
     * @param array $query      ajax request query
     * @param array $default    default query args
     * @return array $default
     * @since 1.2
     * @author Tat Thien
     */
    public function filter_query( $query, $default) {
        // Filter by payment gateway
        if( ! empty( $query['payment'] ) ) {
            $default['meta_query'] = wp_parse_args( array(
                array(
                    'key' => 'et_order_gateway',
                    'value' => $query['payment']
                )
            ), $default['meta_query'] );
        }

        // Filter by status
        if( ! empty( $query['status'] ) ) {
            $default = wp_parse_args( array(
                'post_status' => $query['status']
            ), $default );
        } else {
            $default = wp_parse_args( array(
                'post_status' => 'any'
            ), $default );
        }

        // Sort by date
        if( ! empty( $query['orderby'] ) && $query['orderby'] == 'date' ) {
            $default = wp_parse_args( array(
                'meta_key' => 'et_order_created_time',
                'orderby' => 'meta_value_num',
                'order' => $query['order']
            ), $default );
        }

        // Sort by total
        if( ! empty( $query['orderby']) && $query['orderby'] == 'total' ) {
            $default = wp_parse_args( array(
                'meta_key' => 'et_order_total',
                'orderby' => 'meta_value_num',
                'order' => $query['order']
            ), $default );
        }

        return $default;
    }

    public function custom_invoice_detail_rewrite_rule() {
        $page_id = isset( $this->page_invoice->ID ) ? $this->page_invoice->ID : '';
        add_rewrite_rule(
            '^my-invoices/([^/]*)/?',
            'index.php?page_id=' . $page_id . '&invoice_id=$matches[1]',
            'top'
        );

        add_rewrite_tag( '%invoice_id%', '([^&]+)' );

        flush_rewrite_rules();
    }
}

$instance = MJE_Invoices::get_instance();