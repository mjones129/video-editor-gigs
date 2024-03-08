<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class MJE_Topup_Table extends WP_List_Table
{
    /**
     * @var array $count_users
     */
    protected static $count_users;

    public function __construct()
    {
        parent::__construct( array(
            'singular' => TOPUP_SINGULAR,
            'plural' => TOPUP_PLURAL,
            'ajax' => true
        ) );

        self::get_count_users();

        // remove _wpnonce and _wp_http_referer
        $_SERVER['REQUEST_URI'] = remove_query_arg( '_wpnonce', $_SERVER['REQUEST_URI'] );
        $_SERVER['REQUEST_URI'] = remove_query_arg( '_wp_http_referer', $_SERVER['REQUEST_URI'] );
    }

    /**
     * Retrieve user's data from the database
     *
     * @param int $per_page
     * @param int $page_number
     * @return object
     */
    public static function get_users( $per_page = 20, $page_number = 1 ) {
        $defaults = array(
            'fields' => array( 'ID', 'user_email', 'user_login', 'user_registered' ),
            'number' => $per_page,
            'paged' => $page_number
        );

        $args = $defaults;

        // query arg when admin search users
        if( ! empty( $_GET['s'] ) ) {
            $search_str = trim( $_GET['s'] );
            $search_str = esc_attr( $search_str );
            $args = array_merge( array(
                'search' => '*' . $search_str . '*',
            ), $args );
        }

        // query args when admin filter users by roles
        if( ! empty( $_GET['role'] ) ) {
            $args = array_merge( array(
                'role' => $_GET['role']
            ), $args );
        }

        // query arg when admin sort users by date registered
        if( ! empty( $_GET['orderby'] ) && $_GET['orderby'] != 'available_fund' ) {
            $args = array_merge( array(
                'orderby' => $_GET['orderby'],
                'order' => $_GET['order']
            ), $args );
        }

        $args = self::filter_available_fund_args( $args );

        $user_query = new WP_User_Query( $args );

        return $user_query;
    }

    /**
     * Build query args to filter users by available fund
     *
     * @return array $args
     */
    public static function filter_available_fund_args( $args ) {
        if( ! isset( $_GET['orderby'] ) ) {
            return $args;
        }

        if ( $_GET['orderby'] != 'available_fund' ) {
            return $args;
        }

        $users = get_users( array(
            'number' => -1,
            'fields' => array( 'ID', 'user_email', 'user_login', 'user_registered' )
        ) );

        foreach ( $users as $user ) {
            $user_wallet = AE_WalletAction()->getUserWallet( $user->ID );
            $user->balance = $user_wallet->balance;
        }

        // sort user
        usort( $users, function( $a, $b ) {
           if( $a->balance == $b->balance ) { return 0; }

           if( $_GET['order'] == 'asc' ) {
               return $a->balance > $b->balance ? 1 : -1;
           } else {
               return $a->balance > $b->balance ? -1 : 1;
           }
        });

        $user_ids = array();
        foreach ( $users as $user ) {
            array_push( $user_ids, $user->ID );
        }


        $args = array_merge( array(
            'include' => $user_ids,
            'orderby' => 'include'
        ), $args );

        return $args;
    }

    /**
     * Get total users
     *
     * @return mixed
     */
    public static function get_count_users() {
        self::$count_users = count_users();
        return self::$count_users;
    }

    /**
     * Get available user roles
     *
     * @return mixed
     */
    public static function get_user_roles() {
        unset( self::$count_users['avail_roles']['none'] );
        return self::$count_users['avail_roles'];
    }

    /**
     * Method for user_login column
     *
     * @param object $item
     * @return string
     */
    function column_user_login( $item ) {
        $user_profile_link = get_author_posts_url( $item->ID );
        $title = '<strong><a href="'. $user_profile_link .'" target="_blank">' . $item->user_login . '</a></strong>';

        $actions = array(
            'view' => sprintf( __( '<a href="%s" target="_blank">View profile</a>', 'enginethemes' ), $user_profile_link )
        );

        return $title . $this->row_actions( $actions );
    }

    /**
     * Method for available_fund column
     *
     * @param object $item
     * @return string
     */
    function column_available_fund( $item ) {
        $user_wallet = new AE_WalletAction();
        $user_avail_fund = $user_wallet->getUserWallet( $item->ID );
        $item->user_fund = $user_avail_fund->balance;
        return '<strong class="price">' . mje_format_price( $user_avail_fund->balance ) . '</strong>';
    }

    function column_topup_action( $item ) {
        ?>
        <div class="topup-action-wrap">
            <button class="button button-topup-js"
                    data-id="<?php echo $item->ID; ?>"
                    data-username="<?php echo $item->user_login; ?>"
                    data-email="<?php echo $item->user_email; ?>"
                    data-userregister="<?php echo $item->user_registered; ?>"
                    data-userfund="<?php echo $item->user_fund; ?>"
            >
                <?php _e( 'Edit Credit', 'enginethemes' ); ?>
            </button>
        </div>
        <?php
    }

    function column_change_log( $item ) {
        ?>
        <a href="javascript:void(0);"
            class="price-changelog price-changelog-js"
            data-id="<?php echo $item->ID; ?>"
            data-username="<?php echo $item->user_login; ?>"
            data-email="<?php echo $item->user_email; ?>"
        >
                <?php _e( 'change log', 'enginethemes' ); ?>
        </a>
        <?php
    }

    /**
     * Render a column when no column specific method exists.
     *
     * @param array $item
     * @param string $column_name
     *
     * @return mixed
     */
    public function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'user_login':
            case 'user_email':
            case 'user_registered':
                return $item->$column_name;
            default:
                return print_r( $item, true ); //Show the whole array for troubleshooting purposes
        }
    }

    /**
     * Associative array of columns
     *
     * @return array
     */
    function get_columns() {
       $columns = array(
           'user_login' => __( 'Username' ),
           'user_email' => __( 'Email' ),
           'user_registered' => __( 'Date registered' ),
           'available_fund' => __( 'Available fund', 'enginethemes' ),
           'topup_action' => __( 'Top-up action', 'enginethemes' ),
           'change_log' => ''
       );

       return $columns;
    }

    /**
     * Columns to make sortable
     *
     * @return array
     */
    public function get_sortable_columns()
    {
        $sortable_columns = array(
            'user_registered' => array( 'user_registered', false ),
            'available_fund' => array( 'available_fund', true )
        );

        return $sortable_columns;
    }

    /**
     * Generates content for a single row of the table
     *
     * @since 3.1.0
     * @access public
     *
     * @param object $item The current item
     */
    public function single_row( $item ) {
        echo '<tr id="topup-user-' . $item->ID . '" class="topup-user-row">';
        $this->single_row_columns( $item );
        echo '</tr>';
    }

    /**
     * Render filter by roles
     */
    function render_roles_filter() {
        $user_roles = self::get_user_roles();
        $setting_url = MJE_Topup::get_instance()->get_setting_url();
        ?>
        <ul class="subsubsub">
            <li>
                <a href="<?php echo $setting_url; ?>"
                   class="<?php echo empty( $_GET['role'] ) ? 'current' : ''; ?>"
                >
                    <?php printf( __( 'All <span class="count">(%s)</span>' ), self::$count_users['total_users'] ); ?>
                </a>
            </li>

            <?php foreach ( $user_roles as $role => $count ) : ?>
                | <li>
                    <a href="<?php echo $setting_url . '&role=' . $role; ?>"
                       class="<?php echo ( isset( $_GET['role']) && $_GET['role'] == $role ) ? 'current' : ''; ?>"
                    >
                        <?php printf( __( '%s <span class="count">(%s)</span>' ), ucfirst( $role ), $count ); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php
    }

    /**
     * Message to be displayed when there are no items
     *
     * @since 3.1.0
     * @access public
     */
    public function no_items() {
        _e( 'No users found.' );
    }

    /**
     * Handle data query anh filter, sorting and pagination.
     */
    public function prepare_items()
    {
        $this->_column_headers = $this->get_column_info();

        $per_page = $this->get_items_per_page( 'users_per_page', 20 );
        $current_page = $this->get_pagenum();
        $user_query = self::get_users( $per_page, $current_page );
        $this->items = $user_query->results;

        if( empty( $this->items ) ) {
            $this->set_pagination_args( array() );
        } else {
            $this->set_pagination_args( array(
                'total_items' => $user_query->total_users,
                'total_pages' => ceil( $user_query->total_users / $per_page ),
                'per_page' => $per_page,
            ) );
        }
    }
}