<script type="text/template" id="user-item-template">
    <div class="row et-mem-container">
        <div class="col-md-4 col-sm-4 purchase-info">
            <a href="{{= author_url }}" target="_blank" title="<?php _e('View this public profile', 'enginethemes'); ?>">
                {{= avatar }}
            </a>
            <a href="{{= author_url }}" target="_blank" title="<?php _e('View this public profile', 'enginethemes'); ?>">
                <span class="name">{{= display_name }}</span>
            </a>
        </div>
        <?php do_action( 'ae_admin_before_user_details_js_template'); ?>
        <div class="col-md-2 col-sm-2">
            <div class="et-act purchase-actions">
                <?php if(current_user_can( 'administrator' )){ ?>
                <# if(register_status == "unconfirm"){ #>
                    <a class="action et-act-confirm" data-act="confirm" href="javascript:void(0)" title="Confirm this user">
                        <i class="fa fa-check" aria-hidden="true"></i>
                    </a>
                    <# } #>
                        <?php } ?>
            </div>
        </div>
        <div class="col-md-3 col-sm-3 time-join">
            <span class="date">{{= join_date }}</span>
        </div>
        <div class="col-md-3 col-sm-3 payment-type">
            <span class="mjob_delivery_order">{{= mjob_delivery_order }} </span>
        </div>
        <?php do_action( 'ae_admin_after_user_details_js_template' ); ?>
    </div>
</script>