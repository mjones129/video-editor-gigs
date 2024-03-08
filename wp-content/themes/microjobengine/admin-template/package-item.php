<?php
	$item	=	$this->item;
	/**
	 * This template item in AECore to render list of pack
	 * the template is sample, for using with custom purpose you should override it
	*/
?>
<li class="pack-item item" id="pack_<?php echo $item->ID; ?>" data-ID="<?php echo $item->ID; ?>">
	<div class="sort-handle col-md-1 col-sm-1"><i class="fa fa-database" aria-hidden="true"></i><span><?php echo $item->sku ?></span></div>
	<div class="col-md-3 col-sm-3"><span><?php echo $item->post_title; ?></span></div>
	<div class="col-md-2 col-sm-2"><?php echo $item->package_price; ?></div>
	<div class="col-md-2 col-sm-2"><?php echo $item->package_duration; ?></div>
	<div class="col-md-2 col-sm-2"><?php echo $item->et_number_posts; ?></div>
	<div class="actions col-md-1 col-sm-1">
		<a href="#" title="Edit" class="icon act-edit" rel="<?php echo $item->ID; ?>"><i class="fa fa-pencil" aria-hidden="true"></i></a>
		<a href="#" title="Delete" class="icon act-del" rel="<?php echo $item->ID; ?>"><i class="fa fa-times" aria-hidden="true"></i></a>
	</div>
	<div class="clearfix"></div>
</li>