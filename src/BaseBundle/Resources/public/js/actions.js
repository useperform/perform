$(function () {
  var runAction = function(href, entityClass, ids, button) {
    if (ids.length < 1) {
      return;
    }
    button.attr('disabled', true);
    $.ajax({
      url: href,
      type: 'post',
      data: {
        entityClass: entityClass,
        ids: ids,
      },
      success: function (data) {
        if (!data.redirectType) {
          return console.error('Invalid action response, redirectType is required.');
        }
        if (data.redirectType === 'none') {
          app.func.showSuccess(data.message);
        }
        //url or route redirect
        if (data.redirect) {
          return window.location.href = data.redirect;
        }
        if (data.redirectType === 'previous') {
          return window.location.href = document.referrer;
        }
        if (data.redirectType === 'current') {
          return window.location.reload();
        }

        button.attr('disabled', false);
      },
      error: function (data) {
        var error;
        switch (data.status) {
        case 403:
          error = 'This action is not allowed.';
          break;
        case 404:
          error = 'One or more items were not found.';
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
    runAction($(this).attr('href'), $(this).data('action').entityClass, [$(this).data('action').id], $(this));
  });

  $('.batch-action-button').click(function(e) {
    e.preventDefault();
    var ids = $('.table-crud tbody input[type=checkbox].selector:checked').map(function() {
      return $(this).data('id');
    }).toArray();
    var href = $(this).parent().find('option:selected').val();
    var entityClass = $(this).parent().children('select').data('entity');
    runAction(href, entityClass, ids, $(this));
  });
});
