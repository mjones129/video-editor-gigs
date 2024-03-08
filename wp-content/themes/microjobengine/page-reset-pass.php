<?php
global $current_user;
// Redirect if user logged in
if(!empty($current_user->ID)) {
    ob_start();
    wp_redirect(et_get_page_link('dashboard'));
} elseif(!isset($_GET['user_login']) || !isset($_GET['key'])) {
    wp_redirect(home_url());
}

get_header();
/**
 * Template Name: Reset Password
 * @since 1.0
 * @package MicrojobEngine
 * @category Authentication
 * @author Tat Thien
 */
?>
    <div id="content">
        <div class="container reset-pass reset-pass-active float-center">
            <p class="reset-title"><?php _e('Reset your password', 'enginethemes'); ?></p>
            <?php
                mJobResetPasswordForm();
            ?>
        </div>
    </div>
<?php
get_footer();