import DatePickerInput from './components/DatePickerInput'
import Vue from 'vue'
import md from './util/markdown';

export function datepicker(el, options) {
  new Vue({
    el: el,
    render(h) {
      return h(DatePickerInput, {props: {
        inputName: options.inputName,
        disabled: options.disabled,
        initialValue: options.initialValue,
        flatPickrConfig: Object.assign({}, {
         allowInput: true,
         enableTime: true,
         enableSeconds: false,
         // transform ICU format to string
         /* formatDate() { */
         /* } */
         dateFormat: 'M j, Y h:iK',
        }, options.flatPickrConfig),
      }});
    }
  });
};

export function markdown(input, preview) {
  input.on('keyup', function(e) {
    preview.html(md.render(e.currentTarget.value));
  });
  preview.html(md.render(input.val()));
};
