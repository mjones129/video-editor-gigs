<?php $title =  __("Click to select a featured image", 'enginethemes'); ?>
<script type="text/template" id="ae_carousel_template">
    <li class="image-item" id="{{= attach_id }}" >
        <span class="img-gallery">
            <a href="#"  data-full="{{= mjob_detail_slider[0] }}" data-id="{{= attach_id }}" class="mjob-img-wrapper">
                <img title="<?php echo $title; ?>" data-id="{{= attach_id }}" src="{{= medium_post_thumbnail[0] }}" />
            </a>
            <a href="#" id="mjob-delete-{{=attach_id}}" title="<?php _e("Delete", 'enginethemes'); ?>" class="delete-img delete"><i class="fa fa-times"></i></a>
        </span>
        <input title="<?php echo $title; ?>" type="radio" name="featured_image" value="{{= attach_id }}" <# if(typeof is_feature !== "undefined" ) { #> checked="true" <# } #> />
    </li>
</script>