<?php
global $post, $ae_post_factory;
$post_object = $ae_post_factory->get('mjob_extra');
?>
    <?php
        $args = array(
            'post_type'=> 'mjob_extra',
            'post_status'=> 'publish',
            'showposts'=> ae_get_option('mjob_extra_numbers', 20),
//            'author'=> $post->post_author,
            'post_parent'=> $post->ID
        );
        $posts = query_posts($args);
        $postdata = array();
        echo '<ul class="list-extra mjob-list-extras">';
        if( have_posts()):
            while( have_posts() ):
                the_post();
                $convert = $post_object->convert($post);
                $postdata[] = $convert;
            endwhile;
        else:
            echo '</ul>';
            echo '<p class="no-extra">'. __('There are no extra services', 'enginethemes') .'</p>';
        endif;
        wp_reset_query();
    ?>
<?php
/**
* render post data for js
*/
echo '<script type="data/json" class="extra_postdata" >'.json_encode($postdata).'</script>';