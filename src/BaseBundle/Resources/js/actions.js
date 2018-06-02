$(function () {
  var showSuccess = window.Perform.base.showSuccess;
  var showError = window.Perform.base.showError;

  var runAction = function(href, action, button) {
    if (!href) {
      return console.error('Missing action href');
    }
    if (action.ids.length < 1) {
      return;
    }
    button.attr('disabled', true);
    $.ajax({
      url: href,
      type: 'post',
      data: {
        crudName: action.crudName,
        ids: action.ids,
        options: {
          context: action.context
        }
      },
      success: function (data) {
        if (!data.redirectType) {
          console.error('Invalid action response, redirectType is required.');
          return button.attr('disabled', false);
        }
        if (data.redirectType === 'none') {
          showSuccess(data.message);
          $('#modal-action-confirm').modal('hide');
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
        if (data.responseJSON && data.responseJSON.message) {
          error = data.responseJSON.message;
        } else {
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
        }
        $('#modal-action-confirm').modal('hide');
        showError(error);
        button.attr('disabled', false);
      }
    });
  };

  var confirmAction = function(href, action) {
    var modal = $('#modal-action-confirm');
    //clone the action object before modification to stop any funny business
    var action = JSON.parse(JSON.stringify(action));
    var label = action['label'];
    var message = action['message'];
    //get the action to run when clicking on .action-button in the modal
    action['confirm'] = false;

    modal.find('.modal-title').text(label);
    modal.find('.modal-body .message').text(message);
    modal.find('.action-button')
      .text(label)
      .data('action', action)
      .attr('href', href)
      .removeClass('btn-light btn-primary btn-info btn-warning btn-danger')
      .addClass(action.buttonStyle);

    modal.modal('show');
  };

  $('.action-button').click(function(e) {
    e.preventDefault();
    var action = $(this).data('action');
    var href = $(this).attr('href');
    if (action.confirm) {
      return confirmAction(href, action);
    }
    if (action.link) {
      return window.location = href;
    }
    runAction(href, action, $(this));
  });

  $('.batch-action-button').click(function(e) {
    e.preventDefault();
    var selected = $(this).parents('.batch-actions').find('option:selected');
    var action = selected.data('action');
    action.ids = $('.table-crud tbody input[type=checkbox].selector:checked').map(function() {
      return $(this).data('id');
    }).toArray();
    if (action.ids.length < 1) {
      return;
    }
    var href = selected.val();
    if (action.confirm) {
      return confirmAction(href, action);
    }
    runAction(href, action, $(this));
  });
});
