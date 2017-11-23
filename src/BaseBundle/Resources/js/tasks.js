$(function () {
  Perform.base.tasks.init('#perform-tasks');

  window.addEventListener('beforeunload', function(e) {
    if (Perform.base.tasks.getUnfinished().length > 0) {
      var confirmationMessage = "You have unfinished tasks. Leave this page?";
      e.returnValue = confirmationMessage;
      return confirmationMessage;
    }
  });
});
