<?php
if( ! function_exists('mjob_setting_translations')) {
    function mjob_setting_translations() {
        return array(
            'args' => array(
                'title' => __("Translations", 'enginethemes') ,
                'id' => 'language-settings',
                'icon' => 'G',
                'class' => ''
            ) ,

            'groups' => array(
                array(
                    'args' => array(
                        'title' => __("Website Language", 'enginethemes') ,
                        'id' => '',
                        'class' => '',
                        'name' => '',
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'forgotpass_mail_template',
                            'type' => 'language_list',
                            'title' => __("Choose your language", 'enginethemes'),
                            'desc' => __("Select the language you want to use for your website.", 'enginethemes'),
                            'name' => 'website_language',
                            'class' => ''
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Translator", 'enginethemes') ,
                        'id' => '',
                        'class' => '',
                        'name' => '',
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'translator-field',
                            'type' => 'translator',
                            'title' => __("Translate a language", 'enginethemes') ,
                            'desc' => __("You should save your translation after every 20 strings to make sure they won't be lost", 'enginethemes'),
                            'name' => 'translate',
                            'class' => ''
                        ),
                        array(
                            'id' => 'translator-form-field',
                            'type' => 'translator_form',
                            'title' => '',
                            'name' => 'translator-form',
                            'class' => ''
                        )
                    )
                ),
            )
        );
    }
}

// Render translator form
add_filter('ae_render_field_out_of_column', 'mJobRenderTranslatorForm', 10, 1);
function mJobRenderTranslatorForm($restrict_field_ids) {
    $restrict_field_ids = array_merge($restrict_field_ids, array('translator-form-field'));
    return $restrict_field_ids;
}