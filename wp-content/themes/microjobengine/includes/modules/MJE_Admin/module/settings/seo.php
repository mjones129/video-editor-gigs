<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 8/2/2016
 * Time: 15:09
 */
if( ! function_exists('mjob_setting_seo')) {
    function mjob_setting_seo() {
        return array(
            'args' => array(
                'title' => __("SEO", 'enginethemes') ,
                'id' => 'seo',
                'icon' => 'i',
                'class' => ''
            ) ,
            /* Slug settings */
            'groups' => array(
                array(
                    'args' => array(
                        'title' => __("Slug Settings", 'enginethemes') ,
                        'id' => '',
                        'class' => '',
                        'name' => '',
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'mjob_post_archive',
                            'type' => 'text',
                            'title' => __("Listing microjob page Slug", 'enginethemes') ,
                            'desc' => __("Enter a string to customize the micro job listing permalink structure slug.", 'enginethemes') ,
                            'name' => 'mjob_post_archive',
                            'placeholder' => __("Listing microjob page Slug", 'enginethemes') ,
                            'class' => 'option-item bg-grey-input ',
                            'default' => 'mjob_post'
                        ),
                        array(
                            'id' => 'mjob_post_slug',
                            'type' => 'text',
                            'title' => __("Single microjob page Slug", 'enginethemes') ,
                            'desc' => __("Enter a string to customize the micro job single permalink structure slug.", 'enginethemes') ,
                            'name' => 'mjob_post_slug',
                            'placeholder' => __("Single microjob page Slug", 'enginethemes') ,
                            'class' => 'option-item bg-grey-input ',
                            'default' => 'mjob_post'
                        ),
                        array(
                            'id' => 'mjob_category_slug',
                            'type' => 'text',
                            'title' => __("Microjob category page Slug", 'enginethemes') ,
                            'desc' => __("Enter a string to customize the micro job category permalink structure slug.", 'enginethemes') ,
                            'name' => 'mjob_category_slug',
                            'placeholder' => __("Microjob category page Slug", 'enginethemes') ,
                            'class' => 'option-item bg-grey-input ',
                            'default' => 'mjob_category',
                        ),
                        array(
                            'id' => 'skill_slug',
                            'type' => 'text',
                            'title' => __("Skill tag page Slug", 'enginethemes') ,
                            'desc' => __("Enter a string to customize the tag permalink structure slug.", 'enginethemes') ,
                            'name' => 'skill_slug',
                            'placeholder' => __("Microjob tag page Slug", 'enginethemes') ,
                            'class' => 'option-item bg-grey-input ',
                            'default' => 'skill'
                        )
                    )
                ),
                /* Google Analytics */
                array(
                    'args' => array(
                        'title' => __("Google Analytics Script", 'enginethemes') ,
                        'id' => '',
                        'class' => '',
                        'name' => '',
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'opt-ace-editor-js',
                            'type' => 'textarea',
                            'title' => __("Google Analytics Script", 'enginethemes') ,
                            'desc' => __("Google analytics is a service offered by Google that generates detailed statistics about the visits to a website.", 'enginethemes'),
                            'name' => 'google_analytics',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ),
            )
        );
    }
}