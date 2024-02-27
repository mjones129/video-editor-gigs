<?php
class MJE_Extra_Action extends MJE_Post_Action{
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
    /**
     * the constructor of this class
     *
     */
    public  function __construct($post_type = 'mjob_extra'){
        $this->post_type = 'mjob_extra';
        $this->add_ajax('ae-fetch-mjob_extra', 'fetch_post');
        $this->add_ajax('ae-mjob_extra-sync', 'sync');
        $this->add_filter('ae_convert_mjob_extra', 'convert');
        $this->add_filter('ae_convert_after_insert_mjob_extra', 'convert');
        $this->add_action('admin_menu', 'add_submenu_page');
        $this->ruler = array(
            'post_title'=>'required',
            'et_budget'=>'required'
        );
    }

    public function add_submenu_page() {
        add_submenu_page(
            'edit.php?post_type=' . 'mjob_post',
            __('Extras', 'enginethemes'),
            __('Extras', 'enginethemes'),
            'manage_options',
            'edit.php?post_type=mjob_extra'
        );
    }

    /**
     * sync Post function
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function sync(){
        $request = $_POST;
        $result = $this->validatePost($request);
        if( $result['success'] ){
            $request['post_status'] = 'publish';
            if( $request['et_budget'] < 0 ){
                $request['et_budget'] = 0;
            }
            if( isset($request['post_title']) ) {
                $request['post_content'] = $request['post_title'];
            }
            else{
                $request['post_content'] = '';
            }
            $result = $this->sync_post($request);
        }
        wp_send_json($result);
    }
    /**
     * convert post
     *
     * @param object $result
     * @return object $result after convert
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function convert( $result ){
        $result->et_budget_text = mje_shorten_price($result->et_budget);
        return $result;
    }
    /**
     * validate data
     *
     * @param array $data
     * @return array $result
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function validatePost($data){
        $result = array(
            'success'=> true,
            'msg'=> __('Success!', 'enginethemes')
        );
        return $result;
    }
    /**
     * get extra of a Microjob
     *
     * @param integer $extra_id
     * @param integer $mjob_id;
     * @return object $extra or false
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function get_extra_of_mjob($extra_id, $mjob_id){
        global $ae_post_factory;
        $post_obj = $ae_post_factory->get('mjob_extra');
        $post = get_post($extra_id);
        if( !$post || $post->post_parent != $mjob_id ){
            return false;
        }
        $extra = $post_obj->convert($post);
        return $extra;
    }
    /**
     * filter query_args
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function filter_query_args($query_args){
        $query = $_REQUEST['query'];
        $args = array();
        if( isset($query['post_parent']) ) {
            $args = array(
                'post_type' => 'mjob_extra',
                'post_status' => 'publish',
                'showposts' => ae_get_option('mjob_extra_numbers', 20),
                'post_parent'=> $query['post_parent']
            );
        }
        $query_args = wp_parse_args($args, $query_args);
        return $query_args;
    }
}
new MJE_Extra_Action();