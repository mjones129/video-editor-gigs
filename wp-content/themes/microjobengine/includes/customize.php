<?php
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
        'name' => 'image_backgrounds',
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
        'name' => 'mje_homepage_about_general',
        'title' => __('About - General', 'enginethemes'),
        'description' => '',
        'priority' => 35,
        'panel' => 'mje_tb_panel'
    ),
    array(
        'name' => 'mje_homepage_about_block_1',
        'title' => __('About - Block 1', 'enginethemes'),
        'description' => '',
        'priority' => 35,
        'panel' => 'mje_tb_panel'
    ),
    array(
        'name' => 'mje_homepage_about_block_2',
        'title' => __('About - Block 2', 'enginethemes'),
        'description' => '',
        'priority' => 35,
        'panel' => 'mje_tb_panel'
    ),
    array(
        'name' => 'mje_homepage_about_block_3',
        'title' => __('About - Block 3', 'enginethemes'),
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
     * SITE COLOR      *
     *******************/
    array(
        'setting_name' => 'primary_color',
        'control_id' => 'primary_color',
        'label' => __('Primary Color', 'enginethemes'),
        'section' => 'colors',
        'option_type' => 'theme_mod',
        'field_type' => 'color',
        'default' => '#10a2ef'
    ),
    array(
        'setting_name' => 'header_color',
        'control_id' => 'header_color',
        'label' => __('Header Color', 'enginethemes'),
        'section' => 'colors',
        'option_type' => 'theme_mod',
        'field_type' => 'color',
        'default' => '#ffffff'
    ),
    array(
        'setting_name' => 'footer_color',
        'control_id' => 'footer_color',
        'label' => __('Footer Color', 'enginethemes'),
        'section' => 'colors',
        'option_type' => 'theme_mod',
        'field_type' => 'color',
        'default' => '#2a394e'
    ),
    /*******************
     * HOMEPAGE SEARCH *
     *******************/
    array(
        'setting_name' => 'search_background',
        'control_id' => 'search_background',
        'label' => __('Background', 'enginethemes'),
        'section' => 'image_backgrounds',
        'option_type' => 'theme_mod',
        'field_type' => 'cropped_image',
        'default' => '',
            'description' => __('This background image is used for the main search section in your homepage.', 'enginethemes'),
        'width' => 1920,
        'height' => 1080
    ),
    array(
        'setting_name' => 'home_heading_title',
        'control_id' => 'home_heading_title',
        'label' => __('Heading title', 'enginethemes'),
        'section' => 'image_backgrounds',
        'option_type' => 'theme_mod',
        'field_type' => 'text',
        'default' => __('Get your stuffs done from $5', 'enginethemes'),
        'description' => ''
    ),
    array(
        'setting_name' => 'home_sub_title',
        'control_id' => 'home_sub_title',
        'label' => __('Sub title', 'enginethemes'),
        'section' => 'image_backgrounds',
        'option_type' => 'theme_mod',
        'field_type' => 'text',
        'default' => __('Browse through millions of micro jobs. Choose one you trust. Pay as you go.', 'enginethemes'),
        'description' => ''
    ),
    /*******************
     *  ABOUT SETTINGS *
     *******************/
    array( // Footer background
        'setting_name' => 'footer_background',
        'control_id' => 'footer_background',
        'label' => __('Before-footer background', 'enginethemes'),
        'section' => 'mje_homepage_about_general',
        'option_type' => 'theme_mod',
        'field_type' => 'cropped_image',
        'default' => '',
        'description' => __('This background image is used for the homepage before the footer section. It should be a light color to make sure the readability of the front content.', 'enginethemes'),
        'width' => 1920,
        'height' => 340
    ),
    array(
        'setting_name' => 'about_title',
        'control_id' => 'about_title',
        'label' => __('Title', 'enginethemes'),
        'section' => 'mje_homepage_about_general',
        'option_type' => 'theme_mod',
        'field_type' => 'text',
        'default' => __('ABOUT MICROJOB ENGINE', 'enginethemes'),
        'description' =>'',
    ),
    array(
        'setting_name' => 'about_link',
        'control_id' => 'about_link',
        'label' => __('Find out more url', 'enginethemes'),
        'section' => 'mje_homepage_about_general',
        'option_type' => 'theme_mod',
        'field_type' => 'text',
        'default' => '',
        'description' => '',
    ),
        // Block 1
        array(
            'setting_name' => 'about_col_1_icon',
            'control_id' => 'about_col_1_icon',
            'label' => __('Icon', 'enginethemes'),
            'section' => 'mje_homepage_about_block_1',
            'option_type' => 'theme_mod',
            'field_type' => 'cropped_image',
            'default' => '',
            'description' => __('The optimal dimensions are 60x60 pixels.', 'enginethemes'),
            'width' => 60,
            'height' => 60
        ),
        array(
            'setting_name' => 'about_col_1_title',
            'control_id' => 'about_col_1_title',
            'label' => __('Title', 'enginethemes'),
            'section' => 'mje_homepage_about_block_1',
            'option_type' => 'theme_mod',
            'field_type' => 'text',
            'default' => __('Effortless shopping', 'enginethemes'),
            'description' => ''
        ),
        array(
            'setting_name' => 'about_col_1_link',
            'control_id' => 'about_col_1_link',
            'label' => __('Link', 'enginethemes'),
            'section' => 'mje_homepage_about_block_1',
            'option_type' => 'theme_mod',
            'field_type' => 'text',
            'default' => '',
            'description' => ''
        ),
        array(
            'setting_name' => 'about_col_1_desc',
            'control_id' => 'about_col_1_desc',
            'label' => __('Description', 'enginethemes'),
            'section' => 'mje_homepage_about_block_1',
            'option_type' => 'theme_mod',
            'field_type' => 'textarea',
            'default' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Atque dicta dolorem odit optio placeat praesentium quos reiciendis reprehenderit soluta ullam?',
            'description' => ''
        ),
        // Block 2
        array(
            'setting_name' => 'about_col_2_icon',
            'control_id' => 'about_col_2_icon',
            'label' => __('Icon', 'enginethemes'),
            'section' => 'mje_homepage_about_block_2',
            'option_type' => 'theme_mod',
            'field_type' => 'cropped_image',
            'default' => '',
            'description' => __('The optimal dimensions are 60x60 pixels.', 'enginethemes'),
            'width' => 60,
            'height' => 60
        ),
        array(
            'setting_name' => 'about_col_2_title',
            'control_id' => 'about_col_2_title',
            'label' => __('Title', 'enginethemes'),
            'section' => 'mje_homepage_about_block_2',
            'option_type' => 'theme_mod',
            'field_type' => 'text',
            'default' => __('Be tagged and follow', 'enginethemes'),
            'description' => ''
        ),
        array(
            'setting_name' => 'about_col_2_link',
            'control_id' => 'about_col_2_link',
            'label' => __('Link', 'enginethemes'),
            'section' => 'mje_homepage_about_block_2',
            'option_type' => 'theme_mod',
            'field_type' => 'text',
            'default' => '',
            'description' => ''
        ),
        array(
            'setting_name' => 'about_col_2_desc',
            'control_id' => 'about_col_2_desc',
            'label' => __('Description', 'enginethemes'),
            'section' => 'mje_homepage_about_block_2',
            'option_type' => 'theme_mod',
            'field_type' => 'textarea',
            'default' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Atque dicta dolorem odit optio placeat praesentium quos reiciendis reprehenderit soluta ullam?',
            'description' => ''
        ),
        // Block 3
        array(
            'setting_name' => 'about_col_3_icon',
            'control_id' => 'about_col_3_icon',
            'label' => __('Icon', 'enginethemes'),
            'section' => 'mje_homepage_about_block_3',
            'option_type' => 'theme_mod',
            'field_type' => 'cropped_image',
            'default' => '',
            'description' => __('The optimal dimensions are 60x60 pixels.', 'enginethemes'),
            'width' => 60,
            'height' => 60
        ),
        array(
            'setting_name' => 'about_col_3_title',
            'control_id' => 'about_col_3_title',
            'label' => __('Title', 'enginethemes'),
            'section' => 'mje_homepage_about_block_3',
            'option_type' => 'theme_mod',
            'field_type' => 'text',
            'default' => __('Paid highly', 'enginethemes'),
            'description' => ''
        ),
        array(
            'setting_name' => 'about_col_3_link',
            'control_id' => 'about_col_3_link',
            'label' => __('Link', 'enginethemes'),
            'section' => 'mje_homepage_about_block_3',
            'option_type' => 'theme_mod',
            'field_type' => 'text',
            'default' => '',
            'description' => ''
        ),
        array(
            'setting_name' => 'about_col_3_desc',
            'control_id' => 'about_col_3_desc',
            'label' => __('Description', 'enginethemes'),
            'section' => 'mje_homepage_about_block_3',
            'option_type' => 'theme_mod',
            'field_type' => 'textarea',
            'default' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Atque dicta dolorem odit optio placeat praesentium quos reiciendis reprehenderit soluta ullam?',
            'description' => ''
        ),
    /*******************
     * POST MJOB BLOCK *
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
        'setting_name' => 'mje_other_title_category',
        'control_id' => 'mje_other_title_category',
        'label' => __('Title for categories block', 'enginethemes'),
        'section' => 'mje_other_title',
        'option_type' => 'theme_mod',
        'field_type' => 'text',
        'default' => __('FIND WHAT YOU NEED', 'enginethemes'),
        'description' => ''
    ),
    array(
        'setting_name' => 'mje_other_title_service',
        'control_id' => 'mje_other_title_service',
        'label' => __('Title for services block', 'enginethemes'),
        'section' => 'mje_other_title',
        'option_type' => 'theme_mod',
        'field_type' => 'text',
        'default' => __('LATEST MICROJOBS', 'enginethemes'),
        'description' => ''
    ),
);

// Customize styles
ob_start();
?>
    <style type="text/css">
        <?php
            $footer_image = ae_get_option('footer_background');
            $footer_image_theme_mod = get_theme_mod('footer_background');
            if(!empty($footer_image) || false !== $footer_image) {
                ?>
                 .block-intro {
                     background: url(<?php echo $footer_image['full'][0] ?>) no-repeat center center;
                     background-size: cover;
                 }
                <?php
            } elseif (empty($footer_image) && false !== $footer_image_theme_mod) {
                ?>
                .block-intro {
                    background: url();
                }
                <?php
            }
        ?>
    </style>
<?php
$styles = ob_get_clean();

// Init customizer
new AE_Customizer($fields, $sections, $panels, $styles);