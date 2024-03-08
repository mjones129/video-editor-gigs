<?php

function new_modify_user_table( $column ) {
    $column['status'] = 'Status';
    return $column;
}
add_filter( 'manage_users_columns', 'new_modify_user_table' );

function new_modify_user_table_row( $val, $column_name, $user_id ) {
    $user       = get_userdata($user_id);

    switch ($column_name) {
        case 'status' :
            $is_banned =  get_user_meta($user_id, 'is_banned', true );
            if($is_banned){
                return '<span  class="user-banned"  action="unlockuser" userID="'.$user_id.'"  title="Unlock this user?"></span>';
            }
            return ' <span  class="user-active" userLogin="'.$user->user_login.'" action="banuser" data-toggle="modal" data-target="#statusModal" userID="'.$user_id.'"  title="Ban this user?"></span>';

        default:
    }
    return $val;
}
add_filter( 'manage_users_custom_column', 'new_modify_user_table_row', 10, 3 );

add_action('admin_head', 'add_css_list_User');

function add_css_list_User($hook_suffix) {
    global $pagenow;
    if($pagenow == 'users.php'){ ?>
    <link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri();?>/assets/css/modal-bootstrap.css">
    <style>

        .hidden{
            display: none;
        }
        .fixed .column-status{
            width: 74px;
        }
        .user-active,
        .user-banned{
            background-image: url(<?php echo get_template_directory_uri();?>/assets/img/lock.png);
            width: 15px;
            height: 21px;
            display: block;
            cursor: pointer;
            position: relative;
            top: 2px;
            padding: 10px 15px;
            background-repeat: no-repeat;
        }
        .user-banned{
            background-position: bottom left;
        }
        .user-active{
             background-position: top left;
        }
        #screen-options-wrap .hidden{

        }
    </style>
    <?php
    }
}
function et_add_js(){
    global $pagenow;
    if($pagenow == 'users.php'){ ?>
    <!-- Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        <form class="ban-user">
            <div class="modal-header">
                <h3 class="modal-title" id="statusModalLabel"><?php _e("Ban user ",'enginethemes');?><span id="userLogin"></span> </h3>
                </button>
          </div>
          <div class="modal-body">
              <div class="form-group">
                <!--<label for="message-text" class="col-form-label">Reason:</label> !-->
                <textarea class="form-control" id="reason" name="reason" placeholder="<?php _e('Tell user to know the reason','enginethemes');?>"></textarea>
                <input type="hidden" id="userID" name="userID" >
                <input type="hidden" id="action" name="action" >
              </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php _e('Close','enginethemes');?></button>
            <button type="submit" class="btn btn-primary"><?php _e('Submit','enginethemes');?></button>
          </div>
        </form>
    </div>

  </div>
</div>
<style type="text/css">
    #message-text{
        height: 100px;
    }
    #statusModal{
        top: 15%;
        width1: 500px;
        margin: 0 auto;
    }
    #statusModal .modal-dialog{
        width: 450px;
    }
    textarea#reason{
        height: 100px;
    }
    #userLogin{
        font-style: italic;
    }
</style>
    <script type="text/javascript">
       (function( $ ){
            $( document ).ready(function() {
                $(".ban-user").submit(function(event){
                    var form       = $(event.currentTarget);
                    var userID     = form.find('#userID').val();
                    var reason     = form.find('#reason').val();
                    var action     = form.find("#action").val();

                    $.ajax({
                        url: ae_globals.ajaxURL,
                        type: 'POST',
                        data: {action: action,userID: userID, reason: reason},
                        success: function(resp, status, jqXHR) {
                            $('#statusModal').modal('hide');
                            if(resp.success){
                                location.reload();
                            } else {
                                alert(resp.msg);
                            }
                        }
                    });
                    return false;
                 });
                $(".user-banned").click(function(event){
                    var ok = confirm("Unlock this user. Are you sure?");
                    var btn       = $(event.currentTarget);
                    var  userID = btn.attr('userID');
                    var data = {action:'unlockuser',userID:userID};
                    $.ajax({
                        url: ae_globals.ajaxURL,
                        type: 'POST',
                        data: data,
                        success: function(resp, status, jqXHR) {
                            if(resp.success){
                                location.reload();
                            } else {
                                alert(resp.msg);
                            }
                        }
                    });

                });
            });

            $('#statusModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget) // Button that triggered the modal
                var userID = button.attr('userID');
                var action = button.attr('action');
                var userlogin = button.attr('userLogin');
                var modal = $(this);

                if(action == 'banuser'){
                    // modal.find("#statusModalLabel").html('Ban this user');
                }else{
                    modal.find("#statusModalLabel").html('Unlock this user.');
                }

                modal.find('#userID').val(userID);
                modal.find('#userLogin').html(userlogin);
                modal.find('#action').val(action)
            })


        })(jQuery);
    </script> <?php
    }
}
add_action('admin_footer','et_add_js');

function mje_banuser(){
    $user_id    = $_POST['userID'];
    $reason     = isset($_POST['reason']) ? $_POST['reason'] : '';
    $resp       = array('success' => true,'msg' => 'User has been banned.');
    $user       = get_userdata($user_id);
    $user_roles = $user->roles;


    if ( in_array( 'administrator', $user_roles, true ) || is_super_admin($user_id) || $user->has_cap( 'manage_options' ) ) {
        $resp = array('success' => false,'msg' => 'You can not ban this user.');
        wp_send_json($resp);
    }

    if( current_user_can('manage_options') ){
        update_user_meta($user_id, 'is_banned', true);

        $subject = __("OOP. Your accont has been banned", 'enginethemes');

        $message = "<p>Hello [display_name],</p>

            <p>You have been banned from [blogname] for reason:</p>
            <p>[reason]</p>
            <p>Please contact our staff for more information</p>
            <p> Sincerely, <br />[blogname]</p>";
        $message = str_replace('[reason]', $reason , $message);

        AE_Mailing::get_instance()->wp_mail($user->user_email, $subject, $message, array(
            'user_id' => $user_id
        ));

    } else {
        $resp = array('success' => false,'msg' => __('You can not ban this user.','enginethemes') );
    }
    wp_send_json($resp);
}
add_action('wp_ajax_banuser','mje_banuser');
function mje_unlockuser(){
    $user_id    = $_POST['userID'];
    $resp       = array('success' => true,'msg' => __('User has been unlock.','enginethemes'));
    if( current_user_can('manage_options') ){

        $message = "<p>Hello [display_name],</p>
            <p> You have been unbanned.</p>
            You can login now.
            <p> Sincerely, <br />[blogname]</p>";
        update_user_meta($user_id, 'is_banned', 0);
        $user       = get_userdata($user_id);
        $subject = __("You account has been unbanned", 'enginethemes');
        AE_Mailing::get_instance()->wp_mail($user->user_email, $subject, $message, array(
            'user_id' => $user_id
        ));
    } else{
        $resp       = array('success' => false,'msg' => __('You can not unban this user','enginethemes'));
    }
    wp_send_json($resp);
}
add_action('wp_ajax_unlockuser','mje_unlockuser');