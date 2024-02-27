(function ($, Views) {
  let EditTopupModel = Backbone.Model.extend({
    action: 'mje_topup_sync',
    defaults: {
      mode: 'add',
      amount: ''
    }
  });

  let EditTopupView = Backbone.View.extend({
    tagName: 'tr',
    className: 'inline-edit-row-topup inline-edit-row quick-edit-row inline-editor',
    template: _.template($('#topup-inline-edit-template').html()),
    events: {
      'keyup .topup-amount-input' : 'changeFund',
      'change .topup-mode' : 'switchMode',
      'click .topup-edit-cancel' : 'cancelEdit',
      'click .topup-edit-save' : 'saveEdit'
    },
    initialize: function () {
      this.render();
      this.editMode = 'add';
      this.rowId = `#topup-user-${this.model.get('row_id')}`;
      this.inlineEditId = `#inline-edit-topup-${this.model.get('row_id')}`;

      this.model.on('change', this.render, this)
    },
    cancelEdit: function () {
      $(this.rowId).show();
      $('.inline-edit-row-topup').remove();
    },
    saveEdit: function (event) {
      event.preventDefault();
      let userId = this.model.get('row_id');
      let inlineEditId = this.inlineEditId;
      let view = this;
      $.ajax({
        url: ae_globals.ajaxURL,
        method: 'POST',
        data: {
          amount: view.formatAmount(view.model.get('amount')),
          mode: view.model.get('mode'),
          user: userId,
          message: $(inlineEditId).find('#topup-message').val(),
          action: 'mje_topup_sync',
          method: 'save'
        },
        beforeSend: function () {
          $(inlineEditId).find('.inline-edit-spinner').removeClass('hide');
        },
        success: function (response) {
          $(inlineEditId).find('.inline-edit-spinner').addClass('hide');

          if (!response.success) {
            return false;
          } else {
            // close edit
            view.cancelEdit();
            // change row balance
            $(view.rowId).find('.available_fund .price').html(response.new_balance_html);
          }
        }
      })
    },
    switchMode: function (event) {
      let target = $(event.currentTarget);
      this.editMode = target.val();

      if (this.model.get('amount') === '') { return false; }

      let amount = this.formatAmount(this.model.get('amount'));
      this.previewUpdate(amount, this.editMode);
    },
    changeFund: _.debounce(function (event) {
      let input = event.currentTarget;
      let amount = this.formatAmount(input.value);
      input.blur();
      // update credit
      this.previewUpdate(amount, this.editMode);
    }, 1000),
    formatAmount: function (amount) {
      let pattern = ae_globals.number_format_settings.thousand_sep == '.'
        ? /\./g
        : /\,/g;
      amount = amount.replace(pattern, '');
      amount = amount.replace(/\,/g, '.');
      amount = parseFloat(amount);
      return amount;
    },
    previewUpdate: function (amount, mode) {
      let userId = this.model.get('row_id');
      let inlineEditId = this.inlineEditId;
      let view = this;
      $.ajax({
        url: ae_globals.ajaxURL,
        method: 'GET',
        data: {
          amount: amount,
          mode: mode,
          user: userId,
          action: 'mje_topup_sync',
          method: 'preview'
        },
        beforeSend: function () {
          $(inlineEditId).find('.inline-edit-spinner').removeClass('hide');
        },
        success: function (response) {
          $(inlineEditId).find('.inline-edit-spinner').addClass('hide');

          if (!response.success) {
            return false;
          }

          view.model.set('amount', $(inlineEditId).find('.topup-amount-input').val());
          view.model.set('mode', mode);
          view.model.set('user_fund', response.balance_html);
          view.model.set('old_balance_html', response.old_balance_html);
        }
      })
    },
    render: function () {
      this.$el.html(this.template(this.model.toJSON()));
    }
  });


  let CreditTopupView = Backbone.View.extend({
    el: '#js-credit-topup',
    events: {
      'click .button-topup-js': 'openEdit',
      'click .price-changelog-js': 'viewPriceChangelog',
      'click #search-submit': 'changeCurrentPage',
      'keypress #current-page-selector': 'keepCurrentPage'
    },
    initialize: function () {
      let maskPattern = ae_globals.number_format_settings.thousand_sep == ','
                  ? '000,000,000,000,000.00'
                  : '000.000.000.000.000,00';

      $('.topup-amount-input').mask(maskPattern, {reverse: true});
    },
    keepCurrentPage: function (event) {
      let target = $(event.currentTarget);
      if (event.keyCode === 13) {
        this.$el.find('form').submit();
        return false;
      }
    },
    changeCurrentPage: function () {
      $('#current-page-selector').val(1);
    },
    openEdit: function(event) {
      event.preventDefault();
      let button = event.currentTarget;
      let topupRowId = button.dataset.id;
      let userLogin = button.dataset.username;
      let userEmail = button.dataset.email;
      let userRegistered = button.dataset.userregister;
      let userFund = $(`#topup-user-${topupRowId}`).find('.available_fund .price').html();

      // Init edit view
      let editModel = new EditTopupModel({
        row_id: topupRowId,
        user_login: userLogin,
        user_email: userEmail,
        user_registered: userRegistered,
        user_fund: userFund
      });

      let editView = new EditTopupView({ model: editModel });

      // Remove another inline edit and show the current edit
      $('.inline-edit-row-topup').remove();
      $(editView.el).insertAfter( `#topup-user-${topupRowId}` );

      // Show another rows and hide the current edit row
      $('.topup-user-row').show();
      $(`#topup-user-${topupRowId}`).hide();
    },
    viewPriceChangelog: function (event) {
      let target = $(event.currentTarget);
      let username = target.attr('data-username');
      let useremail = target.attr('data-email');
      let userid = target.attr('data-id');
      this.showDialog(username, useremail, userid)
    },
    showDialog: function (username, useremail, userid) {
      let dialogWrapper = $('.topup-changelog-dialog');
      dialogWrapper.removeClass('hidden');
      dialogWrapper.find('.inner').addClass('is-empty');
      dialogWrapper.find('.inner table').remove();
      dialogWrapper.find('.inner .not-found').remove();

      dialogWrapper.find('.inner .user-info').addClass('hidden');
      dialogWrapper.find('.inner .user-info .username').text(username);
      dialogWrapper.find('.inner .user-info .useremail').text(useremail);

      // request data
      $.ajax({
        url: ae_globals.ajaxURL,
        method: 'GET',
        data: {
          action: 'mje_topup_sync',
          method: 'changelog',
          user: userid
        },
        success: function (response) {
          dialogWrapper.find('.inner').removeClass('is-empty');
          dialogWrapper.find('.inner table').remove();
          dialogWrapper.find('.inner .not-found').remove();
          dialogWrapper.find('.inner .user-info').removeClass('hidden');
          dialogWrapper.find('.inner').append(response.output);
        }
      })
    }
  });
  $(document).ready(function () {
    new CreditTopupView();

    $('.topup-changelog-dialog').on('click', function (event) {
      let target = $(event.currentTarget);
      target.addClass('hidden');
      event.stopPropagation();
    });

    $('.topup-changelog-dialog .inner').on('click', function (event) {
      event.stopPropagation();
    });
  });
})(jQuery, AE.Views);