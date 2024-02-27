<?php
function mjegeo_post_service_input($api_url, $is_ready = 1){
	if( $is_ready){ ?>
		<div class="form-group clearfix">
	        <label><?php _e('Address','mje_geo');?></label>
	        <input type="text" class="input-item form-control text-field" id="et_full_location" placeholder="Enter your address" name="et_full_location" >
	        <input type="hidden" class="input-item" name="et_location_lat" id="et_location_lat">
	        <input type="hidden" class="input-item" name="et_location_lng" id="et_location_lng">
	      	<div id="simplemap"></div>
	      	<script>
		      var map;
		      function initMapBox() {
		      	var pos = {lat: -34.397, lng: 150.644};
		      	if( typeof curPos != 'undefined' && curPos.pos ){
		      		pos = curPos.pos;
		      	}
		        map = new google.maps.Map(document.getElementById('simplemap'), {
		          center: pos,
		          zoom: 13
		        });

		      }
		    </script>
		    <script src="<?php echo $api_url;?>&callback=initMapBox" async defer></script>
	</div>
<?php } ?>

	<div class="form-group clearfix">
	<label><?php _e("LOCATION", 'mje_geo'); ?> </label>
		<?php
		ae_tax_dropdown(
			'location' ,
	        array(  'class' => 'chosen-single tax-item required',
	                'hide_empty' => false,
	                'hierarchical' => true ,
	                'id' => 'location' ,
	                'show_option_all' => __("Select your location", 'mje_geo')
	            )
	        ) ;
	    ?>
	</div>
    <?php
}
