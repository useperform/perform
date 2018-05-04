import Vue from 'vue';
import Selector from './components/Selector';
import store from './store/store';

// selector api
let selector;
// ensure the selector is initialized
const initSelector = function() {
  if (typeof selector === 'object') {
    return;
  }
  const parent = document.createElement('div');
  parent.id = 'perform-media-selector';
  document.body.appendChild(parent);

  selector = new Vue({
    el: '#perform-media-selector',
    data: {
      onSelect: function() {},
      limit: 0,
      multiple: true
    },
    store,
    components: {
      Selector,
    },
    methods: {
      show() {
        this.$refs.selector.show();
      }
    },
    template: '<Selector ref="selector" :onSelect="onSelect" :limit="limit" :multiple="multiple" />'
  });
};

export function selectFiles(options) {
  initSelector();
  Object.assign(selector, {
    limit: 0,
    multiple: true,
  }, options);
  selector.show();
};

export function selectFile(options) {
  return selectFiles(Object.assign({}, options, {
    limit: 1,
    multiple: false
  }));
};
