<?php

function is_order_v2($payment_type){
    // v2 => không verify order tại page-process-payment.
    // order tư hook init và tự verify nếu có response từ server.
    // v2=>k cần lưu lại order_id trong hàm et_write_session.
    if( in_array( $payment_type, array('bitcoin','bitcoincash') ) )
        return true;
    return false;

}
function et_log($input, $file_store = ''){
    if(! ET_DEBUG ) return ;
    if(empty($file_store)){
        $file_store = WP_CONTENT_DIR.'/et_log.css';
    }

    if( is_array( $input ) || is_object( $input ) ){
        error_log( print_r($input, TRUE), 3, $file_store );
    } else {
        error_log( $input . "\n" , 3, $file_store);
    }
}

function et_track_payment($input){
    if( defined('TRACK_PAYMENT')  && TRACK_PAYMENT ) {
        $file_store = WP_CONTENT_DIR.'/et_track_payment.log';


        if( is_array( $input ) || is_object( $input ) ){
            error_log( date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) ). ': '. print_r($input, TRUE), 3, $file_store );
            // fwrite($h,  var_export($input, true) );
            // fwrite($h,". \n");
        } else {
           error_log( date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) ). ': '. $input . "\n" , 3, $file_store);
        }

    }
}
/**
 * check setting of user to know current user is subscriber to receiver email or not.
 * @since: 1.3.7.2
 * @author: danng
*/
function et_get_subscriber_settings($user_id = 0){

    if( ! $user_id ){
        global $user_ID;
        $user_id = $user_ID;
    }

    $et_subscriber =  get_user_meta($user_id,'et_subscriber', true);

    if( $et_subscriber == '2' || $et_subscriber === 2 )
        return false;

    return true;

}
/**
 * detect user is allow to receive email or not.
*/
function mje_is_subscriber($user_id = 0 ){
    return et_get_subscriber_settings($user_id);
}

function mje_loop_item_css($convert){
	$default = 'col-lg-4 col-md-4 col-sm-6 col-xs-6 col-mobile-12 item_js_handle mjob-item-{$convert->ID} ';
	$class= apply_filters('mje_loop_item_css', $default, $convert);
	return $class;
}
function mje_home_loop_item_css($convert){
	$default = "col-lg-3 col-md-3 col-sm-6 col-mobile-12 mjob-item-{$convert->ID} ";
	$class= apply_filters('mje_home_loop_item_css', $default, $convert);
	return $class;
}
/**
 * use this function for post mjob page only.
 * To make the theme compatible with Mjob Featured.
*/
function disable_plan_post_mjob(){
    return ae_get_option('disable_plan', false);
}

if( ! function_exists('has_mje_featured') ) {
    function has_mje_featured(){
        if( function_exists('mje_featured_loaded'))
            return true;
        return false;
    }
}

function has_mje_discount(){
    if( class_exists('Mje_Discount'))
        return true;
    return false;
}
function mje_update_time_used_discount($code){
    global $wpdb;
    $post = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->posts WHERE post_title = %s AND post_type = 'mje_coupon'", $code) );
    if ( null !== $post ) {

        $id = $post->ID;
        // et_log('Discount Post ID: '. $id);
        // et_log('Discount code: '.$code);
        $time_used = (int) get_post_meta($id,'time_used_discount', true);
        $update_time = $time_used + 1;
        update_post_meta($id, 'time_used_discount', $update_time);
    }
}
function get_mje_copyright(){
    $site_copyright = get_theme_mod('site_copyright');
    $copyright = '<span class="site-copyright">' . $site_copyright . '</span>';
    if( empty($site_copyright) ){
        $copyright =  '<span class="enginethemes">Powered by <a href="https://www.enginethemes.com/themes/microjobengine">MicrojobEngine Theme</a></span>';
    }

    return $copyright;
}
 /**
 * add target=_blank to a link on post_content of mJob
 * @since 1.3.9.3 v1.3.9.3
*/
function mje_all_link_blank( $content ){
    if( is_singular(MJOB)  || is_singular('recruit') ){
        $content = str_replace( '<a ', '<a target="blank" ', $content );
    }
    return $content;
}
add_filter('the_content','mje_all_link_blank');

function mje_search_form($heading_title, $sub_title){
    $skin_name = MJE_Skin_Action::get_skin_name();
    if($skin_name !== 'diplomat'){
        mje_search_form_default($heading_title, $sub_title);
    } else {
        mje_search_form_diplomat($heading_title, $sub_title);
    }
}
function mje_search_form_default($heading_title ='', $sub_title = ''){
    ?>
    <div class="search-form">
        <h1 class="wow fadeInDown"><?php echo $heading_title; ?></h1>
        <h4 class="wow fadeInDown"><?php echo $sub_title; ?></h4>
        <form action="<?php echo get_site_url(); ?>" class="form-search line-153">
            <div class="outer-form">
                <span class="text"><?php _e('I am looking for', 'enginethemes');?></span>
                <input type="text" name="s" class="text-search-home" placeholder="<?php _e('a logo design', 'enginethemes');?>">
                <button class="btn-search hvr-buzz-out waves-effect waves-light">
                    <div class="search-title">
                        <span class="text-search"><?php _e('SEARCH NOW', 'enginethemes');?></span>
                    </div>
                </button>
            </div>
        </form>
    </div>
<?php }
function mje_search_form_diplomat($heading_title= '', $sub_title = ''){ ?>
    <div class="search-form">
        <h1><?php echo $heading_title; ?></h1>
        <h4><?php echo $sub_title; ?></h4>
        <form class="form-search line-163">
            <div class="outer-form-search">
                <span class="text"><?php _e('I am looking for', 'enginethemes');?></span>
                <input type="text" name="s" class="text-search-home" placeholder="<?php _e('a logo design', 'enginethemes');?>">
                <button class="btn-diplomat btn-find btn- waves-effect waves-light"><div class="search-title"><span class="text-search"><?php _e('Search now', 'enginethemes');?></span></div></button>
            </div>
        </form>
    </div>

<?php }

function mje_diplomat_slider_block(){

    $skin_assets_path   = MJE_Skin_Action::get_skin_assets_path();
    $has_geo_ext        = apply_filters('has_geo_extension','');
    // Get heading title and sub title
    $heading_title  = get_theme_mod('home_heading_title') ? get_theme_mod('home_heading_title') : __('Get your stuffs done from $5', 'enginethemes');
    $sub_title      = get_theme_mod('home_sub_title') ? get_theme_mod('home_sub_title') : __('Browse through millions of micro jobs. Choose one you trust. Pay as you go.', 'enginethemes'); ?>
    <!--SECTION SLIDER-->
    <div class="block-slider <?php echo $has_geo_ext;?> mje-widget-search-form ">
        <div class="slideshow">
            <!--CONTENT SLIDER-->
            <?php
            $slide_images = array();
            if (get_theme_mod('mje_diplomat_slide_custom')) {
                // Use custom slide image
                for ($i = 1; $i <= 5; $i++) {
                    $image = wp_get_attachment_image_src(get_theme_mod("mje_diplomat_slide_{$i}"), array(1920, 548));
                    if ($image) {
                        $slide_images[] = $image[0];
                    }
                }
            } else {
                // Use default slide image
                for ($i = 1; $i <= 5; $i++) {
                    $slide_images[] = $skin_assets_path . '/img/img-slider-' . $i . '.jpg';
                }
            } ?>
            <div class="slider-wrapper default">
                <div id="slider">
                    <?php
                    foreach ($slide_images as $image) {
                        if (!empty($image)) {
                            echo '<img src="' . $image . '" alt="slide_image" />';
                        }
                    } ?>
                </div>
            </div>
        </div>
          <?php
            if ( ! is_acti_mje_geo() ){
                mje_search_form_diplomat($heading_title, $sub_title);
            } else {
             do_action('mje_geo_search_form', $heading_title, $sub_title);
            } ?>

        <div class="statistic-job-number">
            <p class="link-last-job"><?php printf(__('There are %s microjobs more ', 'enginethemes'), mje_get_mjob_count());?></p><div class="bounce"><i class="fa fa-angle-down"></i></div><p></p>
        </div>
    </div>
<?php }
function disbable_btn_action_if_admin_view($classes){
    if( is_singular('ae_message') && current_user_can('manage_options') ){
        global $post;

        global $post, $ae_post_factory, $user_ID;
        $post_object    = $ae_post_factory->get('ae_message');
        $current        = $post_object->convert($post);

        $from_user      = $current->from_user;
        $to_user        = $current->to_user;
        if( !in_array($user_ID, array($from_user, $to_user))){
            $classes[] = 'view_only';
        }

    }
    return $classes;
}

add_filter('body_class','disbable_btn_action_if_admin_view');