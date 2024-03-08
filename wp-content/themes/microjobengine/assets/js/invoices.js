(function($, Views, Models, Collections) {
  $(document).ready(function() {
    Models.Invoice = Backbone.Model.extend({
      action: 'mje_sync_invoice',
      defaults:{
        post_type:'order'
      },
      initialize: function() {}
    });

    Collections.Invoices = Backbone.Collection.extend({
      model: Models.Invoice,
      action: 'mje_fetch_invoices',
      initialize: function() {
        this.paged = 1;
      }
    });

    var invoiceItem = Views.PostItem.extend({
      tagName: 'tr',
      className: 'invoice-item',
      template: _.template($('#invoice-item-template').html())
    });

    Views.InvoiceList = Views.ListPost.extend({
      tagName: 'table',
      itemClass: 'invoice-item'
    });

    var invoiceContainer = $('#invoices-container');
    if(invoiceContainer.length > 0) {
      if(typeof invoiceCollection === 'undefined') {
        var invoiceData = $('#invoices-data');
        if(invoiceData.length > 0) {
          invoiceCollection = new Collections.Invoices(JSON.parse(invoiceData.html()));
        } else {
          invoiceCollection = new Collections.Invoices();
        }
      }

      var invoiceList = new Views.InvoiceList({
        itemView: invoiceItem,
        collection: invoiceCollection,
        el: invoiceContainer.find('table')
      });

      new Views.BlockControl({
        collection: invoiceCollection,
        el: invoiceContainer,
        blockedEl: invoiceContainer.find('tbody'),
        onAfterFetch: function(result, resp, $target) {
          if(!resp.success) {
            invoiceContainer.find('.nothing-found').show();
            invoiceContainer.find('.help-text').hide();
          } else {
            invoiceContainer.find('.nothing-found').hide();
            invoiceContainer.find('.help-text').show();

            // Sort handle after fetch
            if($target.hasClass('orderby')) {
              $('.sort-head .orderby i').attr('class', 'fa fa-sort');
              order = $target.attr('data-order');
              if(order == 'desc') {
                $target.attr('data-order', 'asc');
                $target.find('.sort-icon i').attr('class', 'fa fa-sort-desc');
              } else if(order == 'asc') {
                $target.attr('data-order', 'desc');
                $target.find('.sort-icon i').attr('class', 'fa fa-sort-asc');
              }
            }
          }
        }
      })
    }
  });
})(jQuery, AE.Views, AE.Models, AE.Collections);