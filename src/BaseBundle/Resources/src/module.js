import {renderCounter, renderDropdown, add, get, getUnfinished, setProgress, cancel} from './tasks';
import {datepicker, markdown} from './form';

let messages = [];

const showMessage = function(type, message) {
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
}

export default {
  fancyForm(form) {
    form.find('.select2').select2();
  },

  showMessage,

  showError(message) {
    return showMessage('danger', message);
  },

  showSuccess(message) {
    return showMessage('success', message);
  },

  tasks: {
    renderCounter,
    renderDropdown,
    add,
    get,
    getUnfinished,
    setProgress,
    cancel,
  },
  form: {
    datepicker,
    markdown
  }
};
