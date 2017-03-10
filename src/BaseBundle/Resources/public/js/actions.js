$(function () {
  var runAction = function(href, ids, button) {
    button.attr('disabled', true);
    $.ajax({
      url: href,
      type: 'post',
      data: {
        ids: ids
      },
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
  };

  $('.action-button').click(function(e) {
    e.preventDefault();
    runAction($(this).attr('href'), [$(this).data('action').id], $(this));
  });

  $('.batch-action-button').click(function(e) {
    e.preventDefault();
    var ids = $('.table-crud input[type=checkbox].selector:checked').map(function() {
      return $(this).data('id');
    }).toArray();
    var href = $(this).parent().find('option:selected').val();
    runAction(href, ids, $(this));
  });
});
