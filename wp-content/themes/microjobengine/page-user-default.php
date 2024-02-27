<?php
/**
 * Template Name: User Default Template
 */
global $current_user;
global $post;
get_header();
?>
    <div id="content" class="my-list-order">
		<div class="block-page">
			<div class="container dashboard withdraw">
				<div class="row title-top-pages">
					<div class="block-title">
						<div class="left-title"><?php echo $post->post_title; ?></div>
					</div>
					<div style="clear:both"></div>
					<p><a href="<?php echo et_get_page_link('dashboard'); ?>" class="btn-back"><i class="fa fa-angle-left"></i><?php _e('Back to dashboard', 'enginethemes'); ?></a></p>
				</div>
				 <div class="row profile">
					 <div class="col-lg-3 col-md-3 col-sm-12 col-sx-12 profile">
						<?php get_sidebar('my-profile'); ?>
					 </div>
					 <div class="col-lg-9 col-md-9 col-sm-12 col-sx-12 outer-revenues">
							<div class="content_page">
								<?php the_content(); ?>
							</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php
get_footer();
?>