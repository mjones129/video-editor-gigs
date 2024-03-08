<?php
/**
 * Adds Mje_Block_Categories widget.
 */
class Mje_Block_Categories extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'Mje_Block_Categories', // Base ID
			esc_html__( 'Mje Block Categories', 'enginethemes' ), // Name
			array( 'description' => esc_html__( 'Mje Block_Categories For Home Page', 'enginethemes' ), ) // Args
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

	public function widget( $args, $instance ){
		$cat_title 	= ! empty( $instance['title'] ) ? $instance['title'] : __('FIND WHAT YOU NEED', 'enginethemes');
		$orderby 	= ! empty( $instance['orderby'] ) ? $instance['orderby'] :'count';
		$order 		= isset($instance['order']) ? $instance['order'] : 'DESC';

		$hide_empty =  false;
		if( isset($instance['hide_empty']) && $instance['hide_empty'] == 'on'){
			$hide_empty= true;
		}
		$featured_cats = 1;
		if( isset($instance['featured_cats']) && $instance['featured_cats'] == 'off'){
			$featured_cats= 0;
		}
		?>
		<div class="block-hot-items">
            <div class="container inner-hot-items wow fadeInUpBig">
                <p class="block-title"><?php echo $cat_title; ?></p>
                <?php
                $df_args = array(
                    'orderby'=> $orderby,
                    'order' => $order,
                    'hide_empty'=> $hide_empty,
                    'offset' => 0, // begin this item possition.
                    'number' => 8,
                );
                if($featured_cats){
                	$df_args['meta_key'] = 'featured-tax';
                	$df_args['meta_value'] = array('1','true',1, true);
                }
				$terms = get_terms(
                    'mjob_category',$df_args
                );?>
                <ul class="row">
                    <?php

                    if ( !empty( $terms ) && !is_wp_error( $terms ) ):

                    	foreach ($terms as $key => $term) {
                            $img_url = get_template_directory_uri() . '/assets/img/icon-1.png';
                            $meta = get_term_meta($term->term_id,'featured-tax', true);
                       		$img = get_term_meta($term->term_id, 'mjob_category_image', true);
                            $link = get_term_link($term->term_id, 'mjob_category');

                    		if ( !empty( $img ) ) {
                    			$img_url = esc_url(wp_get_attachment_image_url($img, 'full'));
                    		}?>

                            <li class="col-lg-3 col-md-3 col-sm-6 col-xs-6">
                                <a href="<?php echo $link; ?>">
                                    <div class="hvr-float-shadow">
                                        <div class="avatar">
                                            <img src="<?php echo $img_url; ?>" alt="">
                                            <div class="line"><span class="line-distance"></span></div>
                                        </div>
                                        <h2 class="name-items"> <?php echo $term->name ?>  </h2>
                                    </div>
                                </a>
                            </li>
                            <?php
                    	}

                    endif;?>
                </ul>
            </div>
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

		$title 			= ! empty( $instance['title'] ) ? $instance['title'] : __( 'FIND WHAT YOU NEED', 'enginetheme' );
		$hide_empty 	= isset($instance['hide_empty']) ? $instance['hide_empty'] : 'off';
		$orderby 		= isset($instance['orderby']) ? $instance['orderby'] : 'count';
		$order 			= isset($instance['order']) ? $instance['order'] : 'DESC';
		$featured_cats 	= isset($instance['featured_cats']) ? $instance['featured_cats'] : 'on';
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'enginethemes' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'featured_cats' ) ); ?>"><?php esc_attr_e( 'Featured Categories:', 'enginethemes' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'featured_cats' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'featured_cats' ) ); ?>" value="1" type="checkbox"  <?php checked('on', $featured_cats);?>>
		</p>

		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'Order By' ) ); ?>"><?php esc_attr_e( 'Order By:', 'enginethemes' ); ?></label>
		<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) ); ?>"  value="<?php echo esc_attr( $orderby ); ?>">
			<option value="count" <?php selected('count',$orderby);?> >Count</option>
			<option value="name"  <?php selected('name',$orderby);?> >Name</option>
		</select>

		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'Order' ) ); ?>"><?php esc_attr_e( 'Order:', 'enginethemes' ); ?></label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'order' ) ); ?>" value="<?php echo esc_attr( $order ); ?>">
				<option value="DESC"  <?php selected('desc',$order);?>>DESC</option>
				<option value="ASC" <?php selected('asc',$order);?>>ASC</option>
			</select>
		</p>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'hide_empty' ) ); ?>"><?php esc_attr_e( 'Hide empty posts:', 'enginethemes' ); ?></label>
		<input class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'hide_empty' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name('hide_empty') ); ?>"  <?php checked('on', $hide_empty);?> type="checkbox">
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
		$instance['title']			= $new_instance['title'];
		$instance['orderby']		= $new_instance['orderby'];
		$instance['order'] 			= $new_instance['order'];
		$instance['featured_cats'] 	= isset($new_instance['featured_cats']) ? 'on' : 'off';
		$instance['hide_empty'] 	= isset($new_instance['hide_empty']) ? 'on' :'off';
		return $instance;
	}

}