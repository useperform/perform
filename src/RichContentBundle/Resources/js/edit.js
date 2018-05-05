import Editor from './components/Editor';
import Vue from 'vue';
import store from './store';

export default function(element, config) {
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
