(function ($, Views, Models, Collections) {
    /* Notification model */
    Models.Notification = Backbone.Model.extend({
        action: 'mje_sync_notification',
        defaults: {
            post_type: 'mje_notification'
        }
    });

    /* Notification collection */
    Collections.Notifications = Backbone.Collection.extend({
        model: Models.Notification,
        action: 'mje_fetch_notifications',
        initialize: function() {
            this.paged = 0;
        }
    })

    /* Notification container */
    var notificationContainer = $('#mje-notification-container');

    /* Notification item view */
    var notificationItem = Views.PostItem.extend({
        tagName: 'li',
        className: 'notification-item',
        template: _.template($('#notification-item-template').html()),
        events: function() {
            return _.extend({}, Views.PostItem.prototype.events, {
                'click .hide-action': 'hideNotification',
                'click .undo-action': 'undoHidden',
                'click .noti-link': 'forceRedirect'
            });
        },
        onItemRendered: function() {
            this.$el.find('.rate-it').raty({
                readOnly: true,
                half: true,
                score: function() {
                    return $(this).attr('data-score');
                },
                hints: raty.hint
            });
        },
        // Hide a single notification
        hideNotification: function(event) {
            event.preventDefault();
            event.stopPropagation();
            var view = this,
                target = $(event.currentTarget),
                parentInner = view.$el.find('.notification-item-inner');

            // Decrease unread count
            if(parentInner.hasClass('unread')) {
                var unreadCount = notificationContainer.find('.unread-count');
                var unreadCountValue = unreadCount.text();
                unreadCountValue -= 1;
                if( unreadCountValue > 0 ) {
                    unreadCount.text(unreadCountValue);
                } else {
                    unreadCount.hide();
                }
            }

            this.model.set('post_status', 'read');
            this.model.save('do_action', 'hide', {
                beforeSend: function() {
                    view.$el.addClass('hidden-noti');
                },
                success: function() {
                    AE.pubsub.trigger('mje:hide:notification');
                }
            });
        },
        // Undo a single notification
        undoHidden: function(event) {
            var target = $(event.currentTarget);
            var parentLi = target.parents('.notification-item');
            this.model.save('do_action', 'undo', {
                beforeSend: function() {
                    parentLi.removeClass('hidden-noti');
                },
                success: function() {
                    AE.pubsub.trigger('mje:undo:notification');
                }
            });
        },
        forceRedirect: function(event) {
            if(window.location.href == $(event.currentTarget).attr('href')) {
                event.preventDefault();
                window.location.reload();
            }
        }
    });

    /* Notification list view */
    Views.NotificationList = Views.ListPost.extend({
        tagName: 'ul',
        itemClass: 'notification-item'
    });

    var notificationCollection = new Collections.Notifications();

    var notificationList = new Views.NotificationList({
        itemView: notificationItem,
        collection: notificationCollection,
        el: notificationContainer.find('.notification-list')
    });

    /* Block control for notification */
    Views.Notification = Backbone.View.extend({
        el: '#mje-notification-container',
        events: {},
        initialize: function() {
            this.initData();
            AE.pubsub.on('mje:hide:notification', this.onHideNotification, this);
            AE.pubsub.on('mje:undo:notification', this.onUndoNotification, this);
        },
        initData: function() {
            this.collection = notificationCollection;
            this.first = true;
            this.isLoading = false;
            this.paged = 1;
            this.maxPages = 0;
            this.totalItems = 0;
        },
        /* Fetch notification */
        fetch: function(data) {
            if(this.first) {
                var view = this;
                if ( typeof data !== 'undefined' ) {
                    view.paged = data.paged
                }

                this.collection.fetch({
                    remove: !view.isLoading,
                    data: {
                        nonce: $('#notification_nonce').val(),
                        paged: view.paged
                    },
                    beforeSend: function() {
                        view.$el.find('.loading').show();
                    },
                    success: function(result, res, xhr) {
                        view.maxPages = res.max_num_pages;
                        view.totalItems += res.post_count;

                        // Mark unread
                        var notiIds = view.getNotificationID(res.data);
                        if( notiIds.length > 0) {
                            view.markRead({ noti_ids: notiIds });
                        }

                        $('#show-notifications').find('.alert-sign').remove();

                        // Init scroll bar
                        view.initScrollbar();

                        // Hide loading
                        if(view.paged >= view.maxPages) {
                            view.$el.find('.loading').hide();
                            if(view.isLoading) {
                                view.$el.find('.notification-reach-end').show();
                            }
                        }

                        // Show bell
                        if(view.totalItems == 0) {
                            view.$el.find('.notification-inner').addClass('notification-empty');
                        }

                        view.first = false;
                        view.isLoading = false;

                        if(res.data.length < res.post_count && view.paged < res.max_num_pages) {
                          view.first = true;
                          view.isLoading = true;
                          view.fetch({ paged: view.paged + 1 });
                        }
                    }
                });
            }
        },
        markRead: function(data) {
            data = _.extend(data, {
                nonce: $('#notification_nonce').val(),
                action: 'mje_mark_read_notifications'
            });
            $.ajax({
                url: ae_globals.ajaxURL,
                type: 'POST',
                data: data
            });
        },
        getNotificationID: function(data) {
          var id = [];
          data.forEach(function(model) {
             if(model.post_status == 'unread') {
                 id.push(model.ID);
             }
          });
          return id;
        },
        getNotificationListHeight: function() {
            var windowHeight = $(window).height();
            var notiHeader = this.$el.find('.notification-header').outerHeight();

            if($('#wpadminbar').length > 0) {
              return windowHeight - notiHeader - $('#wpadminbar').height();
            } else {
              return windowHeight - notiHeader;
            }
        },
        initScrollbar: function() {
            var view = this;
            view.$el.find('.notification-inner').height(view.getNotificationListHeight());
          view.$el.find('.notification-inner').css('overflow-y', 'auto');
          view.$el.find('.notification-inner').on('scroll', function() {
            if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
              if(!view.isLoading) {
                view.loadMore();
              }
            }
          });
        },
        loadMore: function() {
            var view = this;
            if(view.maxPages > 1 && view.maxPages > view.paged ) {
                view.first = true;
                view.isLoading = true;
                view.fetch({ paged: view.paged + 1 });
            }
        },
        reload: function() {
            var view = this;
            console.log('reload');
            if(view.maxPages > 1) {
                view.first = true;
                view.isLoading = false;
                view.paged = 1;
                view.fetch();
            }
        },
        onHideNotification: function() {
            this.totalItems -= 1;
        },
        onUndoNotification: function() {
            this.totalItems += 1;
        }
    });

    $(document).ready(function () {
        var notification =  new Views.Notification();

        var notiOverlay = $('#mje-notification-overlay');
        var notiContainer = $('#mje-notification-container');

        // Show notification
        $('#show-notifications').on('click', function() {
            notiOverlay.fadeIn();
            notiContainer.addClass('slide-in');
            $('body').addClass('stop-scroll-y');

            // Remove hidden noti
            $('.hidden-noti').each(function() {
               $(this).remove();
            });

            // Show bell if there are no notifications found
            if(!notification.first && notiContainer.find('.notification-item').length == 0) {
                notiContainer.find('.notification-inner').addClass('notification-empty');
            }

            // Fetch notification
            notification.fetch();

            // Load more
            if(notification.totalItems > 0 && notification.totalItems < 10) {
                notification.reload();
            }
        });

        // Hide notifications
        notiOverlay.on('click', hideNotification);
        $('.close-notification').on('click', hideNotification);
        function hideNotification () {
            notiOverlay.fadeOut();
            notiContainer.removeClass('slide-in');
            $('body').removeClass('stop-scroll-y');

            // Remove unread count
            notiContainer.find('.unread-count').remove();

            // Remove class unread
            $('.notification-item-inner').each(function() {
                $(this).removeClass('unread');
            });
        }
    });
})(jQuery, window.AE.Views, window.AE.Models, window.AE.Collections);