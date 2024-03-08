<?php
global $post, $user_ID;
$mjob_post = get_post($post->post_parent);
?>
<!-- MODAL FINISH PROJECT-->
<div class="modal fade" id="modal_review" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true"><img
							src="<?php echo get_template_directory_uri() ?>/assets/img/icon-close.png" alt=""></span>
				</button>
				<h4 class="modal-title"><?php _e("Write review", 'enginethemes') ?></h4>
			</div>
			<div class="modal-body">
			<form role="form" id="review_form" class="review-form">
			 <?php
			 	$employer_name = get_the_author_meta( 'display_name', $mjob_post->post_author );
			  ?>
                <div class="form-group">
                    <span class="post_content"><?php printf(__('Rating','enginethemes'),$mjob_post->post_title); ?> </span>
                    <div class="rating-it" style="cursor: pointer;"></div>
				</div>
				<div class="form-group">
					<textarea name="comment_content" cols="8" rows="10" placeholder="<?php _e('Your review here', 'enginethemes'); ?>"></textarea>
				</div>
                <div class="form-group">
                    <button type="submit" class="<?php mje_button_classes( array( 'btn-ok','waves-effect', 'waves-light') ); ?>">
						<?php _e('Send', 'enginethemes') ?>
                    </button>
					<button type="button" class="btn-skip btn-discard">
						<?php _e('Skip', 'enginethemes') ?>
					</button>
				</div>
				<p class="suggest"><?php _e('After you accept the delivery, this order will be marked as finished and you can write a review for the job.', 'enginethemes'); ?></p>
				</form>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog login -->
</div><!-- /.modal -->
<!-- MODAL FINISH PROJECT-->