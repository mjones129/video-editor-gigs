(function($, Views, Models) {
  Views.ChangelogModal = Views.Modal_Box.extend({
    el: '#topup-changelog-modal-front',
    initialize: function () {
      this.loadingEl = this.$el.find('.loading');
      this.innerEl = this.$el.find('.inner');
    },
    onOpen: function () {
      var view = this;
      this.openModal();

      view.loadingEl.show();
      view.innerEl.find('table').remove();
      view.innerEl.find('.not-found').remove();

      // request data
      $.ajax({
        url: ae_globals.ajaxURL,
        method: 'GET',
        data: {
          action: 'mje_topup_user_front',
          method: 'changelog',
          noncetopup: view.$el.find('.nonce').val()
        },
        success: function (response) {
          if (!response.success) {
            AE.pubsub.trigger('ae:notification', {
              notice_type: 'error',
              msg: response.msg
            });
            view.closeModal();
            return false;
          }

          view.loadingEl.hide();
          view.innerEl.append(response.output);
        }
      })
    }
  });

  Views.TopupFront = Backbone.View.extend({
    el: '.mje-main-wrapper',
    events: {
      'click .topup-user-show': 'openModal'
    },
    initialize: function () {
      if (typeof this.modal === 'undefined') {
        this.modal = new Views.ChangelogModal();
      }

      if (window.location.hash == '#topup') {
        this.openModal();
      }
    },
    openModal: function () {
      this.modal.onOpen();
    }
  });

  new Views.TopupFront();
})(jQuery, AE.Views, AE.Models);