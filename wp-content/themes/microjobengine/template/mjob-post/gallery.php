<div class="gallery">
    <!-- <img src="<?php /*echo $current->the_post_thumbnail; */?>" width="100%" alt="">-->
    <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
        <!-- Indicators -->
        <?php  if( !empty($mjob_post->et_carousel_urls) ):
            $active ='active';
            $i = 0;
            ?>
            <ol class="carousel-indicators mjob-carousel-indicators">
                <?php foreach($mjob_post->et_carousel_urls as $key=>$value){
                    ?>
                    <li data-target="#carousel-example-generic" data-slide-to="<?php echo $i ?>" class="<?php echo $active; ?>"></li>
                    <?php
                    $i++;
                    $active = '';
                } ?>
            </ol>
        <?php endif; ?>
        <!-- Wrapper for slides -->
        <?php  if( !empty($mjob_post->et_carousel_urls) ):
            $active ='active';
            ?>
            <div class="carousel-inner mjob-single-carousels" role="listbox">
                <?php
                foreach($mjob_post->et_carousel_urls as $key=>$value){
                    $slide = wp_get_attachment_image_src($value->ID, "mjob_detail_slider");
                    $slide_url = $slide[0];
                    ?>
                    <div class="item <?php echo $active;?>">
                        <img src="<?php echo $slide_url; ?>" alt="">
                    </div>
                    <?php
                    $active = '';
                } ?>
            </div>
        <?php endif; ?>

        <!-- Controls -->
        <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
            <span class="fa fa-angle-left" aria-hidden="true"></span>
            <span class="sr-only"><?php _e('Previous', 'enginethemes'); ?></span>
        </a>
        <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
            <span class="fa fa-angle-right" aria-hidden="true"></span>
            <span class="sr-only"><?php _e('Next', 'enginethemes'); ?></span>
        </a>
    </div>
</div>