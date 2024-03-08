<script type="text/template" id="invoice-item-template">
    <td><a href="{{= detail_url }}">{{= post_title }}</a></td>
    <td>{{= date }}</td>
    <td>{{= total }}</td>
    <td>
    	<#  if( fee_commission) { #>
    		{{= fee_commission + '%' }} 
    	<# } else if (fee_commission == '0'){ #>
    		<?php echo _e('Zero Fee','enginethemes'); ?>
    	<# } else{ #>
    		<?php echo _e('Not Applied','enginethemes'); ?>
    	<# }  #>   		
    </td>
    <td>{{= payment_text }}</td>
    <td>{{= status }}</td>
</script>