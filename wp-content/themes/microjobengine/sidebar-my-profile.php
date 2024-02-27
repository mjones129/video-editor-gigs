<?php
global $wp_query, $ae_post_factory, $post, $current_user, $user_ID;
$user = mJobUser::getInstance();
$user_data = $user->convert($current_user->data);
$user_role = ae_user_role($current_user->ID);

// Convert profile
$profile_obj = $ae_post_factory->get('mjob_profile');
$profile_id = get_user_meta($user_ID, 'user_profile_id', true);
if($profile_id) {
    $profile_post = get_post($profile_id);
    if($profile_post && !is_wp_error($profile_post)) {
        $profile = $profile_obj->convert($profile_post);
    }
}
$timezone_local = $user_data->timezone_local_edit;

$country_id = isset($profile->tax_input['country'][0]) ? $profile->tax_input['country'][0]->term_id : '';
$languages = isset($profile->tax_input['language']) ? $profile->tax_input['language'] : '';
$display_name = isset($user_data->display_name) ? $user_data->display_name : '';
?>
<?php if(is_page_template('page-profile.php')) : ?>
<div class="personal-profile box-shadow">
        <div class="edit-profile">
            <div class="float-center profile-avatar">
                <div class="upload-profile-avatar">
                    <div class="back-top-hover"><i class="fa fa-upload"></i></div>
                     <a href="#" class="">
                        <?php
                        echo mje_avatar($user_ID, 75);
                        ?>
                    </a>
                </div>
            </div>
            <h4 class="float-center">
                <div id="display_name">
                    <div class="text-content" data-edit="user" data-id="#display_name" data-name="display_name" data-type="input"><?php echo $display_name; ?></div>
                </div>

                <div class="user-email">
                    <p><?php echo $user_data->user_email; ?></p>
                </div>
            </h4>
            <div class="line">
                <span class="line-distance"></span>
            </div>
            <ul>
                <li class="location clearfix">
                    <span><i class="fa fa-map-marker"></i><?php _e('From', 'enginethemes'); ?></span>
                    <div class="chosen-location">
                        <?php
                        // Show countries
                        ae_tax_dropdown('country', array(
                            'id' => 'country',
                            'class' => 'chosen-single is-chosen',
                            'hide_empty' => false,
                            'show_option_all' => __('Select your country', 'enginethemes'),
                            'selected' => $country_id,
                            'hierarchical' => true
                        ));
                        ?>
                    </div>
                </li>

                <li class="language clearfix">
                    <span><i class="fa fa-globe"></i><?php _e('Languages', 'enginethemes'); ?></span>
                    <div class="choose-language">
                        <?php
                        // Show languages
                        $temp_languages = array();
                        if(!empty($languages)) {
                            foreach($languages as $language) {
                                $temp_languages[] = $language->term_id;
                            }
                        }

                        ae_tax_dropdown( 'language' , array(
                            'attr' => 'multiple data-placeholder="'.__("Add your languages", 'enginethemes').'"',
                            'class' => 'multi-tax-item is-chosen',
                            'hide_empty' => false,
                            'hierarchical' => true ,
                            'id' => 'language' ,
                            'show_option_all' => false,
                            'selected' =>$temp_languages
                        ));
                        ?>
                    </div>
                </li>

                <?php if( ae_get_option( 'user_local_timezone' ) ) : ?>
                <li class="timezone clearfix">
                    <span><i class="fa fa-clock-o" aria-hidden="true"></i><?php _e('Time zone', 'enginethemes'); ?></span>
                    <div class="choose-timezone">
                        <select id="timezone_local" name="timezone_local" class="chosen-single is-chosen">
                            <?php echo wp_timezone_choice($timezone_local); ?>
                        </select>
                    </div>
                </li>
                <?php endif; ?>
            </ul>
        </div>
</div>
<?php endif; ?>
<div class="block-statistic">
    <div class="dropdown">
        <button class="button-dropdown-menu" id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?php _e('Select page', 'enginethemes') ?>
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu">
            <li class="hvr-wobble-horizontal"><a href="<?php echo et_get_page_link('revenues'); ?>"><?php _e('Revenues', 'enginethemes'); ?></a></li>
            <li class="hvr-wobble-horizontal"><a href="<?php echo et_get_page_link('dashboard') . '#analytics' ?>" class="statistic-order"><?php _e('Order Statistics', 'enginethemes'); ?></a></li>
			<?php
			 /**
			  * Add new item in left sidebar menu after Dashboard
			  *
			  * @since 1.3.1
			  * @author Tan Hoai
			  */
			 do_action('mje_before_user_sidebar_menu');
			?>
            <li class="hvr-wobble-horizontal"><a href="<?php echo et_get_page_link('my-list-order'); ?>"><?php _e('My orders & tasks', 'enginethemes'); ?></a></li>
            <li class="hvr-wobble-horizontal"><a href="<?php echo et_get_page_link('my-listing-jobs'); ?>"><?php _e('My jobs', 'enginethemes'); ?></a></li>
            <li class="hvr-wobble-horizontal"><a href="<?php echo et_get_page_link('my-invoices'); ?>"><?php _e('My Invoices', 'enginethemes'); ?></a></li>
			<?php
			 /**
			  * Add new item in left sidebar menu before line distance
			  *
			  * @since 1.3.1
			  * @author Tan Hoai
			  */
			 do_action('mje_after_user_sidebar_menu');
			?>
            <li class="line-distance"></li>
			<?php
			 /**
			  * Add new item in left sidebar menu before line distance
			  *
			  * @since 1.3.4
			  * @author Tan Hoai
			  */
			 do_action('mje_before_footer_user_sidebar_menu');
			?>
            <li class="hvr-wobble-horizontal"><a href="<?php echo et_get_page_link('settings') ?>"><?php _e('Subscriber Settings', 'enginethemes'); ?></a></li>
            <li class="hvr-wobble-horizontal"><a href="<?php echo et_get_page_link('payment-method') ?>"><?php _e('Payment method', 'enginethemes'); ?></a></li>
            <li class="hvr-wobble-horizontal"><a href="<?php echo et_get_page_link('change-password') ?>"><?php _e('Change password', 'enginethemes'); ?></a></li>



			<?php
			 /**
			  * Add new item in left sidebar menu before line distance
			  *
			  * @since 1.3.4
			  * @author Tan Hoai
			  */
			 do_action('mje_after_footer_user_sidebar_menu');
			?>
        </ul>
    </div>
</div>