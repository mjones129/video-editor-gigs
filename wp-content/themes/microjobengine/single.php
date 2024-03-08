<?php
/**
 * The main Single template file
 *
 * @package WordPress
 * @subpackage MicrojobEngine
 * @since MicrojobEngine 1.0
 */
global $post;
get_header();
the_post();
?>
<div class="container dashboard withdraw">
	<!-- block control  -->
	<div class="row block-posts post-detail" id="post-control">
		<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 blog-sidebar" id="right_content">
			<div class="menu-left">
				<p class="title-menu"><?php _e('Categories', 'enginethemes'); ?></p>
				<?php mje_show_filter_categories('category', array('parent' => 0)); ?>
			</div>
		</div><!-- RIGHT CONTENT -->
		<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 posts-container">
			<div class="blog-wrapper">
	            <div class="row">
					<h1 class="blog-title"><?php the_title(); ?></h1>
					<div class="blog-content">
						<p class="author-post">
							<?php
								printf( __( 'Written by %s in %s', 'enginethemes' ),
									get_the_author(),
									get_the_category_list( __( ', ', 'enginethemes' ) )
								);
							?>
						</p>
						<p class="date-post"><?php the_time('M j');  ?> <sup><?php the_time('S');?></sup>, <?php the_time('Y');?></p>

						<div class="post-content">
							<?php
							the_content();
							wp_link_pages( array(
								'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'enginethemes' ) . '</span>',
								'after'       => '</div>',
								'link_before' => '<span>',
								'link_after'  => '</span>',
							) );
							?>
						</div>
						<div class="cmt">
							<p><span class="text-comment">comments</span>(<?php comments_number(); ?>)</p>
	                    </div><!-- end cmt count -->
					</div>
	            </div>
	        </div>
	        <div class="clearfix"></div>
	        <?php comments_template(); ?>
		</div><!-- RIGHT CONTENT -->
	</div>
	<!--// block control  -->
</div>
<?php
	get_footer();
?>