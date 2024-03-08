<?php
class MJE_Skin_Action extends AE_Base
{
    public static $skin_name;

    public function __construct() {
        self::$skin_name = ae_get_option('mjob_skin_name', 'default');

        $this->add_filter( 'template_include', 'load_skin_template' );
        $this->add_action( 'wp_enqueue_scripts', 'load_skin_assets' );
        $this->add_action( 'customize_preview_init', 'load_live_preview_script' );

        $this->add_action( 'ae_save_option', 'update_color_after_select_skin', 10, 2);

        /* Load customize */
        if( self::is_skin_active() )
            require_once TEMPLATEPATH . '/skins/' . self::$skin_name . '/customize/customize.php';
        else
            require_once TEMPLATEPATH . '/includes/customize.php';
    }

    /**
     * Load skins
     * @param string $template
     * @return string $new_template
     * @since 1.1.4
     * @author Tat Thien
    */
    public function load_skin_template($template ) {
        $new_template = $template;
        $file_name = basename( $template );
        if( is_admin() ) return $template;
        // Return default template if skin is empty or skin is default
        if( empty( self::$skin_name ) || self::$skin_name == 'default' )
            return $template;
        else {
            $child_path = get_stylesheet_directory() . '/skins/' . self::$skin_name . '/' . $file_name;
            $parent_path = get_template_directory() . '/skins/' . self::$skin_name . '/' . $file_name;
            if( file_exists( $child_path ) ) {
                $new_template = $child_path;
            } else if( file_exists( $parent_path ) ) {
                $new_template = $parent_path;
            }
        }
        return $new_template;
    }

    /**
     * If skin is activated return true
     *
     * @param void
     * @return boolean
     * @since 1.1.4
     * @author Tat Thien
     */
    public static function is_skin_active() {
        if( ! empty( self::$skin_name ) && self::$skin_name != 'default' )
            return true;
        else
            return false;
    }

    /**
     * Load load styles and scripts for skin
     * @param void
     * @return void
     * @since 1.1.4
     * @author Tat Thien
     */
    public function load_skin_assets() {
        if( ! empty( self::$skin_name ) && self::$skin_name != 'default' ) {
            $skin_css_url = "/skins/" . self::$skin_name . "/assets/css/main.css";
            $skin_js_url = "/skins/" . self::$skin_name . "/assets/js/" . self::$skin_name . ".js";
            wp_enqueue_style(
                "skin-" . self::$skin_name,
                get_template_directory_uri() . $skin_css_url, array(),
                ET_VERSION,
                'all'
            );

            wp_enqueue_script('slider', get_template_directory_uri() . "/skins/" . self::$skin_name . "/assets/js/slider.js", array(), ET_VERSION, true);

            wp_enqueue_script(
                "skin-" . self::$skin_name . "-js",
                get_template_directory_uri() . $skin_js_url,
                array(
                    'jquery',
                    'underscore',
                    'backbone',
                    'appengine',
                ),
                ET_VERSION,
                true
            );
        }
    }

    /**
     * Load script for customize live preview
     * @param void
     * @return void
     * @since 1.1.4
     * @author Tat Thien
     */
    public function load_live_preview_script() {
        wp_enqueue_script( self::$skin_name . '-live-preview',
            get_template_directory_uri() . '/skins/' . self::$skin_name . '/customize/live-preview.js',
            array('jquery', 'appengine', 'customize-preview', 'ae-theme-customizer'),
            ET_VERSION,
            true
        );
    }

    /**
     * Get skins data
     * @param void
     * @return array skin data
     * @since 1.1.4
     * @author Tat Thien
    */
    public static function get_skins() {
        return array(
            array(
                'title' => 'Default',
                'name' => 'default',
                'desc' => __( 'If you do not select any skin, this skin will be used by default.', 'enginethemes' ),
                'thumbnail' => TEMPLATEURL . '/assets/img/skins/default.jpg',
                'preview' => TEMPLATEURL . '/assets/img/skins/preview-default.jpg',
            ),
            array(
                'title' => 'Diplomat',
                'name' => 'diplomat',
                'desc' => __( 'Being derived inspiration from Diplomats personality type in MBTI test, the diplomat skin is designed for those who tend to be warm, compassionate and gregarious individuals.', 'enginethemes' ),
                'thumbnail' => TEMPLATEURL . '/assets/img/skins/diplomat.jpg',
                'preview' => TEMPLATEURL . '/assets/img/skins/preview-diplomat.jpg',
            )
        );
    }

    /**
     * Get current skin name
     *
     * @param void
     * @return string skin name
     * @since 1.1.4ien
     * @author Tat Th
     */
    public static function get_skin_name() {
        if( self::is_skin_active() ) {
            return self::$skin_name;
        } else {
            return 'default';
        }
    }

    /**
     * Get skin assets path
     *
     * @param void
     * @return string  path to skin assets
     * @since 1.1.4
     * @author Tat Thien
     */
    public static function get_skin_assets_path() {
        if( self::is_skin_active() ) {
            return get_template_directory_uri() . '/skins/' . self::$skin_name . '/assets';
        } else {
            return get_template_directory_uri() . '/assets/';
        }
    }

    /**
     * Update colors when choose skin
     *
     * @param string $name
     * @param string $value
     * @since 1.1.4
     * @author Tat Thien
     */
    public function update_color_after_select_skin($name, $value ) {
        if( $name == 'mjob_skin_name' && $value != 'default' ) {
            // If active skin on the first time
            if( ! get_option( 'is_previous_active_' . $value ) ) {
                set_theme_mod( $value .'_primary_color', '#27ae60' );
                set_theme_mod( $value . '_primary_color_shadow', mje_convert_hex_to_rgb( '#27ae60', 0.7 ) );
                set_theme_mod( $value . '_header_color', '#fff' );
                set_theme_mod( $value . '_footer_color', '#06100a' );
                update_option( 'is_previous_active_' . $value, true );
            }
        }
    }
}

new MJE_Skin_Action();