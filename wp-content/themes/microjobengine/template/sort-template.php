<?php
$type = '';
 if( isset($_GET['orderby']) && !empty($_GET['orderby']) )
 {
 	if( isset($_GET['sort']) && !empty($_GET['sort']) )
 	{
 		if($_GET['orderby'] == "date")
 		{
 			if( $_GET['sort'] == 'DESC')
	 		{
	 			$type = 'DateNewest';
	 		}
	 		else{
	 			$type = 'DateOldest';
	 		}
 		}
 		else{
 			if( $_GET['sort'] == 'DESC')
	 		{
	 			$type = 'BudgetHight';
	 		}
	 		else{
	 			$type = 'BudgetOlLow';
	 		}
 		}
 	}
 	else{
 		$type = $_GET['orderby'];
 	}
 }
?>

<div class="filter-by">
    <span><?php _e('Sort by', 'enginethemes'); ?><span>:</span></span>
   	<select class="status-filter" name="orderby">
   		<?php
   		if( has_mje_featured() ){ ?>
   		 	<option value="et_featured" data-order="DESC" <?php echo ($type == 'et_featured' || empty($type) ) ? 'selected' : ''; ?> ><?php _e('Featured First', 'enginethemes'); ?></option>
   		<?php } ?>

	    <option value="date" data-order="DESC" <?php echo $type == 'DateNewest' ? 'selected' : ''; ?> ><?php _e('Newest', 'enginethemes'); ?></option>
	    <option value="date" data-order="ASC"<?php echo $type == 'DateOldest' ? 'selected' : ''; ?> ><?php _e('Oldest', 'enginethemes'); ?></option>
	    <option value="rating_score" <?php echo $type == 'rating_score' ? 'selected' : ''; ?> ><?php _e('Highest ratings', 'enginethemes'); ?></option>
	    <option value="et_total_sales" <?php echo $type == 'et_total_sales' ? 'selected' : ''; ?> ><?php _e('Bestseller', 'enginethemes'); ?></option>
	    <option value="view_count" <?php echo $type == 'view_count' ? 'selected' : ''; ?> ><?php _e('Highest views', 'enginethemes'); ?></option>
	    <option value="et_budget" data-order="ASC" <?php echo $type == 'BudgetOlLow' ? 'selected' : ''; ?> ><?php _e('Price: Low to High', 'enginethemes'); ?></option>
	    <option value="et_budget" data-order="DESC" <?php echo $type == 'BudgetHight' ? 'selected' : ''; ?> ><?php _e('Price: High to Low', 'enginethemes'); ?></option>
	</select>
</div>
<!--<div class="view-as">-->
<!--    <ul>-->
<!--        <span>--><?php //_e('View as', 'enginethemes'); ?><!--</span>-->
<!--        <li class="grid"><i class="fa fa-th"></i></li>-->
<!--        <li class="list"><i class="fa fa-align-justify"></i></li>-->
<!--    </ul>-->
<!--</div>-->