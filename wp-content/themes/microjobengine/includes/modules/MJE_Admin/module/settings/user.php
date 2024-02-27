<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 8/2/2016
 * Time: 13:06
 */
if( ! function_exists('mjob_setting_user')) {
    function mjob_setting_user() {
        $authentication_page = ae_get_social_connect_page_link();
        $query          = new WP_Query( array( 's' => '[social_connect_page]' ) );
        $list_pages     = array();
        $list_pages[]   = __('Select Social Connect Page','enginethemes');
        if($query->have_posts()){
            while($query->have_posts()){
                $query->the_post();
                global $post;
                $list_pages[$post->ID] = $post->post_title;
            }
        }
        wp_reset_query();

        return array(
            'args' => array(
                'title' => __("Users", 'enginethemes'),
                'id' => 'users-setting',
                'icon' => 'y',
                'class' => ''
            ),
            'groups' => array(
                array(
                    'args' => array(
                        'title' => __("User default avatar", 'enginethemes') ,
                        'id' => '',
                        'class' => '',
                        'name' => '',
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'opt-ace-editor-js',
                            'type' => 'image',
                            'title' => __("Avatar setting", 'enginethemes') ,
                            'desc' => __("Your logo should be in PNG, GIF or JPG format, within 87x87px and less than 1500Kb.", 'enginethemes'),
                            'name' => 'default_avatar',
                            'class' => '',
                            'default' => get_template_directory_uri().'/assets/img/avatar.png',
                            'size' => array(
                                '78',
                                '78'
                            )
                        ),
                    )
                ),
                /* Authentication */
                array(
                    'args' => array(
                        'title' => __("Authentication", 'enginethemes') ,
                        'id' => '',
                        'class' => '',
                        'name' => '',
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'sign_up_intro_text',
                            'type' => 'editor',
                            'title' => __("Sign up introduction text", 'enginethemes') ,
                            'desc' => __("Write a brief description to promote your signup process.", 'enginethemes'),
                            'name' => 'sign_up_intro_text',
                            'class' => '',
                            'reset' => 1
                        ),
                        array(
                            'id' => 'user_confirm',
                            'type' => 'switch',
                            'title' => __("Email confirmation", 'enginethemes') ,
                            'desc' => __("Enabling this will require users to confirm their email addresses after registration.", 'enginethemes'),
                            'name' => 'user_confirm',
                            'class' => ''
                        ),
                    )
                ),
                /*User profile settings*/
                array(
                    'args' => array(
                        'title' => __( "User profile", "enginethemes" ) ,
                        'id' => '',
                        'class' => '',
                        'name' => '',
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'user_local_timezone',
                            'type' => 'switch',
                            'title' => __("Local timezone", 'enginethemes') ,
                            'desc' => __("Enabling this will allow users to select their local timezone.", 'enginethemes'),
                            'name' => 'user_local_timezone',
                            'class' => '',
                            'default' => 'enable'
                        ),
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("General setting social Login", 'enginethemes'),
                        'desc' => __( 'Set up a way for users to log in via their social network accounts', 'enginethemes' ),
                        'id' => '',
                        'class' => '',
                        'name' => ''
                    ),
                    'fields' => array(
                        array(
                            'id' => 'social_connect',
                            'type' => 'multi_input',
                            'search' => false,
                            'multiple' => false,
                            'title' => __("Social connect page url ", 'enginethemes'),
                            'desc' => __( 'You can create a new page and paste the shortcode <code>[social_connect_page]</code> here', 'enginethemes' ),
                            'name' => 'social_connect',
                            'data' => $list_pages,
                            //'placeholder' => __("eg: http://yourdomain.com/social-connect", 'enginethemes') ,
                            'class' => '',
                            'default' => $authentication_page
                        )
                    )
                ),
                /* Config Social Login API*/
                array(
                    'args' => array(
                        'title' => __("Config Social Login API", 'enginethemes') ,
                        'desc'=> __( 'Setup a way for users to login via their social network accounts', 'enginethemes' ),
                        'id' => '',
                        'class' => '',
                        'name' => '',
                    ),
                    'fields' => array(
                        /* Twitter login api */
                        array(
                            'id' => 'twitter_api',
                            'name' => '',
                            'title' => __("Twitter API ", 'enginethemes') ,
                            'desc' => __( 'This allows users to log into your site via their Twitter account. Visit <a href="https://apps.twitter.com/" target="_blank">here</a> to create new apps on Twitter.', 'enginethemes' ),
                            'type' => 'combine',
                            'class' => 'field-social-api',
                            'children' => array(
                                array(
                                    'id' => 'twitter_login',
                                    'type' => 'switch',
                                    'title' => __("Enable Twitter API", 'enginethemes') ,
                                    'desc' => __("Enabling this will allow users to log in via Twitter", 'enginethemes'),
                                    'name' => 'twitter_login',
                                    'class' => ''
                                ),
                                array(
                                    'id' => 'et_twitter_key',
                                    'type' => 'text',
                                    'title' => __("Twitter key ", 'enginethemes') ,
                                    'name' => 'et_twitter_key',
                                    'placeholder' => __("Twitter Consumer Key", 'enginethemes') ,
                                    'class' => '',
                                ),
                                array(
                                    'id' => 'et_twitter_secret',
                                    'type' => 'text',
                                    'title' => __("Twitter secret ", 'enginethemes') ,
                                    'name' => 'et_twitter_secret',
                                    'placeholder' => __("Twitter Consumer Secret", 'enginethemes') ,
                                    'class' => '',
                                )
                            )
                        ) ,
                        /* Facebook login api */
                        array(
                            'id' => 'facebook_api',
                            'name' => '',
                            'title' => __("Facebook API ", 'enginethemes') ,
                            'desc' => __( 'This allows users to log into your site via their Facebook account. Visit <a href="https://developers.facebook.com/" target="_blank">here</a> to upgrade your personal account to a Facebook Developer account and create a new Facebook app.', 'enginethemes' ),
                            'type' => 'combine',
                            'class' => 'field-social-api',
                            'children' => array(
                                array(
                                    'id' => 'facebook_login',
                                    'type' => 'switch',
                                    'title' => __("Enable Facebook API", 'enginethemes') ,
                                    'desc' => __("Enabling this will allow users to log in via Facebook", 'enginethemes'),
                                    'name' => 'facebook_login',
                                    'class' => ''
                                ),
                                array(
                                    'id' => 'et_facebook_key',
                                    'type' => 'text',
                                    'title' => __("Facebook key", 'enginethemes') ,
                                    'name' => 'et_facebook_key',
                                    'placeholder' => __("Facebook Application ID", 'enginethemes') ,
                                    'class' => '',
                                ),
                                array(
                                    'id' => 'et_facebook_secret_key',
                                    'type' => 'text',
                                    'title' => __("Facebook secret key", 'enginethemes') ,
                                    'name' => 'et_facebook_secret_key',
                                    'placeholder' => __("Facebook Secret Key", 'enginethemes') ,
                                    'class' => '',
                                )
                            )
                        ),
                        /* Google login api */
                        array(
                            'id' => 'google_api',
                            'name' => '',
                            'title' => __("Google API", 'enginethemes') ,
                            'desc' => __( 'This allows users to log into your site via their Google account. Visit <a href="https://console.developers.google.com/projectselector/apis/library?pli=1" target="_blank">here</a> to create a new project.', 'enginethemes' ),
                            'type' => 'combine',
                            'class' => 'field-social-api',
                            'children' => array(
                                array(
                                    'id' => 'gplus_login',
                                    'type' => 'switch',
                                    'title' => __("Enable Google API", 'enginethemes') ,
                                    'desc' => __("Enabling this will allow users to log in via Google", 'enginethemes'),
                                    'name' => 'gplus_login',
                                    'class' => ''
                                ),
                                array(
                                    'id' => 'gplus_client_id',
                                    'type' => 'text',
                                    'title' => __("Google key", 'enginethemes') ,
                                    'name' => 'gplus_client_id',
                                    'placeholder' => __("Client ID", 'enginethemes') ,
                                    'class' => '',
                                ),
                                array(
                                    'id' => 'gplus_secret_id',
                                    'type' => 'text',
                                    'title' => __("Google Secret key", 'enginethemes') ,
                                    'name' => 'gplus_secret_id',
                                    'placeholder' => __("Google Secret key", 'enginethemes') ,
                                    'class' => '',
                                )
                            )
                        )
                    )
                ),
                /* User mail template */
                array(
                    'args' => array(
                        'title' => __( "Authentication Mail Template", 'engienthemes' ) ,
                        'desc' => __("Email templates for the authentication process. You can use placeholders to include some specific content.", 'enginethemes') . '<a class="icon btn-toggle-help payment" href="#" title="View more details"><i class="fa fa-long-arrow-down" aria-hidden="true"></i></a>' . '<div class="cont-template-help payment-setting">
                                                    <p><span>[user_login],[display_name],[user_email] :</span>' . __("user's details you want to send mail", 'enginethemes') . '<br />
                                                    <span>[dashboard] : </span>' . __("member dashboard url ", 'enginethemes') . '<br />
                                                    <span>[title], [link], [excerpt],[desc], [author] : </span>' . __("mJob title, link, details, author", 'enginethemes') . ' <br />
                                                    <span>[activate_url] : </span>' . __("activate link is require for user to renew password", 'enginethemes') . ' <br />
                                                    <span>[site_url],[blogname],[admin_email] : </span>' . __(" site info, admin email", 'enginethemes') . '
                                                    <span>[project_list] : </span>' . __("list of mJobs a buyer sends to a seller when inviting him to join", 'enginethemes') . '
                                                    </p>
                                                </div>',
                        'id' => 'user-mail-group',
                        'class' => '',
                        'name' => ''
                    ),
                    'fields' => array(
                        array(
                            'id' => 'register_mail_template',
                            'type' => 'editor',
                            'title' => __("Register mail template", 'enginethemes') ,
                            'desc' => __("Send to user when he registers successfully.", 'enginethemes'),
                            'name' => 'register_mail_template',
                            'class' => '',
                            'reset' => 1,
                            'toggle' => true
                        ),
                        array(
                            'id' => 'confirm_mail_template',
                            'type' => 'editor',
                            'title' => __("Confirm mail template", 'enginethemes') ,
                            'desc' => __("Send to user after he successfully registered when the option of confirming email is on.", 'enginethemes'),
                            'name' => 'confirm_mail_template',
                            'class' => '',
                            'reset' => 1,
                            'toggle' => true
                        ),
                        array(
                            'id' => 'confirmed_mail_template',
                            'type' => 'editor',
                            'title' => __("Confirmed mail template", 'enginethemes'),
                            'desc' => __("Send to user to notify that he has successfully confirmed the email.", 'enginethemes'),
                            'name' => 'confirmed_mail_template',
                            'class' => '',
                            'reset' => 1,
                            'toggle' => true
                        ),
                        array(
                            'id' => 'forgotpass_mail_template',
                            'type' => 'editor',
                            'title' => __("Forgot password mail template", 'enginethemes') ,
                            'desc' => __("Send to user when he requests password reset.", 'enginethemes'),
                            'name' => 'forgotpass_mail_template',
                            'class' => '',
                            'reset' => 1,
                            'toggle' => true
                        ),
                        array(
                            'id' => 'resetpass_mail_template',
                            'type' => 'editor',
                            'title' => __("Reset password mail template", 'enginethemes'),
                            'desc' => __("Send to user to notify him of successful password reset.", 'enginethemes'),
                            'name' => 'resetpass_mail_template',
                            'class' => '',
                            'reset' => 1,
                            'toggle' => true
                        ),
                        array(
                            'id' => 'inbox_mail_template',
                            'type' => 'editor',
                            'title' => __('New message alert', 'enginethemes'),
                            'desc' => __("Send to user when he receives a new message", 'enginethemes'),
                            'class' => '',
                            'name' => 'inbox_mail_template',
                            'reset' => 1,
                            'toggle' => true
                        )
                    )
                )
            )
        );
    }
}