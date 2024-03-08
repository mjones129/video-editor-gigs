<?php
/**
 * Template Name: Page Profile
 */
global $wp_query, $ae_post_factory, $post, $current_user, $user_ID;
$user = mJobUser::getInstance();
$user_data = $user->convert($current_user->data);
$user_role = ae_user_role($current_user->ID);

// Convert profile
$profile_obj = $ae_post_factory->get('mjob_profile');
// Get user profile id
$profile_id = get_user_meta($user_ID, 'user_profile_id', true);

// If user profile id is valid
if($profile_id) {
    // Get profile
    $post = get_post($profile_id);
    // If profile is valid
    if($post && !is_wp_error($post)) {
        // Check if user has profile or not
        if($post->post_author == $user_ID && $post->post_type == 'mjob_profile') {
            $profile = $profile_obj->convert($post);
        } else {
            // Create a new profile, if user has not profile,
            $profile_id = wp_insert_post(array(
                'post_type' => 'mjob_profile',
                'post_status' => 'publish',
                'post_title' => $user_data->display_name,
                'post_author' => $user_data->ID
            ));
            update_user_meta($user_data->ID, 'user_profile_id', $profile_id);
            $profile = $profile_obj->convert(get_post($profile_id));
        }
    }
    echo '<script type="text/json" id="mjob_profile_data" >'.json_encode($profile).'</script>';
}
// Get profile infomation
$description = !empty($profile->profile_description) ? $profile->profile_description : __('There is no content', 'enginethemes');
$payment_info = !empty($profile->payment_info) ? $profile->payment_info : __('There is no content', 'enginethemes');
$billing_full_name = !empty($profile->billing_full_name) ? $profile->billing_full_name : __('There is no content', 'enginethemes');
$billing_full_address = !empty($profile->billing_full_address) ? $profile->billing_full_address : __('There is no content', 'enginethemes');
$billing_country = !empty($profile->billing_country) ? $profile->billing_country : '';
$billing_vat = !empty($profile->billing_vat) ? $profile->billing_vat : __('There is no content', 'enginethemes');
get_header();
?>

<?php if(!mje_is_user_active($user_ID)): ?>
    <div class="active-account">
        <p><?php _e('Your account is not activated yet! Lost the activation link?', 'enginethemes'); ?> <a href="" class="resend-email-confirm"><?php _e('Resend it.', 'enginethemes'); ?></a></p>
    </div>
<?php endif; ?>
<div id="content">
    <div class="container mjob-profile-page">
        <div class="row title-top-pages">
            <div class="col-xs-12">
                <p class="block-title"><?php _e('MY PROFILE', 'enginethemes'); ?></p>
                <p><a href="<?php echo et_get_page_link('dashboard'); ?>" class="btn-back"><i class="fa fa-angle-left"></i><?php _e('Back to dashboard', 'enginethemes'); ?></a></p>    
            </div>
        </div>
        <div class="row profile">
            <div class="col-lg-4 col-md-4 col-sm-12 col-sx-12 block-items-detail profile">
                <?php get_sidebar('my-profile'); ?>
            </div>

            <div class="col-lg-8 col-md-8 col-sm-12 col-sx-12">
                <div class="box-shadow block-profile">
                    <div class="status-customer float-right" style="display: none">
                        <select name="user_status" id="user_status" data-edit="user" class="user-status">
                            <?php if($user_data->user_status == 'online') { ?>
                                <option value="online" selected><?php _e('Online', 'enginethemes'); ?></option>
                                <option value="offline"><?php _e('Offline', 'enginethemes'); ?></option>
                            <?php } else { ?>
                                <option value="online"><?php _e('Online', 'enginethemes'); ?></option>
                                <option value="offline" selected><?php _e('Offline', 'enginethemes'); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="block-intro">
                        <p class="title"><?php _e('DESCRIPTION', 'enginethemes'); ?></p>
                        <div class="vote">
                            <div class="rate-it star" data-score="<?php echo mje_get_total_reviews_by_user($user_ID); ?>"></div>
                        </div>
                        <div id="post_content" class="text-content-wrapper text-content">
                            <div>
                                <textarea class="editable" name="profile_description"><?php echo strip_tags($description); ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="block-payment">
                        <p class="title"><?php _e('PAYMENT INFO', 'enginethemes'); ?></p>
                        <div id="payment_info" class="text-content-wrapper text-content">
                            <div>
                                <textarea class="editable" name="payment_info"><?php echo $payment_info; ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="block-billing">
                        <p class="title"><?php _e('BILLING INFO', 'enginethemes'); ?></p>
                        <ul>
                            <li>
                                <div class="cate-title"><?php _e('Business full name', 'enginethemes'); ?></div>
                                <div id="billing_full_name" class="info-content">
                                    <div class="text-content" data-type="input" data-name="billing_full_name" data-id="#billing_full_name"><p><?php echo $billing_full_name; ?></p></div>
                                </div>
                            </li>
                            <li>
                                <div class="cate-title full-address"><?php _e('Full Address', 'enginethemes'); ?></div>
                                <div id="billing_full_address" class="info-content text-content text-address">
                                    <textarea class="editable" name="billing_full_address"><?php echo $billing_full_address; ?></textarea>
                                </div>
                            </li>
                            <li>
                                <div class="cate-title"><?php _e('Country', 'enginethemes'); ?></div>
                                <div id="billing_country" class="info-content">
                                    <?php
                                    ae_tax_dropdown('country', array(
                                        'id' => 'billing_country',
                                        'name' => 'billing_country',
                                        'class' => 'chosen-single is-chosen',
                                        'hide_empty' => false,
                                        'show_option_all' => __('Select your country', 'enginethemes'),
                                        'selected' => (int) $billing_country,
                                    ));
                                    ?>
                                </div>
                            </li>
                            <li>
                                <div class="cate-title"><?php _e('VAT or Tax Number', 'enginethemes'); ?></div>
                                <div id="billing_vat" class="info-content">
                                    <div class="text-content" data-type="input" data-name="billing_vat" data-id="#billing_vat"><p><?php echo $billing_vat; ?></p></div>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="block-connect-social">
                        <p class="title"><?php _e('CONNECT TO SOCIALS', 'enginethemes'); ?></p>
                        <?php
                        ae_render_connect_social_button();
                        ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <input type="hidden" class="input-item" name="_wpnonce" id="profile_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
</div>
<?php
get_footer();
?>