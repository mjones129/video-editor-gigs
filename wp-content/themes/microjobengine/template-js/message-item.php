<script type="text/template" id="message-item-loop">
    <# if(type == "changelog") { #>
        <div class="changelog-item">
            <div class="changelog-text">
                {{= changelog }}
            </div>

            <div class="message-time">
                {{= post_date }}
            </div>
        </div>
    <# } else { #>
        <div class="{{= message_class }}">
            <div class="img-avatar">
                {{= author_avatar}}
            </div>

            <# if(type == "offer") { #>
                <!-- Custom Offer Message -->
                <div class="conversation-text custom-offer">
                    <p class="offer-label"><a href="#" data-id="{{= custom_order_id }}" class="color-custom-label  name-customer-order"><?php _e('Custom Offer', 'enginethemes'); ?></a></p>
                    {{= post_content_filtered }}
                    <ul>
                        {{= message_attachment }}
                    </ul>
                    <div class="budget"><p><?php _e('Budget', 'enginethemes') ?><span class="mje-price-text"> {{= budget }} </span></p></div>
                    <div class="deadline"><p><?php _e('Time of delivery', 'enginethemes') ?><span>{{= deadline }}</span></p></div>
                </div>
            <# } else if(type == "custom_order") { #>
                <!-- Custom Order Message -->
                <div class="conversation-text custom-order">
                    <p class="offer-label">{{= mjob_title }}</p>
                    <p class="view-custom-order"><a class="link-view-custom-order name-customer-order" data-id="{{= ID }}"><?php _e('View', 'enginethemes'); ?></a> <?php _e('this custom order', 'enginethemes'); ?></p>
                    <div class="budget"><p><?php _e('Budget', 'enginethemes') ?><span class="mje-price-text"> {{= budget }} </span></p></div>
                    <div class="deadline"><p><?php _e('Time of delivery', 'enginethemes') ?><span>{{= deadline }}</span></p></div>
                </div>
             <# } else if(type == 'decline') { #>
                 <div class="conversation-text">
                     <p class="offer-label name-customer-order" data-id="{{= custom_order_id }}"><?php _e('CUSTOM ORDER DECLINED', 'enginethemes'); ?></p>
                     {{= post_content_filtered }}
                 </div>
             <# } else if(type == 'reject') { #>
                 <div class="conversation-text">
                     <p class="offer-label name-customer-order" data-id="{{= custom_order_id }}"><?php _e('OFFER REJECTED', 'enginethemes'); ?></p>
                     {{= post_content_filtered }}
                 </div>

            <# } else { #>
                <div class="conversation-text">
                        {{= post_content_filtered}}
                        <ul>
                            {{= message_attachment }}
                        </ul>
                </div>
            <# } #>

            <div class="message-time linein_57">
                <# if(admin_message == true) { #>
                    <strong><?php _e('by Admin', 'enginethemes') ?></strong> - {{= post_date }}
                    <# } else { #>
                        {{= post_date }}
                        <# } #>
            </div>
        </div>
    <# } #>
</script>