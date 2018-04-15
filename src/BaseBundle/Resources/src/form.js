import DatePickerInput from './components/DatePickerInput'
import Vue from 'vue'
import md from './util/markdown';

export function datepicker(el, opts) {
  new Vue({
    el: el,
    render(h) {
      return h(DatePickerInput, {props: opts});
    }
  });
};

export function markdown(input, preview) {
  input.on('keyup', function(e) {
    preview.html(md.render(e.currentTarget.value));
  });
  preview.html(md.render(input.val()));
};
