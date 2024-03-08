<script type="text/template" id="order-item-loop">
    <div class="mjob-item">
        <div class="status-label">
            <span class="{{= status_class }}">{{= status_text }}</span>
        </div>

        <div class="mjob-item__image">
            <a href="{{= permalink }}">
                <img src="{{= mjob_post_thumbnail }}" alt="{{= post_title }}">
            </a>
        </div><!-- end .mjob-item__image -->

        <div class="mjob-item__entry">
            <div class="mjob-item__title">
                <h2 class="trimmed" title="{{= post_title }}">
                    <a href="{{= permalink }}">{{= post_title }}</a>
                </h2>
            </div><!-- end .mjob-item__title -->

            <div class="mjob-item__price">
                <div class="mjob-item__price-inner">
                    <span class="starting-text customize-color"><?php _e( 'Total price:', 'enginethemes' ); ?></span>
                    <span class="price-text customize-color">{{= amount_text }}</span>
                </div>
            </div><!-- end .mjob-item__price -->

            <div class="mjob-item__bottom clearfix">
                <div class="mjob-item__author">
                    <span>
                        <?php _e( 'Author', 'enginetheme' ); ?>
                        <a href="{{= mjob_author_url }}">{{= mjob_author_name }}</a></span>
                </div><!-- end .mjob-item__author -->

                <div class="order-item__date">
                    {{= post_human_time }}
                </div>
            </div>
        </div>
    </div>
</script>