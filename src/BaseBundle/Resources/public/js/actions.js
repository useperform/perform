$(function () {
  $('.action-button').click(function(e) {
    e.preventDefault();
    $(this).attr('disabled', true);
    var action = $(this).data('action')
    console.log(action);
    $.ajax({
      url: '/admin/_action',
      type: 'post',
      data: action,
      success: function (data) {
        window.location.href = data.redirect;
      },
      error: function (data) {
        app.func.showError('An error occurred.');
      }
    });
  });
});
