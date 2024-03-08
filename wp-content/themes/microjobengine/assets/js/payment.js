/**
 * Created by Jack Bui on 1/21/2016.
 */
(function($, Models, Collections, Views) {
    $(document).ready(function () {
        Views.Order = Backbone.View.extend({
            el: '.mjob-order-page',
            events: {
                'click .mjob-btn-checkout': 'checkOut',
                'click .select-payment': 'selectPayment',
            },
            initialize: function (options) {
                AE.pubsub.on('ae:form:submit:success', this.afterValidateCreditCheckout, this);

                AE.pubsub.on('mje:update:checkout:subtotal', this.updateSubtotal, this);
                AE.pubsub.on('mje:update:checkout:fee', this.updateFee, this);
                AE.pubsub.on('mje:update:checkout:total', this.updateTotal, this);



                AE.pubsub.on('mje:update:checkout:product:data', this.updateProductData, this);

                if (typeof this.extraCollection == 'undefined') {
                    // Get extra data
                    if ($('.extra_postdata').length > 0) {
                        var extra = JSON.parse($('.extra_postdata').html());
                        this.extraCollection = new Collections.Extras(extra);
                    } else {
                        this.extraCollection = new Collections.Extras();
                    }
                }

                this.blockUi = new Views.BlockUi();

                this.setupCheckout();

            },
            afterValidateCreditCheckout: function(result, resp, jqXHR, type) {
                var view = this;
                if(type == 'validate-credit-checkout') {
                    if(resp.success == true) {
                        // Process payment
                        view.checkoutModel.set('p_payment', 'credit');
                        view.productData.payment_type = 'credit';
                        view.saveOrder();
                    }
                }
            },
            checkOut: function(e){
                var view = this;

                $('html, body').animate({
                    scrollTop: $("html, body").offset().top
                }, 1000);

                $('.mjob-order-info').hide();
                $('#checkout-step2').fadeIn(500);
                $(".items-chosen").hide();
                $(".mjob-order-page").addClass("continue");

                e.preventDefault();


                // Add trigger after user click on Checkout button
                AE.pubsub.trigger('mje:after:setup:checkout', view.checkoutModel);
            },
            selectPayment: function(e){
                e.preventDefault();
                var $target = $(e.currentTarget),
                    paymentType = $target.attr('data-type'),
                    view = this;

                this.blockUi.block($target.parents('ul.list-price'));

                // Update payment gateway
                view.checkoutModel.set('p_payment', paymentType);
                view.productData.payment_type = paymentType;

                view.saveOrder();
            },
            setupCheckout: function(){
                var view = this;
                if( typeof view.checkoutModel === 'undefined' ){
                    if( $('#mje-checkout-info').length > 0 ) {
                        view.productData = JSON.parse($('#mje-checkout-info').html());
                        view.checkoutModel = new Models.Order();
                        view.checkoutModel.set('p_data', view.productData);
                        view.checkoutModel.set('p_type', view.productData.post_type);
                        view.checkoutModel.set('p_subtotal', view.productData.subtotal);
                        view.checkoutModel.set('p_total', view.productData.total);
                        view.checkoutModel.set('p_fee', view.productData.custom_fee);
                        view.checkoutModel.set('p_discount', view.productData.discount);
                        view.checkoutModel.set('coupon_code', ''); // can change to cookied in improve
                        view.checkoutModel.set('ef_fixed', view.productData.ef_fixed); //extension extra fee
                        view.checkoutModel.set('ef_percent', view.productData.ef_percent);//extension extra fee
						view.checkoutModel.set('p_nonce', view.$el.find('#_wpnonce').val());
                    }
                }
            },
            updateSubtotal: function(amount) {
                var view = this;
                var subtotal = parseFloat( view.checkoutModel.get('p_subtotal') );

                subtotal += parseFloat( amount );
                view.checkoutModel.set('p_subtotal', subtotal);
                view.productData.subtotal = subtotal;

                view.$el.find('.subtotal-price').html(AE.App.mJobPriceFormat(subtotal));
            },
			updateFee:function(){
				var view = this;
                var commission = ae_globals.fee_order_buyer;
                var subtotal = parseFloat( view.checkoutModel.get('p_subtotal') );
                var total = parseFloat( view.checkoutModel.get('p_fee') );
                var cms_fee = parseFloat(commission*subtotal/100);

                view.checkoutModel.set('p_fee', cms_fee);
                view.productData.custom_fee = cms_fee;
                view.$el.find('.fee-buyer').html( AE.App.mJobPriceFormat(cms_fee) );

                var ef_percent = parseFloat( view.checkoutModel.get('ef_percent') ); // extra fee percent

                if(ef_percent > 0){
                    ex_percent_fee = parseFloat(ef_percent*subtotal/100);
                    view.$el.find('.extra-fee-percent').html( AE.App.mJobPriceFormat(ex_percent_fee) );
                }


			},
            updateTotal: function(){
                console.log('updateTotal');

                var view = this;
                var subtotal = parseFloat( view.checkoutModel.get('p_subtotal') );
                var total = parseFloat( view.checkoutModel.get('p_total') );
                console.log('total: ' + total);

                var commision_fee = parseFloat( view.checkoutModel.get('p_fee') ); // commision fee p_fee

                var ef_fixed = parseFloat( view.checkoutModel.get('ef_fixed') ); // extra fee fixed
                console.log('ef_fixed'+ef_fixed);

                var ef_percent = parseFloat( view.checkoutModel.get('ef_percent') ); //// extra fee percent

                var obj = view.checkoutModel.get('p_discount');
                var amount_discount = 0;

                if(  typeof obj === 'object'){

                    var type = obj.type;
                    if(type == 'percent'){
                        amount_discount = obj.value*subtotal/100;
                    } else {
                        amount_discount =  obj.value;
                    }
                    amount_discount = parseFloat(amount_discount);
                    amount_discount = Math.min(amount_discount, subtotal);

                    view.$el.find('.discount-amount').html(AE.App.mJobPriceFormat(amount_discount));
                }
                amount_discount = parseFloat(amount_discount);
                amount_discount = Math.min(amount_discount, subtotal);

                console.log('subtotal:'+subtotal);
                console.log('amount_discount:'+amount_discount);
                total = subtotal - amount_discount + parseFloat(commision_fee) + ef_fixed ;
                console.log('total: ' + total);
                var t = parseFloat(commision_fee);
                console.log('com fee' + t);
                console.log('ef_fixed'+ef_fixed);

                if(ef_percent > 0){
                    ex_percent_fee = parseFloat(ef_percent*subtotal/100);
                    total = total + ex_percent_fee;
                }
                view.checkoutModel.set('p_total', total);
                view.productData.total = total;
                console.log('total: ' + total);

                view.$el.find('.total-price').html(AE.App.mJobPriceFormat(total));
            },
            updateProductData: function(data, key) {
                var view = this;
                var pData = view.checkoutModel.get('p_data');
                pData[key] = data;
            },
            saveOrder: function(){
                var view = this;
                view.checkoutModel.save( '', '', {
                    beforeSend: function () {
                    },
                    success: function ( result, res, jqXHR ) {
                        if (res.success && res.data.ACK) {
                            window.location.href = res.data.url;
                        } else if(res.form_generate){
                            $("body").append(res.form_generate);
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error'
                            });
                        }
                    }
                } );
            },
        });
        window.AE.order = new Views.Order();

        var responsiveMjobItem = function() {
          if($('body').outerWidth() < 992) {
            $('.mjob-order-page').find('.mjob-list').addClass('mjob-list--horizontal');
          } else {
            $('.mjob-order-page').find('.mjob-list').removeClass('mjob-list--horizontal');
          }
        }
        responsiveMjobItem();
        $(window).resize(function() {
            responsiveMjobItem();
        })
    });
})(jQuery, window.AE.Models, window.AE.Collections, window.AE.Views);
