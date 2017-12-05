import Vue from 'vue';
import store from './store/store';
import router from './router';
import App from './components/App';
import Selector from './components/Selector';

// entry point to the main media application
const startApp = function(el)  {
  new Vue({
    el: el,
    render: h => h(App),
    store,
    router
  });
};

// selector api
let selector;
// ensure the selector is initialized
const initSelector = function() {
  if (selector) {
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

const selectFiles = function(options) {
  initSelector();
  Object.assign(selector, {
    limit: 0,
    multiple: true,
  }, options);
  selector.show();
};

const selectFile = function(options) {
  return selectFiles(Object.assign(options, {
    limit: 1,
    multiple: false
  }));
};

if (!window.Perform) {
  window.Perform = {};
}

window.Perform.media = {
  startApp,
  selectFile,
  selectFiles,
};
