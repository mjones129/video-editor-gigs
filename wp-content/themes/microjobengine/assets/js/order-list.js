/**
 * Created by Jack Bui on 1/28/2016.
 */
(function($, Models, Collections, Views) {
    $(document).ready(function () {
        //
        /**
         * mjob collections
         */
        Collections.Order = Backbone.Collection.extend({
            model: Models.Mjob,
            action: 'ae-fetch-mjob_order',
            initialize: function() {
                this.paged = 1;
            }
        });
        /**
         * define mjob item view
         */
        var orderItem = Views.PostItem.extend({
            tagName: 'li',
            className: 'order-item',
            template: _.template($('#order-item-loop').html()),
            initialize: function() {
            },
            onItemBeforeRender: function() {
                // before render view
            },
            onItemRendered: function() {
                console.log('onItemRendered line 30');

                var view = this;
                if( view.$el.find('input[name="_wpnonce"]').length > 0 ){
                    view.model.set('_wpnonce', view.$el.find('input[name="_wpnonce"]').val());
                }
                this.$el.find(".trimmed").dotdotdot({
                  watch: true,
                  wrap: 'letter'
                });
            },
        });

        // Init view for task item
        var taskItem = Views.PostItem.extend({
            tagName: 'li',
            className: 'order-item task-item',
            template: _.template($('#task-item-loop').html()),
            initialize: function() {
            },
            onItemBeforeRender: function() {
                // before render view
            },
            onItemRendered: function() {
                var view = this;
                if( view.$el.find('input[name="_wpnonce"]').length > 0 ){
                    view.model.set('_wpnonce', view.$el.find('input[name="_wpnonce"]').val());
                }
                this.$el.find(".trimmed").dotdotdot({
                  watch: true,
                  wrap: 'letter'
                });
            },
        });

        /**
         * list view control order list
         */
        ListOrders = Views.ListPost.extend({
            tagName: 'ul',
            itemView: orderItem,
            itemClass: 'order-item'
        });

        /**
         * list view control task list
         */
        ListTasks = Views.ListPost.extend({
            tagName: 'ul',
            itemView: taskItem,
            itemClass: 'task-item'
        });

        $('.order-container-control').each(function() {
            if (typeof orderCollection == 'undefined') {
                //Get public  collection
                if ($('.order_postdata').length > 0) {
                    var order = JSON.parse($('.order_postdata').html());
                    orderCollection = new Collections.Order(order);
                } else {
                    orderCollection = new Collections.Order();
                }
            }
            /**
             * init list blog view
             */
            var listOrders = new ListOrders({
                itemView: orderItem,
                collection: orderCollection,
                el: $(this).find('.list-orders')
            });
            //post-type-archive-project
            //old block-projects
            /**
             * init block control list blog
             */
            new Views.BlockControl({
                collection: orderCollection,
                el: $(this),
                onAfterFetch: function(result, res) {
                    var view = this;
                    if(res.success ){
                        if( view.$el.find('.no-items').length > 0){
                            view.$el.find('.no-items').remove();
                        }
                        if( res.data.length == 0 ){
                            $('.list-orders').html(ae_globals.no_orders);
                        }

                    }
                    else{
                        $('.list-orders').html(ae_globals.no_orders);
                    }
                }
            });
        });

        // Tasks block control
        $('.task-container-control').each(function() {
            if (typeof taskCollection == 'undefined') {
                //Get public  collection
                if ($('.task_postdata').length > 0) {
                    var order = JSON.parse($('.task_postdata').html());
                    taskCollection = new Collections.Order(order);
                } else {
                    taskCollection = new Collections.Order();
                }
            }

            var listTasks = new ListTasks({
                itemView: taskItem,
                collection: taskCollection,
                el: $(this).find('.list-tasks')
            });

            new Views.BlockControl({
                collection: taskCollection,
                el: $(this),
                onAfterFetch: function(result, res){
                    var view = this;
                    if(res.success ){
                        if( view.$el.find('.no-items').length > 0){
                            view.$el.find('.no-items').remove();
                        }
                        if( res.data.length == 0 ){
                            $('.list-tasks').html(ae_globals.no_orders);
                        }

                    }
                    else{
                        $('.list-tasks').html(ae_globals.no_orders);
                    }
                }
            });
        });

        Models.Delivery = Backbone.Model.extend({
            action: 'ae-order_delivery-sync',
            initialize: function() {}
        });
        /**
         * mjob collections
         */
        Collections.Delivery = Backbone.Collection.extend({
            model: Models.Delivery,
            action: 'ae-fetch-order_delivery',
            initialize: function() {
                this.paged = 1;
            }
        });

        Models.OrderAction = Backbone.Model.extend({
            action : 'mjob_order_action',
            defaults: {
                post_type: 'mjob_order'
            }
        });

        Views.SingleOrder = Backbone.View.extend({
            el: '.mjob-single-order-page',
            events: {
                'click .order-delivery-btn': 'openModalDelivery',
                'click .dispute-button': 'disputeOrder',
                'click .order-start-work' : 'startWork',
                'click .btn-accept': 'acceptOrder',
                'click .btn-delay': 'delayOrder'
            },
            initialize: function () {
                var view = this;
                if( $('#order_single_data').length > 0 ){
                    var postdata = JSON.parse($('#order_single_data').html());
                    view.model = new Models.Order(postdata);
                    view.modelAction = new Models.OrderAction(postdata);
                }
                else{
                    view.model = new Models.Order();
                    view.modelAction = new Models.OrderAction();
                }

                AE.pubsub.on('ae:form:submit:success', this.deliverySuccess, this);

                this.blockUi = new Views.BlockUi();

                /**
                 * Admin dispute decision form
                 */
                view.disputeModel = new Models.OrderAction();
                view.disputeModel.set('do_action', 'admin_decide');
                view.disputeModel.set('post_parent', view.modelAction.get('ID'));
                if( $('.mjob-admin-dispute-form').length > 0 ){
                    view.adminDisputeForm = new Views.AE_Form({
                        el: '.mjob-admin-dispute-form', // parent of form
                        model: view.disputeModel,
                        rules: {
                            post_content: 'required'
                        },
                        type: 'admin_decide',
                        blockTarget: '.mjob-admin-dispute-form button'
                    });
                }
            //    Init countdown order
                view.initCountDown();
            },

            openModalDelivery: function(e){
                e.preventDefault();
                var view = this,
                $target = $(e.currentTarget);
                var parent = $target.attr('data-id');
                if( typeof view.deliveryModal  === 'undefined' ){
                    view.deliveryModal = new Views.ModalDelivery();
                }
                view.deliveryModal.onOpen(parent);
            },
            deliverySuccess: function(result, resp, jqXHR, type){
                var view = this;
                if( type == 'delivery' ){
                    view.deliveryModal.closeModal();
                    $('.order_status').html(ae_globals.delivery_status);
                    window.location.reload(true);
                }
                else if( type == 'admin_decide' ){
                   if(resp.success) {
                       window.location.reload(true);
                   }
                }
            },
            disputeOrder: function (e) {
                e.preventDefault();
                var view = this;
                var $target = $(e.currentTarget);

                view.modelAction.set('do_action', 'dispute');

                // Get countdown value
                var expireTime = parseInt($('.expired-date-format').attr('data-expire-time'));
                var currentTime = Math.round((Date.now() / 1000));
                if( currentTime < expireTime ) {
                  var hours = $('.countdown').find('.hours').text();
                  var minutes = $('.countdown').find('.minutes').text();
                  var seconds = $('.countdown').find('.seconds').text();
                  var countdownString = view.renderCountdownString(hours, minutes, seconds);
                  view.modelAction.set('order_countdown_delivery', countdownString)
                } else {
                  view.modelAction.set('order_countdown_delivery', '');
                }

                view.modelAction.save('', '', {
                    beforeSend: function() {
                        view.blockUi.block($target);
                    },
                    success: function(result, res, xhr) {
                        if(res.success) {
                            AE.pubsub.trigger('ae:notification', {
                                notice_type: 'success',
                                msg: res.msg
                            });
                            window.location.reload();
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                notice_type: 'error',
                                msg: res.msg
                            });
                        }

                        view.blockUi.unblock();
                    }
                })
            },

            //Countdown
            getTimeRemaining : function(currenttime, endtime) {
                var t = Date.parse(endtime) - currenttime;
                var seconds = Math.floor((t / 1000) % 60);
                var minutes = Math.floor((t / 1000 / 60) % 60);
                var hours = Math.floor((t / (1000 * 60 * 60)));
                return {
                    'total': t,
                    'hours': hours,
                    'minutes': minutes,
                    'seconds': seconds
                };
            },
            renderCountdownString: function(hours, minutes, seconds) {
                return '<span class="hours">' + hours + '</span><span class="item"> '+ ae_globals.hour_text +'</span> <span class="distance-time">:</span><span class="minutes">' + minutes + '</span><span class="item"> '+ ae_globals.min_text +'</span> <span class="distance-time">:</span><span class="seconds">'+ seconds + '</span><span class="item"> '+ ae_globals.sec_text +'</span>';
            },
            initializeClock : function(currenttime, endtime, expired_time_format){
                var view = this;
                var current_new = Date.parse(currenttime);
                var updateClock = function() {
                    current_new = parseInt(current_new) + 1000;
                    var t = view.getTimeRemaining(current_new, endtime);
                    $('.countdown').html(view.renderCountdownString(t.hours, t.minutes, t.seconds));

                    if(t.total == 0) {
                        window.location.reload();
                    }

                    if(t.total < 0){
                        clearInterval(timeinterval);
                    }
                };
                updateClock();
                var timeinterval = setInterval(updateClock,1000);
            },

            initCountDown : function() {
                if(typeof $('.countdown .expired-date').val() != 'undefined') {
                    var expired_time = $('.countdown .expired-date').val();
                    var current_time = $('.countdown .current-time').val();
                    var expired_time_format = $('.expired-date-format').val();
                    this.initializeClock(current_time, expired_time, expired_time_format);
                }
            },

            startWork: function(e) {
                e.preventDefault();
                var view = this;
                var $target = $(e.currentTarget);
                //Get time expired
                var id_order = $('.order-id').val();
                var seller_id = $('.seller-id').val();

                view.modelAction.set('do_action', 'start-work');
                view.modelAction.set('id_order', id_order);
                view.modelAction.set('seller_id', seller_id);
                view.modelAction.save('', '', {
                    beforeSend: function() {
                        view.blockUi.block($target);
                    },
                    success: function(result, res, xhr) {
                        view.blockUi.unblock();
                        if(res.status === true) {
                            window.location.reload();
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error'
                            });
                        }
                    },
                    error: function() {
                        view.blockUi.unblock();
                        alert('Error, please contact admin');
                    }
                })
            },

            acceptOrder: function() {
                var view = this;
                if( typeof view.reviewModal  === 'undefined' ){
                    view.reviewModal = new Views.ModalReview();
                }
                view.reviewModal.onOpen(view.modelAction);
            },

            delayOrder: function(e) {
                e.preventDefault();
                var $target = $(e.currentTarget);
                var view = this;

                view.modelAction.set('do_action', 'delay');
                view.modelAction.save('', '', {
                    beforeSend: function () {
                        view.blockUi.block($target)
                    },
                    success: function (result, res, xhr) {
                        if (res.success) {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'success'
                            });

                            $('.order_status').html(res.data.status_text);
                            $('.order_status').addClass(res.data.status_text_color);
                            $target.remove();

                            window.location.reload(true);
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error'
                            });
                        }
                        view.blockUi.unblock();

                    }
                });
            }
        });

        Views.ModalDelivery = Views.Modal_Box.extend({
            el: '#delivery',
            initialize: function() {
                AE.Views.Modal_Box.prototype.initialize.call();
                if( typeof this.model === 'undefined' ){
                    this.model = new Models.Delivery();
                }
            },
            onOpen: function(parent){
                var view = this;
                this.model.set('post_type', 'order_delivery');
                view.model.set('post_parent', parent);

                var expireTime = parseInt($('.expired-date-format').attr('data-expire-time'));
                var currentTime = Math.round((Date.now() / 1000));
                if( currentTime < expireTime ) {
                  view.model.set('order_countdown_delivery', $('.countdown').html());
                } else {
                  view.model.set('order_countdown_delivery', '');
                }

                view.openModal();
                view.setupFields();
            },
            setupFields: function(){
                var view = this;
                if (typeof view.carousels === 'undefined') {
                    view.carousels = new Views.Carousel({
                        el: $('.gallery_container'),
                        name_item:'et_carousel',
                        uploaderID:'deliver',
                        model: view.model,
                        extensions: ae_globals.file_types,
                        carouselTemplate: '#ae_carousel_file_template'

                    });
                }
                if( typeof view.deliveryForm === 'undefined') {
                    view.deliveryForm = new Views.AE_Form({
                        el: '.delivery-order', // parent of form
                        model: view.model,
                        rules: {
                            post_content: 'required'
                        },
                        type: 'delivery',
                        blockTarget: '.delivery-order button'
                    });
                }
            }
        });
        Views.ModalReview = Views.Modal_Box.extend({
            el: '#modal_review',
            events: {
                'click .btn-skip': 'skipReview',
                'submit .review-form': 'review'
            },
            initialize: function() {
                AE.Views.Modal_Box.prototype.initialize.call();
                this.blockUi = new Views.BlockUi();

            },
            onOpen: function(model){
                var view = this;
                view.model = model;
                view.openModal();
                view.setupFields();
            },
            setupFields: function(){
                var view = this;
                $('.rating-it').raty({
                    half: true,
                    hints: raty.hint
                });
            },
            skipReview: function(e){
                e.preventDefault();
                $target = $(e.currentTarget);
                var view = this;
                view.saveModel(view.model, $target, false);
            },
            review: function(e){
                e.preventDefault();
                var view = this,
                    $target = $(e.currentTarget);
                if( view.$el.find('input[name="score"]').length > 0  && view.$el.find('input[name="score"]').val() != '') {
                    var score = view.$el.find('input[name="score"]').val();
                }
                else{
                    var score = 0;
                }
                var message = view.$el.find('textarea[name="comment_content"]').val();
                var data = {
                    action: 'mjob-user-review',
                    score: score,
                    comment_content: message,
                    order_id: view.model.get('ID')

                }
                $.ajax({
                    url: ae_globals.ajaxURL,
                    type: 'post',
                    data: data,
                    beforeSend: function() {
                        view.blockUi.block($target);
                    },
                    success: function(res) {
                        view.blockUi.unblock();
                        if (res.success) {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'success'
                            });
                            view.saveModel(view.model, $target, false);
                            setTimeout(function() {
                                window.location.reload();
                            }, 2000);
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error'
                            });
                        }
                    }
                });
            },
            saveModel: function (model, $target, s) {
                var view = this;
                model.set('do_action', 'finish');
                model.save('', '', {
                    beforeSend: function () {
                        view.blockUi.block($target)
                    },
                    success: function (result, res, xhr) {
                        view.blockUi.unblock();
                        if (res.success) {
                            view.closeModal();
                            window.location.reload(true);
                        } else {
                            AE.pubsub.trigger('ae:notification', {
                                msg: res.msg,
                                notice_type: 'error'
                            });
                        }
                    }
                });
            }
        });
        new Views.SingleOrder();
    });
})(jQuery, window.AE.Models, window.AE.Collections, window.AE.Views);