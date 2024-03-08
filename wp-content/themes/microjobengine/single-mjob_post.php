<?php
do_action("before_single_place");
get_header();
global $ae_post_factory, $post;
$mjob_object = $ae_post_factory->get( 'mjob_post' );
$mjob_post = $mjob_object->convert( $post );

$is_edit = false;
if( isset($_GET['action']) && $_GET['action'] = 'edit'){
    $is_edit = MJE_MJob_Action::checkEdit( $mjob_post );
}
$mjob_post->is_edit = $is_edit;
?>
    <div id="content" class="mjob-single-page">
        <?php get_template_part('template/content', 'page');?>
        <div class="container mjob-single-primary">
            <div class="row">
                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                    <div class="mjob-single-content box-shadow">
                        <div class="mjob-single-header mjob-single-block pad-lr-30">
                            <?php mje_get_template( 'template/mjob-post/header.php', array( 'mjob_post' => $mjob_post ) ); ?>
                        </div><!-- end .mjob-single-header -->

                        <div class="mjob-single-gallery <?php echo ($mjob_post->class_video)?'mjob-single-gallery-video':''; ?>">
                            <?php mje_get_template( 'template/mjob-post/video.php', array( 'mjob_post' => $mjob_post ) ); ?>
                        </div><!-- end .mjob-single-gallery -->

                        <div class="mjob-single-share mjob-single-block">
                            <?php mje_get_template( 'template/mjob-post/share.php', array( 'mjob_post' => $mjob_post ) ) ?>
                        </div><!-- end .mjob-single-share -->

                        <div class="mjob-single-description mjob-single-block pad-lr-30">
                            <?php mje_get_template( 'template/mjob-post/description.php', array( 'mjob_post' => $mjob_post ) ) ?>
                        </div><!-- end .mjob-single-description -->
                    </div><!-- end .mjob-single-content -->

                    <div class="mjob-single-content box-shadow">
                        <div class="mjob-single-review mjob-single-block pad-lr-30">
                            <?php mje_get_template( 'template/mjob-post/review.php', array( 'mjob_post' => $mjob_post ) ) ?>
                        </div><!-- end .mjob-single-review -->
                    </div>

                    <div class="mjob-edit-content">
                        <?php get_template_part('template-js/template', 'edit-mjob');?>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <div class="mjob-single-aside">
                        <?php mje_get_template( 'template/mjob-post/aside-block.php', array( 'mjob_post' => $mjob_post ) ); ?>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- end .mjob-single-page -->
<?php
echo '<input type="hidden" name="amount" value="' . $mjob_post->et_budget . '">';
echo '<script type="text/template" id="mjob_single_data" >'.json_encode( $mjob_post ).'</script>';
get_template_part( 'template/modal-send-custom-order' );
get_footer();