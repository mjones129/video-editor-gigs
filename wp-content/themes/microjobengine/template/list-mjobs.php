<?php
/**
 * Template list all mJobs
 */
global $ae_post_factory;
$post_object = $ae_post_factory->get('mjob_post');


?>

<ul class="row mjob-list list-mjobs">
<?php
global $post_data;
$post_data  = array();
    if( function_exists('mjob_featured_block')){
        //mjob_featured_block();
    }
?>

<?php
    if(have_posts()) {
        while (have_posts()) {
            the_post();
            global $post;
            $convert = $post_object->convert( $post );
            $post_data[] = $convert;
            echo '<li class="'. mje_loop_item_css($convert) .'">';
            mje_get_template( 'template/mjob-item.php', array( 'current' => $convert ) );
            echo '</li>';
        }
    } else {
        ?>
        <div class="not-found"><?php _e('There are no mJobs found!', 'enginethemes'); ?></div>
        <?php
    }
?>

</ul>

<?php
echo '<script type="data/json" class="mJob_postdata" >'.json_encode( $post_data ).'</script>';
?>
