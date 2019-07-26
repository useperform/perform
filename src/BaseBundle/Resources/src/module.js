import {renderCounter, renderDropdown, add, get, getUnfinished, setProgress, cancel} from './tasks';
import {datepicker, markdown} from './form';

export default {
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
