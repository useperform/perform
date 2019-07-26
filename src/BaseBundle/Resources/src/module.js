import {renderCounter, renderDropdown, add, get, getUnfinished, setProgress, cancel} from './tasks';
import {datepicker, markdown} from './form';

export default {
  fancyForm(form) {
    form.find('.select2').select2();
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
