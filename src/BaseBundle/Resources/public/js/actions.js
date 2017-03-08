$(function () {
  $('.action-button').click(function(e) {
    e.preventDefault();
    var button = $(this);
    var action = $(this).data('action');

    button.attr('disabled', true);
    $.ajax({
      url: $(this).attr('href'),
      type: 'post',
      data: action,
      success: function (data) {
        if (data.redirect) {
          return window.location.href = data.redirect;
        }
        if (data.message) {
          app.func.showSuccess(data.message);
        }
        button.attr('disabled', false);
      },
      error: function (data) {
        var error;
        switch (data.status) {
        case 403:
          error = 'This action is not allowed.';
          break;
        default:
          error = 'An error occurred.'
          break;
        }
        app.func.showError(error);
        button.attr('disabled', false);
      }
    });
  });
});
