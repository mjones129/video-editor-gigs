<?php
function mjegeo_search_home($api_key, $heading ='', $subtitle ='', $search_adreess= false){
    $class = '';
    if(!$search_adreess){
        $class .= ' no-address-search';
    }
    ?>
    <div class="search-form geo-search-form">
        <h1 class="wow fadeInDown"><?php echo $heading; ?></h1>
        <h4 class="wow fadeInDown"><?php echo $subtitle; ?></h4>
    	<form action="<?php echo get_site_url(); ?>" class="<?php echo $class;?> form-search geo-form-search">
            <div class="outer-form row">
               <div class="col-md-5 col-sm-12 col-text-search">
               		<div class="col-md-12 col-sm-12">
                        <input type="text" name="s" class="text-search-home" placeholder="<?php _e('Enter keyword', 'mje_geo');?>">
                        <i class="fa fa-search" aria-hidden="true"></i>
                    </div>
                    <?php if($search_adreess){ ?>
                        <div class="col-md-6 col-sm-12">
                            <input type="text" name="search_location" id="et_full_location" class="address-search" placeholder="<?php _e("Address ...", 'mje_geo'); ?>">
                            <input type="hidden" name="latitude" id="latitude">
                        	<input type="hidden" name="longitude" id="longitude">
                        	<i class="fa fa-map-marker address-search-icon search-location-marker" aria-hidden="true"></i>
                        </div>
                    <?php } ?>
                </div>

                <div class=" col-md-4  col-sm-12 col-tax-search">

                    <div class=" col-md-12  col-sm-12">
                        <?php
                        ae_tax_dropdown(
                        	'location',
                        	array(
                        		'hide_empty' => true,
                                'class' => 'chosen-single tax-item de-chosen-single',
                                'hierarchical' => true,
                                'show_option_all' => __("All Location", 'mje_geo'),
                                'taxonomy' => 'location',
                                'value' => 'slug',
                               // 'show_option_none' => __("No Location", 'mje_geo'),
                            )
                        ); ?>
                        <i class="fa fa-angle-down"></i>
                     </div>
                </div>
                <div class="col-md-3 text-center col-search-btn   col-sm-12">
                	<button class="btn-search hvr-buzz-out waves-effect waves-light">
                    	<div class="search-title">
                        <span class="text-search"><?php _e('SEARCH', 'mje_geo');?></span>
                    	</div>
                    </button>
                </div>
            </div><!-- end outer-form !-->
        </form>
    </div>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $api_key;?>" async defer></script>
    <?php
}