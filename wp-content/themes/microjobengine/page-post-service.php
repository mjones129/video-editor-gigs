<?php
global $user_ID;
get_header();
/**
 * Template Name: Post a service
 *
 * @param void
 * @return void
 * @since 1.0
 * @package MicrojobEngine
 * @category void
 * @author JACK BUI
 */
$disable_plan = disable_plan_post_mjob();
?>
<div id="content" class="mjob-post-service">
    <div class="container float-center">
        <p class="block-title"><?php _e('POST A MJOB', 'enginethemes'); ?></p>
        <?php if( ! $disable_plan && mje_is_user_active( $user_ID ) ) : ?>
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

        // check disable payment plan or not
        if(! $disable_plan && mje_is_user_active( $user_ID ) ) {
            get_template_part( 'template/post-service', 'step1' );
        }
        if(! $user_ID) {
            get_template_part( 'template/post-service', 'step2' );
        } elseif( ! mje_is_user_active( $user_ID ) ) {
            echo '<p class="not-found">' . __( 'Your account is pending.<br />Please check your email and confirm your account before posting the mJob.', 'enginethemes' ) . '</p>';
        } else {

            get_template_part( 'template/post-service', 'step3' );

            if( ! $disable_plan) {
                get_template_part( 'template/post-service', 'step4' );
            }
        }
        ?>
    </div>
</div>
<?php
get_template_part('template/modal', 'secure-code');
get_footer();