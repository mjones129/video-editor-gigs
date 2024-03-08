<div id="mje-notification-overlay"></div>
<div id="mje-notification-container" class="<?php echo is_admin_bar_showing() ? 'having-adminbar' : ''; ?>">
    <input type="hidden" id="notification_nonce" value="<?php echo wp_create_nonce( 'mje_fetch_notifications' ); ?>">
    <div class="close-notification">
        <img src="<?php echo get_template_directory_uri() . '/assets/img/close.svg'; ?>" alt="<?php _e( 'close notification', 'enginethemes' ); ?>">
    </div>
    <div class="inner">
        <div class="notification-header">
            <h2>
                <?php _e( 'Notifications', 'enginethemes' ); ?>
                <?php if( mje_get_unread_notification_count() > 0 ) : ?>
                    <span class="unread-count <?php echo mje_get_unread_notification_count() > 99 ? 'unread-count-padding' : ''; ?>"><?php echo mje_get_unread_notification_count(); ?></span>
                <?php endif; ?>
            </h2>
        </div>
        <div class="notification-inner">
            <ul class="notification-list">
            </ul>

            <div class="notification-loading loading">
                <div class="loading-img"></div>
            </div>

            <div class="notification-reach-end">
                <p><?php _e( 'It looks like you\'ve reached the end!', 'enginethemes' ); ?></p>
            </div>

            <div class="notification-bell">
                <div class="bell">
                    <div class="bell-tooltip">
                        <span><?php _e( 'Don\'t worry :)', 'enginethemes'); ?></span>
                    </div>
                    <img src="<?php echo get_template_directory_uri() . '/assets/img/bell.svg' ?>">
                </div>
                <div class="notice">
                    <h4><?php _e( 'Notifications are coming to town!', 'enginethemes' ); ?></h4>
                    <p><?php _e( 'We’ll let you know when we’ve got something new for you!', 'enginethemes'); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>