<?php
/**
 * The template for displaying 404 pages (Not Found)
 *
 * @package WordPress
 * @subpackage Freelance Engine
 * @since Freelance Engine 1.0
 */
global $user_ID;
$reason 	= isset($_GET['reason']) ? $_GET['reason'] : 0;
$is_banned 	= get_user_meta($user_ID,'is_banned', true);
get_header(); ?>

<div id="content" class="blog-header-container">
	<div class="container">
		<!-- blog header -->
		<div class="float-center page-404">
		<?php if($reason == 'ban' && $is_banned){ ?>
			<p class="note-wrong"><?php _e('Account Banned','enginethemes');?></p>
			<!--<img src="<?php /*echo get_template_directory_uri(); */?>/assets/img/404.png" alt="">-->

			<p class="content-404"><?php _e('Sorry. Your account is banned. Please contact administrator for further information.
			','enginethemes');?></p>


		<?php } else{?>
			<p class="note-wrong"><?php _e('Something went wrong!','enginethemes');?></p>
			<p class="icon-404"><span>4</span><i class="fa fa-exclamation-circle" aria-hidden="true"></i><span>4</span></p>
			<p class="content-404"><?php _e('The link you are looking for seems to be broken or missing.','enginethemes');?></p>
			<p><?php printf(__('You can go back to the previous page or our <a href="%s">homepage</a>','enginethemes'),get_site_url());?>
			</p>
			<div class="link-back">
				<a href="<?php echo get_site_url() ?>" class="<?php mje_button_classes( array() ); ?>">
					<i class="fa fa-angle-left"></i><?php _e('go back','enginethemes');?></a>
			</div>

	<?php } ?>
	</div>
	</div>
</div>


<?php

get_footer();
