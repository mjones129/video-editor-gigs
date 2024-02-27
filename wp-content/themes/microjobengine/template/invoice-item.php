<?php
global $post, $ae_post_factory;
$invoice = MJE_Invoices::convert_invoice( $post );
?>
<tr class="invoice-item invoid-id-<?php echo $post->ID;?> check-postPype-<?php echo $post->post_type;?>">
    <td><a href="<?php echo $invoice->detail_url; ?>"><?php echo $invoice->post_title; ?></a></td>
    <td><?php echo $invoice->date; ?></td>
    <td><?php echo $invoice->total; ?></td>
    <td>
    	<?php
	    	if($invoice->fee_commission)
	    		echo $invoice->fee_commission.'%';
	    	elseif ($invoice->fee_commission == '0')
	    		echo _e('Zero Fee','enginethemes');
	    	else
	    		echo _e('Not Applied','enginethemes');
    	?>
    </td>
    <td><?php echo $invoice->payment_text; ?></td>
    <td><?php echo $invoice->status; ?></td>
</tr>