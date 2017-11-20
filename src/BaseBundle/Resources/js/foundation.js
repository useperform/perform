(function() {
  var fancyForm = function(form) {
    form.find('.select2').select2();
    form.find('.datepicker').each(function() {
      $(this).datetimepicker({
        format: $(this).data('format'),
        showTodayButton: true,
        showClear: true
      });
    });
  };

  var showMessage = function(type, message) {
    if (typeof Perform.base.showMessage.messages === 'undefined') {
      Perform.base.showMessage.messages = [];
    }
    var msgs = Perform.base.showMessage.messages;
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
  };

  var showError = function(message) {
    return showMessage('danger', message);
  };

  var showSuccess = function(message) {
    return showMessage('success', message);
  };

  if (!window.Perform) {
    window.Perform = {};
  }
  if (!window.Perform.base) {
    window.Perform.base = {};
  }
  window.Perform.base.fancyForm = fancyForm;
  window.Perform.base.showMessage = showMessage;
  window.Perform.base.showSuccess = showSuccess;
  window.Perform.base.showError = showError;
})();
