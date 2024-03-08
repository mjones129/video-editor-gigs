<?php
class AE_Customizer extends AE_Base
{
    public static $instance;
    public $ae_customize;
    public $fields = array();
    public $sections = array();
    public $styles;
    /**
     * Get instance method
     * @param void
     * @return object AE_Customizer
     * @since MicrojobEngine 1.0.4
     * @author Tat Thien
     */
    public static function get_instance() {
        if(!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct($fields = array(), $sections = array(), $panels = array(), $styles = "") {
        $this->add_action('customize_register', 'ae_customize_register');
        $this->add_action('wp_head', 'ae_customize_header_output');
        $this->add_action('customize_preview_init', 'ae_customize_live_preview');
        $this->add_action('customize_controls_print_styles', 'ae_customize_control_styles');
        $this->add_action('customize_save_after', 'ae_update_option');
        $this->add_action('ae_upload_image', 'ae_update_customize_option', 10, 3);

        $this->add_ajax('ae_customize_get_attachment_data', 'ae_customize_get_attachment_data');

        /**
         * Filter for default customize color
         * @param array
         * @return array
         */
        $default_colors = apply_filters( 'mje_customize_default_colors', array(
            'primary' => '#10a2ef',
            'header' => '#ffffff',
            'footer' => '#2a394e'
        ) );

        $default_fields = array(
            // Section: Site Identity
            array(
                'setting_name' => 'site_copyright',
                'control_id' => 'site_copyright',
                'label' => __('Copyright', 'enginethemes'),
                'section' => 'title_tagline',
                'option_type' => 'theme_mod',
                'field_type' => 'textarea',
                'default' => '',
                'description' => __('This copyright information will be appeared in the footer. HTML is supported.', 'enginethemes'),
            ),

            array(
                'setting_name' => 'site_logo',
                'control_id' => 'site_logo',
                'label' => __('Site Logo', 'enginethemes'),
                'section' => 'title_tagline',
                'option_type' => 'theme_mod',
                'field_type' => 'cropped_image',
                'default' => '',
                'description' => __('The optimal dimensions for your Site Logo are 150x50 pixels.', 'enginethemes'),
                'width' => 150,
                'height' => 50
            ),

            /*******************
             * SITE COLOR      *
             *******************/
            array(
                'setting_name' => MJE_Skin_Action::get_skin_name() . '_primary_color',
                'control_id' => 'primary_color',
                'label' => __('Primary Color', 'enginethemes'),
                'section' => 'colors',
                'option_type' => 'theme_mod',
                'field_type' => 'color',
                'default' => $default_colors['primary']
            ),
            array(
                'setting_name' => MJE_Skin_Action::get_skin_name() . '_header_color',
                'control_id' => 'header_color',
                'label' => __('Header Color', 'enginethemes'),
                'section' => 'colors',
                'option_type' => 'theme_mod',
                'field_type' => 'color',
                'default' => $default_colors['header']
            ),
            array(
                'setting_name' => MJE_Skin_Action::get_skin_name() . '_footer_color',
                'control_id' => 'footer_color',
                'label' => __('Footer Color', 'enginethemes'),
                'section' => 'colors',
                'option_type' => 'theme_mod',
                'field_type' => 'color',
                'default' => $default_colors['footer']
            ),
        );

        $this->fields = array_merge($fields, $default_fields);
        $this->sections = $sections;
        $this->panels = $panels;
        $this->styles = $styles;
    }

    /**
     * Register a customizer
     * @param object $wp_customize
     * @return void
     * @since MicrojobEngine 1.0.4
     * @package AE Modules
     * @author Tat Thien
     */
    public function ae_customize_register($wp_customize) {
        foreach($this->panels as $panel) {
            self::ae_customize_add_panel($wp_customize, $panel);
        }

        foreach($this->sections as $section) {
            self::ae_customize_add_section($wp_customize, $section);
        }

        foreach($this->fields as $field) {
            self::ae_customize_add_field($wp_customize, $field);
        }
    }

    /**
     * Render CSS in header
     * @param void
     * @return void
     * @since MicrojobEngine 1.0.4
     * @package AE Modules
     * @author Tat Thien
     */
    public function ae_customize_header_output() {
        // Render default custom st
        $primary_color = MJE_Skin_Action::get_skin_name() . '_primary_color';
        $primary_color_shadow = MJE_Skin_Action::get_skin_name() . '_primary_color_shadow';
        $header_color = MJE_Skin_Action::get_skin_name() . '_header_color';
        $footer_color = MJE_Skin_Action::get_skin_name() . '_footer_color';
        ob_start();
        ?>
        <style type="text/css" id="mje-customize-css">
            <?php
                // Button shadow
                $mod = get_theme_mod( $primary_color_shadow );
                if(!empty($mod)) {
                    ?>
                /* Media query */
                @media (max-width: 991px) {
                    .et-dropdown .et-dropdown-login .post-service-link .plus-circle {
                        background: <?php echo get_theme_mod( $primary_color ); ?>;
                    }
                }
                .search-form .btn-search,
                #et-header .link-post-services .plus-circle,
                .mje-request-default .plus-circle,
                .btn-button,
                .btn-submit,
                .btn-customizer,
                .link-post-job .plus-circle,
                .form-delivery-order .attachment-image .add-img i,
                .outer-conversation .attachment-image .add-img i,
                .post-detail #comments .form-submit .submit {
                    box-shadow: 1px 3px 9px <?php echo $mod; ?>;
                }

                .progress-bar ul li.active span {
                    box-shadow: 2px 5px 30px <?php echo $mod; ?>;
                }

                /* .paginations-wrapper .current {
                     box-shadow: 1px 5px 11px <?php echo $mod; ?>;
                    }*/

                .line-distance:before {
                    background: <?php echo $mod; ?>;
                }
                <?php
            }
            ?>

            <?php AE_Customizer::ae_customize_generate_css( '.et-dropdown .et-dropdown-login:before, .list-price li .outer-payment-items:hover', 'border-bottom-color', $primary_color ); ?>
            <?php AE_Customizer::ae_customize_generate_css( '.post-job li:hover:before', 'border-right-color', $primary_color ); ?>
            <?php AE_Customizer::ae_customize_generate_css( '#et-header .list-message .list-message-box-header:before', 'border-bottom-color', $primary_color ); ?>

            <?php AE_Customizer::ae_customize_generate_css('
                .block-hot-items ul li .avatar img,
                .et-form input[type="text"]:focus,
                .et-form input[type="number"]:focus,
                .et-form input[type="email"]:focus,
                .et-form textarea:focus,
                .frame-delivery-info,
                .form-control:focus,
                .submenu,
                .form-group .checkbox input[type="checkbox"]:checked:after,
                .post-job li:hover,
                .attachment-image ul li input[type="radio"]:not(:checked):before,
                .attachment-image ul li input[type="radio"]:checked:before,
                .mjob-single-order-page .btn-dispute,
                .mjob_conversation_detail_page .conversation-form .line,
                .form-group .checkbox input[type="radio"]:not(:checked) + span:before,
                .form-group .checkbox input[type="radio"]:checked ~ span:before,
                .attachment-image ul li img:hover,
                .text-choosen:before,
                .custom-order-box .btn-decline,
                .content-custom-order .block-text .action .btn-decline,
                .btn-reject,
                .form-group .checkbox input:checked ~ span:before'
                ,'border-color', $primary_color ); ?>

            <?php AE_Customizer::ae_customize_generate_css('
                .dashboard .information-items-detail .nav-tabs > li.active > a, .mjob-dispute-form .compose,
                .nav-tabs > li.active > a, .nav-tabs > li.active > a:hover, .nav-tabs > li.active > a:focus,
                .loading .loading-img,
                .set-status .tooltip > .tooltip-arrow'
                , 'border-top-color', $primary_color ); ?>

            <?php
                // Header background
                AE_Customizer::ae_customize_generate_css('#et-header .et-pull-top', 'background', $header_color );
            ?>
            <?php
                // Footer background
                AE_Customizer::ae_customize_generate_css('#footer', 'background', $footer_color );
            ?>
            <?php
                // Background with primary color
                AE_Customizer::ae_customize_generate_css('
                    .btn-submit, .btn-customizer, .modal-header,
                    .line-distance:after,
                    #et-header .list-message .list-message-box-header,
                    #et-header .list-message .list-message-box-header,
                    .et-dropdown .et-dropdown-login li:hover,
                    .et-dropdown .et-dropdown-login li:first-child,
                    #et-header .link-post-services .plus-circle,
                    .mje-request-block .plus-circle,
                    .search-form .btn-search,
                    .btn-post .cirlce-plus,
                    .hvr-sweep-to-left:before,
                    .progress-bar .progress-bar-success,
                    .post-job .add-more a .icon-plus,
                    .link-post-job .plus-circle,
                    .profile .text-content:hover:before,
                    .profile .block-billing ul li #billing_country .chosen-container .chosen-results li.highlighted,
                    .withdraw #bankAccountForm .chosen-container .chosen-results li.highlighted,
                    .page-template-page-profile .profile .chosen-container .chosen-results li.highlighted,
                    .chosen-container .chosen-results li.highlighted,
                    .form-delivery-order .attachment-image .add-img i,
                     .outer-conversation .attachment-image .add-img i,
                     .mjob_conversation_detail_page .private-message .conversation-text,
                     #content .carousel-indicators li.active,
                     .mCS-minimal.mCSB_scrollTools .mCSB_dragger .mCSB_dragger_bar,
                     .mCS-minimal.mCSB_scrollTools .mCSB_dragger.mCSB_dragger_onDrag .mCSB_dragger_bar,
                     .set-status .tooltip > .tooltip-inner,
                     .custom-order-box .btn-send-offer,
                     .custom-order-box .btn-accept-offer,
                     .content-custom-order .block-text .action .btn-send-offer,
                     .list-price li .outer-payment-items.hvr-underline-from-left:before,
                     #et-nav nav ul li a:hover, #et-nav nav ul li span:hover,
                     #et-nav .navbar-default .navbar-nav > .active > a, #et-nav .navbar-default .navbar-nav > .active > a:hover, #et-nav .navbar-default .navbar-nav > .active > a:focus,
                     #et-nav nav ul li.active a:before,
                     .profile .upload-profile-avatar .back-top-hover, .group-compose .action-link span.img-gallery,
                     .post-detail #comments .form-submit .submit,
                     .form-group .checkbox input:checked ~ span:before,
                     .form-group .radio input:checked ~ span:after'
                    , 'background', $primary_color );
            ?>

            <?php
                // Color with primary color
                AE_Customizer::ae_customize_generate_css('
                    a,
                    #et-header .link-account ul li .open-signup-modal,
                     #et-header .link-post-services a:hover,
                     #et-header .list-message .list-message-box-footer a,
                    .fa-star,
                    .fa-star-half-o,
                    .block-items ul li .inner .price,
                    .mjob-item .item-title h2 a:hover,
                    #footer .et-pull-bottom a,
                    .block-intro .load-more a,
                    .not-member a,
                    .open-forgot-modal:hover,
                    .btn-post:hover .cirlce-plus i,
                    .accordion .link a:hover,
                    #accordion > li.active > .link > a,
                    .not-found-sub-text .new-search-link,
                    .not-found-sub-text a,
                    .accordion li.open i,
                    .accordion .link:hover i,
                    .submenu a:hover,
                    .accordion a.active,
                    .block-items-detail .items-private .mjob-cat .mjob-breadcrumb .child,
                    .block-items-detail .items-private .time-post span,
                    .mjob-single-page .mjob-single-aside .mjob-single-stat .price,
                    .list-extra li .package-price,
                    .mjob-order-info .price,
                    .mjob-order-info .total-price,
                    .form-group .checkbox input[type="checkbox"]:not(:checked) + span:after,
                    .form-group .checkbox input[type="checkbox"]:checked ~ span:after,
                    .progress-bar ul li.active span,
                    .package .chose-package .price,
                    .et-form .chosen-container .chosen-results li:hover,
                    .attachment-image ul li input[type="radio"]:not(:checked):after,
                     .attachment-image ul li input[type="radio"]:checked:after,
                     .count_down,
                     .revenues ul li .currency,
                     .currency-balance .price-balance,
                     .dashboard .information-items-detail .tabs-information .view-all a,
                     .list-job ul li .info-items .price,
                     .list-order li:hover a,
                     .list-job li:hover a,
                     .list-order ul li .author a,
                     #display_name .text-content:hover:before,
                     .profile .block-billing ul li .text-content:hover:before,
                     .profile .block-statistic ul li a:hover,
                     .et-form .chosen-container .chosen-results li.highlighted,
                     .block-items-detail .personal-profile .link-personal ul li .profile-link,
                     .block-items-detail .personal-profile .link-personal ul li .profile-link i,
                     .mjob-single-order-page .functions-items .date,
                     .mjob_conversation_detail_page .message-time,
                     .mjob-admin-dispute-form .text,
                     .mjob-single-order-page .btn-dispute,
                     .compose-conversation .compose .send-message,
                     .mjob-dispute-form .compose .add-img i,
                     .conversation-date,
                     .compose-conversation .carousel_single_conversation-image-list li a,
                     .dashboard .information-items-detail .nav-tabs > li.active > a,
                     .paginations-wrapper .current,
                     .paginations-wrapper .page-numbers:hover,
                     .withdraw .payment-method .link-change-payment a,
                     .form-group .checkbox input[type="radio"]:not(:checked) + span:after,
                     .form-group .checkbox input[type="radio"]:checked ~ span:after,
                     .load-more-post,
                     .accordion .link a.active,
                     .list-job ul li .info-items a:hover,
                     .text-choosen a,
                     .mjob-single-order-page .order-detail-price .price-items,
                     .user-conversation a,
                     .mjob_conversation_detail_page .conversation-form .paginations-wrapper a,
                     .changelog-item .changelog-text a,
                     .countdown,
                     .mjob_conversation_detail_page .guest-message .conversation-text li a,
                     .list-file li a,
                     .nav-tabs > li.active > a, .nav-tabs > li.active > a:hover, .nav-tabs > li.active > a:focus,
                     .text-choosen:after,
                     .reset-pass-active .reset-title,
                     .custom-order-box .btn-decline,
                     .custom-order-box .budget span,
                     .mjob_conversation_detail_page .guest-message .conversation-text .budget span,
                     .content-custom-order .date span,
                     .content-custom-order .block-text .budget span,
                     .content-custom-order .block-text .list-attach li a,
                     .content-custom-order .block-text .budget span,
                     .custom-order-box .more a,
                     .custom-order-link a,
                     .content-custom-order .block-text .action .btn-decline,
                     .btn-reject,
                     .action-form .cata-title,
                     .custom-order .view-custom-order .link-view-custom-order,
                     .color-custom-label,
                     .attachment-file-name ul li a,
                     .custom-order-status a,
                     #et-nav nav #overflow li a:hover,
                     #et-nav .div-main-sub li a:hover,
                     .show-more, .show-less, .show-more:hover, .show-less:hover,
                     .post-detail #comments .logged-in-as a,
                     .post-list li .info-items .group-function .more,
                     .accordion li.open .link a.active,
                     .info-payment-method .sub-title, .page-404 .icon-404, .page-404 .note-wrong,
                     .post-detail #comments .comment-list .comment-reply-link,
                     .mjob-order-page .extra-item.active .package-price,
                     .mjob-item .mjob-item__title h2 a:hover,
                     .customize-color'
                    , 'color', $primary_color );

                    AE_Customizer::ae_customize_generate_css('
                        .compose-conversation .input-compose input::-webkit-input-placeholder'
                        , 'color', $primary_color );

                    AE_Customizer::ae_customize_generate_css('
                    .compose-conversation .input-compose input::-moz-placeholder'
                    , 'color', $primary_color );

                    AE_Customizer::ae_customize_generate_css('
                    .compose-conversation .input-compose input:-moz-placeholder'
                    , 'color', $primary_color );

                    AE_Customizer::ae_customize_generate_css('
                    .compose-conversation .input-compose input:-ms-input-placeholder'
                    , 'color', $primary_color );

                    /**
                     * Fire action when generate customize css
                     *
                     * @param AE_Customize
                     * @param array $color
                     * @since 1.3.1
                     * @author Tat Thien
                     */
                    do_action( 'mje_generate_customize_css', AE_Customizer::get_instance(), array(
                            'primary_color' => $primary_color,
                            'primary_color_shadow' => $primary_color_shadow,
                            'header_color' => $header_color,
                            'footer_color' => $footer_color
                    ) );
            ?>
        </style>
        <?php
        $default_styles = ob_get_clean();
        echo $default_styles;
        echo $this->styles;
    }

    /**
     * Add panel
     * @param object $wp_customize
     * @param array $args Panel arguments
     * @since MicrojobEngine 1.0.4
     * @package AE Modules
     * @author Tat Thien
     */
    public static function ae_customize_add_panel($wp_customize, $args) {
        $panel_value = array(
            'title'    => $args['title'],
            'description' => isset($args['description']) ? $args['description'] : "",
            'capability' => 'edit_theme_options',
            'priority' => isset($args['priority']) ? $args['priority'] : 30
        );
        $wp_customize->add_panel($args['name'], $panel_value);
    }

    /**
     * Add section
     * @param object $wp_customize
     * @param array $args Section arguments
     * @since MicrojobEngine 1.0.4
     * @package AE Modules
     * @author Tat Thien
     */
    public static function ae_customize_add_section($wp_customize, $args) {
        $section_value = array(
            'title'    => $args['title'],
            'description' => isset($args['description']) ? $args['description'] : "",
            'capability' => 'edit_theme_options',
            'priority' => isset($args['priority']) ? $args['priority'] : 30,
            'panel' => isset($args['panel']) ? $args['panel'] : ''
        );
        $wp_customize->add_section($args['name'], $section_value);
    }

    /**
     * Add field
     * @param object $wp_customize
     * @param array $args Setting and control arguments
     * @return void
     * @since MicrojobEngine 1.0.4
     * @package AE Modules
     * @author Tat Thien
     */
    public static function ae_customize_add_field($wp_customize, $args) {
        //2. Register new settings to the WP database...
        $wp_customize->add_setting($args['setting_name'],
            array(
                'default' => $args['default'],
                'type' => $args['option_type'],
                'capability' => 'edit_theme_options',
                'transport' => 'postMessage',
            )
        );

        $field_value = array(
            'label' => $args['label'],
            'section' => $args['section'],
            'settings' => $args['setting_name'],
            'description' => isset($args['description']) ? $args['description'] : '',
            'priority' => 10,
        );

        switch($args['field_type']) {
            case 'color':
                $wp_customize->add_control(new WP_Customize_Color_Control(
                    $wp_customize,
                    $args['control_id'],
                    $field_value
                ));
                break;
            case 'cropped_image':
                $field_value = wp_parse_args($field_value, array(
                    'width' => $args['width'],
                    'height' => $args['height']
                ));
                $wp_customize->add_control(
                    new WP_Customize_Cropped_Image_Control(
                        $wp_customize,
                        $args['control_id'],
                        $field_value
                    )
                );
                break;
            case 'open_group_control':
            case 'close_group_control':
                $field_value = wp_parse_args($field_value, array(
                    'type' => $args['field_type']
                ));
                $wp_customize->add_control(
                    new WP_Customize_Group_Control(
                        $wp_customize,
                        $args['control_id'],
                        $field_value
                    )
                );
                break;
            default:
                $field_value = wp_parse_args($field_value, array(
                   'type' => $args['field_type']
                ));
                $wp_customize->add_control(
                    new WP_Customize_Control(
                        $wp_customize,
                        $args['control_id'],
                        $field_value
                    )
                );
        }
    }

    /**
     * This will generate a line of CSS for use in header output. If the setting
     * ($mod_name) has no defined value, the CSS will not be output.
     *
     * @param string $selector CSS selector
     * @param string $style The name of the CSS *property* to modify
     * @param string $mod_name The name of the 'theme_mod' option to fetch
     * @param string $prefix Optional. Anything that needs to be output before the CSS property
     * @param string $postfix Optional. Anything that needs to be output after the CSS property
     * @param bool $echo Optional. Whether to print directly to the page (default: true).
     * @return string Returns a single line of CSS with selectors and a property.
     * @since MicrojobEngine 1.0.4
     * @package AE Modules
     * @author Tat Thien
     */
    public static function ae_customize_generate_css( $selector, $style, $mod_name, $prefix='', $postfix='', $echo=true ) {
        $return = '';
        $mod = get_theme_mod($mod_name);
        if ( ! empty( $mod ) ) {
            $return = sprintf('%s { %s:%s; }',
                $selector,
                $style,
                $prefix.$mod.$postfix
            );
            if ( $echo ) {
                echo $return;
            }
        }
        return $return;
    }

    /**
     * Enqueue live preview script
     * @param void
     * @return void
     * @since MicrojobEngine 1.0.4
     * @package AE Modules
     * @author Tat Thien
     */
    public function ae_customize_live_preview() {
        wp_enqueue_script('ae-theme-customizer',
            get_template_directory_uri() . '/includes/modules/AE_Customizer/assets/ae-live-preview.js',
            array('jquery', 'appengine', 'customize-preview'),
            ET_VERSION,
            true
        );
    }

    /**
     * Enqueue custom css for customize control
     * @param void
     * @return void
     * @since MicrojobEngine 1.1.2
     * @package AE Modules
     * @author Tat Thien
     */
    public function ae_customize_control_styles() {
        wp_enqueue_style('ae-customize-css',
           get_template_directory_uri() . '/includes/modules/AE_Customizer/assets/ae-customizer.css'
        );
    }

    /**
     * Sync customize settings to theme setting options
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author Tat Thien
     */
    public function ae_update_option($wp_customize_manager) {
        // Sync site_logo
        $customize_site_logo = get_theme_mod('site_logo');
        $attach_data = et_get_attachment_data($customize_site_logo);
        ae_update_option('site_logo', $attach_data);

        // Sync site_icon
        $customize_site_icon = get_option('site_icon');
        $attach_data = et_get_attachment_data($customize_site_icon);
        ae_update_option('site_icon', $attach_data);

        // Sync search background
        $customize_search_background = get_theme_mod('search_background');
        $attach_data = et_get_attachment_data($customize_search_background);
        ae_update_option('search_background', $attach_data);

        // Sync footer background
        $customize_footer_background = get_theme_mod('footer_background');
        $attach_data = et_get_attachment_data($customize_footer_background);
        ae_update_option('footer_background', $attach_data);

        // Sync post job banner background
        $customize_post_job_background = get_theme_mod('post_job_banner');
        $attach_data = et_get_attachment_data($customize_post_job_background);
        ae_update_option('post_job_banner', $attach_data);

        // Set shadow color
        $primary_color = get_theme_mod( MJE_Skin_Action::get_skin_name() . '_primary_color');
        set_theme_mod( MJE_Skin_Action::get_skin_name() . '_primary_color_shadow', mje_convert_hex_to_rgb($primary_color, 0.7));
        set_theme_mod( MJE_Skin_Action::get_skin_name() . '_primary_chart_shadow', mje_convert_hex_to_rgb($primary_color, 0.1));
    }

    /**
     * Sync theme setting options to customize settings
     * @param array $attach_data
     * @param array $post_data
     * @param int $attach_id
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author Tat Thien
     */
    public function ae_update_customize_option($attach_data, $post_data, $attach_id) {
        switch($post_data) {
            case 'site_icon':
                update_option('site_icon', $attach_id);
                break;
            case 'site_logo':
                set_theme_mod('site_logo', $attach_id);
                break;
            case 'search_background':
                set_theme_mod('search_background', $attach_id);
                break;
            case 'footer_background':
                set_theme_mod('footer_background', $attach_id);
                break;
            case 'post_job_banner':
                set_theme_mod('post_job_banner', $attach_id);
        }
    }

    public function ae_customize_get_attachment_data() {
        wp_send_json(array(
            'data' => et_get_attachment_data($_REQUEST['attachment_id'])
        ));
    }
}