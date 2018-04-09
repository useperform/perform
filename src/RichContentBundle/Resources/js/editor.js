import Editor from './components/Editor';
import Vue from 'vue';

import store from './store';

// change to edit
const init = function(element, config) {
  store.commit('EDITOR_ADD', {
    contentId: config.contentId,
  });
  const editorIndex = store.state.editors.length - 1;
  const showToolbar = !!config.showToolbar;

  new Vue({
    el: element,
    render: h => h(Editor, {props: {editorIndex, showToolbar}}),
    store
  });

  if (config.onChange) {
    store.subscribe(() => {
      config.onChange(store, editorIndex);
    });
  }
  if (config.contentId && !config.noLoad) {
    store.dispatch('loadContent', {
      contentId: config.contentId,
      editorIndex
    });
  }

  return editorIndex;
};

const setContent = function(editorIndex, data) {
  if (editorIndex == undefined) {
    console.error('Unknown rich content editor ', editorIndex);
    return;
  }

  store.commit('CONTENT_SET_DATA', {
    editorIndex,
    data,
  });
};

if (!window.Perform) {
  window.Perform = {};
}

window.Perform.richContent = {
  init,
  store,
  setContent,
}
