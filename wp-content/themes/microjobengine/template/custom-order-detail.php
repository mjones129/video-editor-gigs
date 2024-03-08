<div class="outer-detail-custom-order">
    <div class="detail-custom-order custom-order-box">
        <div class="close-detail"><img src="<?php echo get_template_directory_uri() ?>/assets/img/icon-close.jpg" alt=""></div>
        <div class="action-form content-custom-order 999">
            <!--Content custom order detail-->
        </div>
        <?php
        global $post;

            // Load template js custom order detail
            get_template_part('template-js/custom-order-detail');

            get_template_part('template/custom-order-form','reject');

            get_template_part('template/custom-order-form','decline');

            get_template_part('template/custom-order-form', 'send-offer');
        ?>
    </div>
</div>