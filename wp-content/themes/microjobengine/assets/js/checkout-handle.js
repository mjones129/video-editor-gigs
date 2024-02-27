(function($, Models, Collections, Views) {
    Views.HandleCheckout = Backbone.View.extend({
        el: '.mjob-order-page',
        events: {
                'click .extra-item input[type="checkbox"]': 'selectExtra'
        },
        initialize: function () {
            // init checkout info
            if($('#mje-checkout-info').length > 0) {
                this.checkoutData = JSON.parse($('#mje-checkout-info').html());

                switch (this.checkoutData.post_type) {
                    case 'mjob_order':
                        this.handleExtra();
                        break;
                    default:
                    // Do nothing
                }
            }
        },
        handleExtra: function() {
            var view = this;
            if($('#mje-extra-ids').length > 0) {
                this.extraID = JSON.parse($('#mje-extra-ids').html());
            }
            this.$el.find('.extra-item').each(function () {
                var id = $(this).attr('data-id');
                var index = _.indexOf(view.extraID, id);
                if(index !== -1) {
                    $(this).find('input').prop('checked', true);
                    $(this).addClass('active');
                }
            });
        },
        selectExtra: function(event) {
            var view = this;
            var commission = ae_globals.fee_order_buyer;
			var fee_extra  = 0;
            $target = $(event.currentTarget);
            var extraBudget = parseFloat($target.val());
            var id = $target.attr('data-id');
            extraBudget = extraBudget;
            if($target.prop('checked')) {
                $target.parents('.extra-item').addClass('active');
                view.extraID.push(id);
				//fee_extra=commission*extraBudget/100;


                AE.pubsub.trigger('mje:update:checkout:product:data', view.extraID, 'extra_ids');
                AE.pubsub.trigger('mje:update:checkout:subtotal', extraBudget);
				AE.pubsub.trigger('mje:update:checkout:fee');
                AE.pubsub.trigger('mje:update:checkout:total');
            } else {
                $target.parents('.extra-item').removeClass('active');
                view.extraID = _.reject(view.extraID, function(num) { return num == id});

                AE.pubsub.trigger('mje:update:checkout:product:data', view.extraID, 'extra_ids');
               // fee_extra=commission*extraBudget/100;
                AE.pubsub.trigger('mje:update:checkout:subtotal', -extraBudget);
				AE.pubsub.trigger('mje:update:checkout:fee');
                AE.pubsub.trigger('mje:update:checkout:total');

            }
        }
    });

    $(document).ready(function () {
       new Views.HandleCheckout();
    });
})(jQuery, window.AE.Models, window.AE.Collections, window.AE.Views);