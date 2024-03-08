<?php
if(!function_exists('mJobSignUpFormStepOne')) {
    /**
     * Render sign up form step 1
     * @param string $intro      Form intro
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Authentication Template
     * @author Tat Thien
     */
    function mJobSignUpFormStepOne($intro) {
        // Add filter to change the content of note
        //$intro = apply_filters('mjob_signup_form_note', $intro);
        ?>
        <form id="signUpFormStep1" class="form-authentication et-form">
            <div class="inner-form">
                <div class="note-paragraph"><?php echo $intro ?></div>
                <div class="form-group clearfix insert-email">
                    <div class="input-group">
                        <div class="input-group-addon"><i class="fa fa-envelope"></i></div>
                        <button class="<?php mje_button_classes( array( 'btn-continue', 'waves-effect', 'waves-light' ) ); ?>"><?php _e('CONTINUE', 'enginethemes'); ?><i class="fa fa-angle-right"></i></button>
                        <input type="text" name="user_email" id="user_email" class="form-control" placeholder="<?php _e('Enter your email here', 'enginethemes'); ?>">
                    </div>
                </div>
                <div class="form-group no-margin social">
                    <?php
                    if( function_exists('ae_render_social_button')){
                        $before_string = __("Or join us with:", 'enginethemes');
                        ae_render_social_button(array(), array(), $before_string);
                    }
                    ?>
                </div>
            </div>
        </form>
        <?php
    }
}

if(!function_exists('mJobSignUpForm')) {
    /**
     * Render sign up form
     * @param boolean $email    If $email = true then the field email will be hidden
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Authentication Template
     * @author Tat Thien
     */
    function mJobSignUpForm($email = '', $is_page = false, $redirect_url = '', $header_text = "") {
        ?>
        <div id="signUpForm">
            <?php
            if(!empty($header_text)) {
                echo '<p class="form-header-text">'. $header_text .'</p>';
            }
            ?>
            <form class="form-authentication et-form float-left">
                <?php
                    // If self link in home redirect dashboard else reload page
                    $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
                    $action_link = $_SERVER['REQUEST_URI'];
                    if(is_home()) {
                        $redirect_url = et_get_page_link('dashboard');
                        echo '<input type="hidden" name="redirect_url" class="redirect_url" value="'. $redirect_url .'" />';
                    } elseif(!empty($redirect_url)) {
                        echo '<input type="hidden" name="redirect_url" class="redirect_url" value="'. $redirect_url .'" />';
                    } else {
                        echo '<input type="hidden" name="redirect_url" class="redirect_url" value="'. $protocol.$_SERVER['HTTP_HOST'].$action_link .'" />';
                    }
                ?>
                <div class="btn-back-sign">
                    <?php
                    if($is_page == true) {
                        ?>
                        <a href="#" class="focus-signin-form"><i class="fa fa-angle-left"></i><?php _e('Back to sign in', 'enginethemes'); ?></a>
                        <?php
                    }
                    ?>
                </div>
                <div class="inner-form">
                    <?php
                        if(empty($email)) {
                            ?>
                            <div class="form-group clearfix">
                                <div class="input-group">
                                    <label for="user_email"><?php _e( 'Email', 'enginethemes' ); ?></label>
                                    <input type="text" name="user_email" id="user_email" class="form-control">
                                </div>
                            </div>
                            <?php
                        }
                    ?>
                    <!-- Set timezone local -->
                    <input type="hidden" name="timezone_local" class="timezone-local" value="">
                    <script type="text/javascript">
                        var tz = jstz.determine();
                        var timeZoneLocal = tz.name();
                        jQuery('.timezone-local').attr('value',timeZoneLocal);
                    </script>
                    <!--End set timezone local-->

                    <div class="form-group clearfix">
                        <div class="input-group">
                            <label for="user_login"><?php _e( 'Username', 'enginethemes' ); ?></label>
                            <input type="text" name="user_login" id="user_login" class="form-control">
                        </div>
                    </div>
                    <div class="form-group clearfix">
                        <div class="input-group">
                            <label for="user_pass"><?php _e( 'Password', 'enginethemes' ); ?></label>
                            <input type="password" name="user_pass" id="user_pass" class="form-control">
                        </div>
                    </div>
                    <div class="form-group margin-bot-15 clearfix">
                        <div class="input-group">
                            <label for="repeat_pass"><?php _e( 'Confirm your password', 'enginethemes' ); ?></label>
                            <input type="password" name="repeat_pass" id="repeat_pass" class="form-control repeat_pass">
                        </div>
                    </div>
                    <div class="form-group clearfix float-left">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="term_privacy" id="term_privacy"><span class="tos-text"><?php _e('I accept with the', 'enginethemes'); ?>
                                    <a href="<?php echo et_get_page_link('tos'); ?>" target="_blank"><?php _e('terms and conditions', 'enginethemes'); ?></a></span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <button class=" <?php mje_button_classes( array( 'btn-continue', 'waves-effect', 'waves-light' ) ); ?>"><?php _e('JOIN', 'enginethemes'); ?></button>
                    </div>

                    <?php if($is_page == true && !is_page_template('page-process-payment.php')) : ?>
                    <div class="clearfix float-right social">
                        <?php
                        if( function_exists('ae_render_social_button')){
                            $before_string = __("Or sign up with:", 'enginethemes');
                            ae_render_social_button( array(), array(), $before_string );
                        }
                        ?>
                    </div>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        <?php
    }
}

if(!function_exists('mJobSignInForm')) {
    /**
     * Render sign sign in form
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Authentication Template
     * @author Tat Thien
     */
    function mJobSignInForm($is_page = false, $redirect_url = '', $header_text="") {
        ?>
        <div id="signInForm">
            <?php
                if(!empty($header_text)) {
                    echo '<p class="form-header-text">'. $header_text .'</p>';
                }
            ?>
            <form class="form-authentication et-form">
                <?php
                    // If self link in home redirect dashboard else reload page
                    $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
                    $action_link = $_SERVER['REQUEST_URI'];
                    if(is_home()) {
                        $redirect_url = et_get_page_link('dashboard');
                        echo '<input type="hidden" name="redirect_url" class="redirect_url" value="'. $redirect_url .'" />';
                    } elseif(!empty($redirect_url)) {
                        echo '<input type="hidden" name="redirect_url" class="redirect_url" value="'. $redirect_url .'" />';
                    } else {
                        echo '<input type="hidden" name="redirect_url" class="redirect_url" value="'. $protocol.$_SERVER['HTTP_HOST'].$action_link .'" />';
                    }
                ?>

                <div class="inner-form signin-form">
                    <div class="form-group clearfix">
                        <div class="input-group">
                            <label for="user_login"><?php _e('Username or Email', 'enginethemes'); ?></label>
                            <input type="text" name="user_login" id="user_login" class="form-control">
                        </div>
                    </div>
                    <div class="form-group clearfix">
                        <div class="input-group">
                            <label for="user_pass"><?php _e('Password', 'enginethemes'); ?></label>
                            <input type="password" name="user_pass" id="user_pass" class="form-control">
                        </div>
                    </div>
                    <div class="form-group clearfix">
                        <div class="sign-in-button float-left">
                            <button class="<?php mje_button_classes( array( 'btn-continue', 'waves-effect', 'waves-light' ) ); ?>"><?php _e('SIGN IN', 'enginethemes'); ?></button>
                        </div>
                        <div class="forgot-pass float-right">
                            <a href="javascript:void(0)" class="open-forgot-modal"><?php _e('Forgot your password?', 'enginethemes'); ?></a>
                        </div>
                    </div>
                    <div class="clearfix float-right social">
                        <?php
                        if(!is_page_template('page-process-payment.php')) {
                            if( function_exists('ae_render_social_button')){
                                $before_string = __("Or sign in with:", 'enginethemes');
                                ae_render_social_button( array(), array(), $before_string );
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="float-center not-member">
                    <?php
                    if($is_page == true) {
                        ?>
                        <span><?php _e('Not a member yet?', 'enginethemes'); ?></span> <a href="#" class="focus-signup-form"><?php _e('Join us!', 'enginethemes'); ?></a>
                        <?php
                    } else  {
                        ?>
                        <span><?php _e('Not a member yet?', 'enginethemes'); ?></span> <a href="#" class="open-signup-modal"><?php _e('Join us!', 'enginethemes'); ?></a>
                        <?php
                    }
                    ?>
                </div>
            </form>
        </div>
        <?php
    }
}

if(!function_exists('mJobForgotPasswordForm')) {
    /**
     * Render forgot password form
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Authentication Template
     * @author Tat Thien
     */
    function mJobForgotPasswordForm() {
        ?>
        <div id="forgotPasswordForm">
            <form class="form-authentication et-form">
                <div class="inner-form">
                    <div class="form-group">
                        <div class="input-group">
                            <label for="user_login"><?php _e( 'Registered Email', 'enginethemes' ); ?></label>
                            <input type="text" name="user_login" id="user_login" class="form-control">
                        </div>
                    </div>
                    <div class="form-group reset-pass">
                        <button class="<?php mje_button_classes( array( 'btn-continue', 'waves-effect', 'waves-light' ) ); ?>"><?php _e('SUBMIT', 'enginethemes'); ?></button>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }
}

if(!function_exists('mJobResetPasswordForm')) {
    /**
     * Render reset password form
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Authentication Template
     * @author Tat Thien
     */
    function mJobResetPasswordForm() {
        ?>
        <div id="resetPassForm">
            <form class="form-reset et-form">
                <input type="hidden" name="user_login" id="user_login" value="<?php if(isset($_GET['user_login'])) echo $_GET['user_login'] ?>">
                <input type="hidden" name="user_key" id="user_key" value="<?php if(isset($_GET['key'])) echo $_GET['key'] ?>">
                <div class="inner-form">
                    <div class="form-group clearfix">
                        <div class="input-group">
                            <label for="new_password"><?php _e('New password', 'enginethemes'); ?></label>
                            <input type="password" name="new_password" id="new_password">
                        </div>
                    </div>
                    <div class="form-group clearfix">
                        <div class="input-group">
                            <label for="repeat_pass"><?php _e('Confirm your password', 'enginethemes'); ?></label>
                            <input type="password" name="repeat_pass" id="repeat_pass">
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="<?php mje_button_classes( array( 'btn-continue', 'waves-effect', 'waves-light' ) ); ?>"><?php _e('SUBMIT', 'enginethemes'); ?></button>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }
}

if(!function_exists('mJobChangePasswordForm')) {
    /**
     * Render change password form
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Authentication Template
     * @author Tat Thien
     */
    function mJobChangePasswordForm() {
        ?>
        <div id="changePassForm">
            <form class="change-password et-form">
                <div class="form-group clearfix">
                    <div class="input-group">
                        <label for="old_password"><?php _e( 'Current password', 'enginethemes' ); ?></label>
                        <input type="password" name="old_password" id="old_password">
                    </div>
                </div>
                <div class="form-group clearfix">
                    <div class="input-group">
                        <label for="new_password"><?php _e( 'New password', 'enginethemes' ); ?></label>
                        <input type="password" name="new_password" id="new_password">
                    </div>
                </div>
                <div class="form-group clearfix">
                    <div class="input-group">
                        <label for="new_password"><?php _e( 'Confirm new password', 'enginethemes' ); ?></label>
                        <input type="password" name="renew_password" id="renew_password">
                    </div>
                </div>
                <div class="form-group clearfix change-pass-button-method">
                    <button class="<?php mje_button_classes( array( 'btn-continue', 'waves-effect', 'waves-light' ) ); ?>"><?php _e('Change', 'enginethemes'); ?></button>
                </div>
            </form>
        </div>
        <?php
    }
}

if(!function_exists('mJobAuthFormOnPage')) {
    function mJobAuthFormOnPage($redirect_url = false, $signin_text = "", $signup_text = "") {
        echo '<div id="authentication-page" class="form-auth-page">';
        mJobSignInForm(true, $redirect_url, $signin_text);
        mJobSignUpForm('', true, $redirect_url, $signup_text);
        echo '</div>';
    }
}