<?php
class MJE_Post_Action extends AE_PostAction{
    public static $instance;
    public $ruler;
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
    public  function __construct($post_type = 'post'){
        parent::__construct($post_type);
    }
    /**
     * sync post
     *
     * @param array $request
     * @return array $result
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function sync_post($request) {

        /*
         * Defaul result
         */
        global $user_ID;
		/*
		* add var data_price before update mjob
		* author : Tan Hoai
		* version 1.3.1
		*/

        $id = isset($request['ID']) ? $request['ID'] : 0;
		$data_prices = array(
            'ID'=> $id,
            'old'=> get_post_meta( $id, 'et_budget', true),
            'new'=> isset($request['et_budget']) ? $request['et_budget'] : 0,
        );

        $result = array(
            'success' => FALSE,
            'msg'     => __( 'Failed!', 'enginethemes' )
        );
        $ruler = array(
            'post_type' => 'required',
        );
        $resp  = $this->checkPendingAccount($request);
        if( !$resp['success'] ){
            return $resp;
        }
        if ( ! de_verify_nonce( $request['_wpnonce'], 'ae-mjob_post-sync' ) ){
            $result = array(
                'success' => false,
                'msg' => __( "You can't not do this action!", 'enginethemes' )
            );
            return $result;
        }
        $this->ruler = wp_parse_args($this->ruler, $ruler);
        $error_message = array(
            'post_type.required' =>__('Don\'t try to hack 1!', 'enginethemes'),
            'nonce.required' =>__('Don\'t try to hack 2!', 'enginethemes'),
            'nonce.nonce' =>__('Don\'t try to hack 3!', 'enginethemes'),
        );
        $validator = new AE_Validator( $request, $this->ruler, array(), $error_message);
        if ( $validator->fails() ) {
            $result[ 'msg' ]  = __( 'Invalid input. Please try again.', 'enginethemes' );
            $result[ 'data' ] = $validator->getMessages();
        } else {
            //Request pass the rules, call post object to sync it
            global $ae_post_factory;
            if(!isset($request[ 'post_type' ]))
            {
                $result[ 'success' ] = FALSE;
                $result[ 'msg' ]     = __( 'Don\'t try to hack my groom baby!', 'enginethemes' );
                return $result;
            }
            $post_object = $ae_post_factory->get( $request[ 'post_type' ] );
            if ( NULL == $post_object ) {
                $result[ 'success' ] = FALSE;
                $result[ 'msg' ]     = __( 'Don\'t try to hack my groom baby!', 'enginethemes' );
                return $result;
            }
            // unset package data when edit place if user can edit others post
            if (isset($request['archive'])) {
                $request['post_status'] = 'archive';
            }
            if (isset($request['publish'])) {
                $request['post_status'] = 'publish';
            }
            if (isset($request['delete'])) {
                $request['post_status'] = 'trash';
            }
            if (isset($request['disputed'])) {
                $request['post_status'] = 'disputed';
            }
            if (isset($request['pause'])) {
                $request['post_status'] = 'pause';
                unset($request['pause']);
            }
            if (isset($request['unpause'])) {
                $request['post_status'] = 'unpause';
                unset($request['unpause']);
            }
            if (isset($request['finished'])) {
                $request['post_status'] = 'finished';
                unset($request['finished']);
            }
            // Call instance sync

            $post = $post_object->sync( $request );
            if ( is_wp_error( $post ) ) {
                //Not inserted
                $result[ 'success' ] = FALSE;
                $result[ 'msg' ]     = $post->get_error_messages();
                $result[ 'data' ]    = $post->get_error_data();
            } else {
                if ( isset($request['et_carousels']) && ! empty( $request['et_carousels'] ) ) {

                    // loop request carousel id
                    foreach ($request['et_carousels'] as $key => $value) {
                        $att = get_post($value);
                        // just admin and the owner can add carousel
                        global $user_ID;
                        if( isset($att->post_author) ) {
                            if (current_user_can('manage_options') || $att->post_author == $user_ID) {
                                wp_update_post(array(
                                    'ID' => $value,
                                    'post_parent' => $post->ID
                                ));
                            }
                        }
                    }

                    if (current_user_can('manage_options') || $att->post_author == $user_ID) {

                        /**
                         * featured image not null and should be in carousels array data
                         */
                        if (!isset($request['featured_image'])) {
                            set_post_thumbnail($post->ID, $value);
                        }
                    }
                }
                $result[ 'success' ] = TRUE;
                $result[ 'data' ]    = $post_object->convert($post);



                if( 'remove' === $request['method'] ) {
                    $result[ 'msg' ]     = __( 'Delete successfully!', 'enginethemes' );
                } else {
					/**
					 * Add action cheat price change
					 *
					 * @since 1.3.1
					 * @author Tan Hoai
					 */
					do_action('mje_action_after_update_mjob',$data_prices);
					$result[ 'msg' ]     = __( 'Successful!', 'enginethemes' );
                }
            }

        }

        return $result;
    }
    /**
     * check pending account
     *
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author JACK BUI
     */
    public function checkPendingAccount($request){
        global $user_ID;
        $result = array(
            'success'=> true,
            'msg'=> __('Success', 'enginethemes')
        );
        if (!AE_Users::is_activate($user_ID)) {
            $result = array(
                'success' => false,
                'msg' => __("Your account is pending. You have to activate your account to continue this step.", 'enginethemes')
            );
        }
        return apply_filters('mjob_check_pending_account', $result, $request);
    }

}