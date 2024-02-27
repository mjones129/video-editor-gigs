<script type="text/template" id="mjob-item-loop">
    <div class="{{= mjob_class }}">
        <?php
        /**
         * Fire action before mjob item
         *
         * @param object $current
         * @since 1.3.1
         * @author Tat Thien
         */
        do_action( 'mje_mjob_item_js_top' );
        ?>
        <div class="mjob-item__image">
            <?php
            /**
             * Fire action before mjob image
             *
             */
            do_action( 'mje_mjob_item_js_before_image' );
            ?>

            <a href="{{= permalink }}" class="{{= class_video }}">
                {{=mje_get_thumbnail}}
            </a>

            <?php
            /**
             * Fire action after mjob image
             *
             */
            do_action( 'mje_mjob_item_js_after_image' );
            ?>
        </div><!-- end .mjob-item__image -->

        <div class="mjob-item__entry">
            <div class="mjob-item__title">
                <h2 class="trimmed" title="{{= post_title }}">
                    <a href="{{= permalink }}">{{= post_title }}</a>
                </h2>
            </div><!-- end .mjob-item__title -->

            <div class="mjob-item__author">
                <span>{{= author_name }}</span>
            </div><!-- end .mjob-item__author -->

            <div class="mjob-item__price">
                <div class="mjob-item__price-inner">
                    <span class="starting-text"><i class="fa fa-eye" aria-hidden="true"></i>{{= view_count}}</span>
                    <span class="price-text customize-color">{{= et_budget_text }}</span>
                </div>
            </div><!-- end .mjob-item__price -->

            <div class="mjob-item__bottom clearfix">
                <?php
                /**
                 * Fire action before mjob rating
                 *
                 * @author Tat Thien
                 */
                do_action( 'mje_mjob_item_js_before_rating' );
                ?>
                <div class="mjob-item__rating">
                    <div class="rate-it star" data-score="{{= rating_score }}"></div>
                    <span class="total-review">({{= mjob_total_reviews }})</span>
                </div><!-- end .mjob-item__ratings -->
            </div>
        </div>

        <?php
        /**
         * Fire action after mjob item
         *
         * @param object $current
         */
        do_action( 'mje_mjob_item_js_bottom' );
        ?>
    </div>
</script>