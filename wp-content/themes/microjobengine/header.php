<?php
/**
 * The Header for our theme
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage MicrojobEngine
 * @since MicrojobEngine 1.0
 */
global $current_user;
?><!DOCTYPE html>

<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8) ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
    <?php global $user_ID; ?>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1 ,user-scalable=no">
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>?ver=<?php echo ET_VERSION;?>">
    <?php
		$favicon = ae_get_option('site_icon');
		if($favicon) {
			$img = $favicon['thumbnail'][0];
			echo '<link href="'. $img .'" rel="shortcut icon" type="image/x-icon">';
			if(et_load_mobile()) {
				echo '<link href="'. $img .'" rel="apple-touch-icon" />';
			}
		}
	?>
	<?php wp_head(); ?>
</head>
<?php
	$body_class = apply_filters( 'mje_body_class', array( MJE_Skin_Action::get_skin_name() ) );
?>
<body <?php body_class( $body_class ); ?>>
<div class="mje-main-wrapper">
	<header id="et-header">
		<div class="et-pull-top">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle menu-navbar-toggle collapsed" data-toggle="collapse">
					<span class="sr-only">Toggle navigation</span>
					<i class="fa fa-bars"></i>
				</button>
			</div>
			<div class="row">
				<!--Logo-->
				<div class="header-left">
					<div id="logo-site">
						<a href="<?php echo get_site_url() ?>">
						<?php
							$logo_url = ae_get_option('site_logo');
							$img_theme_mod = get_theme_mod('site_logo');
							if(!empty($logo_url)) {
								$logo_url = $logo_url['full']['0'];
								echo '<img src="'. $logo_url .'" alt="'. get_bloginfo('blogname') .'" />';
							} elseif(false === $img_theme_mod) {
								$logo_url = get_template_directory_uri() . '/assets/img/logo.png';
								echo '<img src="'. $logo_url .'" alt="'. get_bloginfo('blogname') .'" />';
							} else {
								$logo_url = "";
							}
						?>
						</a>
					</div>

					<div class="search-bar">
						<?php
						mje_show_search_form();
						?>
					</div>
				</div>
				<!--Function right-->
				<div id="myAccount" class="float-right header-right">
					<?php
					if(is_user_logged_in()) {
						mje_show_user_header();
					} else {
						mje_show_authentication_link();
					}
					?>
				</div>
			</div>
		</div>
		<div class="et-pull-bottom" id="et-nav">
			<nav>
				<div class="navbar navbar-default megamenu">
					<!-- <div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse">
							<span class="sr-only">Toggle navigation</span>
							<i class="fa fa-bars"></i>
						</button>
					</div> -->
					<div class="collapse navbar-collapse">
						<?php
							if(has_nav_menu('et_header_standard')) {
								wp_nav_menu(array(
									'theme_location' => 'et_header_standard',
									'menu_class' => 'nav navbar-nav', // Class UL
									'menu_id'=> 'nav',
									'container' => '',
									'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s<li class="more"><span>'. __('Others', 'enginethemes') .'</span><ul id="overflow"></ul></li></ul>',
									'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
									'walker'            => new wp_bootstrap_navwalker()
								));
							} else if(current_user_can('manage_options')) { ?>
								<ul>
									<li><a href="<?php echo admin_url('/nav-menus.php'); ?>"><?php _e('Add a menu', 'enginethemes'); ?></a></li>
								</ul>
								<?php
							}
							do_action('after_header_menu');
						?>
						<div class="overlay-nav"></div>

					</div>
				</div>
			</nav>
		</div>
	</header><!--End Header-->
<?php
global $user_ID;
if($user_ID) {
	echo '<script type="data/json"  id="user_id">'. json_encode(array('id' => $user_ID, 'ID'=> $user_ID) ) .'</script>';
}