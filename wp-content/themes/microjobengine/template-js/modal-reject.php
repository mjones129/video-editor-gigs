<div class="modal fade" id="reject_post">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><img
							src="<?php echo get_template_directory_uri() ?>/assets/img/icon-close.png" alt=""></span></button>
				<h4 class="modal-title modal-title-sign-in" id="myModalLabel">
                    <?php printf(__("Reject <span>%s</span>", 'enginethemes'), 'post' ) ; ?>
                </h4>
			</div>
			<div class="modal-body">
            	<form class="et-form reject-ad reject-project form_modal_style">
                    		
                    <div class="form-group">
                        <!--<label><?php /*_e("MESSAGE", 'enginethemes') */?><span class="alert-icon">*</span></label>-->
                        <textarea name="reject_message" rows="10" placeholder="<?php _e('Inactive text field', 'enginethemes'); ?>"></textarea>
                    </div>  
                    <div class="clearfix"></div>
                    <button type="submit" class="<?php mje_button_classes( array( 'btn-sumary', 'mjob-button-reject' ) ); ?>">
						<?php _e('Reject', 'enginethemes') ?>
					</button>             
                    
                </form>  
			</div>
			
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->