<?php
if (!function_exists('mje_logo')) {
	function mje_logo($option_name = '', $echo = true) {
		$options = AE_Options::get_instance();
		// save this setting to theme options
		$site_logo = $options->$option_name;
		if (!empty($site_logo)) {
			$img = $site_logo['large'][0];
		} else {
			$img = TEMPLATEURL . '/assets/img/logo.png';
		}

		if ($echo == false) {
			return '<img alt="' . $options->blogname . '" src="' . $img . '" />';
		} else {
			echo '<img alt="' . $options->blogname . '" src="' . $img . '" />';
		}

	}
}

if (!function_exists('mje_avatar')) {
	/**
	 * Show user avatar
	 * @param int $userID
	 * @param int $size             avatar size
	 * @param array $params
	 * @return string $avatar       img tag
	 * @since 1.0
	 * @package MicrojobEngine
	 * @category File Functions
	 * @author Tat Thien
	 */
	function mje_avatar($userID, $size = 150, $params = array('class' => 'avatar', 'title' => '', 'alt' => '')) {
		extract($params);
		$avatar = get_user_meta($userID, 'et_avatar_url', true);
		if (!empty($avatar)) {
			$avatar = '<img src="' . $avatar . '" class="' . $class . '" alt="' . $alt . '" />';
		} else if (ae_get_option('default_avatar')) {
			$avatar = mje_logo('default_avatar', false);
		} else {
			$link = get_avatar($userID, $size);
			preg_match('/src=(\'|")(.+?)(\'|")/i', $link, $array);
			$sizes = get_intermediate_image_sizes();
			$avatar = array();
			foreach ($sizes as $size) {
				$avatar[$size] = $array[2];
			}
			$avatar = '<img src="' . $avatar['thumbnail'] . '" class="' . $class . '" alt="' . $alt . '" />';
		}
		return $avatar;
	}
}

if (!function_exists('mje_show_user_header')) {
	/**
	 * Show user section on main navigation
	 * @param void
	 * @return void
	 * @since 1.0
	 * @package Microjobengine
	 * @category File Functions
	 * @author Tat Thien
	 */
	function mje_show_user_header() {
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
                <a href="<?php echo $post_link; ?>"><?php _e('Post a mJob', 'enginethemes');?>
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
                        <li><a href="<?php echo et_get_page_link("my-listing-jobs"); ?>"><?php _e('My jobs', 'enginethemes');?></a></li>
                        <li><a href="<?php echo et_get_page_link("my-invoices"); ?>"><?php _e('My invoices', 'enginethemes');?></a></li>
                        <li class="post-service-link"><a href="<?php echo et_get_page_link('post-service'); ?>"><?php _e('Post a mJob', 'enginethemes');?>
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

if (!function_exists('mje_show_authentication_link')) {
	/**
	 * Show signup and signin link on main navigation
	 * @param void
	 * @return void
	 * @since 1.0
	 * @package Microjobengine
	 * @category File Functions
	 * @author Tat Thien
	 */
	function mje_show_authentication_link() {
		$sign_in_class = "signin-link open-signin-modal";
		$sign_up_class = "signup-link open-signup-modal";
		if (!is_page_template('page-sign-in.php') && !is_mje_submit_page() && !is_page_template('page-process-payment.php')) {
			?>
            <div class="link-account">
                <ul>
                    <li><a href="" class="<?php echo $sign_in_class; ?>"><?php _e('Signin', 'enginethemes');?></a></li>
                    <li><span><?php _e('or', 'enginethemes');?></span></li>
                    <li><a href="" class="<?php echo $sign_up_class; ?>"><?php _e('Join us', 'enginethemes');?></a></li>
                </ul>
            </div>
            <?php
}
	}
}

if (!function_exists('mje_get_price')) {
	function mje_get_price($price, $open_sign = '(', $close_sign = ')') {
		$currency_sign = ae_currency_sign(false);

		$options = AE_Options::get_instance();
		$align = $options->currency['align'];

		if ($align) {
			return $open_sign . $currency_sign . $close_sign . $price;
		} else {
			return $price . $open_sign . $currency_sign . $close_sign;
		}
	}
}

if (!function_exists('et_get_customization')) {
	/**
	 * @todo Tam thoi de ham nay de khong bi loi khi dung AE_Mailing
	 * Get and return customization values for
	 * @since 1.0
	 */
	function et_get_customization() {
		$style = get_option('ae_theme_customization', true);
		$style = wp_parse_args($style, array(
			'background' => '#ffffff',
			'header' => '#2980B9',
			'heading' => '#37393a',
			'text' => '#7b7b7b',
			'action_1' => '#8E44AD',
			'action_2' => '#3783C4',
			'project_color' => '#3783C4',
			'profile_color' => '#3783C4',
			'footer' => '#F4F6F5',
			'footer_bottom' => '#fff',
			'font-heading-name' => 'Raleway,sans-serif',
			'font-heading' => 'Raleway',
			'font-heading-size' => '15px',
			'font-heading-style' => 'normal',
			'font-heading-weight' => 'normal',
			'font-text-name' => 'Raleway, sans-serif',
			'font-text' => 'Raleway',
			'font-text-size' => '15px',
			'font-text-style' => 'normal',
			'font-text-weight' => 'normal',
			'font-action' => 'Open Sans, Arial, Helvetica, sans-serif',
			'font-action-size' => '15px',
			'font-action-style' => 'normal',
			'font-action-weight' => 'normal',
			'layout' => 'content-sidebar',
		));
		return $style;
	}
}

if (!function_exists('mje_format_price')) {
	/**
	 * Price format
	 *
	 * @param float $amount
	 * @param string $style
	 * @return string
	 * @since 1.0
	 * @package MicrojobEngine
	 * @category File Functions
	 * @author JACK BUI
	 */
	function mje_format_price($amount, $style = 'sup', $render_currency = true, $format_class = true) {
		$number_format_settings = mje_get_number_format_settings();
		extract($number_format_settings);

		$number_format = number_format((double) $amount, $decimal, $decimal_point, $thousand_sep);

		if (empty($number_format)) {
			$number_format = 0;
		}

		if ($render_currency) {
			// Render number with currency sign
			return mje_price_render_currency($number_format, $style, $format_class);
		} else {
			return $number_format;
		}

	}
}

if (!function_exists('mje_shorten_price')) {
	/**
	 * Shorten price to K, M, B, T
	 *
	 * @param float|string|int $number
	 * @param string $style
	 * @return string
	 * @since 1.1.4
	 * @author Tat Thien
	 */
	function mje_shorten_price($number, $style = 'sup') {
		$enable_shorten = ae_get_option('disable_long_price', 1);
		if (!$enable_shorten) {
			return mje_format_price($number, $style);
		}

		$divisors = array(
			'0' => '',
			'1' => 'K',
			'2' => 'M',
			'3' => 'B',
			'4' => 'T',
		);

		// Loop through eaach $divisor and find the lowest amount that match
		$match_divisor = 1;
		$match_shorthand = '';
		foreach ($divisors as $divisor => $shorthand) {
			$divisor = pow(1000, $divisor);
			if ($number < ($divisor * 1000)) {
				$match_divisor = $divisor;
				$match_shorthand = $shorthand;
				break;
			}
		}

		// Reverse decimal point and thousand separator for shorten number
		$shorten_number = mje_format_price((float) $number / $match_divisor, $style, false) . $match_shorthand;

		// Render number with currency sign
		$shorten_number = mje_price_render_currency($shorten_number);

		return '<span title="' . mje_format_price($number, '', true, false) . '">' . $shorten_number . '</span>';
	}
}

if (!function_exists('mje_clean_number')) {
	/**
	 * Trailing 0 before and after number
	 * @param float|string|int $number
	 * @return float|string|int $number
	 * @since 1.1.4
	 * @author Tat Thien
	 */
	function mje_clean_number($number, $decimal_point) {
		$number = ltrim($number, '0');
		$number = rtrim($number, $decimal_point);
		return $number;
	}
}

if (!function_exists('mje_get_number_format_settings')) {
	function mje_get_number_format_settings() {
		$number_format_settings = array();

		// Get number format;
		$number_format = ae_get_option('number_format');
		$decimal = (int) apply_filters('mje_decimal_number', 2);
		$disable_decimal =  ae_get_option('disable_decimal_price', 0);
		if($disable_decimal)
			$decimal = 0;
		$number_format_settings['decimal'] = $decimal;

		if ($number_format_settings['decimal'] < 0) {
			$number_format_settings['decimal'] = 0;
		}

		$number_format_settings['thousand_sep'] = ',';
		$number_format_settings['decimal_point'] = '.';

		// type_1 -> decimal point = "." thousand separator = ","
		// type_1 -> decimal point = "," thousand separator = "."
		if (isset($number_format['dec_thou']) && $number_format['dec_thou'] == 'type_2') {
			$number_format_settings['thousand_sep'] = '.';
			$number_format_settings['decimal_point'] = ',';
		}

		return $number_format_settings;
	}
}

if (!function_exists('mje_price_render_currency')) {
	/**
	 * Render a price with currency sign
	 *
	 * @param string $number
	 * @param string $style    currency styles
	 * @return string $number   with currency sign
	 * @since 1.1.4
	 * @author Tat Thien
	 */
	function mje_price_render_currency($number, $style = 'sup', $format_class = true) {
		$currency = ae_get_option('currency', array(
			'align' => 'left',
			'code' => 'USD',
			'icon' => '$',
		));
		$align = $currency['align'];
		$currency = $currency['icon'];

		switch ($style) {
		case 'sup':
			$format = '<sup>%s</sup>';
			break;
		case 'sub':
			$format = '<sub>%s</sub>';
			break;
		default:
			$format = '%s';
			break;
		}

		if ($align != "0") {
			$format = $format_class ? '<span class="mje-price">' . $format . '%s' . '</span>' : $format . '%s';
			return sprintf($format, $currency, $number);
		} else {
			$format = $format_class ? '<span class="mje-price">' . '%s' . $format . '</span>' : '%s' . $format;
			return sprintf($format, $number, $currency);
		}
	}
}

if (!function_exists('mje_format_number')) {
	/**
	 * number format
	 *
	 * @param float $amount
	 * @param boolean $echo
	 * @return string
	 * @since 1.0
	 * @package MicrojobEngine
	 * @category File Functions
	 * @author JACK BUI
	 */
	function mje_format_number($amount, $echo = true) {
		// Get number format;
		$number_format = ae_get_option('number_format');
		$decimal = (isset($number_format['et_decimal'])) ? $number_format['et_decimal'] : get_theme_mod('et_decimal', 2);
		$decimal_point = '.';
		$thousand_sep = ',';

		// type_1 -> decimal point = "." thousand separator = ","
		// type_1 -> decimal point = "," thousand separator = "."
		if (isset($number_format['dec_thou']) && $number_format['dec_thou'] == 'type_2') {
			$decimal_point = ',';
			$thousand_sep = '.';
		}

		$number_format = number_format((double) $amount, $decimal, $decimal_point, $thousand_sep);

		if ($echo) {
			echo $number_format;
		} else {
			return $number_format;
		}
	}
}

if (!function_exists('mje_show_filter_categories')) {
	/**
	 * Show categories filter on search result
	 * @param array $taxonomies
	 * @return void
	 * @since 1.0
	 * @package MicrojobEngine
	 * @category File Functions
	 * @author Tat Thien
	 */
	function mje_show_filter_categories($taxonomy = 'category', $args = array(), $current = "", $custom_filter = true) {
		$terms = get_terms($taxonomy, $args);
		$search_item = get_query_var('s');
		?>
        <div class="dropdown">
            <button class="button-dropdown-menu" id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?php _e('Categories','enginethemes');?>
                <span class="caret"></span>
            </button>
            <ul id="accordion" class="accordion <?php echo ($custom_filter) ? 'custom-filter-query' : '' ?> dropdown-menu" aria-labelledby="dLabel">
                <?php
if (!is_category() && !is_singular(array('post', 'page'))) {
			if (is_search()) {
				// render link all
				?>
                            <li>
                                <div class="link">
                                    <a href="<?php echo get_site_url() . "?s=&$taxonomy=0"; ?>" data-name="<?php echo $taxonomy; ?>" data-value="0" class="hvr-wobble-horizontal">
                                        <?php _e('All', 'enginethemes');?>
                                    </a>
                                </div>
                            </li>
                            <?php
} else {
				?>
                            <li>
                                <div class="link">
                                    <a href="<?php echo get_post_type_archive_link('mjob_post'); ?>" data-name="<?php echo $taxonomy; ?>" data-value="0" class="hvr-wobble-horizontal">
                                        <?php _e('All', 'enginethemes');?>
                                    </a>
                                </div>
                            </li>
                            <?php
}
		}
		foreach ($terms as $term) {
			// Get term link
			if (is_search()) {
				$term_link = get_site_url() . "?s=&$taxonomy=$term->term_id";
			} else {
				$term_link = get_term_link($term);
			}

			$current_term = get_term($current);
			?>
                        <li class="<?php echo (!is_wp_error($current_term) && $current_term->parent == $term->term_id) ? 'open active' : ''; ?>">
                            <?php
// Get child term
			$child_terms = get_terms($taxonomy, array('parent' => $term->term_id));
			?>
                            <div class="link">
                                <a href="<?php echo $term_link; ?>" data-name="<?php echo $taxonomy; ?>" data-value="<?php echo $term->term_id ?>" class="<?php echo ($current == $term->term_id) ? 'active' : ''; ?> hvr-wobble-horizontal"><?php echo $term->name; ?>


                                </a>
                                <?php
if (!empty($child_terms)):
				echo '<span class="show-accordion"><i class="fa fa-chevron-right"></i></span>';
			endif;
			?>
                            </div>

                            <?php if (!empty($child_terms)) {
				?>
                            <ul class="submenu">
                                <?php
foreach ($child_terms as $child) {
					// Get term link
					if (is_search()) {
						$term_link = get_site_url() . "?s=&$taxonomy=$child->term_id";
					} else {
						$term_link = get_term_link($child);
					}

					?>
                                    <li><a href="<?php echo $term_link; ?>" data-name="<?php echo $taxonomy; ?>" data-value="<?php echo $child->term_id; ?>" class="<?php echo ($current == $child->term_id) ? 'active' : ''; ?> hvr-wobble-horizontal"><?php echo $child->name; ?></a></li>
                                    <?php
}
				?>
                            </ul>
                            <?php }?>
                        </li>
                        <?php
}
		?>
            </ul>
        </div>
        <?php
}
}

if (!function_exists('mje_show_filter_tags')) {
	/**
	 * Show tags filter on search result
	 * @param array $taxonomies
	 * @return void
	 * @since 1.0
	 * @package MicrojobEngine
	 * @category File Functions
	 * @author Tat Thien
	 */
	function mje_show_filter_tags($taxonomies, $args = array('hide_empty' => true), $current_tag = "", $custom_filter = true) {
		$defaults = array(
			'hide_empty' => true,
		);
		$args = wp_parse_args($args, $defaults);
		$terms = get_terms($taxonomies, $args);
		echo '<div class="tags et-form">';
		if ($custom_filter) {
			echo '<select data-no_results_text1 ="zero chon 2"  name="skill" class="multi-tax-item"  data-placeholder="' . __('Filter by tag', 'enginethemes') . '" multiple >';
			foreach ($terms as $term) {
				if ($current_tag == $term->slug) {

					?>
                    <option value="<?php echo $term->term_id ?>" selected><?php echo $term->name; ?></option>
                    <?php
} else {
					?>
                    <option value="<?php echo $term->term_id ?>"><?php echo $term->name; ?></option>
                    <?php
}
			}
			echo '</select>';
		} else {
			foreach ($terms as $term) {
				?>
                <a href="<?php echo get_term_link($term); ?>"><?php echo $term->name; ?></a>
                <?php
}
		}
		echo "</div>";
	}
}

if (!function_exists('mje_list_tax_of_mjob')) {
	/**
	 * display html of list skill or category of project
	 * @param  int $id project id
	 * @param  string $title - title apperance in h3
	 * @param  string $slug taxonomy slug
	 * @return display list taxonomy of project.
	 */
	function mje_list_tax_of_mjob($id, $title = '', $taxonomy = 'mjob_category', $class = '') {
		$class = 'list-categories';
		if ($class = 'skill') {
			$class = 'list-skill';
		}

		$terms = get_the_terms($id, $taxonomy);?>

        <?php if (!empty($title)): ?>
            <h3 class="title">
                <?php printf($title);?>
            </h3>
        <?php endif;?>

        <?php

		if ($terms && !is_wp_error($terms)): ?>
		            <div class="list-require-skill-project list-taxonomires list-<?php
		echo $taxonomy; ?>">
		                <?php
		the_taxonomy_list($taxonomy, '<span class="skill-name-profile">', '</span>');?>
		            </div>
		            <?php
		endif;
	}
}
if (!function_exists('mje_extra_action')) {
	/**
	 * get instance of class mje_extra_action
	 *
	 * @param void
	 * @return object mje_extra_action
	 * @since 1.0
	 * @package MicrojobEngine
	 * @category void
	 * @author JACK BUI
	 */
	function mje_extra_action() {
		return MJE_Extra_Action::get_instance();
	}
}
if (!function_exists('mje_show_search_form')) {
	/**
	 * Show search form
	 * @param void
	 * @return void
	 * @since 1.0
	 * @package MicrojobEngine
	 * @category File Functions
	 * @author Tat Thien
	 */
	function mje_show_search_form() {?>
        <form action="<?php echo get_site_url(); ?>" class="et-form">
        <?php
			if (isset($_COOKIE['mjob_search_keyword'])) {

				$keyword = $_COOKIE['mjob_search_keyword'];
			} else {
				$keyword = '';
			}
			$place_holder  = __('Search mjob','enginethemes');
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
}
if (!function_exists('mje_mjob_action')) {
	/**
	 * get instance of class mje_extra_action
	 *
	 * @param void
	 * @return object mje_extra_action
	 * @since 1.0
	 * @package MicrojobEngine
	 * @category File Functions
	 * @author JACK BUI
	 */
	function mje_mjob_action() {
		return MJE_MJob_Action::get_instance();
	}
}
if (!function_exists('mje_mjob_order_action')) {
	/**
	 * get instance of class mje_mjob_order_action
	 *
	 * @param void
	 * @return object mje_extra_action
	 * @since 1.0
	 * @package MicrojobEngine
	 * @category File Functions
	 * @author JACK BUI
	 */
	function mje_mjob_order_action() {
		return MJE_MJob_Order_Action::get_instance();
	}
}
/**
 * get currency of this site
 *
 * @param void
 * @return array $currency
 * @since 1.0
 * @package MicrojobEngine
 * @category File Functions
 * @author JACK BUI
 */
function mje_get_currency() {
	$currency = ae_get_option('currency', array(
		'align' => 'left',
		'code' => 'USD',
		'icon' => '$',
	));

	return $currency;
}
/**
 * get temp user
 *
 * @param void
 * @return integer $temp_user
 * @since 1.0
 * @package MicrojobEngine
 * @category File Functions
 * @author JACK BUI
 */
function mje_get_temp_user_id() {
	return ae_get_option('mjob_temp_user_id', 1);
}

/**
 * Check user is active or not
 * @param int $user_id
 * @return boolean
 * @since 1.0
 * @package MicrojobEngine
 * @category File Functions
 * @author Tat Thien
 */
if (!function_exists('mJobUserActive')) {
	function mje_is_user_active($user_id) {
		return AE_Users::is_activate($user_id);
	}
}

/**
 * Check page template
 * @param string $slug
 * @return boolean
 * @since 1.0
 * @package MicrojobEngine
 * @category File Functions
 * @author Tat Thien
 */
function mje_is_page_template($slug) {
	$pageTemplate = get_page_template();
	$pageArray = explode("/", $pageTemplate);
	$pageTemplate = end($pageArray);
	if ($pageTemplate == $slug) {
		return true;
	} else {
		return false;
	}
}

/**
 * Render contact link
 * @param void
 * @return void
 * @since 1.0
 * @package MicrojobEngine
 * @category File Functions
 * @author Tat Thien
 */
function mje_show_contact_link($to_user) {
	global $user_ID;

	if (mje_is_has_conversation($user_ID, $to_user)) {
		?>
        <li><a href="<?php echo mje_get_conversation_link($user_ID, $to_user); ?>" class="contact-link"><?php _e('Contact me', 'enginethemes');?><i class="fa fa-comment"></i></a></li>
        <?php
} else if ($to_user != $user_ID) {
		?>
        <li><a href="" class="contact-link do-contact" data-touser= "<?php echo $to_user; ?>"><?php _e('Contact me', 'enginethemes');?><i class="fa fa-comment"></i></a></li>
        <?php
}

}
/**
 * price after commission
 *
 * @param float $price
 * @return float $price after subtract commission
 * @since 1.0
 * @package MicrojobEngine
 * @category void
 * @author JACK BUI
 */

function mje_get_price_after_commission($price) {
	$commission = ae_get_option('order_commission', 10);
	if ( $commission > 0) {
		$price = (float) $price;
		$price = $price - (float) $commission * $price / 100;
	}
	return $price;
}
function mje_get_commission_amount_for_buyer($subtotal) {

	$commission = ae_get_option('order_commission_buyer', 0);
	if ( $commission > 0) {
		return  (float) $commission * $subtotal / 100;
	}
	return 0;
}
/**
 * Apply discount code to subtotal and calculator again total.
 *
 * @since from version 1.3.7.4
*/

function mje_get_price_mjob_order_for_buyer($subtotal, $request){

	$subtotal = (float) $subtotal;
	$discount_amount = mje_get_discount_amount($subtotal, $request);
	$discount_amount = min( $subtotal, $discount_amount );
	$amount_after_discount = $subtotal - $discount_amount; //>=0;

	$commission_fee = mje_get_commission_amount_for_buyer($subtotal);
	$total = $amount_after_discount + $commission_fee;

	return apply_filters('mje_get_total_mjob_order', $total, $subtotal); //@since 1.3.7.5 apply extra fee extension on this hook
}
function mje_get_discount_amount($subtotal, $request){
	$discount_amount = 0;

	return apply_filters('mje_cal_discount_amount_of_subtotal', $discount_amount, $subtotal, $request );
}
function mje_get_price_after_commission_for_buyer($price) {

	$commission = (float) ae_get_option('order_commission_buyer', 0);
	if ($commission > 0) {
		$price = $price + $commission * $price / 100;
	}

	return $price;
}
function mje_get_extra_after_fee_for_buyer($fee, $price, $total) {
	$fee = $fee / 100;
	$extra = ($total - ($price * $fee + $price)) / ($fee + 1);
	return $extra;
}
function mje_get_fee_buy($price)
{
    $commission = ae_get_option('order_commission_buyer', 0);
    $fee = (float)$commission / 100;
    $total = (float)($price * $fee);
    return $total;
}
/**
 * Count all microjob
 *
 * @param void
 * @return int|string $count_posts
 * @since 1.0
 * @package MicrojobEngine
 * @category void
 * @author JACK BUI
 */
function mje_get_mjob_count() {
	global $wp_query;
	$count_posts = 0;
	$args = array(
		'post_type' => 'mjob_post',
		'post_status' => array('publish', 'unpause'),
	);
	$post = query_posts($args);
	$count_posts = $wp_query->found_posts;
	wp_reset_query();
	return $count_posts;
}

if (!function_exists('mje_get_mjob_order_count')) {
	/**
	 * Get mjob order count
	 * @param int $mjob_post_id
	 * @return int $count_posts
	 * @since 1.0
	 * @package MicrojobEngine
	 * @category File Functions
	 * @author Tat Thien
	 */
	function mje_get_mjob_order_count($mjob_post_id = '') {
		global $wp_query;
		$count_posts = 0;
		$args = array(
			'post_type' => 'mjob_order',
			'post_status' => array('publish', 'late', 'disputing', 'disputed', 'delivery', 'finished'),
			'posts_per_page' => -1,
		);

		if (!empty($mjob_post_id)) {
			$args = wp_parse_args(array('post_parent' => $mjob_post_id), $args);
		}

		query_posts($args);
		$count_posts = $wp_query->found_posts;
		wp_reset_query();
		return $count_posts;
	}
}

if (!function_exists('mje_get_total_reviews')) {
	/**
	 * Get review count
	 * @param int $mjob_post_id
	 * @return int $count
	 * @since 1.0
	 * @package MicrojobEngine
	 * @category File Functions
	 * @author Tat Thien
	 */
	function mje_get_total_reviews($mjob_post_id = '') {
		$count = 0;
		$args = array(
			'status' => 'approve',
			'comment_type' => 'mjob_review',
		);

		if (!empty($mjob_post_id)) {
			$args = wp_parse_args(array('post_id' => $mjob_post_id), $args);
		}

		$comments = get_comments($args);

		$count = count($comments);
		return $count;
	}
}

if (!function_exists('mje_get_total_reviews_by_user')) {
	function mje_get_total_reviews_by_user($user_id) {
		$posts = get_posts(array(
			'post_type' => 'mjob_post',
			'post_status' => array(
				'publish',
				'pause',
				'unpause',
			),
			'meta_query' => array(
				array(
					'key' => 'rating_score',
					'value' => 0,
					'compare' => '>',
				),
			),
			'posts_per_page' => -1,
			'author' => $user_id,
		));

		$count = 0;
		foreach ($posts as $post) {
			$rating_score = get_post_meta($post->ID, 'rating_score', true);
			$count += $rating_score;
		}

		if (count($posts) != 0) {
			return $count / count($posts);
		} else {
			return 0;
		}
	}
}

if (!function_exists('mje_shorten_number')) {
	/**
	 * Convert long number to K, M. Eg: 1000 -> 1K, 1000.000 -> 1M
	 * @param float $number
	 * @return string $number_format
	 * @since 1.0
	 * @package MicrojobEngine
	 * @category File Functions
	 * @author Tat Thien
	 */
	function mje_shorten_number($number) {
		$number_format = 0;

		if (!empty($number)) {
			if ($number < 1000) {
				// Anything less than 1 thousand
				$number_format = number_format($number);
			} else if ($number < 1000000) {
				// Anything less than 1 milion
				$number_format = number_format($number / 1000) . 'K';
			} else if ($number < 1000000000) {
				// Anything less than 1 billion
				$number_format = number_format($number / 1000000) . 'M';
			} else {
				$number_format = number_format($number / 1000000000) . 'B';
			}
		}

		return $number_format;
	}
}
function mje_get_url_origin($s, $use_forwarded_host = false) {
	$ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on');
	$sp = strtolower($s['SERVER_PROTOCOL']);
	$protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
	$port = $s['SERVER_PORT'];
	$port = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
	$host = ($use_forwarded_host && isset($s['HTTP_X_FORWARDED_HOST'])) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : null);
	$host = isset($host) ? $host : $s['SERVER_NAME'] . $port;
	return $protocol . '://' . $host;
}

function mje_get_full_url($s, $use_forwarded_host = false) {
	return urlencode(mje_get_url_origin($s, $use_forwarded_host) . $s['REQUEST_URI']);
}

if (!function_exists('mje_render_progress_bar')) {
	/**
	 * progress bar
	 *
	 * @param integer $type
	 * @param string $echo
	 * @return void
	 * @since 1.0
	 * @package MicrojobEngine
	 * @category void
	 * @author JACK BUI
	 */
	function mje_render_progress_bar($type = 3, $echo = '') {
		ob_start();
		if ($type == 4):
		?>
        <ul class="step-4-col">
            <li class="post-service-step-1 active" data-id="step1"><span class="link-step1"><?php _e('1', 'enginethemes');?></span></li>
            <li class="post-service-step-2" data-id="step2"><span class="link-step2"><?php _e('2', 'enginethemes');?></span></li>
            <li class="post-service-step-3" data-id="step-post"><span class="link-step3"><?php _e('3', 'enginethemes');?></span></li>
            <li class="post-service-step-4" data-id="step4"><span class="link-step4"><?php _e('4', 'enginethemes');?></span></li>
            <div class="progress-bar-success"></div>
        </ul>
<?php else: ?>
            <ul class="step-3-col">
                <li class="post-service-step-1 active" data-id="step1"><span class="link-step1"><?php _e('1', 'enginethemes');?></span></li>
                <li class="post-service-step-2" data-id="step-post"><span class="link-step2"><?php _e('2', 'enginethemes');?></span></li>
                <li class="post-service-step-3" data-id="step4"><span class="link-step3"><?php _e('3', 'enginethemes');?></span></li>
                <div class="progress-bar-success"></div>
            </ul>
<?php endif;
		$html = ob_get_clean();
		if ($echo):
			echo $html;
		else:
			return $html;
		endif;
	}
}

if (!function_exists('mje_get_range_of_date')) {
	/**
	 * Get date range
	 * @param $first      start date
	 * @param $end        end date
	 * @param @step
	 * @format            date format
	 * @return array $dates
	 * @since 1.0
	 * @package MicrojobEngine
	 * @category void
	 * @author Tat Thien
	 */
	function mje_get_range_of_date($first, $last, $step = '+1 day', $format = 'Y/m/d') {
		$timezone_opt = get_option('timezone_string');
		if( !empty($timezone_opt) ){
			date_default_timezone_set(get_option('timezone_string'));
		}

		$dates = array();
		$current = strtotime($first);
		$last = strtotime($last);
		while ($current <= $last) {
			$dates[] = date_i18n($format, $current);
			$current = strtotime($step, $current);
		}

		return $dates;
	}
}

if (!function_exists('mje_get_mjob_order_count_by_date')) {
	/**
	 * Get order by date
	 * @param $date
	 * @return $count_orders
	 * @since 1.0
	 * @package MicrojobEngine
	 * @category void
	 * @author Tat Thien
	 */
	function mje_get_mjob_order_count_by_date($date) {
		global $wpdb, $user_ID;
		$sql = "SELECT *
                FROM $wpdb->posts AS p
                INNER JOIN $wpdb->postmeta AS pm ON pm.post_id = p.ID
                WHERE p.post_type = 'mjob_order'
                  AND p.post_status != 'pending'
                  AND p.post_date LIKE '$date%'
                  AND p.post_author != $user_ID
                  AND pm.meta_key = 'seller_id'
                  AND pm.meta_value = $user_ID";
		$results = $wpdb->get_results($sql);
		$count_orders = count($results);
		return $count_orders;
	}
}

if (!function_exists('mje_get_mjob_order_chart')) {
	/**
	 * Get order data for chat
	 * @param void
	 * @return array $orders
	 * @since 1.0
	 * @package MicrojobEngine
	 * @category void
	 * @author Tat Thien
	 */
	function mje_get_mjob_order_chart() {
		$date_format = 'Y-m-d';
		$current_date = date($date_format, time());
		$last_week = date($date_format, strtotime('-1 week'));
		$dates = mje_get_range_of_date($last_week, $current_date, '+1 day', $date_format);
		$orders = array();
		foreach ($dates as $date) {
			$orders[] = mje_get_mjob_order_count_by_date($date);
		}

		return $orders;
	}
}

if (!function_exists('mje_add_facebook_sharing_image')) {
	function mje_add_facebook_sharing_image() {
		if (is_singular('mjob_post')) {
			global $post;

			$attachment_id = get_post_thumbnail_id($post->ID);
			if($attachment_id){
				$feature_image = wp_get_attachment_image_src($attachment_id, 'medium');
				$feature_image_url = $feature_image['0'];
			} else {
				$feature_image_url = get_template_directory_uri() . '/assets/img/mjob_thumbnail.png';
				if ( ae_get_option('default_mjob') ) {
					$default 			= ae_get_option('default_mjob');
					$defautl_thumb 		= $default['medium_post_thumbnail'];
					$feature_image_url 	= $defautl_thumb[0];
				}
			}
			echo '<meta property="og:image" content="' . $feature_image_url . '"/>';
		}
	}

	add_action('wp_head', 'mje_add_facebook_sharing_image');
}

/**
 * Convert hex to Rgba
 * @param string $color
 * @param boolean $opacity
 * @return string $output
 * @since 1.0.4
 * @package MicrojobEngine
 * @category void
 * @author Tat Thien
 */
function mje_convert_hex_to_rgb($color, $opacity = false) {

	$default = 'rgb(0,0,0)';

	//Return default if no color provided
	if (empty($color)) {
		return $default;
	}

	//Sanitize $color if "#" is provided
	if ($color[0] == '#') {
		$color = substr($color, 1);
	}

	//Check if color has 6 or 3 characters and get values
	if (strlen($color) == 6) {
		$hex = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
	} elseif (strlen($color) == 3) {
		$hex = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
	} else {
		return $default;
	}

	//Convert hexadec to rgb
	$rgb = array_map('hexdec', $hex);

	//Check if opacity is set(rgba or rgb)
	if ($opacity) {
		if (abs($opacity) > 1) {
			$opacity = 1.0;
		}

		$output = 'rgba(' . implode(",", $rgb) . ',' . $opacity . ')';
	} else {
		$output = 'rgb(' . implode(",", $rgb) . ')';
	}

	//Return rgb(a) color string
	return $output;
}

/**
 * Add change log for order actions
 * @param int $order_id
 * @param int $user_id
 * @param string $log
 * @return int $post_id
 * @since 1.0.5
 * @package MicrojobEngine
 * @category File Functions
 * @author Tat Thien
 */
function mje_add_mjob_order_changelog($order_id, $user_id, $action, $log = "", $post_date = null) {
	$log_content = !empty($log) ? $log : $action;
	$array_post = array(
		'post_title' => sprintf(__('Log for order %s', 'enginethemes'), $order_id),
		'post_content' => $log_content,
		'post_author' => $user_id,
		'post_type' => 'ae_message',
		'post_parent' => $order_id,
		'post_status' => 'publish',
	);
	if (isset($post_date)) {
		$array_post['post_date'] = $post_date;
	}

	$post_id = wp_insert_post($array_post);

	if (!is_wp_error($post_id)) {
		update_post_meta($post_id, 'type', 'changelog');
		update_post_meta($post_id, 'action_type', $action);
		update_post_meta($post_id, 'parent_conversation_id', $order_id);
	}

	return $post_id;
}

/**
 * Get list attach file of post
 * @param int $post_id
 * @return string
 * @since 1.1
 * @package MicrojobEngine
 * @category void
 * @author Tat Thien
 */
if (!function_exists('mje_get_list_attach_files')) {
	function mje_get_list_attach_files($post_id) {
		$str = '';
		$str .= '<ul class="list-attach">';
		$attach = get_post_meta($post_id, 'et_carousels');
		if (!empty($attach)) {
			foreach ($attach[0] as $k => $v) {
				if ($title = get_the_title($v) and $guid = get_the_guid($v)) {

					$str .= "<li><a href='$guid' target='_blank'><i class='fa fa-paperclip'></i>$title</a></li>";
				}
			}
		}

		$str .= '</ul>';
		return $str;
	}
}

/**
 * Show less / more content
 * @param string $content
 * @param int $word_limit
 * @return void
 * @since 1.1
 * @package MicrojobEngine
 * @author Tat Thien
 */
if (!function_exists('mje_render_toggle_content')) {
	function mje_render_toggle_content($content, $word_limit = 50) {
		$total_words = str_word_count($content);
		if ($word_limit < $total_words):
		?>
            <div class="full-text hide">
                <div class="content-text-description"><?php echo $content; ?></div>
                <a href="#" class="show-less"><?php _e('View less', 'enginethemes');?> <i class="fa fa-angle-double-up" aria-hidden="true"></i></a>
            </div>
            <div class="trim-text">
                <?php echo wp_trim_words($content, 50, '... <a href="#" class="show-more">' . __('View more', 'enginethemes') . '<i class="fa fa-angle-double-down" aria-hidden="true"></i></a>'); ?>
            </div>
            <?php
else:
		?>
            <div class="full-text">
                <?php echo $content; ?>
            </div>
            <?php
endif;
	}
}

/**
 * Show attach button
 * @return string $output
 * @since 1.1
 * @package MicrojobEngine
 * @author Tat Thien
 */
function mje_render_attach_file_button($container_id, $text = "") {
	$text = $text ? $text : __("Attach file", 'enginethemes');
	ob_start();
	?>
    <div>
        <span class="plupload_buttons" id="<?php echo $container_id ?>_container">
            <span class="img-gallery" id="<?php echo $container_id ?>_browse_button">
                <a href="#" class="add-img"><?php echo $text; ?> <i class="fa fa-plus"></i></a>
            </span>
        </span>
    </div>
    <span class="et_ajaxnonce" id="<?php echo wp_create_nonce('ad_carousels_et_uploader'); ?>"></span>
    <?php
$output = ob_get_clean();
	echo $output;
}

/**
 * Define timezone name
 * @return string array
 * @since 1.1
 * @package MicrojobEngine
 * @author Dang Bui
 */
if (!function_exists('mje_define_timezone_name')) {
	function mje_define_timezone_name() {
		$arr = array(
			'UTC+14' => 'Pacific/Kiritimati',
			'UTC-12' => 'Kwajalein',
		);
		return $arr;
	}
}

/**
 * Convert gmt offset to timezone string. PHP <5.4 not using gmt offset in function DateTimeZone()
 * @return timezone name
 * @input timezone gmt offset. Format UTC-XX.XX
 * @since 1.1
 * @package MicrojobEngine
 * @author Dang Bui
 */
if (!function_exists('mje_convert_timezone_offset_to_string')) {
	function mje_convert_timezone_offset_to_string($timezone, $convert = false) {
		$localtime = $timezone;
		//Check if is gmt offset - convert to timezone name
		if ($convert && strpos($timezone, 'UTC') === 0 && strlen($timezone) != 3) {
			$timezone = substr($timezone, 3);
			$list = explode('.', $timezone);
			$seconds = 0;
			if (isset($list[0])) {
				$seconds = $seconds + $list[0] * 60 * 60;
			}

			if (isset($list[1])) {
				$seconds = $seconds + (int) ('0.' . $list[1]) * 60 * 60;
			}

			$tz = timezone_name_from_abbr('', $seconds, 1);
			if ($tz === false) {
				$tz = timezone_name_from_abbr('', $seconds, 0);
			}

			if ($tz === false) {
				$tz = mje_define_timezone_name();
				$tz = isset($tz[$localtime]) ? $tz[$localtime] : 'UTC';
			}
			return $tz;
		}
		return $localtime;
	}
}

/**
 * Get timezone
 *
 * @param string $timezone_local
 * @param boolean $convert
 * @return string
 * @since 1.1
 * @package MicrojobEngine
 * @author Dang Bui
 */
if (!function_exists('mje_get_timezone')) {
	function mje_get_timezone($timezone_local, $convert = false) {
		$option_timezone = 'UTC';
		if ($timezone_strong = get_option('timezone_string')) {
			$option_timezone = $timezone_strong;
		} else if ($gmt_offset = get_option('gmt_offset')) {
			if (intval($gmt_offset) > 0) {
				$gmt_offset = '+' . $gmt_offset;
			}

			$option_timezone = 'UTC' . $gmt_offset;
		}
		//Set timezone user. If not set get timezone option in setting
		$timezone_local = (isset($timezone_local) and $timezone_local != '') ? $timezone_local : $option_timezone;
		// Using function convert gmt offset to timezone string
		if ($convert) {
			return mje_convert_timezone_offset_to_string($timezone_local, true);
		}

		return mje_convert_timezone_offset_to_string($timezone_local);
	}
}

/**
 * Locate a template and return the path for inclusion.
 * @param $template_name
 * @param string $template_path
 * @param string $default_path
 * @return string
 * @since MicrojobEngine 1.1.4
 * @author Tat Thien
 */
function mje_locate_template($template_name, $template_path = '') {
	if (MJE_Skin_Action::is_skin_active()) {
		$template_path = '/skins/' . MJE_Skin_Action::get_skin_name() . '/';
	}

	$template = locate_template(
		array(
			trailingslashit($template_path) . $template_name,
			$template_name,
		)
	);

	return $template;
}

/**
 * Get template, passing attributes and including the file
 * @param $template_name
 * @param array $args
 * @param string $template_path
 */
function mje_get_template($template_name, $args = array(), $template_path = '') {
	if (!empty($args) && is_array($args)) {
		extract($args);
	}

	$located = mje_locate_template($template_name, $template_path);

	if (!file_exists($located)) {
		return;
	}

	include $located;
}

/**
 * Render button classes
 *
 * @param array $classes
 * @return string
 * @since 1.1.4
 * @author Tat Thien
 */
function mje_button_classes($classes = array(), $return = false) {
	if (MJE_Skin_Action::is_skin_active()) {
		array_push($classes, 'btn-' . MJE_Skin_Action::get_skin_name());
	} else {
		array_push($classes, 'btn-submit');
	}

	if ($return) {
		return $classes;
	} else {
		echo join(' ', $classes);
	}

}

/**
 * Get template part includes skin template
 *
 * @param string $slug
 * @param string $name
 * @return void
 * @since 1.1.4
 * @author Tat Thien
 */
function mje_get_template_part($slug, $name = null) {
	// Get file name
	$template = "{$slug}.php";

	$name = (string) $name;
	if (!empty($name)) {
		$template = "{$slug}-{$name}.php";
	}

	// Check template in skin exist
	if (MJE_Skin_Action::is_skin_active()) {
		$skin_name = MJE_Skin_Action::get_skin_name();
		$child_path = get_stylesheet_directory() . '/skins/' . $skin_name . '/' . $template;
		$parent_path = get_template_directory() . '/skins/' . $skin_name . '/' . $template;

		if (file_exists($child_path) || file_exists($parent_path)) {
			$template = '/skins/' . $skin_name . '/' . $template;
		}
	}

	locate_template($template, true, false);
}

/**
 * Render payment name
 *
 * @param void
 * @return array
 * @since 1.2
 * @author Tat Thien
 */
function mje_render_payment_name() {
	$path = get_template_directory_uri() . '/assets/img';
	$payments = array(
		'paypal' => __('PayPal', 'enginethemes'),
		'cash' => __('Cash', 'enginethemes'),
		'credit' => __('Credit', 'enginethemes'),
		'2checkout' => __('2Checkout', 'enginethemes'),
		'bank' => __('Bank', 'enginethemes'),
	);
	$payment_name = array();
	foreach ($payments as $key => $name) {
		$icon_path = "{$path}/card-{$key}.svg";
		$payment_name[$key] = "<p class='payment-name {$key}' title='{$name}'><img src='{$icon_path}'/><span>{$name}</span></p>";
	}

	return apply_filters('mje_render_payment_name', $payment_name);
}

/**
 * Get the list of payment
 *
 * @param array $not_allowed
 * @return array $payment_list
 * @since 1.2
 * @author Tat Thien
 */
function mje_get_payment_list($not_allowed = array()) {
	$default_payment_list = array(
		'cash' => __("Cash", 'enginethemes'),
		'paypal' => __("PayPal", 'enginethemes'),
		'2checkout' => __("2Checkout", 'enginethemes'),
		'credit' => __("Credit", 'enginethemes'),
	);

	$payment_list = array_diff_key($default_payment_list, array_flip($not_allowed));

	return apply_filters('mje_payment_list', $payment_list);
}

/**
 * Render order button for mjob detail
 *
 * @param object $mjob_post
 * @param string $text
 * @since 1.3.1
 * @author Tat Thien
 */
function mje_render_order_button($mjob_post, $text = '') {
	$disable_class = 'mjob-order-action';
	if (in_array($mjob_post->post_status, array('pause', 'pending', 'draft', 'reject', 'archive'))) {
		$disable_class = 'mjob-order-disable';
	}
	$text = !empty($text) ? $text : __('ORDER NOW', 'enginethemes');
	$price_mjob = mje_shorten_price(mje_get_price_after_commission_for_buyer($mjob_post->et_budget));
	?>
    <button class="<?php mje_button_classes(array('btn-order', 'waves-effect', 'waves-light'))?> <?php echo $disable_class; ?>" >
        <?php echo sprintf(__('%s (<span class="mjob-price mje-price-text">%s</span>)', 'enginethemes'), $text, $price_mjob); ?>
    </button>
    <?php
}

/**
 * Get array of page id
 *
 * @return array $page_ids
 * @since 1.3.1
 * @author Tat Thien
 */
function mje_get_page_ids() {
	$pages = get_pages(array(
		'post_status' => array('publish'),
	));

	$page_ids = array();
	foreach ($pages as $page) {
		$page_ids[$page->ID] = $page->post_title;
	}

	return $page_ids;
}

/**
 * Get modules url
 *
 * @param string $modules
 * @return string $uri
 * @since 1.4
 * @author Thien Nguyen
 */
function mje_get_modules_uri($modules = '') {
	$uri = get_template_directory_uri() . '/includes/modules';
	if (!empty($modules)) {
		$uri .= "/$modules";
	}
	return $uri;
}
/**
 * Get UTC system
 *
 * @param string $modules
 * @return string UTC+
 * @since 1.4
 * @author Tan Hoai
 */
if (!function_exists('mje_text_timezone')) {
	function mje_text_timezone() {
		$min = 60 * get_option('gmt_offset');
		$sign = $min < 0 ? "-" : "+";
		$absmin = abs($min);
		$tz = sprintf("UTC%s%02d:%02d", $sign, $absmin / 60, $absmin % 60);
		return $tz;
	}
}

/**
 * hook allow zip uoload
 *
 * @param allow zip uoload
 * @return allow zip uoload
 * @since 1.4
 * @author Tan Hoai
 */

if (!function_exists('mje_add_allow_upload_extension_exception')) {
	function mje_add_allow_upload_extension_exception($file, $filename, $mimes) {
		$ext = substr($mimes, strpos($mimes, '.'), strlen($mimes) - 1);
		if ($ext == ".zip") {
			$ext = 'zip';
			$type = 'multipart/x-zip';
			return compact('ext', 'type', false);
		} else {
			return $file;
		}
	}
}
add_filter('wp_check_filetype_and_ext', 'mje_add_allow_upload_extension_exception', 10, 3);

/***
 * add filter total balance in revenue
 * author @tanhoai
 * verison 1.4
 */
if (!function_exists('revenue_total_balance_function')) {
	function revenue_total_balance_function($content, $total_earned) {
		ob_start();
		?>
			<div class="balance-withdraw">
				<p class="currency-balance"><span><?php _e('Total Balance:', 'enginethemes');?></span><span class="price-balance"><?php echo $total_earned; ?></span></p>
				<p class="note-balance"><?php _e('(Working + Available + Pending)', 'enginethemes');?></p>
			</div>
			<?php
return ob_get_clean();
	}
}
add_filter('revenue_total_balance', 'revenue_total_balance_function', 10, 2);
/***
 * add filter total balance in revenue
 * author @tanhoai
 * verison 1.4
 */
if (!function_exists('revenue_withdraw_spent_function')) {
	function revenue_withdraw_spent_function($content, $withdrew_text, $checkout_text) {
		ob_start();
		?>
			<div class="balance-checkout">
				<div class="row">
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 withdrew-text">
						<span class="total-currency"><?php _e('Withdrawn:', 'enginethemes');?></span>
						<span class="mje-price-text"> <?php echo $withdrew_text; ?></span>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 withdrew-text">
						<span class="total-currency"><?php _e('Spent:', 'enginethemes');?></span>
						<span class="mje-price-text"><?php echo $checkout_text; ?></span>
					</div>
				</div>
			</div>
			<?php
return ob_get_clean();
	}
}
add_filter('revenue_withdraw_spent', 'revenue_withdraw_spent_function', 10, 3);

/***
 * add filter show_template_revenue_withdraw_form
 * author @tanhoai
 * verison 1.3.5
 */
if (!function_exists('show_template_revenue_withdraw_form_function')) {
	function show_template_revenue_withdraw_form_function($content) {
		ob_start();
		get_template_part('template/revenue', 'withdraw-form');
		return ob_get_clean();
	}
}
add_filter('show_template_revenue_withdraw_form', 'show_template_revenue_withdraw_form_function', 10, 1);

/***
 * check if it video is youtube, vimeo or .mp4
 * author @tanhoai
 * verison 1.3.5
 */
if (!function_exists('mje_is_video')) {
	function mje_is_video($url) {
		if (preg_match('/https:\/\/(www\.)*youtube\.com\/watch\?v=.*/', $url)) {
			return true;
		}
		if (preg_match('/https:\/\/(www\.)*vimeo\.com\/.*/', $url)) {
			$rest_url = 'https://vimeo.com/api/oembed.json?url=' . $url;
			$json = json_decode(file_get_contents($rest_url), true);
			if (isset($json['video_id'])) {
				return true;
			}
		}
		if (preg_match('/.mp4.*/', $url)) {
			return true;
		}
		return false;
	}
}

/***
 * return content video
 * author @tanhoai
 * verison 1.3.5
 */
if (!function_exists('mje_get_video')) {
	function mje_get_video($url, $width = '100%', $height = '100%', $autoplay = 0) {
		if (preg_match('/https:\/\/(www\.)*youtube\.com\/.*/', $url)) {
			preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $url, $iframe);
			ob_start();
			?>
				<iframe width="<?php echo $width ?>" height="<?php echo $height; ?>" src="https://www.youtube.com/embed/<?php echo $iframe[1]; ?>?showinfo=0&controls=0&autoplay=<?php echo $autoplay; ?>" frameborder="0" allowfullscreen></iframe>
				<?php
return ob_get_clean();
		}

		if (preg_match('/https:\/\/(www\.)*vimeo\.com\/.*/', $url)) {
			ob_start();
			$rest_url = 'https://vimeo.com/api/oembed.json?url=' . $url;
			$json = json_decode(file_get_contents($rest_url), true);
			?>
					<iframe src="https://player.vimeo.com/video/<?php echo $json['video_id'] ?>?showinfo=0&controls=0&title=0&byline=0&portrait=0" width="<?php echo $width ?>" height="<?php echo $height; ?>" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
				<?php
return ob_get_clean();
		}

		if (preg_match('/.mp4.*/', $url)) {
			ob_start();
			?>
				<video width="<?php echo $width ?>" height="<?php echo $height; ?>" controls>
				  <source src="<?php echo $url; ?>" type="video/mp4">
				</video>
				<?php
return ob_get_clean();
		}
		return '';
	}
}

/***
 * return content video in single
 * author @tanhoai
 * verison 1.3.5
 */
if (!function_exists('mje_get_video_single')) {
	function mje_get_video_single($url, $width = '100%', $height = '100%', $autoplay = 0) {
		if (preg_match('/https:\/\/(www\.)*youtube\.com\/.*/', $url)) {
			preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $url, $iframe);
			ob_start();
			?>
				<iframe width="<?php echo $width ?>" height="<?php echo $height; ?>" src="https://www.youtube.com/embed/<?php echo $iframe[1]; ?>?showinfo=0&controls=1&autoplay=<?php echo $autoplay; ?>" frameborder="0" allowfullscreen></iframe>
				<?php
return ob_get_clean();
		}

		if (preg_match('/https:\/\/(www\.)*vimeo\.com\/.*/', $url)) {
			ob_start();
			$rest_url = 'https://vimeo.com/api/oembed.json?url=' . $url;
			$json = json_decode(file_get_contents($rest_url), true);
			?>
					<iframe src="https://player.vimeo.com/video/<?php echo $json['video_id'] ?>?showinfo=0&controls=1&title=0&byline=0&portrait=0" width="<?php echo $width ?>" height="<?php echo $height; ?>" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
				<?php
return ob_get_clean();
		}

		if (preg_match('/.mp4.*/', $url)) {
			ob_start();
			?>
				<video width="<?php echo $width ?>" height="<?php echo $height; ?>" controls>
				  <source src="<?php echo $url; ?>" type="video/mp4">
				</video>
				<?php
return ob_get_clean();
		}
		return '';
	}
}

/**
 * @author: danng.
 * @since 1.3.9.3
*/
function mje_get_post_thumbnail_url($id){
	$thumbnail_size = apply_filters('mje_default_thumbnail_size','medium_post_thumbnail');
	if( wp_is_mobile() ){
		$thumbnail_size = 'mjob_detail_slider';
	}
	return get_the_post_thumbnail_url( $id, $thumbnail_size );
}

/***
 * return thumbnail
 * author @tanhoai
 * verison 1.3.5
 */

if (!function_exists('mje_get_thumbnail')) {
	function mje_get_thumbnail($id) {
		ob_start();
		$video = get_post_meta($id, 'video_meta', true);
		$photo_default = get_template_directory_uri() . '/assets/img/mjob_thumbnail.png';
		if (ae_get_option('default_mjob')) {
			$default = ae_get_option('default_mjob');
			$defautl_thumb = $default['medium_post_thumbnail'];
			$photo_default = $defautl_thumb[0];
		}
		$img = (has_post_thumbnail($id)) ? mje_get_post_thumbnail_url($id) : $photo_default;

		if ($video) {
			echo mje_get_video($video);
		} else { ?>
			<img src="<?php echo $img; ?>"> <?php
		}

		return ob_get_clean();
	}
}
/***
 * add video in ae_convert_mjob_post
 * author @tanhoai
 * verison 1.3.5
 */
add_filter('ae_convert_mjob_post', 'add_filter_content_video');
if (!function_exists('add_filter_content_video')) {
	function add_filter_content_video($mjob_post) {
		$mjob_post->mje_get_thumbnail = mje_get_thumbnail($mjob_post->ID);
		$video_url = get_post_meta($mjob_post->ID, 'video_meta', true);
		$mjob_post->class_video = (mje_is_video($video_url))?'mjob-item__video':'';
		return $mjob_post;
	}
}


/***
 * custom css admin bar
 * author @tanhoai
 * verison 1.3.5
 */
add_action('get_header', 'remove_css_admin_bar');
if (!function_exists('remove_css_admin_bar')) {
	function remove_css_admin_bar() {
		remove_action('wp_head', '_admin_bar_bump_cb');
	}
}
add_action('wp_head', 'add_css_admin_bar');
if (!function_exists('add_css_admin_bar')) {
	function add_css_admin_bar(){
		if(current_user_can( 'manage_options' )){
			?>
			<style type="text/css" media="screen">
				html { margin-top: 32px !important; }
				* html body { margin-top: 32px !important; }
				@media screen and ( max-width: 782px ) {
					html { margin-top: 0px !important; }
					* html body { margin-top: 0px !important; }
				}
			</style>
			<?php
		}
	}
}

/***
 * add fee for buyer below button order
 * author @tanhoai
 * verison 1.3.5
 */
 if (!function_exists('mje_render_buy_fee ')) {
	function mje_render_buy_fee() {
		$fee=(ae_get_option('order_commission_buyer'))?ae_get_option('order_commission_buyer'):'';
		if($fee){
			?>
			<div class="mje-commission-fee">
				<span><?php printf(__('%s%%  commission fee included', 'enginethemes'), $fee); ?></span>
			</div>
			<?php
		}
	}
}
if (! function_exists('is_mje_submit_page') ){
	function is_mje_submit_page(){
		if( is_page_template('page-post-service.php') || is_page_template('page-post-recruit.php') )
			return true;
		return false;
	}
}
function mje_enable_extra_fee(){
	if( function_exists( '_mjeef_load_plugin') )
		return true;
	return false;
}
/**
  * Check the plugin MJE_Geolocation is actived.
  * @since 1.3.10
  * @return boolean
  * @author: danng.
*/
function is_acti_mje_geo(){
	if( class_exists('MJE_Geolocation') )
		return true;
	return false;
}
?>