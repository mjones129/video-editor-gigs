<?php
if(!defined('ABSPATH')) {
	exit(1);
}

$admin_url = admin_url('admin.php');
$admin_setting_url = $admin_url . '?page=et-settings';
$admin_customizer_url = admin_url() . 'customize.php';
?>
<div class="wrapper-welcome">
	<div class="wl-top">
		<div class="wl-header">
			<div class="top">
				<p><?php _e('Hola, thank you for loading MjE admin area !', 'enginethemes'); ?></p>
			</div>
			<div class="bottom">
				<div class="title"><i class="fa fa-tachometer" aria-hidden="true"></i><h2><?php _e('Welcome to MicrojobEngine', 'enginethemes'); ?></h2></div>
				<p>MicrojobEngine <?php echo ET_VERSION; ?> - PHP <?php echo phpversion(); ?></p>
			</div>
			<span class="logo-et"></span>
		</div>
		<div class="key-active">
			<form class="input-key-form">
				<p class="block-title"><?php _e('Get Stuffs Done Right the First Time', 'enginethemes'); ?></p>
				<input type="text" placeholder="<?php _e('Enter Your License Key to active...', 'enginethemes'); ?>" class="input-active-key regular-text" name="et_license_key" id="et_license_key" value="<?php echo get_option('et_license_key'); ?>">
			</form>
			<a class="btn-submit btn-install" href="<?php echo $admin_url . '?page=et-wizard' ?>" target="_blank"><?php _e('Install Demo', 'enginethemes'); ?></a>
		</div>
		<div class="explore-content">
			<p class="block-title"><?php _e('Explore MicrojobEngine', 'enginethemes'); ?></p>
			<ul class="block-list-explore">
				<li class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
					<a href="http://docs.enginethemes.com/" target="_blank" class="knowledge" title="<?php _e('Knowledge Base', 'enginethemes'); ?>">
						<div class="icon-explore"><span></span></div>
						<p><?php _e('Knowledge Base', 'enginethemes'); ?></p>
					</a>
				</li>
				<li class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
					<a href="https://codex.wordpress.org/Getting_Started_with_WordPress" target="_blank" class="tutorials" title="<?php _e('Basic WP Tutorials', 'enginethemes'); ?>">
						<div class="icon-explore"><span></span></div>
						<p><?php _e('Basic WP Tutorials', 'enginethemes'); ?></p>
					</a>
				</li>
				<li class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
					<a href="https://www.udemy.com/inbound-marketing-course/" target="_blank" class="marketing" title="<?php _e('Basic Marketing', 'enginethemes'); ?>">
						<div class="icon-explore"><span></span></div>
						<p><?php _e('Basic Marketing', 'enginethemes'); ?></p>
					</a>
				</li>
				<li class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
					<a href="https://www.enginethemes.com/themes/" target="_blank" class="product" title="<?php _e('Our Products', 'enginethemes'); ?>">
						<div class="icon-explore"><span></span></div>
						<p><?php _e('Our Products', 'enginethemes'); ?></p>
					</a>
				</li>
				<li class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
					<a href="https://www.enginethemes.com/microjobengine-extensions/" target="_blank" class="extension" title="<?php _e('Mje Extensions', 'enginethemes'); ?>">
						<div class="icon-explore"><span></span></div>
						<p><?php _e('Mje Extensions', 'enginethemes'); ?></p>
					</a>
				</li>
				<li class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
					<a href="https://www.enginethemes.com/help/" target="_blank" class="channel" title="<?php _e('Support Channel', 'enginethemes'); ?>">
						<div class="icon-explore"><span></span></div>
						<p><?php _e('Support Channel', 'enginethemes'); ?></p>
					</a>
				</li>

			</ul>
			<div class="clearfix"></div>
		</div>
	</div>
	<div class="wl-bottom">
		<div class="information-structure">
		<div class="row inner">
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 aside-left">
				<p class="name-structure"><i class="fa fa-cog" aria-hidden="true"></i><?php _e('Theme Instructions', 'enginethemes'); ?></p>
				<p class="line"></p>
				<div class="tree-systems">
					<ul class="install">
						<span>1</span>
						<p class="name-config"><?php _e('Install', 'enginethemes'); ?></p>

						<?php if(get_option('option_sample_data', 0)) : ?>
							<li><a href="<?php echo $admin_url . '?page=et-wizard'; ?>" target="_blank"><?php _e('Delete Sample Data', 'enginethemes'); ?></a></li>
						<?php else: ?>
							<li><a href="<?php echo $admin_url . '?page=et-wizard'; ?>" target="_blank"><?php _e('Install Sample Data', 'enginethemes'); ?></a></li>
						<?php endif; ?>
					</ul>
					<ul class="config">
						<span>2</span>
						<p class="name-config"><?php _e('Configure', 'enginethemes'); ?></p>
						<li><a href="<?php echo $admin_customizer_url; ?>" target="_blank"><?php _e('Theme customization', 'enginethemes'); ?></a></li>
						<li><a href="<?php echo $admin_setting_url . '#section/users-setting'; ?>" target="_blank"><?php _e('Users', 'enginethemes'); ?></a></li>
						<li><a href="<?php echo $admin_setting_url . '#section/microjob-settings'; ?>" target="_blank"><?php _e('MicroJobs (mJobs)', 'enginethemes'); ?></a></li>
						<li><a href="<?php echo $admin_setting_url . '#section/currency-settings'; ?>" target="_blank"><?php _e('Currency', 'enginethemes'); ?></a></li>
						<li><a href="<?php echo $admin_url . '?page=et-payment-gateways'; ?>" target="_blank"><?php _e('Payment Gateways', 'enginethemes'); ?></a></li>
						<li><a href="<?php echo $admin_setting_url . '#section/payment-type-settings'; ?>" target="_blank"><?php _e('Payment Type (packages)', 'enginethemes'); ?></a></li>
						<li><a href="<?php echo $admin_setting_url . '#section/withdraw-settings'; ?>" target="_blank"><?php _e('Withdraw Config', 'enginethemes'); ?></a></li>
						<li><a href="<?php echo $admin_setting_url . '#section/seo'; ?>" target="_blank"><?php _e('SEO', 'enginethemes'); ?></a></li>
						<li><a href="<?php echo $admin_setting_url . '#section/language-settings'; ?>" target="_blank"><?php _e('Translation', 'enginethemes'); ?></a></li>
					</ul>
					<ul class="make-site">
						<span>3</span>
						<p class="name-config"><?php _e('Make your site work', 'enginethemes'); ?></p>
						<li><a href="<?php echo $admin_url . '?page=et-payments'; ?>" target="_blank"><?php _e('Package Purchases', 'enginethemes'); ?></a></li>
						<li><a href="<?php echo $admin_url . '?page=et-mjob-order'; ?>" target="_blank"><?php _e('Orders', 'enginethemes'); ?></a></li>
						<li><a href="<?php echo $admin_url . '?page=et-withdraws'; ?>" target="_blank"><?php _e('Money Withdrawal', 'enginethemes'); ?></a></li>
						<li><a href="<?php echo $admin_url . '?page=et-users'; ?>" target="_blank"><?php _e('Member List', 'enginethemes'); ?></a></li>
					</ul>
				</div>
			</div>

			<?php
			// Get blog feed data
			$rss = fetch_feed('https://www.enginethemes.com/blog/feed');
			$maxitems = 0;
			if( ! is_wp_error($rss)) {
				$maxitems = $rss->get_item_quantity(10);
				$rss_items = $rss->get_items(0, $maxitems);
			}
			?>
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 aside-right">
				<div class="top">
					<p class="name-structure"><i class="fa fa-rss" aria-hidden="true"></i><?php _e('Blog & News Feed', 'enginethemes'); ?></p>
					<p class="line"></p>
					<ul class="list-blog">
						<?php
						if( ! empty($rss_items)) {
							foreach ($rss_items as $item) {
								?>
								<li class="clearfix"><a href="<?php echo $item->get_permalink(); ?>" target="_blank"><i class="fa fa-circle" aria-hidden="true"></i><span><?php echo $item->get_title(); ?></span></a></li>
								<?php
							}
						} else {
							_e('Not yet', 'enginethemes');
						}

						?>
					</ul>
					<a href="https://www.enginethemes.com/blog/" class="link-more" target="_blank"><p class="icon"></p><?php _e('Learn more', 'enginethemes'); ?></a>
				</div>
				<div class="bottom">
					<p class="name-structure"><i class="fa fa-info-circle" aria-hidden="true"></i><?php _e('Theme version', 'enginethemes'); ?></p>
					<p class="line"></p>
					<div class="version">
						<div class="img-version">
							<a href=""><img src="<?php echo get_template_directory_uri(); ?>/assets/img/admin/img-version.png" alt="" class="img-version"></a>
						</div>
						<div class="text-version">
							<p class="name-project"><span></span>MicrojobEngine</p>
							<p class="name-version"><?php echo ET_VERSION; ?></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	</div>
</div>