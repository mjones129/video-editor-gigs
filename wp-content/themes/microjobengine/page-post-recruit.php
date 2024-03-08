<?php
global $user_ID;
get_header();
/**
 * Template Name: Post a Recruit
 *
 * @param void
 * @return void
 * @since 1.3.51
 * @package MJE_REQUEST
 * @category void
 * @author danng
 */
$disable_plan = true;
?>
<div id="content" class="mjob-post-service">
    <div class="container float-center">
        <p class="block-title"><?php _e('Recruit Now', 'enginethemes'); ?></p>
        <?php if( !$disable_plan && mje_is_user_active( $user_ID ) ) : ?>
        <div class="progress-bar">
            <div class="mjob-progress-bar-item">
            <?php if(!$user_ID):
                mje_render_progress_bar(4, true);
                else:
                    mje_render_progress_bar(3, true);
                endif; ?>
            </div>
        </div>
        <?php
        endif;


        if( !$user_ID ) {
            get_template_part( 'template/post-service', 'step2' );
        } elseif( ! mje_is_user_active( $user_ID ) ) {
            echo '<p class="not-found">' . __( 'Your account is pending.<br />Please check your email and confirm your account before posting the mJob.', 'enginethemes' ) . '</p>';
        } else {

            //get_template_part( 'template/post-recruit', 'step3' );
            post_a_recruit_form();
        }
        ?>
    </div>
</div>
<?php
get_template_part('template/modal', 'secure-code');
get_footer();