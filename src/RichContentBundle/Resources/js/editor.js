import Editor from './components/Editor';
import Vue from 'vue';

import store from './store';

const init = function(element, config) {
  store.commit('EDITOR_ADD', config.contentId);
  const editorIndex = store.state.editors.length - 1;

  new Vue({
    el: element,
    render: h => h(Editor, {props: {editorIndex}}),
    store
  });

  if (config.onChange) {
    store.subscribe(() => {
      config.onChange(store, editorIndex);
    });
  }
  if (config.contentId) {
    store.dispatch('loadContent', {
      contentId: config.contentId,
      editorIndex
    });
  }
};

if (!window.Perform) {
  window.Perform = {};
}

window.Perform.richContent = {
  init,
  store
}
