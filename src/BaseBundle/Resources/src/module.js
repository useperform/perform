let messages = [];

export default {
  fancyForm(form) {
    form.find('.select2').select2();
  },

  showMessage(type, message) {
    var template = _.template($('#template-flash-message').html());
    var id = Date.now();
    var msg = {
      id: id,
      type: type,
      message: message
    };
    //store message history for possible fanciness later
    messages.push(msg);
    $('.admin-flash-messages').html(template(msg));

    setTimeout(function() {
      $('#flash-message-'+id).slideUp();
    }, 3000);
  },

  showError(message) {
    return this.showMessage('danger', message);
  },

  showSuccess(message) {
    return this.showMessage('success', message);
  },
};
