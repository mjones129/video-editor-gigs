<?php
if( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if access directly.
}

class MJE_MJob_Template extends AE_Base
{
    public function __construct() {
        /* Hook into mjob item template */
        $this->add_action( 'mje_mjob_item_after_image', 'render_mjob_item_action' );
        $this->add_action( 'mje_mjob_item_js_after_image', 'render_mjob_item_js_action' );
        $this->add_action( 'mje_mjob_item_top', 'render_mjob_item_status' );
        $this->add_action( 'mje_mjob_item_js_top', 'render_mjob_item_js_status' );
    }

    /**
     * Render mjob item actions
     *
     * @param object $mjob
     */
    public function render_mjob_item_action( $mjob ) {
        if( is_user_logged_in() &&
            ( is_search()
                || is_post_type_archive( 'mjob_post' )
                || is_page_template( 'page-my-listing-jobs.php' )
            ) ) {
            mje_get_template( 'template/manage-action.php', array( 'current' => $mjob ) );
        }
    }

    public function render_mjob_item_js_action() {
        if( is_user_logged_in() &&
            ( is_search()
                || is_post_type_archive( 'mjob_post' )
                || is_page_template( 'page-my-listing-jobs.php' )
            ) ) {
            mje_get_template( 'template-js/manage-action.php' );
        }
    }

    /**
     * Render mjob status
     *
     * @param $mjob
     */
    public function render_mjob_item_status( $mjob ) {
        if( is_post_type_archive( 'mjob_post' )
            || is_page_template( 'page-my-listing-jobs.php' )
            || is_page_template( 'page-dashboard.php' )
        ) {
            ?>
            <div class="status-label">
                <span class="<?php echo $mjob->status_class; ?>"><?php echo $mjob->status_text; ?></span>
            </div>
            <?php
        }
    }

    public function render_mjob_item_js_status() {
        if( is_post_type_archive( 'mjob_post' )
            || is_page_template( 'page-my-listing-jobs.php' )
            || is_page_template( 'page-dashboard.php' )
        ) {
            ?>
            <div class="status-label">
                <span class="{{= status_class }}">{{= status_text }}</span>
            </div>
            <?php
        }
    }
}

new MJE_MJob_Template();