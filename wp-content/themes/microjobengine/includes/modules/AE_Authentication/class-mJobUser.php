<?php
// Class mJobUser
class mJobUser extends AE_Users
{
    public static $instance;

    /**
     * Get instance method
     */
    public static function getInstance() {
        if(!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Constructor of class
     */
    public function __construct() {
        parent::__construct(array(
            'user_status',
            'timezone_local'
        ));
    }

    /**
     * Show user timezone
     * @param int|string $user_id
     * @return void
     * @since MicrojobEngine 1.1.4
     * @author Tat Thien
     */
    public static function showUserTimeZone( $user_id ) {
        $timezone_local = get_user_meta( $user_id, 'timezone_local', true );
        if( empty( $timezone_local ) ) {
            $timezone_local = 'UTC';
        }

        // Convert timezone
        $timezone_local = mje_get_timezone($timezone_local, true);

        if( ae_get_option( 'user_local_timezone' ) ) :
        ?>
            <li class="timezone clearfix">
                <div class="pull-left">
                    <span><i class="fa fa-clock-o" aria-hidden="true"></i><?php _e( 'Local time ', 'enginethemes' ); ?></span>
                </div>
                <div class="pull-right">
                    <?php
                    $date = new DateTime( 'now', new DateTimeZone( $timezone_local ) );
                    echo $date->format( get_option( 'date_format') ) . "<br />";
                    echo $date->format( get_option( 'time_format' ) );
                    ?>
                </div>
            </li>
        <?php
        endif;
    }
}