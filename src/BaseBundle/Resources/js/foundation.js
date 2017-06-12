$(function() {
  var app = {
    models: {},
    views: {},
    collections: {},
    vars: {},
    func: {
      fancyForm: function(form) {
        form.find('.select2').select2();
        form.find('.datepicker').each(function() {
          $(this).datetimepicker({
            format: $(this).data('format'),
            showTodayButton: true,
            showClear: true
          });
        });
      },

      showMessage: function(type, message) {
        if (typeof app.func.showMessage.messages === 'undefined') {
          app.func.showMessage.messages = [];
        }
        var msgs = app.func.showMessage.messages;
        var template = _.template($('#template-flash-message').html());
        var id = Date.now();
        var msg = {
          id: id,
          type: type,
          message: message
        };
        //store message history for possible fanciness later
        msgs.push(msg);
        $('.admin-flash-messages').html(template(msg));

        setTimeout(function() {
          $('#flash-message-'+id).slideUp();
        }, 3000);
      },

      showError: function(message) {
        return app.func.showMessage('danger', message);
      },

      showSuccess: function(message) {
        return app.func.showMessage('success', message);
      }
    }
  };

  window.app = app;
});
