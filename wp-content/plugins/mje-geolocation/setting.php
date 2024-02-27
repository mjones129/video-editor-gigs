<?php
if( class_exists('AE_Base') ){

    class MjeGeoSettings extends AE_Base {
        /**
         *
         */
        public $prefix = '';
        public $lang_code = array();
        /**
         * mje_geo_Admin constructor.
         */
        public function __construct() {
            $this->prefix = 'mje_geolocation';
            $this->add_action( 'ae_admin_menu_pages', 'add_admin_menu_pages' );
            global $lang_code;
            $this->lang_code = $lang_code;
        }

        /**
         * Add settings page for Stripe
         *
         * @param array $pages
         * @return array $pages
         * @since 1.0.0
         * @author danng
         */
        public function add_admin_menu_pages( $pages ) {
            $options = AE_Options::get_instance();
            $temp = array();
            $sections = array();
            $sections['general'] = $this->get_general_section();

            // Generate sections
            foreach ( $sections as $section ) {
                $temp[] = new AE_section( $section['args'], $section['groups'], $options );
            }

            // Create container
            $bookmark_container = new AE_Container( array(
                'class' => '',
                'id' => 'settings'
            ), $temp, '' );

            // Create page
            $pages['mje-geolocation'] = array(
                'args' => array(
                    'parent_slug' => 'et-welcome',
                    'page_title' => __( 'Mjob Geolocation', 'mje_geo' ),
                    'menu_title' => __( 'Mjob Geolocation', 'mje_geo' ),
                    'cap' => 'administrator',
                    'slug' => 'et-mje-geolocation',
                    'icon' => 'fa fa-map',
                    'desc' => __( 'An extension for MicrojobEngine', 'mje_geo' )
                ),
                'container' => $bookmark_container
            );

            return $pages;
        }

        /**
         * Generate general settings section
         *
         * @param void
         * @return array $sections
         * @since 1.0
         * @author Danng
         */

        public function get_general_section () {
            $sections = array(
                'args' => array(
                    'title' => __( 'General Settings', 'mje_geo' ),
                    'id' => 'ms-general',
                    'class' => '',
                    'icon' => '',
                ),
                'groups' => array(

                    array(
                       'args' => array(
                            'title' => __( 'General', 'mje_geo' ),
                            'id' => '',
                            'class' => '',
                            'desc' => ''
                        ),
                        'fields' => array(
                            array(
                                'id' => 'mje_geo_map_api_key',
                                'name' => 'mje_geo_map_api_key',
                                'class' => '',
                                'type' => 'text',
                                'title' => __( 'Google Map API Key', 'mje_geo' ),
                                'desc' => __( 'Make sure you have activated “Maps JavaScript API” as well as “Geocoding API” Library', 'mje_geo')
                            ),
                            /* disable in beta version 1.0
                            array(
                                'id' => 'mjegeo_distance',
                                'class' => '',
                                'type' => 'number',
                                'title' => __( 'Max Distance Filter', 'mje_geo' ),
                                'desc' => __( 'Distance Filter Default In the Search Bar', 'mje_geo' ),
                                'name' => 'mjegeo_distance',
                                'default' => 1000,
                                'step' => 10,

                            ),
                            array(
                                'id' => 'mjegeo_unit',
                                'class' => '',
                                'type' => 'select',
                                'title' => __( 'Distance unit', 'mje_geo' ),
                                'desc' => __( 'Distance unit', 'mje_geo'),
                                'name' => 'mjegeo_unit',
                                'data' => array('MILES','KM'),
                                'default' => 'MILES',

                            ),
                            */
                            array(
                                'id' => 'mjegeo_lang_code',
                                'class' => '',
                                'type' => 'select',
                                'title' => __( 'Map language', 'mje_geo' ),
                                'desc' => __( 'Map language', 'mje_geo'),
                                'name' => 'mjegeo_lang_code',
                                'data' => $this->lang_code,
                                'default' => 'en',

                            ),
                            array(
                                'id' => 'mjegeo_hover',
                                'class' => '',
                                'type' => 'switch',
                                'title' => __( 'Hover mjob event', 'mje_geo' ),
                                'desc' => __( 'Show full address of mjob when hover the mjob', 'mje_geo' ),
                                'name' => 'mjegeo_hover',
                                'default' => true,
                            ),


                            // array(
                            //     'id' => 'mjegeo_debug',
                            //     'class' => '',
                            //     'type' => 'switch',
                            //     'title' => __( 'Debug Mode', 'mje_geo' ),
                            //     'desc' => __( 'Debug Mode', 'mje_geo'),
                            //     'name' => 'mjegeo_debug',
                            // ),

                        )
                    ),
                )
            );

            return $sections;
        }
    }
    new MjeGeoSettings();

}