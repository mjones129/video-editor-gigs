<?php
/**
 * Template Name: Update Data
 */
global $user_ID;
if( ! is_super_admin( $user_ID ) ) {
    wp_redirect( get_home_url() );
}

get_header();
?>
<div id="content">
    <div class="block-page">
        <div class="container dashboard withdraw">
            <div class="row title-top-pages">
                <h2 class="block-title">Update Data</h2>
            </div>

            <div class="update-data-wrap">
                <p>You must update the data before using the advanced search for mJob. Otherwise, the search results won't display the right mJob matching criteria you filter.</p>

                <button id= "update-data" class="<?php mje_button_classes( array( 'waves-effect', 'waves-light' ) ) ?>"><div class="search-title"><span class="text-search">Update</span></div></button>

            </div>

        </div>
    </div>
</div>
<?php
get_footer();
?>
<script type="text/javascript">
jQuery(document).ready(function(){
    var blockUi = new AE.Views.BlockUi();
    jQuery('#update-data').on('click', function() {
            jQuery.ajax({
            url: ae_globals.ajaxURL,
            type: 'POST',
            data: {
                    action: 'mje-update_data',
                    method: 'update_total_sale'
                },
            beforeSend: function() {
                    blockUi.block("#update-data");
                },
            success: function(res) {
                if(res.success) {
                    AE.pubsub.trigger('ae:notification', {
                      notice_type: 'success',
                      msg: res.msg
                    });
                }
                else
                {
                    AE.pubsub.trigger('ae:notification', {
                        notice_type: 'error',
                        msg: res.msg
                    });
                }
            },
            complete: function(){
                blockUi.unblock();
            }
        });
    });
});
</script>