<footer id="footer">
    <?php

    if( is_active_sidebar( 'mjob-footer-1' )    || is_active_sidebar( 'mjob-footer-2' )
    || is_active_sidebar( 'mjob-footer-3' ) || is_active_sidebar( 'mjob-footer-4' )
    ) {
        $flag = true; ?>
        <div class="et-pull-top">
            <div class="container">
                <div class="row">
                    <div class="col-md-3 col-sm-4 col-xs-12">
                        <?php if (is_active_sidebar('mjob-footer-1')) dynamic_sidebar('mjob-footer-1'); ?>
                    </div>
                    <div class="col-md-3 col-sm-4 col-xs-12">
                        <?php if (is_active_sidebar('mjob-footer-2')) dynamic_sidebar('mjob-footer-2'); ?>
                    </div>
                    <div class="col-md-3 col-sm-4 col-xs-12">
                        <?php if (is_active_sidebar('mjob-footer-3')) dynamic_sidebar('mjob-footer-3'); ?>
                    </div>
                    <div class="col-md-3 col-sm-4 col-xs-12">
                        <?php if (is_active_sidebar('mjob-footer-4')) dynamic_sidebar('mjob-footer-4'); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }


    $copyright = apply_filters('mje_copyright_footer',get_mje_copyright());
    ?>
    <div class="et-pull-bottom">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-9 col-xs-12">
                    <?php echo $copyright; ?>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-3 col-xs-12 float-right">
                    <div class="social-link">
                        <?php
                            if(has_nav_menu('et_footer_social')) {
                                wp_nav_menu(array(
                                    'theme_location' => 'et_footer_social',
                                    'container' => false,
                                    'link_before' => '<span class="screen-reader-text">',
                                    'link_after' => '</span>'
                                ));
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer><!--End Footer-->

<?php
    do_action( 'mje_main_wrapper_footer' );
?>
</div><!-- end .mje-main-wrapper -->
<?php
	wp_footer();
?>
</body>
</html>