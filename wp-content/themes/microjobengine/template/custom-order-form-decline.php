<div class="action-form" id="send-decline">
    <a class="btn-back btn-back-custom-order"><i class="fa fa-angle-left"></i><?php _e('Back', 'enginethemes'); ?></a>
    <form class="decline-form  et-form">
        <p class="cata-title"><?php _e('Decline', 'enginethemes'); ?></p>
        <div class="form-group clearfix">
            <textarea name="why_decline" rows="10" placeholder="<?php _e('Why do you decline this custom order?', 'enginethemes') ?> "></textarea>
        </div>
        <input type="hidden" class="input-item _wpnonce" name="_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync');?>" />
        <button type="submit" class="<?php mje_button_classes( array( 'submit', 'waves-effect', 'waves-light' ) ); ?>">
            <?php _e('Send', 'enginethemes'); ?>
        </button>
    </form>
</div>