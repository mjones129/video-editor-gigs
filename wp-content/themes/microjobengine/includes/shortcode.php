<?php
function mje_get_list_mjobs($args) {

	$order 			= isset($args['order']) ? $args['order'] : 'DESC';
	$orderby 		= isset($args['orderby']) ? $args['orderby'] : 'date';
	$number_posts 	= isset($args['number_posts']) ? $args['number_posts'] : 8;
	$args = array(
        'post_type' => 'mjob_post',
        'post_status' => 'publish',
        'showposts' => $number_posts,
        'orderby' => $orderby,
        'order' => $order,
    );

    $mjob_query = new WP_Query($args);
    global $ae_post_factory;
    $post_object = $ae_post_factory->get('mjob_post');
    ob_start();
    ?>
    <ul class="row mjob-list">

        <?php
        while ($mjob_query->have_posts()):
        	$mjob_query->the_post();
            global $post;
            $convert = $post_object->convert($post);?>

            <li class="<?php echo mje_home_loop_item_css($convert);?>">
                <?php  mje_get_template('template/mjob-item.php', array('current' => $convert)); ?>
            </li><?php
        endwhile;




	return ob_get_clean();
}

function mje_mjobs_list( $atts ) {

	$order = isset($atts['order']) ? $atts['order'] : 'DESC';
	$orderby = isset($atts['orderby']) ? $atts['orderby'] : 'date';
	$number_posts = isset($atts['number_posts']) ? $atts['number_posts'] : 8;

	$args = shortcode_atts( array(
		'number_posts' => $number_posts,
		'orderby' => $orderby,
		'order' => $order,
	), $atts ,'mje_mjobs');



	return mje_get_list_mjobs($args);
}
add_shortcode( 'mje_mjobs', 'mje_mjobs_list' );

/**
 * html of mje_categories shortode shows
*/
function mje_categories_shortcode($atts){
	$args = shortcode_atts( array(
		'number' => 8,
		'orderby' => 'count',
		'hide_empty' => false,
	), $atts ,'mje_categories');

	$number_posts = $args['number'];
	$orderby = $args['orderby'];
	$terms = get_terms(
                    'mjob_category',
                    array(
                        'orderby'=> $orderby,
                        'order' => 'DESC',
                        'hide_empty'=> $args['hide_empty'],

                    )
                );

    ob_start();
    ?>
    <div class="block-hot-items" style="display: block; clear: both; background-color: transparent;">
	    <div class="container inner-hot-items wow fadeInUpBig">
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
    return ob_get_clean();

}
add_shortcode( 'mje_categories', 'mje_categories_shortcode' );