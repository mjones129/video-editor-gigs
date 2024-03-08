<?php
define('ADMIN_PATH', TEMPLATEPATH . '/admin');

if (!class_exists('AE_Base')) {
	return;
}

/**
 * Handle admin features
 * Adding admin menus
 */
class ET_Admin extends AE_Base {
	function __construct() {
		/**
		 * admin setup
		 */
		$this->add_action('init', 'admin_setup');

		/**
		 * update first options
		 */
		$this->add_action('after_switch_theme', 'update_first_time');

		//declare ajax classes
		$this->add_ajax('ae-reset-option', 'reset_option');

		/* User Actions */
		$this->add_action('ae_upload_image', 'ae_upload_image', 10, 2);

		/**
		 * set default options
		 */
		$options = AE_Options::get_instance();
		if (!$options->init) {
			$options->reset($this->get_default_options());
		}

		// kick subscriber user
		if (!current_user_can('manage_options') && basename($_SERVER['SCRIPT_FILENAME']) != 'admin-ajax.php') {

			// wp_redirect( home_url(  ) );
			// exit;

		}
		$this->add_filter('ae_setup_wizard_template', 'fre_setup_wizard_template');
		$this->add_filter('ae_disable_notice_wizard', 'fre_notice_after_installing_theme');
		$this->add_action('ae_insert_sample_data_success', 'fre_after_insert_sample_data');
	}

	/**
	 * update user avatar
	 */
	public function ae_upload_image($attach_data, $data) {
		$options = AE_Options::get_instance();
		switch ($data) {
		case 'site_logo_black':
		case 'site_logo_white':

			// save this setting to theme options
			$options->$data = $attach_data;
			if ($data == 'site_logo_black') {
				$options->site_logo = $attach_data;
			}
			$options->save();

			break;

		default:
			if (!is_array($data)) {
				$options->$data = $attach_data;
				$options->save();
			}
			break;
		}
	}

	/**
	 * ajax function reset option
	 */
	function reset_option() {

		$option_name = $_REQUEST['option_name'];
		$default_options = $this->get_default_options();

		if (!empty($_REQUEST['default_value'])) {
			$default_value = $_REQUEST['default_value'];
		} else {
			$default_value = isset($default_options[$option_name]) ? $default_options[$option_name] : '';
		}

		$options = AE_Options::get_instance();
		$options->$option_name = stripslashes($default_value);
		$options->save();

		wp_send_json(array(
			'msg' => $options->$option_name,
		));
	}

	function admin_custom_css() {
		?>
        <style type="text/css">
        .custom-icon {
            margin: 10px;
        }
        .custom-icon input {
            width: 80%;
        }
        </style>
    <?php
}

	/**
	 * retrieve site default options
	 */
	function get_default_options() {

		return apply_filters('fre_default_setting_option', array(
			'blogname' => get_option('blogname'),
			'blogdescription' => get_option('blogdescription'),
			'copyright' => get_mje_copyright(),
			// '<span class="enginethemes"> <a href=http://www.enginethemes.com/themes/microjobengine/ >MicrojobEngine</a> - Powered by WordPress </span>',

			'project_demonstration' => array(
				'home_page' => 'The best way to <br/>  find a professional',
				'list_project' => 'A Million of Project.<br/> Find it out!',
			),
			'profile_demonstration' => array(
				'home_page' => 'Need a job? <br/> Tell us your story',
				'list_profile' => 'Need a job? <br/> Tell us your story',
			),

			// default forgot passmail
			'forgotpass_mail_template' => '<p>Hello [display_name],</p><p>You have just sent a request to recover the password associated with your account in [blogname]. If you did not make this request, please ignore this email; otherwise, click the link below to create your new password:</p><p>[recover_url]</p><p>Regards,<br />[blogname]</p>',

			// default register mail template
			'register_mail_template' => '<p>Hello [display_name],</p><p>You have successfully registered an account with &nbsp;&nbsp;[blogname].&nbsp;Here is your account information:</p><ol><li>Username: [user_login]</li><li>Email: [user_email]</li></ol><p>Thank you and welcome to [blogname].</p>',

			// default confirm mail template
			'password_mail_template' => '<p>Hello [display_name],</p><p>You have successfully registered an account with &nbsp;&nbsp;[blogname].&nbsp;Here is your account information:</p><ol><li>Username: [user_login]</li><li>Email: [user_email]</li><li>Password: [password]</li></ol><p>Thank you and welcome to [blogname].</p>',

			//  default reset pass mail template
			'resetpass_mail_template' => "<p>Hello [display_name],</p><p>You have successfully changed your password. Click this link &nbsp;[site_url] to login to your [blogname]'s account.</p><p>Sincerely,<br />[blogname]</p>",

			// default confirm mail template
			'confirm_mail_template' => '<p>Hello [display_name],</p><p>You have successfully registered an account with &nbsp;&nbsp;[blogname].&nbsp;Here is your account information:</p><ol><li>Username: [user_login]</li><li>Email: [user_email]</li></ol><p>Please click the link below to confirm your email address.</p><p>[confirm_link]</p><p>Thank you and welcome to [blogname].</p>',

			// default confirmed mail template
			'confirmed_mail_template' => "<p>Hi [display_name],</p><p>Your email address has been successfully confirmed.</p><p>Thank you and welcome to [blogname].</p>",

			//  default inbox mail template
			'inbox_mail_template' => "<p>Hello [display_name],</p><p>You have just received the following message from user: <a href=\"[sender_link]\">[sender]</a></p>
                                        <p>|--------------------------------------------------------------------------------------------------|</p>
                                        [message]
                                        <p>|--------------------------------------------------------------------------------------------------|</p>
                                        <p>Sincerely,<br />[blogname]</p>",

			//  default inbox mail template
			'new_mjob_mail_template' => "<p>Hi,</p>
                                               <p>User [author] has submitted a new mJob on your site. You could review it [here].</p>
                                               <p>Regards,<br>[blogname]</p>",

			'approve_mjob_mail_template' => "<p>Dear [display_name],</p>
                                                <p>Your post [link] posted in [blogname] has been approved.</p>
                                                <p>Sincerely,<br>[blogname]</p>",

			'reject_mail_template' => "<p>Dear [display_name],</p>
                                                <p>Your post [link] submitted in [blogname] has been rejected. Noted reason: [reject_message]</p>
                                                <p>Please contact the admin via [admin_email] for further information, or go to your dashboard at [dashboard] to edit your job offer and submit again.</p>
                                                <p>Sincerely,<br>[blogname]</p>",

			'archived_mjob_mail_template' => "<p>Dear [display_name],</p>
                                                <p>Your post [link] posted in [blogname] has been archived. Noted reason: [reject_message]</p>
                                                <p>Please contact the admin via [admin_email] for further information, or go to your dashboard at [dashboard] to edit your job offer and submit again.</p>
                                                <p>Sincerely,<br>[blogname]</p>",

			'new_order' => "<p>Dear [display_name],</p>
                                                <p>Your mJob - [link] -  posted in [blogname] has a new order.</p>
                                                <p>Here are the order’s details:</p>
                                                <p><ol>
                                                <li>Name:  [buyer_name]</li>
                                                <li>mJob: [mjob_price]</li>
                                                <li>Fee buyer: [mjob_price_fee_buyer]</li>
                                                <li>Extra:  [extra_price]</li>
                                                <li>Total: [order_price]</li>
                                                </ol></p>
                                                <p>For this order, the platform has deducted an amount of [commission] from your total earning of [order_price]. You can view your order details here: [order_link].</p>
                                                <p>Sincerely,<br>[blogname]</p>",
             'new_order_admin' => "<p>Hello Admin,,</p>
                                                <p>Your website [blogname] has a new order.</p>
                                                <p>Here are the order’s details:</p>
                                                <p><ol>
                                                <li>mJob Post: [link]</li>
                                                <li>Buyer Name:  [buyer_name]</li>
                                                <li>mJob Price : [mjob_price]</li>
                                                <li>Payment Gateway : [payment_gateway]</li>
                                                <li>Payment Status : [payment_status]</li>
                                                <li>Fee buyer: [mjob_price_fee_buyer]</li>
                                                <li>Extra:  [extra_price]</li>
                                                <li>Total: [order_price]</li>
                                                </ol></p>
                                                <p>You can view order details here: [order_link].</p>
                                                <p>Sincerely,<br>[blogname]</p>",

			'delivery_order' => "<p>Dear [display_name],</p>
                                                <p>Your order for mJob - [link] -  has been delivered.</p>
                                                <p>Here are the delivery details: [note].</p>
                                                <p>And here is the link to your order details: [order_link].</p>
                                                <p>Please note that your order will be automatically closed at: [finish_time]</p>
                                                <p>Sincerely,<br>[blogname]</p>
                                                ",

			'accepted_order' => "<p>Dear [display_name],</p>
                                                <p>Your order delivery for mJob - [link] - has been accepted.</p>
                                                <p>Here is the buyer’s review for your service: [note].</p>
                                                <p>For this order, the platform has deducted an amount of [commission] from your total earning of [order_price]. You can view your order details here: [order_link].</p>
                                                <p>Sincerely,<br>[blogname]</p>
                                                ",

			'finished_automatically_order' => "<p>Dear [display_name],</p>
                                                <p>The duration for disputation has passed. Your Order for the mJob - <a href='[link]' target='_blank'>[link]</a> - has been changed to Finished. You can view your order details here: <a href='[order_link]' target='_blank'>[order_link]</a>.</p>
                                                <p>Sincerely,<br>[blogname]</p>
                                                ",

			'finished_order_commission' => "<p>Hello Admin,</p>
                                                <p>An order has been finished. You receive a commission of [commission_amount] for this order.</p>
                                                <p>You can view the order details here: [order_link].</p>
                                                <p>Sincerely,<br>[blogname]</p>
                                                ",

			'cancel_order' => "<p>Dear [display_name],</p>
                                                <p>The seller [author] has canceled your order for the mJob: [link]. </p>
                                                <p>You can review your order: [order_link] and stop the delivery.</p>
                                                <p>Sincerely,<br>[blogname]</p>
                                                ",
			'dispute_order' => "<p>Hello Admin,</p>
                                                <p>[title] is in dispute.</p>
                                                <p>You can review the order at: [link]</p>
                                                <p>Sincerely,<br>[blogname]</p>",

			'dispute_order_user' => "<p>Hello [display_name],</p>
                                                <p>[title] you’ve worked on has been reported by your partner. You should review and send your feedback in 36 hours.</p>
                                                <p>You can review the order at: [link]</p>
                                                <p>Sincerely,<br>[blogname]</p>",

			'dispute_seller_win' => "<p>Dear [display_name],</p>
                                              <p>Admin has made the final decision about the disputed [title].</p>
                                              <p>The payment will be transferred to the seller.</p>
                                              <p>You can review the order at: [link]</p>
                                              <p>Sincerely,<br>[blogname]</p>",
			'dispute_buyer_win' => "<p>Dear [display_name],</p>
                                              <p>Admin has made the final decision about the disputed [title].</p>
                                              <p>The payment will be transferred back to the buyer.</p>
                                              <p>You can review the order at: [link]</p>
                                              <p>Sincerely,<br>[blogname]</p>",
			'new_withdraw' => "<p>Hi,</p>
                                                <p>User [user_name] has sent a withdrawal request.</p>
                                                <p>Here are the withdrawal details:</p>
                                                <p>
                                                    <ul>
                                                    <li>Name:  [user_name]</li>
                                                    <li>Total: [total]</li>
                                                    <li>Withdraw info: [withdraw_info]</li>
                                                    </ul>
                                                </p>
                                                <p>And here is the link to the user info: [user_link].</p>
                                                <p>You can go to dashboard to approve or decline the request.</p>
                                                <p>Sincerely,<br>[blogname]</p>
                                                ",

			'approve_withdraw' => "<p>Dear [display_name],</p>
                                                <p>Your withdrawal request has been approved. Please check your payment account.</p>
                                                <p>Your current balance is: [balance].</p>
                                                <p>Sincerely,<br>[blogname]</p>
                                                ",

			'decline_withdraw' => "<p>Dear [display_name],</p>
                                                <p>Your withdrawal request has been declined. Noted reason: [note]</p>
                                                <p>Your current balance is: [balance].</p>
                                                <p>Sincerely,<br>[blogname]</p>
                                                ",
			'decline_mjob_order' => "<p>Dear [display_name],</p>
                                                <p>Your microjob order request has been declined. Noted reason: [note]</p>
                                                <p>Sincerely,<br>[blogname]</p>
                                                ",
			'admin_delete_order_mail_template' => "<p>Dear [display_name],</p>
                                                <p>Your order [title] has just been deleted. The fund related to this order has been updated accordingly.</p>
                                                <p>Should you have any further concerns, please don’t hesitate to contact us.</p>
                                                <p>Sincerely,<br>[blogname]</p>
                                                ",

			'admin_restore_order_mail_template' => "<p>Dear [display_name],</p>
                                                <p>Your order [title] has just been restored. The fund related to this order has been updated accordingly. You can view this order detail at: [link].</p>
                                                <p>Should you have any further concerns, please don’t hesitate to contact us.</p>
                                                <p>Sincerely,<br>[blogname]</p>
                                                ",

			'new_offer_mail_template' => "<p>Dear [display_name],</p>
                                                <p>[seller_display_name] has sent you an offer for your custom order regarding the mjob “[title]”.
You can view this offer detail here: [link].</p>
                                                <p>Sincerely,<br>[blogname]</p>
                                                ",

			'pay_package_by_cash' => "<p>Dear [display_name],</p>
                                                <p>Please send the payment to XXX account to complete the order.</p>
                                                <p>Here are the details of your transaction:</p>
                                                <p>Detail: [detail]</p>
                                                <p><strong>Customer info</strong></p>
                                                <p>
                                                    <ul>
                                                    <li>Name: [display_name]</li>
                                                    <li>Email: [user_email]</li>
                                                    </ul>
                                                </p>
                                                <p><strong>Invoice</strong></p>
                                                <p>
                                                    </ul>
                                                    <li>Invoice No: [invoice_id]</li>
                                                    <li>Date: [date]</li>
                                                    <li>Payment: [payment]</li>
                                                    <li>Total: [total] [currency]</li>
                                                    </ul>
                                                </p>
                                                <p>Sincerely,<br>[blogname]</p>",

			'ae_receipt_mail' => "<p>Dear [display_name],</p>
                                                <p>Thank you for your payment.</p>
                                                <p>Here are the details of your transaction:</p>
                                                <p>Detail: [detail]</p>
                                                <p><strong>Customer info</strong></p>
                                                <p>
                                                    <ul>
                                                    <li>Name: [display_name]</li>
                                                    <li>Email: [user_email]</li>
                                                    </ul>
                                                </p>
                                                <p><strong>Invoice</strong></p>
                                                <p>
                                                    </ul>
                                                    <li>Invoice No: [invoice_id]</li>
                                                    <li>Date: [date]</li>
                                                    <li>Payment: [payment]</li>
                                                    <li>Total: [total] [currency]</li>
                                                    </ul>
                                                </p>
                                                <p>Sincerely,<br>[blogname]</p>",

			'secure_code_mail' => "<p>Dear [display_name],</p>
                                                <p>Here is your secure code: [secure_code]</p>
                                                <p>Sincerely,<br>[blogname]</p>",

			'sign_up_intro_text' => "<p><strong>Welcome to MicrojobEngine!</strong></p><p>If you have amazing skills, we have amazing mJobs. MicrojobEngine has opportunities for all types of fun. Let's turn your little hobby into Big Bucks.</p>",

//            Custom order
			'custom_order_send' => "
                <p>Dear [display_name],</p>
                <p>You have just received a Custom order from [buyer_display_name] regarding your mJob \"[title]\".</p>
                <p>You can view this custom order detail at: <a href='[link]'>[link]</a>.</p>

                <p>
                Sincerely, <br />
                [blogname]
                </p>

            ",
			//            Custom order
			'decline_custom_order' => "
                <p>Dear [display_name],</p>
                <p>Your custom order regarding the mjob \"[title]\" from [seller_display_name] has been declined.</p>
                <p class='decline_msg'>
                [decline_msg]
                </p>
                <p>You can visit your conversation with this seller to enquire for more details: <a href='[link]'>[link]</a>.</p>
                <p>
                Sincerely, <br />
                [blogname]
                </p>

            ",
			'reject_custom_order' => "
                <p>Dear [display_name],</p>
                <p>[buyer_display_name] has rejected your offer regarding the custom order for \"[title]\".</p>
                <p>
                [reject_msg]
                </p>
                <p>
                You can visit your conversation with this buyer for more details: <a href='[link]'>[link]</a>.
                </p>
                <p>
                Sincerely,
                [blogname]
                </p>
            ",
			'init' => 1,
		));
	}

	function update_first_time() {
		update_option('de_first_time_install', 1);
		update_option('revslider-valid-notice', 'false');
	}
	/**
	 * FrE setup wizard html template
	 * @param string $html
	 * @return string $html
	 * @since 1.6.2
	 * @package void
	 * @category void
	 * @author Tambh
	 */
	public function fre_setup_wizard_template($html) {
		ob_start();
		?>
        <div class="et-main-content" id="overview_settings">
            <div class="et-main-right">
                <div class="et-main-main clearfix inner-content" id="wizard-sample">
                    <div class="group-wrapper">
                        <div class="title group-title">
                            <?php _e('Sample data', 'enginethemes')?>
                        </div>
                        <div class="group-fields clearfix">
                            <div class="field-item clearfix">
                                <?php $sample_data_op = get_option('option_sample_data');?>

                                <?php if (!$sample_data_op): ?>
                                    <div class="field-desc col-lg-5 col-md-12 col-sm-12 col-xs-12">
                                        <p><?php _e('Import MicrojobEngine sample data', 'enginethemes')?></p>
                                        <span><?php _e('Click on the "Import sample data" button to begin importing data', 'enginethemes');?></span>
                                    </div>
                                    <div class="field-content form no-margin no-padding no-background col-lg-7 col-md-12 col-sm-12 col-xs-12 install-data">
                                        <button class="primary-button" id="install_sample_data"><?php _e("Import sample data", 'enginethemes')?></button>
                                    </div>
                                <?php else: ?>
                                    <div class="field-desc col-lg-5 col-md-12 col-sm-12 col-xs-12">
                                        <p><?php _e('Delete sample data', 'enginethemes')?></p>
                                        <span><?php _e('Click on the "Delete sample data" button to delete data', 'enginethemes');?></span>
                                    </div>
                                    <div class="field-content form no-margin no-padding no-background col-lg-7 col-md-12 col-sm-12 col-xs-12 install-data">
                                        <button class="primary-button" id="delete_sample_data"><?php _e("Delete sample data", 'enginethemes')?></button>
                                    </div>
                                <?php endif;?>
                            </div><!-- end .field-item -->
                        </div>
                    </div><!-- end .group-wrapper -->


                    <div id="select-skins" class="group-wrapper">
                        <div class="title group-title">
                            <?php _e('Select your favorite skin below', 'enginethemes')?>
                        </div>
                        <div class="group-fields clearfix">
                            <div class="row">
                                <?php
$skins = MJE_Skin_Action::get_skins();
		if (!empty($skins) && is_array($skins)):
			foreach ($skins as $skin) {
				?>
	                                            <div class="col-lg-4 col-md-6 col-sm-6 skin-item <?php echo ($skin['name'] == ae_get_option('mjob_skin_name', 'default') ? 'selected' : ''); ?>">
	                                                <div class="inner">
	                                                    <div class="action-overlay">
	                                                        <nav>
	                                                            <a href="javascript:void(0)" class="select" data-name="<?php echo $skin['name']; ?>"><i class="fa fa-check"></i><span><?php _e('Select', 'enginethemes');?></span></a>
	                                                            <a href="javascript:void(0)" class="preview" data-preview="<?php echo $skin['preview']; ?>"><i class="fa fa-eye"></i><span><?php _e('Preview', 'enginethemes');?></span></a>
	                                                        </nav>
	                                                        <div class="skin-description">
	                                                            <h2><?php echo $skin['title'] ?></h2>
	                                                            <p><?php echo $skin['desc'] ?></p>
	                                                        </div>
	                                                    </div>
	                                                    <figure>
	                                                        <img src="<?php echo $skin['thumbnail'] ?>" alt="<?php echo $skin['name'] . "_thumbnail"; ?>">
	                                                    </figure>
	                                                </div>
	                                            </div>
	                                        <?php
	}
		endif;
		?>
                            </div>
                        </div>
                    </div><!-- end .group-wrapper -->
                </div>
            </div>
        </div>
    <?php
$html = ob_get_clean();
		return $html;
	}
	/**
	 * Turn off notification after setup theme
	 * @param void
	 * @return boolean
	 * @since 1.1.2
	 * @package void
	 * @category void
	 * @author Tambh
	 */
	public function fre_notice_after_installing_theme() {
		return true;
	}
	/**
	 * Set static page after insert sample data
	 * @param void
	 * @return void
	 * @since void
	 * @package void
	 * @category void
	 * @author Tambh
	 */
	public function fre_after_insert_sample_data() {
		if (get_page(8)) {
			update_option('page_on_front', 8);
		}
		if ($blogid = url_to_postid(et_get_page_link('blog'))) {
			update_option('page_for_posts ', $blogid);
		}
		update_option('show_on_front', 'page');
	}
	/**
	 * Admin setup
	 */
	function admin_setup() {
		// disable admin bar for all users except admin
		if (!current_user_can('administrator') && !is_admin()) {
			show_admin_bar(false);
		}

		$sections = array();
		//        User setting
		$sections['users-setting'] = mjob_setting_user();
		//        Microjob Setting
		$sections['microjob-setting'] = mjob_setting_microjob();
		//        Payment cofig
		$sections['currency'] = mje_setting_currency_section();
		//        Payment type
		$sections['payment-type'] = mjob_setting_payment_type();
		//        Withdraw config
		$sections['withdraw-config'] = mjob_setting_widthdraw_config();
		//        SEO setting
		$sections['seo-setting'] = mjob_setting_seo();
		//        Translation
		$sections['translation'] = mjob_setting_translations();

		$temp = array();
		$options = AE_Options::get_instance();
		foreach ($sections as $key => $section) {
			$temp[] = new AE_section($section['args'], $section['groups'], $options);
		}

		$pages = array();

		$pages['welcome'] = array(
			'args' => array(
				'parent_slug' => 'et-welcome',
				'page_title' => __('Welcome', 'enginethemes'),
				'menu_title' => __('WELCOME', 'enginethemes'),
				'cap' => 'administrator',
				'slug' => 'et-welcome',
				'icon' => 'L',
				'desc' => sprintf(__("%s welcome", 'enginethemes'), $options->blogname),
				'own_frame' => true,
			),
			'container' => new mJob_Welcome(),
		);

		/**
		 * setup wizard view
		 */

		$pages['install_demo'] = array(
			'args' => array(
				'parent_slug' => 'et-welcome',
				'page_title' => __('Site Construction & Design', 'enginethemes'),
				'menu_title' => __('SITE CONSTRUCTION & DESIGN', 'enginethemes'),
				'cap' => 'administrator',
				'slug' => 'et-wizard',
				'icon' => 'fa-download',
				'desc' => __("Set up and manage all content of your site", 'enginethemes'),
			),
			'container' => new AE_Wizard(),
		);

		/**
		 * setting view
		 */
		$container = new AE_Container(array(
			'class' => '',
			'id' => 'settings',
		), $temp, '');
		$pages['settings'] = array(
			'args' => array(
				'parent_slug' => 'et-welcome',
				'page_title' => __('Theme Options', 'enginethemes'),
				'menu_title' => __('THEME OPTIONS', 'enginethemes'),
				'cap' => 'administrator',
				'slug' => 'et-settings',
				'icon' => 'fa fa-cogs',
				'desc' => __("Manage how your MicrojobEngine looks and feels", 'enginethemes'),
			),
			'container' => $container,
		);

		/* Payment Config */
		$payment_gateways_sections = mje_get_payment_gateway_sections();
		$payment_gateways_container = new AE_Container(array(
			'class' => '',
			'id' => 'settings',
		), $payment_gateways_sections, '');
		$pages['payment_config'] = array(
			'args' => array(
				'parent_slug' => 'et-welcome',
				'page_title' => __('Payment Gateways', 'enginethemes'),
				'menu_title' => __('Payment Gateways', 'enginethemes'),
				'cap' => 'administrator',
				'slug' => 'et-payment-gateways',
				'icon' => 'fa fa-credit-card',
				'desc' => __("Manage your payment gateways", 'enginethemes'),
			),
			'container' => $payment_gateways_container,
		);

		/**
		 * order list view
		 */
		$pages['payments'] = array(
			'args' => array(
				'parent_slug' => 'et-welcome',
				'page_title' => __('Package Purchases', 'enginethemes'),
				'menu_title' => __('PACKAGE PURCHASES', 'enginethemes'),
				'cap' => 'administrator',
				'slug' => 'et-payments',
				'icon' => 'fa-shopping-basket',
				'desc' => __("Synthetize the purchase of pricing plans", 'enginethemes'),
			),
			'container' => new AE_OrderList(),
		);

		/**
		 * order list view
		 */
		$mjob_order_edit_url = admin_url('edit.php?post_type=mjob_order');
		$pages['mjob_order'] = array(
			'args' => array(
				'parent_slug' => 'et-welcome',
				'page_title' => __('Orders', 'enginethemes'),
				'menu_title' => __('ORDERS', 'enginethemes'),
				'cap' => 'administrator',
				'slug' => 'et-mjob-order',
				'icon' => 'fa-shopping-cart',
				'desc' => sprintf(__("Synthetize all the microjob orders. <a href='%s'>View Microjob Orders (for advanced users).</a>", 'enginethemes'), $mjob_order_edit_url),
			),
			'container' => new MJE_MJob_Order_Container(),
		);

		/*
			         * withdraw list view
		*/
		$pages['withdraws'] = array(
			'args' => array(
				'parent_slug' => 'et-welcome',
				'page_title' => __('Money Withdrawal', 'enginethemes'),
				'menu_title' => __('MONEY WITHDRAWAL', 'enginethemes'),
				'cap' => 'administrator',
				'slug' => 'et-withdraws',
				'icon' => 'fa-money',
				'desc' => __("Overview of all withdraws", 'enginethemes'),
			),
			'container' => new AE_WithdrawList(),
		);

		/**
		 * user list view
		 */
		$pages['members'] = array(
			'args' => array(
				'parent_slug' => 'et-welcome',
				'page_title' => __('Member List', 'enginethemes'),
				'menu_title' => __('MEMBERS LIST', 'enginethemes'),
				'cap' => 'administrator',
				'slug' => 'et-users',
				'icon' => 'fa-users',
				'desc' => __("Overview of registered members", 'enginethemes'),
			),
			'container' => new AE_UsersContainer(),
		);

		/**
		 * overview container
		 */
		$container = new AE_Overview(array(
			'mjob_profile',
			'mjob_post',
			'mjob_order',
		), true);

//         $pages['overview'] = array(
		//             'args' => array(
		//                 'parent_slug' => 'et-welcome',
		//                 'page_title' => __('MjE Status', 'enginethemes') ,
		//                 'menu_title' => __('MICROJOBENGINE STATUS', 'enginethemes') ,
		//                 'cap' => 'administrator',
		//                 'slug' => 'et-overview',
		//                 'icon' => 'fa-bar-chart',
		//                 'desc' => sprintf(__("%s overview", 'enginethemes') , $options->blogname)
		//             ) ,
		//             'container' => $container,
		//             // 'header' => $header
		//         );

		/**
		 *  filter pages config params so user can hook to here
		 */
		$pages = apply_filters('ae_admin_menu_pages', $pages);

		/**
		 * add menu page
		 */
		$this->admin_menu = new AE_Menu($pages);

		/**
		 * add sub menu page
		 */
		foreach ($pages as $key => $page) {
			new AE_Submenu($page, $pages);
		}
	}
}
