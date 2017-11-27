import Vue from 'vue'
import Selector from './components/Selector'
import store from './store/store'

Vue.config.productionTip = false

const parent = document.createElement('div');
parent.id = 'perform-media-selector';
document.body.appendChild(parent);

const selectFiles = function(options) {
  options = Object.assign({
    multiple: true,
    onSelect: function() {},
  }, options);

  app.onSelect = options.onSelect;
  app.show();
};

const selectFile = function(options) {
  return selectFiles(Object.assign(options, {
    multiple: false
  }));
};

if (!window.Perform) {
  window.Perform = {};
}
if (!window.Perform.media) {
  window.Perform.media = {};
}

window.Perform.media.selectFiles = selectFiles;
window.Perform.media.selectFile = selectFile;

const app = new Vue({
  el: '#perform-media-selector',
  data: {
    onSelect: false,
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
  template: '<Selector ref="selector" :onSelect="onSelect" />',
});
