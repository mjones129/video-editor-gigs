/**
 * Create Dang Bui
 */
(function($, Models, Collections, Views) {
    $(document).ready(function() {
        //Get user login
        var fromUser = $('#bt-send-custom').attr('data-from-user');
        var toUser = $('#bt-send-custom').attr('data-to-user');
        var conversationParent = $('#bt-send-custom').attr('data-conversation-parent');
        var mJob = $('#bt-send-custom').attr('data-mjob');
        var conversationGuid = $('#bt-send-custom').attr('data-conversation-guid');
        var mJobName = $('#bt-send-custom').attr('data-mjob-name');
        $('#customOrderModal .mjob-name').text(mJobName);
        /**
         * Custom order show modal and check validate and redirect page
         * @param void
         * @return void
         * @since 1.0
         * @package MicrojobEngine
         * @category void
         * @author Dang Bui
         */
        Views.CustomOrder = Backbone.View.extend({
            el: 'body',
            events: {
                'click .bt-send-custom': 'modalCustom',
                'click #bt-send-custom-disable' : 'infoCustomDisable'
            },
            initialize: function () {
                if($('#current_user').length > 0) {
                    if(typeof currentUser.data !== "undefined") {
                        this.user = new Models.mJobUser(currentUser.data);
                    } else {
                        this.user = new Models.mJobUser(currentUser);
                    }
                } else {
                    this.user = new Models.mJobUser();
                }
            },

            modalCustom: function (event) {
                console.log('123');
                event.preventDefault();
                if (fromUser == 0 || !fromUser) {
                    // Open sign in modal
                    if (typeof this.signInModal === "undefined") {
                        this.signInModal = new Views.SignInModal();
                    }
                    this.signInModal.openModal();
                    AE.pubsub.trigger('mjob:open:signin:modal', this.signInModal);
                }
                else {
                    if(typeof currentUser.data.register_status !== 'undefined' && currentUser.data.register_status == '') {
                        if (typeof this.customOrder == 'undefined')
                            this.customOrder = new Views.ModalCustom();

                        this.customOrder.open_modal();
                    }else {
                        AE.pubsub.trigger('ae:notification', {
                            notice_type: 'error',
                            msg: ae_globals.pending_account_error_txt
                        })
                    }
                }
            },

            infoCustomDisable: function (e) {
                e.preventDefault();
                AE.pubsub.trigger('ae:notification', {
                    msg: ae_globals.disableNotification,
                    notice_type: 'error'
                })
            }
        });

        Views.ModalCustom = Views.Modal_Box.extend({
            el: '#customOrderModal',
            initialize: function () {
                AE.Views.Modal_Box.prototype.initialize.call();
                if (typeof this.model === 'undefined') {
                    this.model = new Models.Message();
                    this.model.set('post_type', 'ae_message');
                    this.model.set('type', 'custom_order');
                    this.model.set('from_user', fromUser);
                    this.model.set('to_user', toUser);
                    if (conversationParent == 0)
                        this.model.set('is_conversation', 1);
                    this.model.set('conversation_status', 'unread');
                    this.model.set('post_title', conversation_global.conversation_title);
                    this.model.set('post_parent', conversationParent);
                    this.model.set('mjob', mJob);
                    this.model.set('conversation_guid', conversationGuid);
                }

                AE.pubsub.on("ae:form:submit:success", this.afterSendCustomOrder, this);
            },
            afterSendCustomOrder: function (result, resp, jqXHR, type) {
                if (type == "send-custom-order") {
                    this.closeModal();
                    // Xử lý việc chuyển trang- nếu mà chưa có conversation thì tạo và chuyển đến, nếu có rồi thì chuyển đến conversation đã có sẳn
                    if (resp.data.post_parent == '0') {
                        window.location = resp.data.permalink;
                    } else if (result['_previousAttributes'].conversation_guid != '') {
                        window.location = result['_previousAttributes'].conversation_guid;
                    }
                }
            },
            open_modal: function (data) {
                view = this;
                view.openModal();
                view.setupFields();
            },
            setupFields: function () {
                $('#form_post_content').val('');
                var view = this;
                if (typeof view.carousels === 'undefined') {
                    view.carousels = new Views.Carousel({
                        el: $('.gallery_container'),
                        name_item: 'et_carousel',
                        uploaderID: 'custom-order',
                        model: view.model,
                        extensions: ae_globals.file_types,
                        carouselTemplate: '#ae_carousel_file_template'
                    });
                }

                if(typeof view.CustomOrderForm === "undefined") {
                    view.CustomOrderForm = new Views.AE_Form({
                        el: '#customOrderModal', // parent of form
                        model: view.model,
                        rules: {
                            post_content: 'required',
                            budget: {
                                min: 1,
                                required: true
                            },
                            deadline: {
                                required: true,
                                min: 1
                            }
                        },
                        type: 'send-custom-order',
                        blockTarget: 'button.send-custom-order'
                    });
                }

            },
        });
        //Call event click send custom order in single mjob
        new Views.CustomOrder();

        /**
         * Event click Load more custom order in message detail
         * @param void
         * @return void
         * @since 1.0
         * @package MicrojobEngine
         * @category void
         * @author Dang Bui
         */
        var customOrderContainer = $('.custom-order-box');
        if (customOrderContainer.length > 0) {
            Models.PageCustomOrder = Backbone.Model.extend({
                defaults: {
                    post_type: 'ae_message'
                }
            });
            Collections.PageCustomOrder = Backbone.Collection.extend({
                model: Models.PageCustomOrder,
                action: 'ae-fetch-ae_custom_post',
                initialize: function () {
                    this.paged = 1;
                }
            });
            var customOrderItem = Views.PostItem.extend({
                tagName: 'li',
                className: 'clearfix',
                template: _.template($('#custom-order-item').html())
            });


            Views.customOrderList = Views.ListPost.extend({
                tagName: 'ul',
                itemView: customOrderItem,
                initialize: function (options) {
                    _.extend(this, options);
                    Views.ListPost.prototype.initialize.call(this, options);
                },
            });
            if (typeof customOrderCollection === "undefined") {
                if ($('.custom_order_postdata').length > 0) {
                    var custom_order = JSON.parse($('.custom_order_postdata').html());
                    customOrderCollection = new Collections.PageCustomOrder(custom_order);
                } else {
                    customOrderCollection = new Collections.PageCustomOrder();
                }
            }

            // Custom order list view
            var customOrderList = new Views.customOrderList({
                itemView: customOrderItem,
                collection: customOrderCollection,
                el: customOrderContainer.find('.list-custom-order'),
            });

            //Call events load more in core
            new Views.BlockControl({
                collection: customOrderCollection,
                el: customOrderContainer
            });
        }


        /**
         * View custom order detail
         * @param void
         * @return void
         * @since 1.0
         * @package MicrojobEngine
         * @category void
         * @author Dang Bui
         */

        if($('.box-content-custom-order').length > 0) {
            Models.CustomOrderDetail = Backbone.Model.extend({
                action: 'show-custom-order-detail',
                default: {
                    'custom_order_id': '0'
                }
            });

            Views.CustomOrderDetail = Backbone.View.extend({
                template: _.template($('.box-content-custom-order').html()),
                el: 'body',
                events: {
                    'click .btn-send-offer': 'showSendOfferForm',
                    'click .name-customer-order': 'showCustomOrderDetail',
                    'click .btn-back-custom-order' : 'showCustomOrderDetail',
                    'click .btn-decline' : 'showDeclineForm',
                    'click .btn-reject' : 'showRejectForm',
                    'click .close-detail': 'hideOuterDetail'
                },
                initialize: function () {
                    this.blockUi = new Views.BlockUi();
                    AE.pubsub.on('ae:form:submit:success', this.afterSubmitForm, this);
                    this.initOfferModel();
                    this.initDeclineModel();
                    this.initRejectModel();
                },
                showOuterDetail: function() {
                    $(".outer-detail-custom-order" ).show({direction: "left" }, 1000);
                    $('.single-ae_message .overlay-custom-detail').fadeIn();
                    $(".outer-detail-custom").mCustomScrollbar({
                        theme:"minimal",
                    });
                },
                hideOuterDetail: function() {
                    $(".outer-detail-custom-order").hide('slide');
                    $('.single-ae_message .overlay-custom-detail').fadeOut();
                },
                showCustomOrderDetail: function (e) {
                    e.preventDefault();
                    var target = $(e.currentTarget);
                    var idCustomOrder = $(target).attr('data-id');
                    var view = this;
                    view.model = new Models.CustomOrderDetail;
                    view.model.set('custom_order_id', idCustomOrder);
                    view.model.save('', '', {
                        beforeSend: function () {
                            view.blockUi.block(target.parent());
                        },
                        success: function (result, res, xhr) {
                            view.blockUi.unblock();
                            if (!res.success) {
                                AE.pubsub.trigger('ae:notification', {
                                    msg: res.msg,
                                    notice_type: 'error'
                                });
                            } else {
                                //Sroll top
                                var body = $("html, body");
                                body.stop().animate({scrollTop: 0}, '500', 'swing');
                                var data = res.data;
                                view.model.set('mjob_name', data.mjob_name);
                                view.model.set('post_modified', data.post_modified);
                                view.model.set('post_content', data.post_content);
                                view.model.set('budget', data.budget);
                                view.model.set('deadline', data.deadline);
                                view.model.set('attach_file', data.attach_file);
                                view.model.set('offer_content', data.offer_content);
                                view.model.set('offer_budget', data.offer_budget);
                                view.model.set('offer_etd', data.offer_etd);
                                view.model.set('order_status', data.order_status);
                                view.model.set('is_offer', data.is_offer);
                                view.model.set('custom_order_status_text', data.custom_order_status_text);
                                $('.content-custom-order').html(view.template(view.model.toJSON()));
                                //Hide all action form in outer detail custom order
                                $('.outer-detail-custom-order .action-form').hide();
                                //Show contect custom order
                                $('.content-custom-order').show();
                                /*$('.outer-detail-custom-order').show();*/
                                view.showOuterDetail();
                            }
                        }
                    });
                },
                /* Show form send offer */
                showSendOfferForm: function (event) {
                    event.preventDefault();

                    /* Scroll to top */
                    var body = $("html, body");
                    body.stop().animate({scrollTop: 0}, '500', 'swing');
                    //Hide all action form in outer detail custom order
                    $('.outer-detail-custom-order .action-form').hide();
                    $('#send-offer').show();
                    this.showOuterDetail();
                    this.initSendOfferForm(event);
                },
                initOfferModel: function() {
                    this.offerModel = new Models.Message();
                    this.offerModel.set({
                        post_parent: $('#conversation_id').val(),
                        _wpnonce: $('#_wpnonce').val(),
                        type: 'offer',
                        from_user: $('#from_user').val(),
                        to_user: $('#to_user').val()
                    });
                },
                initSendOfferForm: function (event) {
                    var view = this;
                    var target = $(event.currentTarget);
                    view.offerModel.set('custom_order_id', target.attr('data-custom-order'));
                    //Set data-id button back
                    $('.btn-back-custom-order').attr('data-id', target.attr('data-custom-order'));
                    /* Set up carousel */
                    if (typeof view.offerCarousels === 'undefined') {
                        view.offerCarousels = new Views.Carousel({
                            el: $('.gallery_container_send_offer'),
                            uploaderID: 'send_offer',
                            model: view.offerModel,
                            carouselTemplate: '#ae_carousel_file_template',
                            extensions: ae_globals.file_types
                        });
                    }
                    /* Set up form */
                    if (typeof this.sendOfferForm === "undefined") {
                        this.sendOfferForm = new Views.AE_Form({
                            el: '#send-offer', // Wrapper of form,
                            model: view.offerModel,
                            rules: {
                                post_content: 'required',
                                budget: {
                                    required: true,
                                    min: 1
                                },
                                etd: {
                                    required: true,
                                    min: 1
                                }
                            },
                            type: 'send-offer',
                            blockTarget: '#send-offer .submit',
                            showNotice: false
                        });
                    }
                },
                resetSendOfferForm: function (event) {
                    var offerForm = $("#send-offer");
                    offerForm.find('input[type="text"]').val('');
                    offerForm.find('input[type="number"]').val('');
                    offerForm.find('textarea').val('');
                    offerForm.find('.gallery-image').html('');
                },
                afterSubmitForm: function (result, resp, xhr, type) {
                    $('.single-ae_message .overlay-custom-detail').fadeOut();
                    var view = this;
                    /* After sending offer */
                    if (type == 'send-offer') {
                        if (resp.success == true) {
                            AE.pubsub.trigger('ae:notification', {
                                notice_type: 'success',
                                msg: resp.msg
                            });

                            // Close form
                            $(".outer-detail-custom-order").toggle("slide");
                            // Remove button
                            var currentCustomOrder = $("#custom-order-" + resp.data.custom_order_id);
                            currentCustomOrder.find('.custom-order-btn').remove();
                            // Change label
                            currentCustomOrder.find('h2').after(resp.data.label_offer_sent);
                            // Scroll to current custom order
                            var top = currentCustomOrder.offset().top;
                            $('html, body').stop().animate({scrollTop: top - 20}, 1000, 'swing');
                            // Reset form
                            view.resetSendOfferForm();
                            view.initOfferModel();
                            view.sendOfferForm.resetModel(this.offerModel);
                        } else {
                            if (typeof resp.msg.budget !== 'undefined') {
                                AE.pubsub.trigger('ae:notification', {
                                    notice_type: 'error',
                                    msg: resp.msg.budget
                                });
                            }
                            if (typeof resp.msg.etd !== 'undefined') {
                                AE.pubsub.trigger('ae:notification', {
                                    notice_type: 'error',
                                    msg: resp.msg.etd
                                });
                            }
                            if (typeof resp.msg.budget === 'undefined' && typeof resp.msg.etd === 'undefined') {
                                AE.pubsub.trigger('ae:notification', {
                                    notice_type: 'error',
                                    msg: resp.msg
                                });
                            }
                        }
                    }

                    if(type == 'decline') {
                        if (resp.success == true) {
                            var current_custom_order = resp.data.custom_order_id;
                            $(".outer-detail-custom-order").hide('slide');
                            $('#custom-order-' + current_custom_order).parent().remove();
                            //Clear form input
                            $('.decline-form').find('textarea').attr('value','');
                            view.initDeclineModel();
                            view.sendDeclineForm.resetModel(view.declineModel);
                        }
                    }

                    if( type == 'reject') {
                        if(resp.success == true) {
                            var current_custom_order = resp.data.custom_order_id;
                            $(".outer-detail-custom-order").hide('slide');
                            $('#custom-order-' + current_custom_order).parent().remove();
                            //Clear form
                            $('.reject-form').find('textarea').attr('value','');
                            view.initRejectModel();
                            view.sendRejectForm.resetModel(view.rejectModel);
                        }
                    }

                    //Keep bottom function
                    if($.trim($('.list-custom-order').html()) == ''){
                        $('.list-custom-order').html('<p class="no-custom">No custom orders</p>');
                    }
                },

                //Show decline form
                showDeclineForm: function(event) {
                    event.preventDefault();
                    /* Scroll to top */
                    var body = $("html, body");
                    body.stop().animate({scrollTop: 0}, '500', 'swing');
                    $('.outer-detail-custom-order .action-form').hide();
                    $('#send-decline').show();
                    this.showOuterDetail();
                    this.initSendDeclineForm(event);
                },
                //
                initDeclineModel: function() {
                    this.declineModel = new Models.Message();
                    this.declineModel.set({
                        post_parent: $('#conversation_id').val(),
                        _wpnonce: $('#_wpnonce').val(),
                        type: 'decline',
                        from_user: $('#from_user').val(),
                        to_user: $('#to_user').val(),
                        post_title: ae_globals.custom_order_decline
                    });
                },
                initSendDeclineForm: function (event) {
                    var view = this;
                    var target = $(event.currentTarget);
                    view.declineModel.set('custom_order_id', target.attr('data-custom-order'));
                    //Set data-id button back
                    $('.btn-back-custom-order').attr('data-id', target.attr('data-custom-order'));

                    /* Set up form */
                    if (typeof this.sendDeclineForm === "undefined") {
                        this.sendDeclineForm = new Views.AE_Form({
                            el: '#send-decline', // Wrapper of form,
                            model: view.declineModel,
                            // rule: {},
                            type: 'decline',
                            blockTarget: '#send-decline .submit',
                        });
                    }
                },
                
                //Reject offer
                showRejectForm: function (event) {
                    event.preventDefault();
                    /* Scroll to top */
                    var body = $('html, body');
                    body.stop().animate({scrollTop: 0}, '500', 'swing');
                    $('.outer-detail-custom-order .action-form').hide();
                    $('#send-reject').show();
                    this.showOuterDetail();
                    this.initSendRejectForm(event);
                },
                initRejectModel: function() {
                    this.rejectModel = new Models.Message();
                    this.rejectModel.set({
                        post_parent: $('#conversation_id').val(),
                        _wpnonce: $('#_wpnonce').val(),
                        type: 'reject',
                        from_user: $('#from_user').val(),
                        to_user: $('#to_user').val(),
                        post_title: ae_globals.custom_order_reject
                    });
                },

            //    Init reject form
                initSendRejectForm: function(event) {
                    var view = this;
                    var target = $(event.currentTarget);
                    view.rejectModel.set('custom_order_id', target.attr('data-custom-order'));
                    //Set data-id button back
                    $('.btn-back-custom-order').attr('data-id', target.attr('data-custom-order'));
                    /* Set up form */
                    if (typeof this.sendRejectForm === "undefined") {
                        this.sendRejectForm = new Views.AE_Form({
                            el: '#send-reject', // Wrapper of form,
                            model: view.rejectModel,
                            type: 'reject',
                            blockTarget: '#send-reject .submit',
                        });
                    }
                }
            });
            //Call Views Custom Order Detail
            new Views.CustomOrderDetail();
        }

    });
})(jQuery, window.AE.Models, window.AE.Collections, window.AE.Views);

