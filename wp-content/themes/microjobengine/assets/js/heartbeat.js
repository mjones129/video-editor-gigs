(function ($) {
  $(document).ready(function () {
    $(document).on('heartbeat-send', function (event, data) {
      data.conversation_nonce = mje_heartbeat.conversation_nonce;
    });

    $(document).on('heartbeat-tick', function (event, data) {
      if (typeof data.unread_messages.count === 'undefined') { return; }

      var parent = $('#et-header').find('.message-icon');
      parent.find('.alert-sign').remove();
      parent.find('.link-message').prepend('<span class="alert-sign">'+ data.unread_messages.count +'</span>');
      parent.find('.list-message-box-header .unread-message-count').text(data.unread_messages.count);
      parent.find('.list-message-box-body').html(data.unread_messages.dropdown_html);
    });
  });
})(jQuery);