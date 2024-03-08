<script type="text/template" id="mjob_my_account_header">
    <div class="dropdown et-dropdown">
        <div class="dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown">
            <span class="avatar">
                <span class="display-avatar"><# if(typeof avatar !== 'undefined') { #>{{= avatar }}<# } #></span>
                <span class="display-name"><# if(typeof display_name !== 'undefined') { #>{{= display_name }}<# } #></span>
            </span>
            <span><i class="fa fa-angle-right"></i></span>
        </div>
        <ul class="dropdown-menu et-dropdown-login" aria-labelledby="dLabel">
            <li><a href="<?php echo et_get_page_link('dashboard'); ?>"><?php _e('Dashboard', 'enginethemes'); ?></a></li>
            <li><a href="<?php echo et_get_page_link("profile"); ?>"><?php _e('My profile', 'enginethemes'); ?></a></li>
            <li><a href="<?php echo et_get_page_link("my-list-order"); ?>"><?php _e('My orders', 'enginethemes'); ?></a></li>
            <li><a href="<?php echo et_get_page_link("my-listing-jobs"); ?>"><?php _e('My jobs', 'enginethemes'); ?></a></li>
            <li class="post-service-link"><a href="<?php echo et_get_page_link('post-service'); ?>"><?php _e('Post a mJob', 'enginethemes'); ?>
                    <div class="plus-circle"><i class="fa fa-plus"></i></div>
                </a></li>
            <li class="get-message-link">
                <a href="<?php echo et_get_page_link('my-list-messages'); ?>"><?php _e('Message', 'enginethemes'); ?></a>
            </li>
            <li><a href="<?php echo wp_logout_url(home_url()); ?>"><?php _e('Sign out', 'enginethemes'); ?></a></li>
        </ul>
    </div>
</script>