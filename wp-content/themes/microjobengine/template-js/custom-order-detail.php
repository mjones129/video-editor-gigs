<script type="text/template" class="box-content-custom-order">
    <div class="outer-detail-custom">
            <h2>{{= mjob_name }}</h2>
            <p class="date"><?php _e('Modify date:', 'enginethemes') ?> <span>{{= post_modified }}</span></p>
            <div class="block-text">
                <p class="title"><?php _e('brief', 'enginethemes') ?></p>
                <p>{{= post_content }}</p>
                <div class="budget"><p><?php _e('Budget','enginethemes') ?><span class="mje-price-text">{{= budget }}</span></p></div>
                <div class="deadline"><p><?php _e('Time of Delivery', 'enginethemes') ?><span>{{= deadline }} <?php _e('day(s)', 'enginethemes') ?></span></p></div>
                {{= attach_file }}
            </div>

            <div class="block-text">
                <# if (is_offer == true) { #>
                    <# if(post_author == currentUser.data.ID) { #>
                        <p class="title"><?php _e('seller\'s offer', 'enginethemes' ) ?></p>
                    <# } else { #>
                        <p class="title"><?php _e('your offer', 'enginethemes' ) ?></p>
                    <# } #>

                    <div class="padding-top10">
                        {{= offer_content }}
                    </div>

                    <div class="budget">
                        <p><?php _e('Price', 'enginethemes' ) ?><span class="mje-price-text">{{= offer_budget }}</p>
                    </div>
                    <div class="deadline">
                        <p><?php _e('Time of Delivery', 'enginethemes' ) ?><span>{{= offer_etd }} <?php _e('day(s)', 'enginethemes')?></span></p>
                    </div>

                    {{= offer_attach_file }}

                    <# if(post_author == currentUser.data.ID) { #>
                        <# if(custom_order_status == false) { #>
                        <div class="action">
                            <button class="btn-reject" data-custom-order="{{= ID }}"><?php _e('Reject offer', 'enginethemes' ) ?></button>
                            <a href="<?php echo et_get_page_link('order') . '?pid=' ?>{{= ID }}&type=custom-order" class="btn-accept-offer"><?php _e('Accept & Checkout', 'enginethemes' ) ?></a>
                        </div>
                        <# } #>
                    <# } #>
                <#  } else { #>
                    <# if(post_author != currentUser.data.ID) { #>
                        <# if(custom_order_status == false) { #>
                        <div class="custom-order-btn">
                            <button class="btn-decline" data-custom-order="{{= ID }}"><?php _e('Decline', 'enginethemes'); ?></button>
                            <button class="btn-send-offer" data-custom-order="{{= ID }}"><?php _e('Send offer', 'enginethemes') ?></button>
                        </div>
                        <# } #>
                    <# } #>
                <# } #>
                    <div class="custom-order-status {{= custom_order_status }}">
                        {{= custom_order_status_text }}
                    </div>

            </div>
    </div>
</script>