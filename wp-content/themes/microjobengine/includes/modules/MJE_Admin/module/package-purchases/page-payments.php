<div class="et-main-content order-container" id="">
    <div class="et-main-main">
        <div class="group-wrapper">
            <div class="group-fields">
                <div class="search-box et-member-search">
                    <p class="title-search"><?php _e("All Purchases", 'enginethemes');?></p>
                    <div class="function-filter">
                    <span class="et-search-role">
                        <select name="role" id="" class="gateway et-input" >
                            <option value="" ><?php _e("All payment types", 'enginethemes');?></option>
                            <?php
                            foreach ($support_gateway as $name => $label) {
                            	echo '<option value="' . $name . '" >' . $label . '</option>';
                            }?>
                        </select>
                    </span>
                        <span class="et-search-status">
                        <select name="post_status" id="" class="post_status et-input" >
                            <option value="" ><?php _e("All status", 'enginethemes');?></option>
                            <?php
                                foreach ($post_status as $name => $label) {
                                	echo '<option value="' . $name . '" >' . $label . '</option>';
                                }
                                ?>
                        </select>
                    </span>
                        <span class="et-search-input">
                        <input type="text" class="et-input order-search" name="keyword" placeholder="<?php _e("Search...", 'enginethemes');?>">
                        <i class="fa fa-search" aria-hidden="true"></i>
                    </span>
                    </div>
                </div>
                <!-- // user search box -->

                <div class="et-main-main no-margin clearfix overview list">
                    <!-- order list  -->
                    <div class="list-payment-package package-purchases/page-payments.php">
                        <ul class="row title-list">
                            <li class="col-md-2 col-sm-2"><?php _e("Price", 'enginethemes');?></li>
                            <li class="col-md-6 col-sm-5"><?php _e("Purchase info", 'enginethemes');?></li>
                            <li class="col-md-2 col-sm-2"></li>
                            <li class="col-md-2 col-sm-3 payment-type"><?php _e("Payment type", 'enginethemes');?></li>
                        </ul>

                        <ul class="list-inner list-payment users-list">
                        <?php
                        if ($orders->have_posts()) {
                        	while ($orders->have_posts()) {
                        		$orders->the_post();
                        		get_template_part('includes/modules/MJE_Admin/module/package-purchases/template/order', 'item');
                        	}
                        } else {
                        	_e('<li class="no-payments">There are no payments yet.</li>', 'enginethemes');
                        } ?>
                        </ul>
                        <div class="paginations-wrapper">
                        <?php
                        ae_pagination($orders, PAGINATION_START, 'page');
                        wp_reset_query();
                        ?>
                        </div>
                    </div>
                    <!--// order list  -->
                </div>
                <!-- //user list -->
            </div>
        </div>
    </div>
</div>