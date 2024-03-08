<?php
global $user_ID;
$invoice = MJE_Invoices::get_single_invoice( $invoice_id );
$invoice_parent = get_post( $invoice->post_parent );

if( $user_ID != $invoice->post_author ) :?>
    <div class="float-center">
        <p class="no-items"><?php _e( 'You don\'t have permission to view this invoice.', 'enginethemes' ); ?></p>
        <a href="<?php echo et_get_page_link( 'my-invoices' ); ?>"><?php _e( 'Back to my invoices', 'enginethemes' ); ?></a>
    </div>
<?php else : ?>
    <div class="invoice-detail table-wrapper">
        <div class="table-content">
            <table>
                <thead>
                    <tr>
                        <th><?php _e( 'Invoice #', 'enginethemes'); ?></th>
                        <th><?php _e( 'Date', 'enginethemes'); ?></th>
                        <th class="td-w-20"><?php _e( 'Total', 'enginethemes'); ?></th>
                        <th><?php _e( 'Payment Type', 'enginethemes'); ?></th>
                        <th><?php _e( 'Status', 'enginethemes'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo $invoice->post_title; ?></td>
                        <td><?php echo $invoice->date; ?></td>
                        <td><?php echo $invoice->total; ?></td>
                        <td><?php echo $invoice->payment_text; ?></td>
                        <td><?php echo $invoice->status; ?></td>
                    </tr>
                </tbody>
            </table>
        </div><!-- /.table-content -->

        <?php if( $invoice->payment_type == 'cash' ) : ?>
            <?php
            $cash_options = ae_get_option('cash');
            $cash_message = $cash_options['cash_message'];
            ?>
            <div class="invoice-note">
                <p class="type-cash"><?php _e('CASH NOTE', 'enginethemes'); ?></p>
                <?php echo $cash_message; ?>
            </div>
        <?php endif; ?>

        <?php if( $invoice->post_status != 'draft') : ?>
            <div class="link-detail-method">
                <?php if( $invoice_parent->post_type == 'mjob_post' ) : ?>
                    <a href="<?php echo get_the_permalink( $invoice_parent->ID ) ?>" class="<?php mje_button_classes( array( ) ); ?>"><?php _e('Visit your mJob', 'enginethemes'); ?><i class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
                <?php else : 
                     /*?><a href="<?php echo get_the_permalink( $invoice_parent->ID ) ?>" class="<?php mje_button_classes( array( ) ); ?>"><?php _e('Visit your mJob Order', 'enginethemes'); ?><i class="fa fa-long-arrow-right" aria-hidden="true"></i></a><?php */
					echo apply_filters('show_text_button_process_payment',$content="", $invoice_parent);
                endif ?>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>
