/**
 * backend order, control user list in user manage list
 * search order by post name
 * filter order by gateway
 * use Views.BlockUi add view block loading
 */
(function(Models, Views, Collections, $, Backbone) {
    Views.OrderList = Backbone.View.extend({
        events: {
            'click a.page-numbers' : 'pagination',
            'keyup input.order-search': 'search',
            'change select.et-input': 'search',
            'submit .search-box form': 'search',
            'click a.publish' : 'publishOrder',
            'click a.decline' : 'declineOrder'
        },

        initialize: function(options) {
            this.paged = 1;
            this.pages = options.pages;
            this.blockUi = new Views.BlockUi();

            if(typeof options.item_wrapper !== undefined) {
                this.item_wrapper = options.item_wrapper;
            } else {
                this.item_wrapper = 'ul';
            }
        },
        /**
         * build ajax params for ajax
         */
        buildParams: function(reset) {
            var view = this,
                keywork = this.$('input.order-search').val(),
                loadmore = view.$('.load-more'),
                gateway = this.$('select.gateway').val(),
                post_status = this.$('select.post_status').val(),
                // get ajax params from AE globals
                ajaxParams = AE.ajaxParams;
            if ( ! reset) {
                $target = this.$('.page-numbers');
            } else {
                $target = this.$(this.item_wrapper);
                view.paged = 1;
            }

            ajaxParams.success = function(result) {
                var data = result.data;
                view.blockUi.unblock();
                view.paged = result.page;
                if (reset) view.$(view.item_wrapper).html('');
                //html data
                view.$(view.item_wrapper).html(data);
                view.$('.paginations-wrapper').html(result.paginate);
                if (data == '') {
                    view.$(view.item_wrapper).html('<p class="no-items">' + result.msg + '</p>');
                }

            };
            ajaxParams.beforeSend = function() {
                view.blockUi.block($target);
            };
            /**
             * filter param
             */
            ajaxParams.data = {
                search: keywork,
                paged: view.paged
            };
            if (gateway != '') ajaxParams.data.payment = gateway;

            if(post_status != '') ajaxParams.data.post_status = post_status;

            ajaxParams.data.action = 'ae-fetch-orders';
            return ajaxParams
        },

        /**
         * pagination user event
         */

        pagination: function(event) {
            event.preventDefault();
            var view = this,
                $target = $(event.currentTarget);

            if($target.hasClass('next'))
                this.paged = parseInt(this.paged) + 1;
            else if($target.hasClass('prev'))
                this.paged = parseInt(this.paged) - 1;
            else
                this.paged = $target.html();

            var ajaxParams = this.buildParams(false);
            $.ajax(ajaxParams);

            $('html, body').animate({
                scrollTop: view.$el.offset().top - 180
            }, 800);
        },
        /**
         * search user
         */
        search: function(e) {
            e.preventDefault();
            this.paged = 1;
            var ajaxParams = this.buildParams(true);
            $.ajax(ajaxParams);
        },
        /**
         * admin publish and order
         */
        publishOrder : function(event){
            if( ! confirm(ae_globals.alert_confirm_approve)) return false;
            event.preventDefault();
            var $target =  $(event.currentTarget),
                data = {
                    'ID' : $target.attr('data-id'),
                    'status' : 'publish',
                    'action' : 'ae-sync-order'
                };
            this.syncOrder(data, $target);
        },
        /**
         * admin decline an order
         */
        declineOrder : function(event){
            if( ! confirm(ae_globals.alert_confirm_decline)) return false;
            event.preventDefault();
            var $target =  $(event.currentTarget),
                data = {
                    'ID' : $target.attr('data-id'),
                    'status' : 'draft',
                    'action' : 'ae-sync-order'
                };
            this.syncOrder(data, $target);
        },
        /**
         * js sync order
         */
        syncOrder : function(data, $target){
            var ajaxParams = AE.ajaxParams,
                view = this;

            ajaxParams.data = data;
            ajaxParams.success = function(response) {
                view.blockUi.unblock();

                if(response.success){
                    if(data.status == 'publish') {
                        $target.closest('li').find('.status i').attr('class', 'fa fa-check-circle');
                    }else{
                        $target.closest('li').find('.status i').attr('class', 'fa fa-times-circle');
                    }
                    $target.parent().find('.action').remove();
                }
            };
            ajaxParams.beforeSend = function() {
                view.blockUi.block($target);
            };

            $.ajax(ajaxParams);
        }

    });

})(window.AE.Models, window.AE.Views, window.AE.Collections, jQuery, Backbone);