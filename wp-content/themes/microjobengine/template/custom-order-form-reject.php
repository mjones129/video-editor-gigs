<div class="action-form" id="send-reject">
    <a class="btn-back btn-back-custom-order"><i class="fa fa-angle-left"></i><?php _e('Back', 'enginethemes');?></a>
    <form class="reject-form et-form">
        <p class="cata-title"><?php _e('Reject offer', 'enginethemes');?></p>
        <textarea rows="10" name="why_reject" placeholder="<?php _e('Why do you reject this offer?', 'enginethemes');?>"></textarea><br/><br/>
        <input type="hidden" class="input-item _wpnonce" name="_wpnonce" value="<?php echo de_create_nonce('ae-mjob_post-sync'); ?>" />
        <button class="<?php mje_button_classes(array('submit', 'waves-effect', 'waves-light'));?>"><?php _e('Send', 'enginethemes');?></button>
    </form>
</div>