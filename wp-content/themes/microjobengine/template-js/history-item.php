<script type="text/template" id="history-item-loop">
    <td>{{= payment_method_text }}</td>
    <td>{{= date_text }}</td>
    <td>{{= amount_text }}</td>
    <td class="<# if(history_status == 'completed') { #> successful <# } else if(history_status == 'cancelled') { #> rejected <# } else { #> pending-text <# } #>">{{= history_status_text }}</td>
</script>