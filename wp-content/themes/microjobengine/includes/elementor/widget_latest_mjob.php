<?php
/**
 * Adds Mje_Block_LatestMjob widget.
 */
class Mje_Block_LatestMjob extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'Mje_Block_LatestMjob', // Base ID
			esc_html__( 'Mje Latest Mjob Block', 'enginethemes' ), // Name
			array( 'description' => esc_html__( 'Mje Latest Mjob Block', 'enginethemes' ), ) // Args
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

		$title 		= ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Latest Microjobs', 'enginetheme' );
		$orderby 	= ! empty( $instance['orderby'] ) ? $instance['orderby'] : 'date';
		$order 		= ! empty( $instance['order'] ) ? $instance['order'] : 'DESC';
		$number_posts = ! empty( $instance['number_posts'] ) ? $instance['number_posts'] : 8;

		$args 	= array('orderby'=>$orderby,'order'=>$order,'number_posts' => $number_posts);

		?>
		<div class="block-items">
            <div class="container">
            	<p class="block-title float-center"><?php echo $title;?></p>

            	<?php
            	$this->list_mjob($args); ?>
			</div>
		</div>
		<?php
	}
	function list_mjob($args){

		$orderby = $args['orderby'];
		$order = $args['order'];
		$number_posts = $args['number_posts'];

		echo do_shortcode( "[mje_mjobs number_posts={$number_posts} orderby = {$orderby} order = {$order}]" );
		?>
		<div class="view-all-jobs-wrap widget-custom-button">
            <a class="btn-order waves-effect waves-light btn-submit mjob-order-action" href="<?php echo get_post_type_archive_link('mjob_post');?>">
                <?php _e('View all jobs','enginetheme');?>
            </a>
        </div>
        <?php
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Latest Microjobs', 'enginetheme' );
		$number_posts = ! empty( $instance['number_posts'] ) ? $instance['number_posts'] : 8;
		$orderby = ! empty( $instance['orderby'] ) ? $instance['orderby'] : 'date';
		$order = ! empty( $instance['order'] ) ? $instance['order'] : 'DESC';

		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'enginethemes' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'number_posts' ) ); ?>"><?php esc_attr_e( 'Number Posts:', 'enginethemes' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'number_posts' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number_posts' ) ); ?>" type="number" value="<?php echo esc_attr( $number_posts ); ?>">
		</p>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'Order By' ) ); ?>"><?php esc_attr_e( 'Order By:', 'enginethemes' ); ?></label>
		<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) ); ?>"  value="<?php echo esc_attr( $orderby ); ?>">
			<option value="date" <?php selected('date',$orderby);?> >Post Date</option>
			<!--<option value="featured"  <?php selected('featured',$orderby);?> > Featured Mjob</option> !-->
			<option value="rand"  <?php selected('random',$orderby);?> >Random</option>
		</select>

		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'Order' ) ); ?>"><?php esc_attr_e( 'Order:', 'enginethemes' ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'order' ) ); ?>" value="<?php echo esc_attr( $order ); ?>">
				<option value="DESC"  <?php selected('desc',$order);?>>DESC</option>
				<option value="ASC" <?php selected('asc',$order);?>>ASC</option>
			</select>
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
		$instance['orderby'] = ( ! empty( $new_instance['orderby'] ) ) ? sanitize_text_field( $new_instance['orderby'] ) : 'date';
		$instance['order'] = ( ! empty( $new_instance['order'] ) ) ? sanitize_text_field( $new_instance['order'] ) : 'DESC';
		$instance['number_posts'] = ( ! empty( $new_instance['number_posts'] ) ) ? sanitize_text_field( $new_instance['number_posts'] ) : 8;

		return $instance;
	}

}