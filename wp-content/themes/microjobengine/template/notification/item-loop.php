<script type="template" id="notification-item-template">
    <div class="notification-item-inner {{= post_status }} template/notification/item-loop.php">
        <div class="read-noti <# if(noti_link == '') { #>no-link<# } #>">
            <# if(noti_link != '') { #>
            <a href="{{= noti_link }}" class="noti-link" title="<?php _e( 'View notification detail', 'enginethemes' ); ?>">
            <# } #>
                <div class="noti-icon">
                    {{= noti_icon }}
                </div>
                <div class="noti-content">
                    <p>{{= noti_content }}</p>
                </div>
                <div class="noti-meta noti-time-meta">
                    <span class="time">{{= noti_time }}</span>
                    <span href="javascript:void(0)" class="hide-action" title="<?php _e( 'Hide this notification', 'enginethemes' ); ?>"><?php _e( 'Hide', 'enginethemes' ); ?></span>
                </div>
            <# if(noti_link != '') { #>
            </a><!-- end . noti-link -->
            <# } #>
        </div>

        <div class="undo-noti">
            <?php _e( 'This notification is now hidden.', 'enginethemes'); ?>
            <span href="javascript:void(0)" class="undo-action"><?php _e( 'Undo', 'enginethemes' ); ?></span>
        </div>
    </div><!-- end .notification-item-inner -->
</script>