<?php
function defaultColors( $default_color ) {
    return array (
        'primary' => '#27ae60',
        'header' => '#ffffff',
        'footer' => '#06100a'
    );
}
add_filter( 'mje_customize_default_colors', 'defaultColors' );

// Customize panel
$panels = array(
    array(
        'name' => 'mje_tb_panel',
        'title' => __('Title & Background', 'enginethemes'),
        'description' => "",
    )
);

// Customize sections
$sections = array(
    array(
        'name' => 'mje_diplomat_block_search',
        'title' => __('Homepage Search', 'enginethemes'),
        'description' => '',
        'priority' => 35,
        'panel' => 'mje_tb_panel'
    ),
    array(
        'name' => 'mje_other_title',
        'title' => __('Homepage Lists', 'enginethemes'),
        'description' => '',
        'priority' => 35,
        'panel' => 'mje_tb_panel'
    ),
    array(
        'name' => 'mje_diplomat_block_what',
        'title' => __('Block What', 'enginethemes'),
        'description' => __( 'Enter title and description into these following fields to give a brief introduction about your website, which is displayed below the homepage search.', 'enginethemes' ),
        'priority' => 35,
        'panel' => 'mje_tb_panel'
    ),
    array(
        'name' => 'mje_diplomat_block_why',
        'title' => __('Block Why', 'enginethemes'),
        'description' => __( 'This block allows you to show the core values, qualities, etc of your website/company.', 'enginethemes' ),
        'priority' => 35,
        'panel' => 'mje_tb_panel'
    ),
    array(
        'name' => 'mje_diplomat_block_how',
        'title' => __('Block How', 'enginethemes'),
        'description' => __( 'This block will give a quick explanation about main workflow in your website.', 'enginethemes' ),
        'priority' => 35,
        'panel' => 'mje_tb_panel'
    ),
    array(
        'name' => 'mje_diplomat_block_footer',
        'title' => __('Block Footer', 'enginethemes'),
        'description' => '',
        'priority' => 35,
        'panel' => 'mje_tb_panel'
    ),
    array(
        'name' => 'mje_posting_banner',
        'title' => __('mJob-posting Banner', 'enginethemes'),
        'description' => '',
        'priority' => 35,
        'panel' => 'mje_tb_panel'
    ),
);

// Customize options
$fields = array(
    /*******************
     * HOMEPAGE SEARCH *
     *******************/
    array(
        'setting_name' => 'mje_diplomat_slide_custom',
        'control_id' => 'mje_diplomat_slide_custom',
        'label' => __('Use custom slider', 'enginethemes'),
        'section' => 'mje_diplomat_block_search',
        'option_type' => 'theme_mod',
        'field_type' => 'checkbox',
        'default' => '',
        'description' => __( 'Tick the box if you would like to use your own sliders with custom images displaying in the search page. Otherwise, the default slides will be used. 
Add image sliders below.', 'enginethemes' ),
    ),
    array(
        'setting_name' => 'mje_diplomat_slide_1',
        'control_id' => 'mje_diplomat_slide_1',
        'label' => __('Slide image', 'enginethemes'),
        'section' => 'mje_diplomat_block_search',
        'option_type' => 'theme_mod',
        'field_type' => 'cropped_image',
        'default' => '',
        'description' => __('Choose a maximum of 5 slide images from your existing images in the media library or upload new ones. These background images are used for the main search section in your homepage. The optimal dimensions are 1920x548 pixels.', 'enginethemes'),
        'width' => 1920,
        'height' => 548
    ),
    array(
        'setting_name' => 'mje_diplomat_slide_2',
        'control_id' => 'mje_diplomat_slide_2',
        'label' => '',
        'section' => 'mje_diplomat_block_search',
        'option_type' => 'theme_mod',
        'field_type' => 'cropped_image',
        'default' => '',
        'description' => '',
        'width' => 1920,
        'height' => 548
    ),
    array(
        'setting_name' => 'mje_diplomat_slide_3',
        'control_id' => 'mje_diplomat_slide_3',
        'label' => '',
        'section' => 'mje_diplomat_block_search',
        'option_type' => 'theme_mod',
        'field_type' => 'cropped_image',
        'default' => '',
        'description' => '',
        'width' => 1920,
        'height' => 548
    ),
    array(
        'setting_name' => 'mje_diplomat_slide_4',
        'control_id' => 'mje_diplomat_slide_4',
        'label' => '',
        'section' => 'mje_diplomat_block_search',
        'option_type' => 'theme_mod',
        'field_type' => 'cropped_image',
        'default' => '',
        'description' => '',
        'width' => 1920,
        'height' => 548
    ),
    array(
        'setting_name' => 'mje_diplomat_slide_5',
        'control_id' => 'mje_diplomat_slide_5',
        'label' => '',
        'section' => 'mje_diplomat_block_search',
        'option_type' => 'theme_mod',
        'field_type' => 'cropped_image',
        'default' => '',
        'description' => '',
        'width' => 1920,
        'height' => 548
    ),
    array(
        'setting_name' => 'home_heading_title',
        'control_id' => 'home_heading_title',
        'label' => __('Heading title', 'enginethemes'),
        'section' => 'mje_diplomat_block_search',
        'option_type' => 'theme_mod',
        'field_type' => 'text',
        'default' => __('Get your stuffs done from $5', 'enginethemes'),
        'description' => ''
    ),
    array(
        'setting_name' => 'home_sub_title',
        'control_id' => 'home_sub_title',
        'label' => __('Sub title', 'enginethemes'),
        'section' => 'mje_diplomat_block_search',
        'option_type' => 'theme_mod',
        'field_type' => 'text',
        'default' => __('Browse through millions of micro jobs. Choose one you trust. Pay as you go.', 'enginethemes'),
        'description' => ''
    ),
    /*******************
     * BLOCK WHAT *
     *******************/
    array(
        'setting_name' => 'mje_diplomat_what_title',
        'control_id' => 'mje_diplomat_what_title',
        'label' => __('Title', 'enginethemes'),
        'section' => 'mje_diplomat_block_what',
        'option_type' => 'theme_mod',
        'field_type' => 'textarea',
        'default' => 'What is <p class="name-site"><span class="bold">Microjob</span> ENGINE?',
        'description' => __( 'HTML content is allowed.', 'enginethemes' )
    ),
    array(
        'setting_name' => 'mje_diplomat_what_desc',
        'control_id' => 'mje_diplomat_what_desc',
        'label' => __('Description', 'enginethemes'),
        'section' => 'mje_diplomat_block_what',
        'option_type' => 'theme_mod',
        'field_type' => 'textarea',
        'default' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam blandit feugiat molestie. Suspendisse ut eros diam. Maecenas mattis enim in tristique eleifend. Sed vel turpis sagittis, venenatis nunc a, interdum tortor. Duis tortor diam, vestibulum et mi ac, molestie iaculis sapien. Donec ac fermentum urna. Nullam quis erat vel dui commodo bibendum. Quisque posuere est dolor, vitae semper lorem scelerisque in. Donec semper volutpat turpis suscipit feugiat. Nam cursus vel libero sed dapibus. Cras commodo, ante id efficitur pharetra, tortor elit suscipit nunc, id faucibus turpis ex sed felis. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.',
        'description' => ''
    ),
    /*******************
     * BLOCK WHY       *
     *******************/
    // Item 1
    array(
        'setting_name' => 'mje_diplomat_why_title',
        'control_id' => 'mje_diplomat_why_title',
        'label' => __('Title', 'enginethemes'),
        'section' => 'mje_diplomat_block_why',
        'option_type' => 'theme_mod',
        'field_type' => 'text',
        'default' => __( 'Why works with MjE?', 'enginethemes' ),
        'description' => ''
    ),
    array(
        'setting_name' => 'mje_diplomat_why_item_1_img',
        'control_id' => 'mje_diplomat_why_item_1_img',
        'label' => __('Item 1', 'enginethemes'),
        'section' => 'mje_diplomat_block_why',
        'option_type' => 'theme_mod',
        'field_type' => 'cropped_image',
        'default' => '',
        'description' => __('Leave empty if you want to use the default icon. The optimal dimensions are 65x65 pixels.', 'enginethemes'),
        'width' => 65,
        'height' => 65
    ),
    array(
        'setting_name' => 'mje_diplomat_why_item_1_title',
        'control_id' => 'mje_diplomat_why_item_1_title',
        'label' => '',
        'section' => 'mje_diplomat_block_why',
        'option_type' => 'theme_mod',
        'field_type' => 'text',
        'default' => __( 'Top Safe', 'enginethemes' ),
        'description' => __( 'Title', 'enginethemes' )
    ),
    array(
        'setting_name' => 'mje_diplomat_why_item_1_desc',
        'control_id' => 'mje_diplomat_why_item_1_desc',
        'label' => '',
        'section' => 'mje_diplomat_block_why',
        'option_type' => 'theme_mod',
        'field_type' => 'textarea',
        'default' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cum ducimus enim id minus mollitia numquam odit placeat provident, quasi quis quod quos?',
        'description' => __( 'Description', 'enginethemes' )
    ),
    // Item 2
    array(
        'setting_name' => 'mje_diplomat_why_item_2_img',
        'control_id' => 'mje_diplomat_why_item_2_img',
        'label' => __('Item 2', 'enginethemes'),
        'section' => 'mje_diplomat_block_why',
        'option_type' => 'theme_mod',
        'field_type' => 'cropped_image',
        'default' => '',
        'description' => __('Leave empty if you want to use the default icon. The optimal dimensions are 65x65 pixels.', 'enginethemes'),
        'width' => 65,
        'height' => 65
    ),
    array(
        'setting_name' => 'mje_diplomat_why_item_2_title',
        'control_id' => 'mje_diplomat_why_item_2_title',
        'label' => '',
        'section' => 'mje_diplomat_block_why',
        'option_type' => 'theme_mod',
        'field_type' => 'text',
        'default' => __( 'Secured Transaction', 'enginethemes' ),
        'description' => __( 'Title', 'enginethemes' )
    ),
    array(
        'setting_name' => 'mje_diplomat_why_item_2_desc',
        'control_id' => 'mje_diplomat_why_item_2_desc',
        'label' => '',
        'section' => 'mje_diplomat_block_why',
        'option_type' => 'theme_mod',
        'field_type' => 'textarea',
        'default' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cum ducimus enim id minus mollitia numquam odit placeat provident, quasi quis quod quos?',
        'description' => __( 'Description', 'enginethemes' )
    ),
    // Item 2
    array(
        'setting_name' => 'mje_diplomat_why_item_3_img',
        'control_id' => 'mje_diplomat_why_item_3_img',
        'label' => __('Item 3', 'enginethemes'),
        'section' => 'mje_diplomat_block_why',
        'option_type' => 'theme_mod',
        'field_type' => 'cropped_image',
        'default' => '',
        'description' => __('Leave empty if you want to use the default icon. The optimal dimensions are 65x65 pixels.', 'enginethemes'),
        'width' => 65,
        'height' => 65
    ),
    array(
        'setting_name' => 'mje_diplomat_why_item_3_title',
        'control_id' => 'mje_diplomat_why_item_3_title',
        'label' => '',
        'section' => 'mje_diplomat_block_why',
        'option_type' => 'theme_mod',
        'field_type' => 'text',
        'default' => __( 'Top Sellers', 'enginethemes' ),
        'description' => __( 'Title', 'enginethemes' )
    ),
    array(
        'setting_name' => 'mje_diplomat_why_item_3_desc',
        'control_id' => 'mje_diplomat_why_item_3_desc',
        'label' => '',
        'section' => 'mje_diplomat_block_why',
        'option_type' => 'theme_mod',
        'field_type' => 'textarea',
        'default' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cum ducimus enim id minus mollitia numquam odit placeat provident, quasi quis quod quos?',
        'description' => __( 'Description', 'enginethemes' )
    ),

    /*******************
     * BLOCK HOW       *
     *******************/
    // Item 1
    array(
        'setting_name' => 'mje_diplomat_how_title',
        'control_id' => 'mje_diplomat_how_title',
        'label' => __('Title', 'enginethemes'),
        'section' => 'mje_diplomat_block_how',
        'option_type' => 'theme_mod',
        'field_type' => 'text',
        'default' => __( 'How MjE works?', 'enginethemes' ),
        'description' => ''
    ),
    array(
        'setting_name' => 'mje_diplomat_how_item_1_img',
        'control_id' => 'mje_diplomat_how_item_1_img',
        'label' => __('Item 1', 'enginethemes'),
        'section' => 'mje_diplomat_block_how',
        'option_type' => 'theme_mod',
        'field_type' => 'cropped_image',
        'default' => '',
        'description' => __( 'Leave empty if you want to use the default icon. The optimal dimensions are 85x85 pixels.', 'enginethemes' ),
        'width' => 85,
        'height' => 85
    ),
    array(
        'setting_name' => 'mje_diplomat_how_item_1_title',
        'control_id' => 'mje_diplomat_how_item_1_title',
        'label' => '',
        'section' => 'mje_diplomat_block_how',
        'option_type' => 'theme_mod',
        'field_type' => 'text',
        'default' => __( 'Enter your needs', 'enginethemes' ),
        'description' => __( 'Title', 'enginethemes' )
    ),
    array(
        'setting_name' => 'mje_diplomat_how_item_1_desc',
        'control_id' => 'mje_diplomat_how_item_1_desc',
        'label' => '',
        'section' => 'mje_diplomat_block_how',
        'option_type' => 'theme_mod',
        'field_type' => 'textarea',
        'default' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cum ducimus enim id minus mollitia numquam odit placeat provident, quasi quis quod quos?',
        'description' => __( 'Description', 'enginethemes' )
    ),
    // Item 2
    array(
        'setting_name' => 'mje_diplomat_how_item_2_img',
        'control_id' => 'mje_diplomat_how_item_2_img',
        'label' => __('Item 2', 'enginethemes'),
        'section' => 'mje_diplomat_block_how',
        'option_type' => 'theme_mod',
        'field_type' => 'cropped_image',
        'default' => '',
        'description' => __( 'Leave empty if you want to use the default icon. The optimal dimensions are 85x85 pixels.', 'enginethemes' ),
        'width' => 85,
        'height' => 85
    ),
    array(
        'setting_name' => 'mje_diplomat_how_item_2_title',
        'control_id' => 'mje_diplomat_how_item_2_title',
        'label' => '',
        'section' => 'mje_diplomat_block_how',
        'option_type' => 'theme_mod',
        'field_type' => 'text',
        'default' => __( 'Select your favorite seller', 'enginethemes' ),
        'description' => __( 'Title', 'enginethemes' )
    ),
    array(
        'setting_name' => 'mje_diplomat_how_item_2_desc',
        'control_id' => 'mje_diplomat_how_item_2_desc',
        'label' => '',
        'section' => 'mje_diplomat_block_how',
        'option_type' => 'theme_mod',
        'field_type' => 'textarea',
        'default' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cum ducimus enim id minus mollitia numquam odit placeat provident, quasi quis quod quos?',
        'description' => __( 'Description', 'enginethemes' )
    ),
    // Item 3
    array(
        'setting_name' => 'mje_diplomat_how_item_3_img',
        'control_id' => 'mje_diplomat_how_item_3_img',
        'label' => __('Item 3', 'enginethemes'),
        'section' => 'mje_diplomat_block_how',
        'option_type' => 'theme_mod',
        'field_type' => 'cropped_image',
        'default' => '',
        'description' => __( 'Leave empty if you want to use the default icon. The optimal dimensions are 85x85 pixels.', 'enginethemes' ),
        'width' => 85,
        'height' => 85
    ),
    array(
        'setting_name' => 'mje_diplomat_how_item_3_title',
        'control_id' => 'mje_diplomat_how_item_3_title',
        'label' => '',
        'section' => 'mje_diplomat_block_how',
        'option_type' => 'theme_mod',
        'field_type' => 'text',
        'default' => __( 'Get your stuffs done', 'enginethemes' ),
        'description' => __( 'Title', 'enginethemes' )
    ),
    array(
        'setting_name' => 'mje_diplomat_how_item_3_desc',
        'control_id' => 'mje_diplomat_how_item_3_desc',
        'label' => '',
        'section' => 'mje_diplomat_block_how',
        'option_type' => 'theme_mod',
        'field_type' => 'textarea',
        'default' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cum ducimus enim id minus mollitia numquam odit placeat provident, quasi quis quod quos?',
        'description' => __( 'Description', 'enginethemes' )
    ),

    /*******************
     * MJOB POSTING    *
     *******************/
    array(
        'setting_name' => 'mje_diplomat_footer_img',
        'control_id' => 'mje_diplomat_footer_img',
        'label' => __('Background', 'enginethemes'),
        'section' => 'mje_diplomat_block_footer',
        'option_type' => 'theme_mod',
        'field_type' => 'cropped_image',
        'default' => '',
        'description' => __('1920x395px'),
        'width' => 1920,
        'height' => 395
    ),
    array(
        'setting_name' => 'mje_diplomat_login_footer_heading_title',
        'control_id' => 'mje_diplomat_login_footer_heading_title',
        'label' => __('After login', 'enginethemes'),
        'section' => 'mje_diplomat_block_footer',
        'option_type' => 'theme_mod',
        'field_type' => 'text',
        'default' => __( 'Congrats! You are in!', 'enginethemes' ),
        'description' => __( 'Heading title', 'enginethemes' )
    ),
    array(
        'setting_name' => 'mje_diplomat_login_footer_sub_title',
        'control_id' => 'mje_diplomat_login_footer_sub_title',
        'label' => '',
        'section' => 'mje_diplomat_block_footer',
        'option_type' => 'theme_mod',
        'field_type' => 'text',
        'default' => __( 'Let\'s begin to get stuffs done in MicrojobEngine', 'enginethemes' ),
        'description' => __( 'Sub title', 'enginethemes' )
    ),
    array(
        'setting_name' => 'mje_diplomat_login_footer_button_text',
        'control_id' => 'mje_diplomat_login_footer_button_text',
        'label' => '',
        'section' => 'mje_diplomat_block_footer',
        'option_type' => 'theme_mod',
        'field_type' => 'text',
        'default' => __( 'GO TO DASHBOARD', 'enginethemes' ),
        'description' => __( 'Button text', 'enginethemes' )
    ),

    array(
        'setting_name' => 'mje_diplomat_non_login_footer_heading_title',
        'control_id' => 'mje_diplomat_non_login_footer_heading_title',
        'label' => __('Before login', 'enginethemes'),
        'section' => 'mje_diplomat_block_footer',
        'option_type' => 'theme_mod',
        'field_type' => 'text',
        'default' => __( 'Join Now', 'enginethemes' ),
        'description' => __( 'Heading title', 'enginethemes' )
    ),
    array(
        'setting_name' => 'mje_diplomat_non_login_footer_sub_title',
        'control_id' => 'mje_diplomat_non_login_footer_sub_title',
        'label' => '',
        'section' => 'mje_diplomat_block_footer',
        'option_type' => 'theme_mod',
        'field_type' => 'text',
        'default' => __( 'Thank you for showing an interest in MicrojobEngine', 'enginethemes' ),
        'description' => __( 'Sub title', 'enginethemes' )
    ),
    array(
        'setting_name' => 'mje_diplomat_non_login_footer_button_text',
        'control_id' => 'mje_diplomat_non_login_footer_button_text',
        'label' => '',
        'section' => 'mje_diplomat_block_footer',
        'option_type' => 'theme_mod',
        'field_type' => 'text',
        'default' => __( 'BE A MEMBER TODAY', 'enginethemes' ),
        'description' => __( 'Button text', 'enginethemes' )
    ),

    /*******************
     * MJOB POSTING    *
     *******************/
    array(
        'setting_name' => 'mje_disable_banner',
        'control_id' => 'mje_disable_banner',
        'label' => __('Disable mjob-posting banner', 'enginethemes'),
        'section' => 'mje_posting_banner',
        'option_type' => 'theme_mod',
        'field_type' => 'checkbox',
        'default' => '',
        'description' => __( 'Hide/Show mjob-posting banner.', 'enginethemes' ),
    ),
    array(
        'setting_name' => 'post_job_banner',
        'control_id' => 'post_job_banner',
        'label' => __('Background', 'enginethemes'),
        'section' => 'mje_posting_banner',
        'option_type' => 'theme_mod',
        'field_type' => 'cropped_image',
        'default' => '',
        'description' => __('This background image is used for the top banner in most pages to encourage users to post a mjob.', 'enginethemes'),
        'width' => 1920,
        'height' => 340
    ),
    array(
        'setting_name' => 'post_job_title',
        'control_id' => 'post_job_title',
        'label' => __('Title', 'enginethemes'),
        'section' => 'mje_posting_banner',
        'option_type' => 'theme_mod',
        'field_type' => 'text',
        'default' => __('Get your stuffs done from $5', 'enginethemes'),
        'description' => ''
    ),

    /*******************
     * OTHER TITLE BLOCK *
     *******************/
    array(
        'setting_name' => 'mje_other_title_service',
        'control_id' => 'mje_other_title_service',
        'label' => __('Title for services block', 'enginethemes'),
        'section' => 'mje_other_title',
        'option_type' => 'theme_mod',
        'field_type' => 'text',
        'default' => __('Latest Microjobs', 'enginethemes'),
        'description' => ''
    ),
    array(
        'setting_name' => 'mje_other_title_category',
        'control_id' => 'mje_other_title_category',
        'label' => __('Title for categories block', 'enginethemes'),
        'section' => 'mje_other_title',
        'option_type' => 'theme_mod',
        'field_type' => 'text',
        'default' => __('Job Categories', 'enginethemes'),
        'description' => ''
    ),
);

// Customize styles
ob_start();
?>
    <style type="text/css">
        <?php
            $footer_image_theme_mod = get_theme_mod( 'mje_diplomat_footer_img' );
            $footer_image = wp_get_attachment_image_src( $footer_image_theme_mod, array( 1920, 395 ) );
            if( $footer_image ) {
                ?>
                 .diplomat .block-login {
                     background: url(<?php echo $footer_image[0] ?>) no-repeat center center;
                     background-size: cover;
                 }
                <?php
            }
        ?>

        <?php
            // Background color
            AE_Customizer::ae_customize_generate_css(
                '.diplomat .btn-diplomat, .diplomat .bg-customize',
                'background',
                'diplomat_primary_color'
            );

            AE_Customizer::ae_customize_generate_css(
                '.block-intro-how .text-information .icon',
                'background',
                'diplomat_primary_color_shadow'
            );

            // Color
            AE_Customizer::ae_customize_generate_css(
                '.diplomat .color-customize, .block-intro-how .steps .number-steps',
                'color',
                'diplomat_primary_color'
            );

            // Border color
            AE_Customizer::ae_customize_generate_css(
                '.block-intro-how .steps .number-steps, .block-intro-how .steps:before, .block-intro-how .text-information .steps-line',
                'border-color',
                'diplomat_primary_color'
            );
        ?>
    </style>
<?php
$styles = ob_get_clean();

// Init customizer
new AE_Customizer($fields, $sections, $panels, $styles);