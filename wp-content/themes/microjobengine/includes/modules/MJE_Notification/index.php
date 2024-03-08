<?php
define( 'MJE_NOTIFY_ACTIVATED_USER', 'type=activated_user' );
define( 'MJE_NOTIFY_APPROVE_WITHDRAWAL', 'type=approve_withdrawal' );
define( 'MJE_NOTIFY_DECLINE_WITHDRAWAL', 'type=decline_withdrawal' );

require_once dirname( __FILE__ ) . '/class-mje-notification-post-type.php';
require_once dirname( __FILE__ ) . '/class-mje-notification-action.php';
require_once dirname( __FILE__ ) . '/class-mje-notification-hook.php';
require_once dirname( __FILE__ ) . '/notification-functions.php';