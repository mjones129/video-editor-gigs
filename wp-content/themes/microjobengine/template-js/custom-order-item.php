<script type="text/template" id="custom-order-item">
<div id="custom-order-{{= ID }}">
    <h2>
        <a data-id="{{= ID }}" title="{{= mjob_title }}" class="name-customer-order">
            {{=  mjob_title }}
        </a>
    </h2>
    <# if (label_status != '') { #>
        <div class="label-status order-color {{= label_class }}"><span>{{= label_status }}</span></div>
    <# } #>
    <p class="post-content"> {{= short_content }}</p>
        <div class="outer-etd">
            <div class="deadline"><p><i class="fa fa-calendar" aria-hidden="true"></i><span>{{= deadline }} <?php _e('days', 'enginethemes') ?></span></p></div>

            <div class="budget"><p><span class="mje-price-text">{{= budget }}</span></p></div>
        </div>
    <# if(post_author != currentUser.data.ID && status != 'offer_sent') { #>
    <div class="custom-order-btn">
        <button class="btn-decline" data-custom-order="{{= ID }}"><?php _e('Decline', 'enginethemes'); ?></button>
        <button class="btn-send-offer" data-custom-order="{{= ID }}"><?php _e('Send offer', 'enginethemes') ?></button>
    </div>
    <# } #>
</div>
</script>