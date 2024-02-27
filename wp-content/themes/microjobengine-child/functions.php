<?php

function mcj_diplomat_slider_block(){

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
            mcj_search_form_diplomat($heading_title, $sub_title);
        } else {
         do_action('mje_geo_search_form', $heading_title, $sub_title);
        } ?>

    <div class="statistic-job-number">
        <p class="link-last-job"><?php printf(__('There are %s more services ', 'enginethemes'), mje_get_mjob_count());?></p><div class="bounce"><i class="fa fa-angle-down"></i></div><p></p>
    </div>
</div>
<?php }

function mcj_show_search_form() {?>
    <form action="<?php echo get_site_url(); ?>" class="et-form">
    <?php
        if (isset($_COOKIE['mjob_search_keyword'])) {

            $keyword = $_COOKIE['mjob_search_keyword'];
        } else {
            $keyword = '';
        }
        $place_holder  = __('Search Services','enginethemes');
        ?>
        <span class="icon-search"><i class="fa fa-search"></i></span>
        <?php if (is_singular('mjob_post')): ?>
            <input type="text" name="s" id="input-search" placeholder="<?php echo $place_holder;?>" value="<?php echo $keyword; ?>">
        <?php elseif (is_search()): ?>
            <input type="text" name="s" id="input-search" placeholder="<?php echo $place_holder;?>"  value="<?php echo get_query_var('s'); ?>">
        <?php else: ?>
            <input type="text" name="s" placeholder="<?php echo $place_holder;?>"  id="input-search">
        <?php endif;?>
    </form>
    <?php
}

if (!function_exists('mcj_show_user_header')) {
	/**
	 * Show user section on main navigation
	 * @param void
	 * @return void
	 * @since 1.0
	 * @package Microjobengine
	 * @category File Functions
	 * @author Tat Thien
	 */
	function mcj_show_user_header() {
		global $current_user;
		$conversation_unread = mje_get_unread_conversation_count();
		// Check empty current user
		if (!empty($current_user->ID)) {
			?>
            <div class="notification-icon list-message et-dropdown">
                <span id="show-notifications" class="link-message">
                    <?php echo mje_is_has_unread_notification() ? '<span class="alert-sign">' . mje_get_unread_notification_count() . '</span>' : ''; ?>
                    <i class="fa fa-bell"></i>
                </span>
            </div>

            <div class="message-icon list-message dropdown et-dropdown">
                <div class="dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown">
                    <span class="link-message">
                         <?php
if ($conversation_unread > 0) {
				echo '<span class="alert-sign">' . $conversation_unread . '</span>';
			}
			?>
                        <i class="fa fa-comment"></i>
                    </span>
                </div>
                <div class="list-message-box dropdown-menu" aria-labelledby="dLabel">
                    <div class="list-message-box-header">
                        <span>
                            <?php
printf(__('<span class="unread-message-count">%s</span> New', 'enginethemes'), $conversation_unread);
			?>
                        </span>
                        <a href="#" class="mark-as-read"><?php _e('Mark all as read', 'enginethemes');?></a>
                    </div>

                    <ul class="list-message-box-body">
                        <?php
mje_get_user_dropdown_conversation();
			?>
                    </ul>

                    <div class="list-message-box-footer">
                        <a href="<?php echo et_get_page_link('my-list-messages'); ?>"><?php _e('View all', 'enginethemes');?></a>
                    </div>
                </div>
            </div>

            <!--<div class="list-notification">
                <span class="link-notification"><i class="fa fa-bell"></i></span>
            </div>-->
            <?php
$absolute_url = mje_get_full_url($_SERVER);
			if ( is_mje_submit_page() ) {
				$post_link = '#';
			} else {
				$post_link = et_get_page_link('post-service') . '?return_url=' . $absolute_url;
			}
			?>
            <div class="link-post-services">
                <a href="<?php echo $post_link; ?>"><?php _e('Post Your Service', 'enginethemes');?>
                    <div class="plus-circle"><i class="fa fa-plus"></i></div>
                </a>
            </div>
            <div class="user-account">
                <div class="dropdown user-account-dropdown et-dropdown">
                    <div class="dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown">
                        <span class="avatar">
                            <span class="display-avatar"><?php echo mje_avatar($current_user->ID, 35); ?></span>
                            <span class="display-name"><?php echo $current_user->display_name; ?></span>
                        </span>
                        <span><i class="fa fa-angle-right"></i></span>
                    </div>
                    <ul class="dropdown-menu et-dropdown-login" aria-labelledby="dLabel">
                        <li><a href="<?php echo et_get_page_link('dashboard'); ?>"><?php _e('Dashboard', 'enginethemes');?></a></li>
                        <?php
/**
			 * Add new item menu after Dashboard
			 *
			 * @since 1.3.1
			 * @author Tan Hoai
			 */
			do_action('mje_before_user_dropdown_menu');
			?>
						<li><a href="<?php echo et_get_page_link("profile"); ?>"><?php _e('My profile', 'enginethemes');?></a></li>
                        <li><a href="<?php echo et_get_page_link("my-list-order"); ?>"><?php _e('My orders', 'enginethemes');?></a></li>
                        <li><a href="<?php echo et_get_page_link("my-listing-jobs"); ?>"><?php _e('My services', 'enginethemes');?></a></li>
                        <li><a href="<?php echo et_get_page_link("my-invoices"); ?>"><?php _e('My invoices', 'enginethemes');?></a></li>
                        <li class="post-service-link"><a href="<?php echo et_get_page_link('post-service'); ?>"><?php _e('Post a Service', 'enginethemes');?>
                                <div class="plus-circle"><i class="fa fa-plus"></i></div>
                        </a></li>
                        <li class="get-message-link">
                            <a href="<?php echo et_get_page_link('my-list-messages'); ?>"><?php _e('Message', 'enginethemes');?></a>
                        </li>
						<?php
/**
			 * Add new item menu before Sign out
			 *
			 * @since 1.3.1
			 * @author Tan Hoai
			 */
			do_action('mje_after_user_dropdown_menu');
			?>
                        <li><a href="<?php echo wp_logout_url(home_url()); ?>"><?php _e('Sign out', 'enginethemes');?> </a></li>
                    </ul>
                    <div class="overlay-user"></div>
                </div>
            </div>
            <?php
}
	}
}

function mcj_search_form_diplomat($heading_title= '', $sub_title = ''){ ?>
    <div class="search-form">
        <h1><?php echo $heading_title; ?></h1>
        <h4><?php echo $sub_title; ?></h4>
        <form class="form-search line-163">
            <div class="outer-form-search">
                <span class="text"><?php _e('I am looking for', 'enginethemes');?></span>
                <input type="text" name="s" class="text-search-home" placeholder="<?php _e('a music video', 'enginethemes');?>">
                <button class="btn-diplomat btn-find btn- waves-effect waves-light"><div class="search-title"><span class="text-search"><?php _e('Search now', 'enginethemes');?></span></div></button>
            </div>
        </form>
    </div>
<?php }



function mcj_ae_render_social_button( $icon_classes = array(), $button_classes = array(), $before_text = '', $after_text = ''){
	/* check enable option*/
	$use_facebook = ae_get_option('facebook_login');
    $use_twitter = ae_get_option('twitter_login');
    $gplus_login = ae_get_option('gplus_login');
    $linkedin_login = ae_get_option('linkedin_login') ;
    if( $icon_classes == ''){
    	$icon_classes = 'fa fa-facebook-square';
    }
    $defaults_icon = array(
    	'fb' => 'fa fa-facebook',
    	'gplus' => 'fa fa-google',
    	'tw' => 'fa fa-twitter',
    	'lkin' => 'fa fa-linkedin'
    	);
	$icon_classes = wp_parse_args( $icon_classes, $defaults_icon );
	$icon_classes = apply_filters('ae_social_icon_classes', $icon_classes );
	$defaults_btn = array(
    	'fb' => '',
    	'gplus' => 'fa-brands fa-google',
    	'tw' => '',
    	'lkin' => ''
    	);
	$button_classes = wp_parse_args( $button_classes, $defaults_btn );
	$button_classes = apply_filters('ae_social_button_classes', $button_classes );
	if( $use_facebook || $use_twitter || $gplus_login || $linkedin_login ){
		if( $before_text != '' ){ ?>
			<div class="socials-head"><?php echo $before_text ?></div>
		<?php } ?>
		<ul class="list-social-login">
			<?php if($use_facebook){ ?>
	    	<li>
	    		<a href="#" class="fb facebook_auth_btn <?php echo $button_classes['fb']; ?>">
	    			<i class="<?php echo $icon_classes['fb']; ?>"></i>
	    			<span class="social-text"><?php _e("Facebook", 'enginethemes') ?></span>
	    		</a>
	    	</li>
	    	<?php } ?>
	    	<?php if($gplus_login){ ?>
	        <li>
	        	<a href="#" class="gplus gplus_login_btn <?php echo $button_classes['gplus']; ?>" >
	        		<img src="https://editorgigsdev.wpengine.com/wp-content/uploads/2022/04/btn_google_signin_dark_normal_web.png" alt="">
	        	</a>
	        </li>
	        <?php } ?>
	    	<?php if($use_twitter){ ?>
	        <li>
	        	<a href="<?php echo add_query_arg('action', 'twitterauth', home_url()) ?>" class="tw <?php echo $button_classes['tw']; ?>">
	        		<i class="<?php echo $icon_classes['tw']; ?>"></i>
	        		<span class="social-text"><?php _e("Twitter", 'enginethemes') ?></span>
	        	</a>
	        </li>
	        <?php } ?>
	        <?php if($linkedin_login){ ?>
			<li>
	    		<a href="#" class="lkin <?php echo $button_classes['tw']; ?>">
	    			<i class="<?php echo $icon_classes['lkin']; ?>"></i>
	    			<span class="social-text"><?php _e("Linkedin", 'enginethemes') ?></span>
	    		</a>
	    	</li>
			<?php } ?>
	    </ul>
	<?php
		if( $after_text != '' ){ ?>
			<div class="socials-footer"><?php echo $after_text ?></div>
		<?php }
	}
}
