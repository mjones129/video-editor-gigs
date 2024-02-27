<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class MJE_Topup
{
    private static $_instance;

    public $topup_table = '';

    /**
     * @return MJE_Topup $_instance
     */
    public static function get_instance()
    {
        if( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct()
    {
        // register menu
        add_action( 'admin_menu', array( $this, 'register_menu' ) );
        // enqueue admin scripts
        add_action( 'admin_enqueue_scripts', array( $this, 'add_scripts' ) );
        // enqueue front scripts
        add_action( 'wp_enqueue_scripts', array( $this, 'add_front_scripts' ) );
        // add templates js
        add_action( 'admin_footer', array( $this, 'add_template_js' ) );
        // add front template for js
        add_action( 'wp_footer', array( $this, 'add_front_template_js' ) );
        // handle ajax request
        add_action( 'wp_ajax_mje_topup_sync', array( $this, 'topup_sync' ) );
        add_action( 'wp_ajax_mje_topup_user_front', array( $this, 'topup_user_front_sync' ) );
        // convert notification
        add_action( 'mje_other_type_notification', array( $this, 'convert_notification' ) );

        define( 'TOPUP_SINGULAR', __( 'Credit Top-up', 'enginethemes' ) );
        define( 'TOPUP_PLURAL', __( 'Credit Top-up', 'enginethemes' ) );
    }

    public function register_menu() {
        $hook = add_menu_page(
            TOPUP_SINGULAR,
            TOPUP_SINGULAR,
            'manage_options',
            'credit-topup.php',
            array( $this, 'render_setting_page' ),
            mje_get_modules_uri( 'MJE_Topup' ) . '/assets/img/topup-icon.png',
            59
        );

        add_action( "load-$hook", array( $this, 'screen_option' ) );
    }

    /**
     * Screen options
     */
    public function screen_option() {
        $option = 'per_page';
        $args = array(
            'label' => __( 'Number of users per page', 'enginethemes' ),
            'default' => 20,
            'option' => 'users_per_page'
        );

        add_screen_option( $option, $args );

        $this->topup_table = new MJE_Topup_Table();
    }

    public function add_scripts() {
        if( isset( $_GET['page']) && $_GET['page'] == 'credit-topup.php' ) {
            wp_enqueue_script( 'jquery-ui-dialog' );
            wp_enqueue_style( 'wp-jquery-ui-dialog' );            wp_enqueue_style( 'credit-topup', mje_get_modules_uri( 'MJE_Topup' ) . '/assets/css/credit-topup.css', array(), ET_VERSION, 'all' );

            wp_enqueue_script( 'jquery-mask', get_template_directory_uri() . '/assets/js/lib/jquery.mask.min.js', array(), ET_VERSION, true );
            wp_enqueue_script( 'credit-topup', mje_get_modules_uri( 'MJE_Topup' ) . '/assets/js/credit-topup.js', array(
                'jquery',
                'underscore',
                'backbone',
                'appengine',
                'jquery-mask'
            ), ET_VERSION, true );
        }
    }

    public function add_front_scripts() {
        if( is_page_template( 'page-dashboard.php') || is_page_template( 'page-revenues.php' ) ) {
            wp_enqueue_script( 'credit-topup-front', mje_get_modules_uri( 'MJE_Topup' ) . '/assets/js/credit-topup-front.js', array(
                'jquery',
                'underscore',
                'backbone',
                'appengine',
            ), ET_VERSION, true );
        }
    }

    public function add_template_js() {
        if( isset( $_GET['page'] ) and $_GET['page'] == 'credit-topup.php' ) {
            get_template_part( 'template/topup/inline-edit' );
            get_template_part( 'template/topup/changelog-modal' );
        }
    }

    public function add_front_template_js() {
        if( is_page_template( 'page-dashboard.php') || is_page_template( 'page-revenues.php' ) ) {
            get_template_part( 'template/topup/changelog-front-modal' );
        }
    }

    /**
     * Setting page for Topup
     */
    public function render_setting_page() {
        mje_get_template( 'template/topup/settings.php', array( 'topup_table' => $this->topup_table ) );
    }

    /**
     * Getting setting page url
     *
     * @return string
     */
    public function get_setting_url() {
        return admin_url( 'admin.php?page=credit-topup.php' );
    }

    /**
     * Handle ajax request for topup admin
     */
    public function topup_sync() {
        global $user_ID;
        if( ! is_super_admin( $user_ID ) ) {
            wp_send_json(array(
                'success' => false,
                'msg' => __('Permission denied', 'enginethemes')
            ));
        }

        switch ( $_REQUEST['method'] ) {
            case 'preview':
                $amount = (float)$_GET['amount'];
                $this->preview_credit( $_GET['user'], $amount, $_GET['mode'] );
                break;
            case 'save':
                $this->save_credit();
                break;
            case 'changelog':
                $this->get_changelog();
                break;
            default:
                wp_send_json( array(
                    'success' => true,
                    'msg' => ''
                ) );
                // do nothing
        }
    }

    public function topup_user_front_sync() {
        global $user_ID;
        //verify nonce
        /*if( ! wp_verify_nonce( $_GET['noncetopup'], 'mje_topup_user_front' ) ) {
            wp_send_json( array(
                'success' => false,
                'msg' => __( 'Invalid action!', 'enginethemes' )
            ) );
        }*/
        $this->get_changelog();
    }

    public function preview_credit( $user, $amount, $mode ) {
        // get user balance
        $user_wallet = new AE_WalletAction();
        $user_avail_fund = $user_wallet->getUserWallet( $user );

        if( $mode === 'add' ) {
            $balance = $user_avail_fund->balance + $amount;
        } else if( $mode === 'minus' ) {
            $balance = $user_avail_fund->balance - $amount;
            if( $amount > $user_avail_fund->balance ) {
                $balance = 0;
            }
        }

        $res = array(
            'success' => true,
            'old_balance' => $user_avail_fund->balance,
            'balance' => $balance,
            'old_balance_html' => mje_format_price( $user_avail_fund->balance ),
            'balance_html' => mje_format_price( $balance )
        );

        if( $balance == $user_avail_fund->balance ) {
            unset( $res['old_balance'] );
            unset( $res['old_balance_html'] );
        }

        wp_send_json( $res );
    }

    public function save_credit() {
        $data = $_POST;
        $user = $data['user'];
        $amount = (float)$data['amount'];
        $mode = $data['mode'];
        $message = $data['message'];
        $user_wallet =  AE_WalletAction()->getUserWallet( $user );
        if( is_object($user_wallet) ){
            $user_wallet = (object) $user_wallet;
        }


        if( $amount == 0 ) {

            wp_send_json( array(
                'success' => false,
                'new_balance_html' => mje_format_price( $user_wallet->balance )
            ) );
        }

        // get user balance
        $balance = 0;

        if( $mode === 'add' ) {
            $balance = $user_wallet->balance + $amount;
        } else if( $mode === 'minus' ) {
            $balance = $user_wallet->balance - $amount;
            if( $amount > $user_wallet->balance ) {
                $balance = 0;
            }
        }

        if( $user_wallet->balance == 0 && $balance == 0 ) {

            wp_send_json( array(
                'success' => true,
                'new_balance_html' => mje_format_price( $user_wallet->balance )
            ) );
        }

        // create change log
        $changelog = get_user_meta( $user, 'topup_changelog', true );
        if( ! $changelog ) {
            $changelog = array();
        }

        $changelog_item = array(
            'time' => time(),
            'from' => $user_wallet->balance,
            'to' => $balance,
            'amount' => $amount,
            'message' => $message
        );

        array_unshift( $changelog, $changelog_item );
        update_user_meta( $user, 'topup_changelog', $changelog );

        // create notification
        $code = 'type=' . $mode . '_credit';
        $code .= '&from=' . $user_wallet->balance;
        $code .= '&to=' . $balance;
        $code .= '&amount=' . $amount;
        $code .= '&message=' . $message;
        MJE_Notification_Action::get_instance()->create( $code, $user );

        // update user balance
        $user_wallet->balance = $balance;

        AE_WalletAction()->setUserWallet( $user, $user_wallet );

        wp_send_json( array(
            'success' => true,
            'new_balance_html' => mje_format_price( $balance )
        ) );
    }

    public function get_changelog() {
        global $user_ID;

        $data = $_GET;
        if( ! isset( $data['user'] ) ) {
            $user = $user_ID;
        } else {
            $user = $data['user'];
        }

        $changelog = get_user_meta( $user, 'topup_changelog', true );
        if ( empty( $changelog ) ) {
            $output = '<p class="not-found">'. __( 'Nothing found!', 'enginethemes') .'</p>';
        } else {
            ob_start();
            ?>
            <div class="table-wrapper">
                <div class="table-content">
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                        <tr>
                            <th><?php _e( 'Date & time', 'enginethemes' ); ?></th>
                            <th><?php _e( 'Old', 'enginethemes' ); ?></th>
                            <th><?php _e( 'Current', 'enginethemes' ); ?></th>
                            <th><?php _e( 'Amount', 'enginethemes' ); ?></th>
                            <th><?php _e( 'Message', 'enginethemes' ); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach( $changelog as $item ) : ?>
                            <tr>
                                <?php $date_format = get_option('date_format') . ' ' . get_option( 'time_format' ); ?>
                                <td nowrap><?php echo date( $date_format, $item['time'] ); ?></td>
                                <td nowrap><?php echo mje_format_price( $item['from'] ); ?></td>
                                <td nowrap><?php echo mje_format_price( $item['to'] ); ?></td>
                                <td nowrap>
                                    <?php
                                    if( $item['to'] > $item['from'] ) {
                                        $sign = '+';
                                        $class = 'changelog-amount add';
                                    } else {
                                        $sign = '-';
                                        $class = 'changelog-amount minus';
                                    }
                                    ?>
                                    <strong class="<?php echo $class; ?>">
                                        <?php echo $sign . ' ' . mje_format_price( $item['amount'] ); ?>
                                    </strong>
                                </td>
                                <td><?php echo $item['message']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
            $output = ob_get_clean();
        }

        wp_send_json( array(
            'success' => true,
            'output' => $output
        ) );
    }

    public function convert_notification( $post ) {
        $code = trim( $post->post_content );
        $code = str_ireplace( '&amp;', '&', $post->post_content );
        $code = strip_tags( $code );

        // Convert string to variables
        parse_str( $code , $result);
        // version 1.3.6
        $type = isset($result['type']) ? $result['type'] : '';
        $amount = isset($result['amount']) ? $result['amount'] : '';
        $from = isset($result['from']) ? $result['from'] : '';
        $to = isset($result['to']) ? $result['to'] : '';

        if( 'add_credit' == $type || 'minus_credit' == $type ) {
            $amount = mje_format_price( $amount );
            $from = mje_format_price( $from );
            $to = mje_format_price( $to );

            if( 'add_credit' == $type ) {
                $post->noti_content = sprintf( __( 'Admin gave you %s. Your current credit has been changed from %s to %s', 'enginethemes'), $amount, $from, $to );
            } else if ( 'minus_credit' == $type ) {
                $post->noti_content = sprintf( __( 'Admin deducted %s from your available credit. Your current credit has been changed from %s to %s', 'enginethemes'), $amount, $from, $to );
            }

            $post->noti_link = et_get_page_link( 'revenues' ) . '#topup';
        }
    }
}

$topup_instance = MJE_Topup::get_instance();