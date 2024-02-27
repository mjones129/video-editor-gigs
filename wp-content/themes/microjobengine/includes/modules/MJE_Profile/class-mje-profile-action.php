<?php
class MJE_Profile_Action extends MJE_Post_Action
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
    /**
     * the constructor of this class
     *
     */
    public function __construct($post_type = 'mjob_profile') {
        parent::__construct($post_type);
        $this->add_ajax('mjob_sync_profile', 'sync');
        $this->add_ajax('mjob_crop_avatar', 'crop_avatar');
        $this->add_action('ae_insert_user', 'insert_profile', 10, 2);
        $this->add_action('ae_login_user', 'insert_profile_after_login', 10, 1);
        $this->add_action('wp_footer', 'add_profile_modal');
    }

    /**
     * Insert profile after user sign up
     * @param int $result
     * @param object $user_data
     * @since 1.0
     * @package MicrojobEngine
     * @category Profile
     * @author Tat Thien
     */
    public function insert_profile($result, $user_data) {
        $user = get_userdata($result);
        $profile = wp_insert_post(array(
            'post_type' => 'mjob_profile',
            'post_status' => 'publish',
            'post_title' => $user->display_name,
            'post_author' => $result
        ));

        if(!is_wp_error($profile)) {
            update_user_meta($result, 'user_profile_id', $profile);
        }
    }

    /**
     * If is assign user from multi site then create a profile
     * @param object $result
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category void
     * @author Tat Thien
     */
    public function insert_profile_after_login($result) {
        $user_profile_id = get_user_meta($result->ID, 'user_profile_id', true);
        $profile = get_post($user_profile_id);
        if(empty($user_profile_id) || empty($profile)) {
            $profile = wp_insert_post(array(
                'post_type' => 'mjob_profile',
                'post_status' => 'publish',
                'post_title' => $result->display_name,
                'post_author' => $result->ID
            ));

            if(!is_wp_error($profile)) {
                update_user_meta($result->ID, 'user_profile_id', $profile);
            }
        }
    }

    /**
     * Sync profile
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Profile
     * @author Tat Thien
     */
    public function sync() {
        global $current_user;
        $request = $_REQUEST;
        // Check valid user
        if($request['post_author'] != $current_user->ID) {
            wp_send_json(array(
                'success' => false,
                'msg' => __('Invalid user.', 'enginethemes')
            ));
        }

        // Check active user
        if(!mje_is_user_active($current_user->ID)) {
            wp_send_json(array(
                'success' => false,
                'msg' => __('Your account is pending. You have to activate your account to continue this step.', 'enginethemes')
            ));
        }
        // Update timzone user
        if(isset($request['timezone_local']))
            update_user_meta($current_user->ID, 'timezone_local', $request['timezone_local']);

        $result = $this->sync_post($request);

        if($result['success'] != false && !is_wp_error($result)) {
            if($request['method'] == 'create') {
                update_user_meta($current_user->ID, 'user_profile_id', $result['data']->ID);
            }

            wp_send_json(array(
                'success' => true,
                'data' => $result['data'],
                'msg' => __('Successful update.', 'enginethemes')
            ));
        } else {
            wp_send_json(array(
                'success' => false,
                'msg' => $result['msg']
            ));
        }
    }

    /**
     * Crop user avatar
     * @param void
     * @return void
     * @since 1.0
     * @package MicrojobEngine
     * @category Profile
     * @author Tat Thien
     */
    public function crop_avatar() {
        global $current_user;
        $request = $_REQUEST;

        // Check valid image
        if(!isset($request['attach_id']) || empty($request['attach_id'])) {
            wp_send_json(array(
                'success' => false,
                'msg' => __('Invalid image!', 'enginethemes')
            ));
        }

        // Check valid user
        if($request['user_id'] != $current_user->ID) {
            wp_send_json(array(
                'success' => false,
                'msg' => __('Invalid user!', 'enginethemes')
            ));
        }

        $des_file = wp_crop_image(
            $request['attach_id'],
            $request['crop_x'],
            $request['crop_y'],
            $request['crop_width'],
            $request['crop_height'],
            $request['crop_width'],
            $request['crop_height']
        );

        // Check the type of file. We'll use this as the 'post_mime_type'.
        $filetype = wp_check_filetype(basename( $des_file ), null);

        // Get the path to the upload directory.
        $wp_upload_dir = wp_upload_dir();

        // Prepare an array of post data for the attachment.
        $attachment = array(
            'guid'           => $wp_upload_dir['url'] . '/' . basename($des_file),
            'post_mime_type' => $filetype['type'],
            'post_title'     => preg_replace( '/\.[^.]+$/', '', basename($des_file)),
            'post_content'   => '',
            'post_status'    => 'inherit'
        );

        // Insert the attachment.
        $attach_id = wp_insert_attachment($attachment, $des_file);

        // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
        require_once( ABSPATH . 'wp-admin/includes/image.php' );

        // Generate the metadata for the attachment, and update the database record.
        $attach_data = wp_generate_attachment_metadata($attach_id, $des_file);
        wp_update_attachment_metadata($attach_id, $attach_data);

        $attach_data = et_get_attachment_data($attach_id);

        if (!isset($request['user_id'])) return;

        $ae_users = AE_Users::get_instance();

        //update user avatar
        $user = $ae_users->update(array(
            'ID' => $request['user_id'],
            'et_avatar' => $attach_data['attach_id'],
            'et_avatar_url' => $attach_data['thumbnail'][0]
        ));

        wp_send_json(array(
            'success' => true,
            'msg' => __('Your profile picture has been uploaded successfully.', 'enginethemes') ,
            'data' => $attach_data
        ));
    }

    public function convert_profile($result) {
        $result->post_content= !empty($result->post_content) ? $result->post_content : __('There is no content', 'enginethemes');
        $result->payment_info = !empty($result->payment_info) ? $result->payment_info : __('There is no content', 'enginethemes');
        $result->billing_full_name = !empty($result->billing_full_name) ? $result->billing_full_name : __('There is no content', 'enginethemes');
        $result->billing_full_address = !empty($result->billing_full_address) ? $result->billing_full_address : __('There is no content', 'enginethemes');
        $result->billing_country = !empty($result->billing_country) ? $result->billing_country : __('There is no content', 'enginethemes');
        $result->billing_vat = !empty($result->billing_vat) ? $result->billing_vat : __('There is no content', 'enginethemes');

        return $result;
    }

    public function add_profile_modal() {
        ?>
        <div class="modal fade" id="uploadAvatar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><img
                                    src="<?php echo get_template_directory_uri() ?>/assets/img/icon-close.png" alt=""></span></button>
                        <h4 class="modal-title" id="myModalLabel1"><?php _e('Upload Avatar', 'enginethemes'); ?></h4>
                    </div>
                    <div class="modal-body">
                        <div class="inner-form">
                            <div id="upload_avatar_container" class="image-upload" style="margin-bottom: 30px;">
                                <div id="upload_avatar_browse_button" class="browse_button">
                                    <div class="drag-image">
                                        <i class="fa fa-cloud-upload"></i>
                                        <span class="drag-image-title"><?php _e('Drag profile image here', 'enginethemes'); ?></span>
                                        <span class="drag-image-text"><?php _e('or', 'enginethemes'); ?></span>
                                        <a class="drag-image-select-button"><?php _e('upload from local storage', 'enginethemes'); ?></a>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" class="et_ajax_nonce" value="<?php echo de_create_nonce( 'upload_avatar_et_uploader' ); ?>">
                            <div class="form-group float-right">
                                <button class="<?php mje_button_classes( array( 'btn-save') ); ?>" disabled="true"><?php _e('SAVE', 'enginethemes'); ?></button>
                                <a href="#" class="btn-remove"><?php _e('REMOVE IMAGE', 'enginethemes'); ?></a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
$new_instance = MJE_Profile_Action::get_instance();