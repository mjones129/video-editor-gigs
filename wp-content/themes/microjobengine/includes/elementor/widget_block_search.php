<?php
/**
 * Adds Foo_Widget widget.
 */
class Mje_Block_Search extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'Mje_Block_Search', // Base ID
			esc_html__( 'Mje Search Block', 'enginethemes' ), // Name
			array( 'description' => esc_html__( 'Mje Search For Home Page', 'enginethemes' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {

		$website = "http://example.com";

		// Get heading title and sub title
		$heading_title 	= get_theme_mod('home_heading_title') ? get_theme_mod('home_heading_title') : __('Get your stuffs done from $5', 'enginethemes');
		$sub_title 		= get_theme_mod('home_sub_title') ? get_theme_mod('home_sub_title') : __('Browse through millions of micro jobs. Choose one you trust. Pay as you go.', 'enginethemes');

		$img_url		= ae_get_option('search_background');
		$img_theme_mod 	= get_theme_mod('search_background');
		if (!empty($img_url)) {
			$img_url = $img_url['full']['0'];
		} elseif (false === $img_theme_mod) {
			$img_url = get_template_directory_uri() . '/assets/img/bg-slider.jpg';
		} else {
			$img_url = "";
		}
		$has_geo_ext 	= apply_filters('has_geo_extension','');
		 $skin_name 	= MJE_Skin_Action::get_skin_name();
    	if($skin_name !== 'diplomat'){ ?>
		    <div class="slider <?php echo $has_geo_ext;?> mje-wiget-block">
	            <?php
	            if ( ! is_acti_mje_geo() ){
	            	mje_search_form($heading_title, $sub_title);
	        	} else{
		            do_action('mje_geo_search_form', $heading_title, $sub_title);
		        } ?>

		        <div class="background-image">
		            <div class="backgound-under" style="background: url(<?php echo $img_url; ?>); background-repeat: no-repeat; background-size: cover;"></div>
		            <!-- <img src="<?php //echo $img_url; ?>" alt="" class="wow fadeIn"> -->
		        </div>
		        <div class="statistic-job-number">
		            <p class="link-last-job"><?php echo sprintf(__('There are %s microjobs more', 'enginethemes'), mje_get_mjob_count()); ?> <div class="bounce"><i class="fa fa-angle-down"></i></div></p>
		        </div>
		    </div>
				<?php
		} else {
			mje_diplomat_slider_block();
		}

	}

	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Latest Microjobs', 'enginetheme' );
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'enginethemes' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';

		return $instance;
	}

}