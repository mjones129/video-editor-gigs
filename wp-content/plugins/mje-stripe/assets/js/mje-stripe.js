(function($, Models, Views) {
  /**
   * View for Stripe checkout modal
   */
  Views.StripeCheckoutModal = Views.Modal_Box.extend({
    el: '#stripe_checkout_modal',
    events: {
      'submit #payment-form': 'submitPayment'
    },
    initialize: function(options) {
      Views.Modal_Box.prototype.initialize.apply(this, arguments);
      this.options = _.extend(this, options);
      this.isPostJob = this.options.isPostJob;
      if(typeof this.options.checkoutData !== 'undefined') {
        this.checkoutData = this.options.checkoutData; // setup order data
        this.productData = this.checkoutData.get('p_data');
      }

      if(typeof this.options.model !== 'undefined'){
        this.model = this.options.model;
      }

      // Reset payment form
      //this.resetPaymentForm();

      this.blockUi = new Views.BlockUi();

      // Set Stripe publishable key
      this.stripe = Stripe(mje_stripe.publishable_key);
      var elements = this.stripe.elements();

      var style = {
        base: {
          color: '#303238',
          fontSize: '16px',
          fontSmoothing: 'antialiased',
          '::placeholder': {
            color: '#ccc',
          },
        },
        invalid: {
          color: '#e5424d',
          ':focus': {
            color: '#303238',
          },
        },
      };
      this.card = elements.create('card', { style: style });
      this.card.mount('#card-element');

      var self = this;
      this.card.addEventListener('change', function (event) {
        var displayError = self.$el.find('#card-errors');
        if (event.error) {
          displayError.text(event.error.message);
        } else {
          displayError.text('');
        }
      });
    },
    submitPayment: function (e) {
      e.preventDefault();
      $form = $(e.currentTarget);

      // Disable the submit button to prevent repeated clicks
      var self = this;
      self.blockUi.block($form.find('.submit-payment'));

      var promise = this.createToken();
      promise.catch(function (event) {

        if(!event) {
          self.blockUi.unblock();
        } else {
          // Process payment
          self.processPayment($('#stripeToken').val());
        }
      });
    },
    createToken: function () {
      var self = this;
      var promise = this.stripe.createToken(this.card).then(function (result) {
        if (result.error) {
          self.$el.find('#card-errors').text(result.error.message);
          throw false;
        } else {
          self.stripeTokenHandler(result.token);
          throw true;
        }
      });
      return promise;
    },
    stripeTokenHandler: function (token) {
      var form = this.$el.find('#payment-form');
      form.append('<input type="hidden" name="stripeToken" id="stripeToken" value="'+ token.id +'"/>');
    },

    processPayment: function (token) {
      var view = this;
      if(view.isPostJob){
            var data = {
                    action: 'et-setup-payment',
                    // post id
                    ID: view.model.id,
                    // author
                    author: view.model.get('post_author'),
                    // package sku id
                    packageID: view.model.get('et_payment_package'),
                     //package type
                    packageType: view.model.get('et_package_type'),
                    // payment gateway
                    paymentType: 'stripe',
                    //stripe token
                    token: token,
                    // send coupon code if exist
                   // coupon_code: view.$('#coupon_code').val(),
                    package_price_plan: this.model.get('et_package_price_plan')
                };

          $.ajax({
            type: "post",
            url: ae_globals.ajaxURL,
            dataType: 'json',
            data: data,

            beforeSend: function () {

            },
            success: function (res, status, xhr) {

                if (res.success) {
                    AE.pubsub.trigger('ae:notification', {
                        msg: res.data.msg,
                        notice_type: 'success'
                    });
                    setTimeout(function() {
                        window.location.href = res.data.url;
                        view.blockUi.unblock();
                    }, 2000);
                } else{
                    AE.pubsub.trigger('ae:notification', {
                        msg: res.data.msg,
                        notice_type: 'error'
                    });

                }
                view.blockUi.unblock();
            }
        });

      } else {

          view.checkoutData.set('p_payment', 'stripe');
          view.productData.payment_type = 'stripe';
          view.checkoutData.set('token', token);
          view.checkoutData.save('', '', {
            beforeSend: function() {

            },
            success: function(status, response, xhr) {

              if(response.success) {
                AE.pubsub.trigger('ae:notification', {
                  notice_type: 'success',
                  msg: response.data.msg
                });

                setTimeout(function() {
                    window.location.href = response.data.url;
                    view.blockUi.unblock();
                }, 2000)
              } else {
                AE.pubsub.trigger('ae:notification', {
                  notice_type: 'error',
                  msg: response.msg
                });
                view.blockUi.unblock();
              }
            }
          });
      }
    },

  });

  /**
   * View for payment gateway wrapper
   */
  Views.ListPayment = Backbone.View.extend({
    el: '.list-payment-gateway',
    events: {
      'click #stripe-gateway': 'openStripeModal'
    },
    initialize: function() {
      AE.pubsub.on('mje:after:setup:checkout', this.afterSetupCheckout, this);

    },
    openStripeModal: function() {
      var view = this;
      if(mje_stripe.is_has_api_key === '1') {
        if(typeof this.stripeModal === 'undefined') {

          this.stripeModal = new Views.StripeCheckoutModal({
            checkoutData: view.checkoutData
          });
        }
        this.stripeModal.openModal();
      }
    },
    afterSetupCheckout: function(data) {
      this.checkoutData = data;
    }
  });

  Views.postJob = Backbone.View.extend({
    el: '.post-job',
    events: {
      'click #stripe-gateway': 'openStripeModal'
    },
    initialize: function() {
        this.model = {};

        AE.pubsub.on('ae:submitPostSuccess',this.setupData, this);

    },
    setupData: function(model,res) {
        this.model = model;
    },
    openStripeModal: function() {
      var view = this;
      if(mje_stripe.is_has_api_key === '1') {

        if(typeof this.stripeModal === 'undefined') {

            this.stripeModal = new Views.StripeCheckoutModal({
            model: view.model,
            isPostJob : 1,
          });
        }
        this.stripeModal.openModal();
      }
    },

  });

  /**
   * Document ready
   */
    $(document).ready(function() {
        new Views.ListPayment();

        if( mje_stripe.is_postMjob ){
            new Views.postJob();
        }

        // Focus on parent el
        var focus_el = '#exp_year, #exp_month, #cvc';
        $(focus_el).bind('click focus', function (e) {
            e.stopPropagation();
            $(this).parent('.form-label').addClass('active');
        });

        // Remove class active when focus out of input
        $(focus_el).bind('focusout', function (e) {
            e.stopPropagation();
            $(this).parent('.form-label').removeClass('active');
        });

        // Remove class active when click outside of card expiration input
        $(window).on('click', function () {
            $('.card-expiration').removeClass('active');
        });
    });
})(jQuery, AE.Models, AE.Views);