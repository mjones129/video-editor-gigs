(function($, Models, Collections, Views) {
    $(document).ready(function() {
        /**
         * model withdraw
         */
        Models.mJobOrder = Backbone.Model.extend({
            action: 'mjob-admin-order-sync',
            initialize: function() {}
        });
        Collections.mJobOrder = Backbone.Collection.extend({
            model: Models.mJobOrder,
            action: 'mjob-admin-fetch-order',
            initialize: function() {
                this.paged = 1;
            }
        });
        var mJobOrderItem = Views.PostItem.extend({
            tagName: 'li',
            className: 'mjob-order-item',
            template: _.template($('#mjob-order-loop').html()),
            onItemBeforeRender: function() {
            },
            onItemRendered: function() {
                console.log('onItemRendered');
            },
            render: function() { // v 1.3.9.7
                var view = this;
                this.$el.html(this.template(this.model.toJSON()));
                console.log('render');

                return this;
            },
        });
        ListMjobOrders = Views.ListPost.extend({
            tagName: 'li',
            itemView: mJobOrderItem,
            itemClass: 'mjob-order-item'
        });
        // notification list control
        if( $('.mjob-order-container').length > 0 ){
            if( $('.mjob-order-container').find('.mjob_order_data').length > 0 ){
                var postsdata = JSON.parse($('.mjob-order-container').find('.mjob_order_data').html()),
                    posts = new Collections.mJobOrder(postsdata);
            } else {
                var posts = new Collections.mJobOrder();
            }

            /**
             * init list blog view
             */
            new ListMjobOrders({
                itemView: mJobOrderItem,
                collection: posts,
                el: $('.mjob-order-container').find('.list-mjob-orders')
            });
            /**
             * init block control list blog
             */
            new Views.BlockControl({
                collection: posts,
                el: $('.mjob-order-container'),
                onAfterFetch: function(result, res, target) {

                    $('.mjob-order-container').find('.list-mjob-orders .no-items').remove();
                    if(res.success == false) {

                        $('.mjob-order-container').find('.list-mjob-orders').html('<p class="no-items">'+ ae_globals.mjob_order_not_found_text +'</p>');
                        $(".btnExportOrder").addClass('hide');
                    } else {
                        console.log('show btn download');
                        $(".btnExportOrder").removeClass('hide');
                    }

                }
            });

                // Withdraw container view
            Views.mJobOrderContainer = Backbone.View.extend({
                el: '.mjob-order-container',
                initialize: function() {
                    AE.pubsub.on('ae:model:ondecline-mjob-order', this.declineMjobOrder, this);
                },
                declineMjobOrder: function(model) {
                    this.model = model;
                    var message = prompt("Please give a decline reason", "");
                    if(message == null) return false;
                    if(message == '') {
                        alert('Decline reason required');
                        return false;
                    }

                    // remove white spaces
                    var compareStr = message.replace(/\s+/g, '');
                    // remove break lines
                    compareStr = message.replace(/(\r\n|\n|\r)/gm,"");
                    if(compareStr != "") {
                        this.model.set('reject_message', message);
                        this.model.set('archive', 1);
                        this.model.save('post_status', 'draft', {
                            beforeSend: function() {

                            },
                            success: function(status, resp, jqXHR) {
                                if(resp.success == true) {

                                } else {

                                }
                            }
                        })
                    }
                }
            });
            new Views.mJobOrderContainer();
        }
        var blockUi = new Views.BlockUi();
        jQuery('#update-data').on('click', function() {
                jQuery.ajax({
                url: ae_globals.ajaxURL,
                type: 'POST',
                data: {
                        action: 'mje-update_data',
                        method: 'update_total_sale'
                    },
                beforeSend: function() {
                        blockUi.block("#update-data");
                    },
                success: function(res) {
                    if(res.success) {
                       alert(res.msg);
                       $(".notice-dismiss").trigger( "click" );
                    }
                    else
                    {
                        alert(res.msg);
                    }
                },
                complete: function(){
                    blockUi.unblock();
                }
            });
        });
    });
})(jQuery, window.AE.Models, window.AE.Collections, window.AE.Views);