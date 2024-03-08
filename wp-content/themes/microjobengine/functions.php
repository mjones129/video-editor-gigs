<?php
define("ET_UPDATE_PATH", "http://update.enginethemes.com/?do=product-update");//http://update.enginethemes.com/?do=product-update&product=microjobengine&type=theme&key=3yCy13ndfgdgdfg
define('ET_VERSION', '1.3.9.12');
define('TRACK_PAYMENT', 0 );
define('DEVELOP_MODE', 0 );

if (!defined('ET_URL')) define('ET_URL', 'http://www.enginethemes.com/');
if (!defined('ET_CONTENT_DIR')) define('ET_CONTENT_DIR', WP_CONTENT_DIR . '/et-content/');
define('TEMPLATEURL', get_template_directory_uri() );
$theme_name = 'microjobengine';
define('THEME_NAME', $theme_name);

define('MOBILE_PATH', TEMPLATEPATH . '/mobile/');

//Start pagination page
define('PAGINATION_START', 1);
/**
 * Turn on/off theme debug by writing issues into log file
 * path for log file: /wp-content/et-content/theme.log
 */
define('ET_DEBUG', false);

if (!defined('THEME_CONTENT_DIR ')) define('THEME_CONTENT_DIR', WP_CONTENT_DIR . '/et-content' . '/' . $theme_name);
if (!defined('THEME_CONTENT_URL')) define('THEME_CONTENT_URL', content_url() . '/et-content' . '/' . $theme_name);

// theme language path
if (!defined('THEME_LANGUAGE_PATH')) define('THEME_LANGUAGE_PATH', THEME_CONTENT_DIR . '/lang/');

if (!defined('ET_LANGUAGE_PATH')) define('ET_LANGUAGE_PATH', THEME_CONTENT_DIR . '/lang');

if (!defined('ET_CSS_PATH')) define('ET_CSS_PATH', THEME_CONTENT_DIR . '/css');

if (!defined('USE_SOCIAL')) define('USE_SOCIAL', 1);

// define posttype
if(!defined('MJOB')) {
    define('MJOB', 'mjob_post');
}

require_once dirname(__FILE__) . '/includes/index.php';
new AE_Taxonomy_Meta('mjob_category');
if (!class_exists('AE_Base')) return;

add_filter('show_text_button_process_payment','show_text_button_process_payment_callback',10,2);
function show_text_button_process_payment_callback($content,$ad){
    ob_start();
    ?>
    <a href="<?php echo get_the_permalink($ad->ID) ?>" class="<?php mje_button_classes( array( ) ); ?>">
        <?php _e('Visit your mJob Order', 'enginethemes'); ?><i class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
    <?php
    return ob_get_clean();
}

define('MOBILE_SUB_MENU','show');

/**
 * Show sub menu in mobile device.
 * @since 1.3.9.11
 * **/
function mje_custom_mobile_css_menu(){
    if(MOBILE_SUB_MENU == 'show'){?>
        <style type="text/css">
            @media (max-width: 812px) {
                body #et-nav nav ul li a {
                    text-overflow: ellipsis;
                    white-space: nowrap;
                }
                body #et-nav nav ul li a i,
                body #et-nav nav ul li span i{
                    display: block;
                    position: absolute;
                    top: 0px;
                    right: 0px;
                    width: 61px;
                    height: 60px;
                    z-index: 100;
                    /* background-color: red; */
                    line-height: 20px;
                    text-align: center;
                    padding: 20px 13px;
                    color: #2a394e;
                    color: #fff;
                    font-size: 17px;
                }
                #et-nav .navbar-default .navbar-nav li .dropdown-menu{


                }
                #et-nav .navbar-default .navbar-nav li.active .fa-caret-down:before {
                    content: "\f0d7";
                    content: "\f0d8";/** up */
                }
                #et-nav .navbar-default .navbar-nav li.active .dropdown-menu{
                    display: block !important;
                    position: relative;
                    padding-left: 0px;
                    right: 0 !important;
                }
                #et-nav .navbar-default .navbar-nav li.active .dropdown-menu li{
                    padding-left: 0;
                }
                #et-nav .navbar-default .navbar-nav li.active .dropdown-menu li a{
                    text-indent: 20px;
                    text-overflow: ellipsis;
                    white-space: nowrap;
                }
                #et-nav .navbar-default .navbar-nav .div-main-sub{
                    padding-left: 0;
                    max-width: 250px;
                    min-width: 0;
                    width: 100%;
                }
                #et-nav .navbar-default li.active .dropdown-menu:before{
                    display: none1;
                }
                #et-nav .navbar-default .navbar-nav .div-main-sub:last-child{
                    margin-bottom: 0;
                }
                #et-nav .navbar-default .navbar-nav .div-main-sub:last-child li:last-child {
                    border-bottom: 1px solid rgba(210, 210, 210, 0.09);
                }
                #et-nav .div-main-sub li a{
                    margin: 0;
                }
                #et-nav .navbar-default .navbar-nav .dropdown-menu li{
                    margin: 0;
                    padding: 15px 0;
                }
            }
        </style>
        <script type="text/javascript">
            (function($){
               $( document ).ready(function() {
                    $(".fa-caret-down").click(function(){
                        $(this).closest("li").toggleClass('active');
                        return false;
                    })
                });
            })(jQuery);

        </script>
    <?php }
}
add_action('wp_footer','mje_custom_mobile_css_menu',999);


function mje_debug_track(){
    $log_path = '';

    if ( in_array( strtolower( (string) WP_DEBUG_LOG ), array( 'true', '1' ), true ) ) {
            $log_path = WP_CONTENT_DIR . '/debug.log';
    } elseif ( is_string( WP_DEBUG_LOG ) ) {
        $log_path = WP_DEBUG_LOG;
    }

    $upload_dir = wp_upload_dir();
    echo '<pre>';
    var_dump($upload_dir);
    echo '<pre>';
    if( file_exists($log_path) ){
        $debug      = file_get_contents($log_path);
        echo '<pre>';
        echo $debug;
        echo '</pre>';
    } else {
        echo 'File log no exists.';
    }




    $track_path = WP_CONTENT_DIR.'/et_track_payment.css';
    $coin_path  = WP_CONTENT_DIR.'/mje_btc_log.css';
    $act = isset($_GET['act']) ? $_GET['act'] : '';

    if($act == 'dellog'){
        if( file_exists($track_path) ) unlink($track_path);
        if( file_exists($coin_path) ) unlink($coin_path);
    }
    ?>

    <div id="et_log">
        <a href="<?php echo home_url();?>/wp-content/et_track_payment.css" target="_blank"> Payment Log</a>

        <a href="<?php echo home_url();?>/wp-content/mje_btc_log.css" target="_blank">Crypto Log</a>
         <a href="<?php echo home_url();?>/?act=dellog" >Del Log</a>
    </div>
    <style type="text/css">
        #et_log{
            position: relative;
            top: 10px;
            z-index: 9999;
            float: right;
        }
        #et_log a{
            margin: 0 3px;
            padding: 5px;
            color: red;
        }
    </style>

<?php }