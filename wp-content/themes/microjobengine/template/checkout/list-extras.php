<?php
global $ae_post_factory;
$post_object = $ae_post_factory->get('mjob_extra');
?>
<?php
$args = array(
    'post_type'=> 'mjob_extra',
    'post_status'=> 'publish',
    'post_parent'=> $post->ID
);
$posts = query_posts($args);
$postdata = array();
if( have_posts()):
    echo '<ul class="list-extra mjob-list-extras">';
    global $post;
    while( have_posts() ):
        the_post();
        mje_get_template_part( 'template/extra', 'item' );
        $convert = $post_object->convert($post);
        $postdata[] = $convert;
    endwhile;
    echo '</ul>';
else:
    echo '<p class="no-extra">'. __('There are no extra services', 'enginethemes') .'</p>';
endif;
wp_reset_query();
?>
<?php

// render post data for js
echo '<script type="data/json" class="extra_postdata" >'.json_encode($postdata).'</script>';