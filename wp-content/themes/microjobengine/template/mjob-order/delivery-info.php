<?php  if( ! empty( $mjob_order->order_delivery ) ) : ?>
<div class="clearfix"></div>
<div class="frame-delivery-info">
    <div class="delivery-info">
        <p class="title-description"><?php _e('Delivery info', 'enginethemes') ?></p>
        <div class="delivery-content">
            <?php echo $mjob_order->order_delivery['0']->post_content;  ?>
        </div>
    </div>

    <div class="file-attachment">
        <p class="title-description"><?php _e('File attachment', 'enginethemes'); ?></p>

        <?php if( !empty( $mjob_order->order_delivery['0']->et_carousels ) ) : ?>
            <ul class="list-file">
                <?php foreach($mjob_order->order_delivery['0']->et_carousels as $key=> $value ) { ?>
                    <li class="image-item" id="<?php echo $value->ID ?>">
                        <a href="<?php echo $value->guid;?>"><i class="fa fa-paperclip"></i> <?php echo $value->post_title; ?></a>
                    </li>
                <?php } ?>
            </ul>
        <?php else : ?>
            <p class="no-attachment"><?php _e( 'No files attached', 'enginethemes' ) ?></p>
        <?php endif; ?>

    </div>
</div>
<?php endif; ?>