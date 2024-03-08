(function($, Views, Models) {
    /**
     * Revenue Model
     */
    Models.Revenue = Backbone.Model.extend({
        action: 'mjob_revenue_sync'
    });
    /**
     * Create Modal Secure Code
     */
    Views.SecureModal = Views.Modal_Box.extend({
        el: '#checkout_secure_code',
        initialize: function(options) {
            this.options = _.extend(this, options);

            this.revenue = new Models.Revenue();
            this.blockUi = new Views.BlockUi();

            if(typeof this.secureCodeForm === "undefined") {
                var model = new Models.Revenue();
                model.set('do_action', 'validate_checkout');

                if(this.checkoutType == 'checkout_order') {
                    model.set('extra_ids', this.extra_ids);
                    model.set('checkout_type', this.checkoutType);
                    if(typeof this.custom_offer_id !== "undefined") {
                        model.set('custom_offer_id', this.custom_offer_id);
                    } else {
                        model.set('mjob_id', this.mjob_id);
                    }
                } else if(this.checkoutType == 'checkout_package') {
                    model.set('package_id', this.packageID);
                    model.set('checkout_type', this.checkoutType);
                }

                this.secureCodeForm = new Views.AE_Form({
                    el: '#checkout_secure_code', // Wrapper of form
                    model: model,
                    rules: {
                        secure_code: 'required'
                    },
                    type: 'validate-credit-checkout',
                    blockTarget: '#checkout_secure_code button.submit',
                    showNotice: false
                });

                AE.pubsub.on('ae:form:submit:success', this.afterCheckout, this);
            }
        },
        events: {
            'click .request-secure-code': 'requestSecureCode',
        },
        requestSecureCode: function(event) {
            event.preventDefault();
            var view = this;
            var target = $(event.currentTarget);
            view.revenue.set({
                'do_action': 'request_secure_code',
                '_wpnonce': view.$el.find('#_wpnonce').val()
            })
            view.revenue.save('', '', {
                beforeSend: function() {
                    view.blockUi.block(target);
                },
                success: function(status, resp, jqXHR) {
                    if(resp.success == true) {
                        AE.pubsub.trigger('ae:notification', {
                            msg: resp.msg,
                            notice_type: 'success'
                        })
                    } else {
                        AE.pubsub.trigger('ae:notification', {
                            msg: resp.msg,
                            notice_type: 'error'
                        })
                    }

                    view.blockUi.unblock();
                }
            })
        },
        resetModal: function() {
            this.$el.find('input').val('');
        },
        afterCheckout: function(result, resp, xhr, type) {
            if(type == 'validate-credit-checkout') {
                if(resp.success == true) {
                    $('.checkout_secure_code_form .submit').prop('disabled', true)
                } else {
                    AE.pubsub.trigger('ae:notification', {
                        msg: resp.msg,
                        notice_type: 'error'
                    })
                }
            }
        }
    });

    Views.ListPayment = Backbone.View.extend({
        el: '.list-payment-gateway',
        events: {
            'click #credit-gateway': 'openCreditModal'
        },
        initialize: function() {
            AE.pubsub.on('mje:after:setup:checkout', this.afterSetupOrder, this);
        },
        afterSetupOrder: function(data) {
            this.checkoutData = data;
            this.productData  = data.get('p_data');

            // Disable gateway
            if(! this.validateCredit()) {
                var creditGateway = $('#credit-gateway');
                creditGateway.attr('data-enable', false);
                creditGateway.parent('div').addClass('disable-gateway');

                // Add tool tip
                creditGateway.parent('div').attr('data-toggle', 'tooltip');
                creditGateway.parent('div').attr('data-placement', 'top');
                creditGateway.parent('div').attr('data-original-title', ae_globals.credit_balance_not_enough);
            }
        },
        openCreditModal: function(event) {
            var view = this;
            $target = $(event.currentTarget);
            if(typeof currentUser.data !== 'undefined') {
                if( this.validateCredit() ) {
                    // Init modal secure code
                    if(typeof this.secureModal === "undefined") {
                        this.secureModal = new Views.SecureModal({
                            checkoutType: $target.attr('data-checkout-type'),
                            extra_ids: view.productData.extra_ids,
                            mjob_id: view.productData.post_parent,
                            custom_offer_id: view.productData.custom_offer_id
                        });
                    }
                    this.secureModal.openModal();
                }
            } else {
                // Open authentication modal
                if(typeof this.signInModal === "undefined") {
                    this.signInModal = new Views.SignInModal({
                        reload: true
                    });
                }
                this.signInModal.openModal();
                AE.pubsub.trigger('mjob:open:signin:modal', this.signInModal);
            }
        },
        validateCredit: function() {
            var view = this;
            if(typeof currentUser.data !== 'undefined') {
                if(currentUser.data.available_fund >= view.checkoutData.get('p_total')) {
                    return true;
                } else {
                    return false;
                }
            }

            return true;
        }
    });

    new Views.ListPayment();
})(jQuery, window.AE.Views, window.AE.Models);