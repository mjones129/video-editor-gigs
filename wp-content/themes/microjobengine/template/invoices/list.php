<?php
$payment_list = mje_get_payment_list();
$invoice_status_list = array(
    '' => __( 'All status', 'enginethemes' ),
    'publish' => __( 'Completed', 'enginethemes' ),
    'pending' => __( 'Pending', 'enginethemes' ),
    'draft' => __( 'Cancelled', 'enginethemes' ),
);
?>

<div class="table-wrapper">
    <div class="table-filter clearfix">
        <select name="status">
            <?php foreach ( $invoice_status_list as $key => $name ) { ?>
                <option value="<?php echo $key; ?>"><?php echo $name; ?></option>
            <?php } ?>
        </select>

        <select name="payment">
            <option value=""><?php _e( 'All payment types', 'enginethemes' ); ?></option>
            <?php foreach ( $payment_list as $key => $name ) { ?>
                <option value="<?php echo $key; ?>"><?php echo $name; ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="table-content">
        <table id="" class="template_/invoices_/list.php">
            <thead>
            <tr>
                <th><?php _e( 'Invoice #', 'enginethemes'); ?></th>
                <th class="sort-head">
                    <a href="#" class="orderby clearfix" data-sort="date" data-order="asc">
                        <?php _e( 'Date', 'enginethemes'); ?>
                        <span class="sort-icon"><i class="fa fa-sort-desc"></i></span>
                    </a>
                </th>
                <th class="sort-head td-w-20">
                    <a href="#" class="orderby clearfix" data-sort="total" data-order="desc">
                        <?php _e( 'Total', 'enginethemes'); ?>
                        <span class="sort-icon"><i class="fa fa-sort"></i></span>
                    </a>
                </th>
                <th><?php _e( 'Fee', 'enginethemes'); ?></th>
                <th><?php _e( 'Payment Type', 'enginethemes'); ?></th>
                <th><?php _e( 'Status', 'enginethemes'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $args = MJE_Invoices::get_query_args();
            $invoice_query = new WP_Query($args);
            $invoice_data = array();
            if( $invoice_query->have_posts() ) :
                while( $invoice_query->have_posts() ) :
                    $invoice_query->the_post();
                    global $post;
                    $invoice_data[] = MJE_Invoices::convert_invoice( $post );
                    mje_get_template_part('template/invoice', 'item');
                endwhile;
            endif;
            ?>
            </tbody>
            <script type="data/json" id="invoices-data"><?php echo json_encode( $invoice_data ); ?></script>
        </table>
    </div><!-- /.table-content -->

    <?php if( $invoice_query->have_posts() ) : ?>
        <div class="table-footer clearfix">
            <div class="paginations-wrapper">
                <?php ae_pagination( $invoice_query, get_query_var( 'paged' ), 'page') ; ?>
            </div>

            <p class="help-text"><?php _e( '10 invoices / page', 'enginethemes' ); ?></p>
        </div>

        <div class="nothing-found">
            <p class="no-items" ><?php _e( 'There are no invoices found!', 'enginethemes' ); ?></p>
        </div>
    <?php else : ?>
        <div class="nothing-found" style="display: block !important;">
            <p class="no-items" ><?php _e( 'There are no invoices found!', 'enginethemes' ); ?></p>
        </div>
    <?php endif; ?>
</div><!-- /.table-wrapper -->