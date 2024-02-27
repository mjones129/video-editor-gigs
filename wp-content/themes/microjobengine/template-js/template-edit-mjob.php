<?php global $user_ID;?>
<form  class="post-job step-post post et-form edit-mjob-form" style="display: none">
    <p class="mjob-title"><?php _e('Edit your job', 'enginethemes');?></p>

    <div class="loading">
        <div class="loading-img"></div>
    </div>

    <div class="form-group clearfix">
        <div class="input-group">
            <label for="post_title" class="input-label"><?php _e('Job name', 'enginethemes');?></label>
            <input type="text" class="input-item input-full" name="post_title" value="" required>
        </div>
    </div>
    <div class="form-group row clearfix <?php echo ('1' == ae_get_option('custom_price_mode') || is_super_admin($user_ID)) ? 'has-price-field' : ''; ?>">
        <?php if ('1' == ae_get_option('custom_price_mode') || is_super_admin($user_ID)): ?>
            <?php
$min_price = ae_get_option('mjob_min_price') ? absint(ae_get_option('mjob_min_price')) : 5;
$max_price = ae_get_option('mjob_max_price') ? absint(ae_get_option('mjob_max_price')) : 15;
$currency_code = ae_currency_code(false);
?>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 clearfix">
                <div class="input-group">
                    <?php
if (is_super_admin($user_ID) && '1' != ae_get_option('custom_price_mode')) {
	?>
                        <label for="et_budget"><?php printf(__('Price (%s)', 'enginethemes'), $currency_code);?></label>
                        <input type="number" name="et_budget" class="input-item et_budget field-positive-int time-delivery" min="1" pattern="[-+]?[0-9]*[.,]?[0-9]+" required>
                        <?php
} else {
	?>
                        <label for="et_budget"><?php printf(__('Price (%s)', 'enginethemes'), $currency_code);?></label>
                        <input type="number" name="et_budget" placeholder="<?php printf(__('%s - %s', 'enginethemes'), mje_format_price($min_price, "", true, false), mje_format_price($max_price, "", true, false));?>" class="input-item et_budget field-positive-int time-delivery" min="<?php echo $min_price ?>" max="<?php echo $max_price ?>" pattern="[-+]?[0-9]*[.,]?[0-9]+" required>
                        <?php
}
?>
                </div>
            </div>
        <?php endif?>

        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 delivery-area">
            <div class="input-group delivery-time">
                <label for="time_delivery"><?php _e('Time of delivery (Day)', 'enginethemes');?></label>
                <input type="number" name="time_delivery" value="" class="input-item time-delivery" min="0">
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 category-area">
            <div class="input-group">
                <label for="mjob_category"><?php _e('Category', 'enginethemes');?></label>
                <?php ae_tax_dropdown('mjob_category',
	array('attr' => 'data-chosen-width="100%" data-chosen-disable-search=""  data-placeholder="' . __("Choose categories", 'enginethemes') . '"',
		'class' => 'chosen chosen-single tax-item required',
		'hide_empty' => false,
		'hierarchical' => true,
		'id' => 'mjob_category',
		'show_option_all' => false,
	)
);?>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="mb-20"><?php _e('Description', 'enginethemes')?></label>
        <?php wp_editor('', 'post_content', ae_editor_settings());?>
    </div>
    <div class="form-group group-attachment gallery_container" id="gallery_container">
        <label class="mb-20"><?php _e('Gallery', 'enginethemes');?></label>
        <div class="outer-carousel-gallery">
            <div class="img-avatar carousel-gallery">
                <img width="100%" src="<?php echo TEMPLATEURL ?>/assets/img/image-avatar.jpg" alt="">
                <input type="hidden" class="input-item" name="et_carousels" value="" />
            </div>
        </div>
        <div class="attachment-image has-image clearfix">
            <ul class="carousel-image-list image-list" id="image-list">

            </ul>

            <span class="image-upload carousel_container" id="carousel_container">
                <span for="file-input" class="carousel_browse_button" id="carousel_browse_button">
                    <a class="add-img"><img src="<?php echo get_template_directory_uri() ?>/assets/img/icon-plus.png" alt=""></a>
                </span>
            </span>

            <span class="et_ajaxnonce" id="<?php echo wp_create_nonce('ad_carousels_et_uploader'); ?>"></span>
        </div>
    </div>
    <?php do_action('edit_input_address_field');?>
	<div class="form-group clearfix">
		<label><?php _e('Video', 'enginethemes');?></label>
		<input type="text" class="input-item form-control text-field" id="video_meta" placeholder="<?php _e("Add link from Youtube, Vimeo or .MP4 ", 'enginethemes');?>" name="video_meta"  autocomplete="off" spellcheck="false" >
		<ul class="skills-list" id="skills_list"></ul>
	</div>
    <div class="form-group clearfix">
        <label class="mb-20"><?php _e('Extra services', 'enginethemes');?></label>
        <div class="mjob-extras-wrapper">
        </div>
        <div class="add-more">
            <a href="#" class="mjob-add-extra-btn"><?php _e('Add extra', 'enginethemes');?><span class="icon-plus"><i class="fa fa-plus"></i></span></a>
        </div>
    </div>
    <div class="form-group clearfix skill-control">
        <label><?php _e('Tags', 'enginethemes');?></label>
        <?php
$switch_skill = ae_get_option('switch_skill');
if (!$switch_skill) {
    if( wp_is_mobile () ){ ?>
                <textarea class="form-control text-field skill et-suggest-skill" id="skill"  placeholder ="<?php _e('Enter microjob tags','enginethemes');?>"></textarea>
                <span class="add-skill"><i class="fa fa-sign-in"></i></span>
                <ul class="skills-list" id="skills_list"></ul>
    <?php } else { ?>
            <input type="text" class="form-control text-field skill et-suggest-skill" id="skill" placeholder="<?php _e("Enter microjob tags", 'enginethemes');?>" name=""  autocomplete="off" spellcheck="false" >
            <ul class="skills-list" id="skills_list"></ul>
            <?php
    }
} else {
	ae_tax_dropdown('skill', array('attr' => 'data-chosen-width="100%" data-chosen-disable-search="" multiple data-placeholder="' . __(" Skills (max is 5)", 'enginethemes') . '"',
		'class' => 'sw_skill chosen multi-tax-item tax-item required',
		'hide_empty' => false,
		'hierarchical' => true,
		'id' => 'skill',
		'show_option_all' => false,
	)
	);
}

?>
    </div>
    <script type="text/template" id="openingMessageTemplate">
        <div class="box-shadow opening-message">
            <div class="aside-title">
                <?php _e('Opening Message', 'enginethemes')?> <i class="fa fa-question-circle popover-opening-message" style="cursor: pointer" aria-hidden="true"></i>
            </div>
            <div class="content">
                <div class="content-opening-message">

                </div>
                <a class="show-opening-message"></a>
            </div>
        </div>
    </script>
    <div class="form-group skill-control">
        <label for="opening_message"><?php _e('Opening message', 'enginethemes')?> <i class="fa fa-question-circle popover-opening-message" aria-hidden="true"></i></label>
            <p class="note-message">
                <?php _e('Opening message is automatically displayed as your first message in the order detail page.', 'enginethemes');?>
            </p>
        <textarea name="opening_message" class="input-item"></textarea>
    </div>
    <div class="form-group">
        <button class="<?php mje_button_classes(array('btn-save'));?>" type="submit"><?php _e('SAVE', 'enginethemes');?></button>
        <a href=="#" class="btn-discard mjob-discard-action"><?php _e('DISCARD', 'enginethemes');?></a>
        <input type="hidden" class="input-item post-service_nonce" name="_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync'); ?>" />
    </div>
</form>