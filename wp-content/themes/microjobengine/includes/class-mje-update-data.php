<?php 

class MJE_Update_Data_Action extends MJE_Post_Action
{
	public static $instance;
    /**
     * get_instance method
     *
     */
    public static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public  function __construct(){
    	$this->add_ajax('mje-update_data', 'update_data');       
            add_action( 'load-index.php', 
            function(){
                add_action( 'admin_notices', array( $this, 'my_admin_notice') );
            });     
    }
    public function update_data(){  	
    $post = $_POST;
        if( is_super_admin() ) {
            switch ($post['method']) {
                case 'update_total_sale':
                    $this->update_total_sale();
                    break;       
                default:
                    // do nothing
                    do_action('update_some_thing', $post);
                    break;
            }
            $response = array(
                'success' => true,
                'msg' => __("Congrats. Update successful!", 'enginethemes')
            );
        }
        else
        {
             $response = array(
                    'success'=>false,
                    'msg'=> __('Update failed!', 'enginethemes')
                );
        }
        wp_send_json($response);
    }
    public function update_total_sale()
    {
        $args = array(
            'post_type' => 'mjob_post',
            'post_status' => 'any',
            'showposts' => -1
            );
        $posts = get_posts( $args );
        foreach ( $posts as $post ) {
           update_post_meta( $post->ID, 'et_total_sales', mje_get_mjob_order_count($post->ID));
           $view_count = get_post_meta( $post->ID, 'view_count', true );
           if( ! $view_count ) {
               update_post_meta( $post->ID, 'view_count', 0 );
            }
        }
    }
    public function check_data_update()
    {
        $temp = false;
        $args = array(
            'post_type' => 'mjob_post',
            'post_status' => 'any',
            'showposts' => -1
            );
        $posts = get_posts( $args );
        foreach ( $posts as $post ) {          
           $sales = get_post_meta( $post->ID, 'et_total_sales', true );
           if( $sales == '' ) {          
               return true;
            }
        }
        return false;
    }
    public function my_admin_notice() {
        if($this->check_data_update()){ ?>
           <div class="notice notice-info is-dismissible"> 
                <p><strong><?php _e("You must update the data before using the advanced search for mJob. Otherwise, the search results won't display the right mJob matching criteria you filter.",'enginethemes') ?></strong></p>
                <input type="button" id="update-data" style="margin-left: 5px; margin-bottom: 5px;" class="button button-primary" value="<?php _e('Update data','enginethemes') ?>">
                
            </div>
            <?php
       }
    }
}
new MJE_Update_Data_Action();
?>