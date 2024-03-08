<?php
/**
 * this template for payment fail, you can overide this template by child theme
*/
global $ad;	
?>
<div class="redirect-content" >
	<div class="main-center float-center">
		<h3 class="block-title"><?php _e("PAYMENT FAIL, FRIEND",'enginethemes');?></h3>
		<div class="checkout-payment">
			<?php
			if($ad) :
				$permalink	=	et_get_page_link('post-service', array( 'id' => $ad->ID ));
			?>
				<div class="content">
					<p><?php _e("You are now redirected to submit listing page",'enginethemes');?></p>
					<p class="time-leave"><?php printf(__('Time left: %s', 'enginethemes' ), '<span class="count_down">10</span>')  ?></p>
				</div>
				<?php echo '<a href="'.$permalink.'" class="btn-submit">'.__("Post a mjob", 'enginethemes').'</a>';
			else :
				$permalink	=	home_url();
			?>
			<div class="content">
					<p><?php _e("You are now redirected to home page",'enginethemes');?></p>
					<p class="time-leave"><?php printf(__('Time left: %s', 'enginethemes' ), '<span class="count_down">10</span>')  ?></p>
			</div>
				<?php echo '<a href="'.$permalink.'" class="btn-submnit">'.__("Home page", 'enginethemes').'</a>';
			endif;
			?>
		</div>
	</div>
</div>
<?php
