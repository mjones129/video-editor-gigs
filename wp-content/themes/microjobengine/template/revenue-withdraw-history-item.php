<?php
    global $ae_post_factory, $post;
    $post_obj = $ae_post_factory->get('ae_withdraw_history');
    $current = $post_obj->convert($post);
?>
<tr class="history-item">
    <td><?php echo $current->payment_method_text; ?></td>
    <td><?php echo date(get_option('date_format').' '.get_option('time_format'),$current->post_date).' '.mje_text_timezone(); //$current->history_time; ?></td>
    <td><?php echo $current->amount_text; ?></td>
    <?php
        if($current->history_status == 'completed'):
            echo '<td class="successful">'. __('Successful', 'enginethemes') .'</td>';
        elseif($current->history_status == 'cancelled'):
            echo '<td class="rejected">'. __('Rejected', 'enginethemes') .'</td>';
        else:
            echo '<td class="pending-text">'. __('Pending', 'enginethemes') .'</td>';
        endif;
    ?>

</tr>