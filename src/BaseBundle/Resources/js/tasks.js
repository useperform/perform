$(function () {
  Perform.base.tasks.renderDropdown('#perform-tasks-dropdown');
  Perform.base.tasks.renderCounter('#perform-tasks-counter');

  window.addEventListener('beforeunload', function(e) {
    if (Perform.base.tasks.getUnfinished().length > 0) {
      var confirmationMessage = "You have unfinished tasks. Leave this page?";
      e.returnValue = confirmationMessage;
      return confirmationMessage;
    }
  });
});
