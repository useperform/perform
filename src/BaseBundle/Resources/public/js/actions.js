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
        app.func.showError('An error occurred.');
        button.attr('disabled', false);
      }
    });
  });
});
