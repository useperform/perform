import perform from './modules';
window.Perform = perform;

import {renderDropdown, renderCounter, getUnfinished} from './tasks';

  renderDropdown('#perform-tasks-dropdown');
  renderCounter('#perform-tasks-counter');

  window.addEventListener('beforeunload', function(e) {
    if (getUnfinished().length > 0) {
      var confirmationMessage = "You have unfinished tasks. Leave this page?";
      e.returnValue = confirmationMessage;
      return confirmationMessage;
    }
  });
