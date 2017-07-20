import Editor from './components/Editor';
import ReactDOM from 'react-dom';
import React from 'react';

import {loadContent} from './actions';
import {newStore, addEditor} from './store';

const store = newStore();

store.subscribe(() => {
  console.debug('New state: ', store.getState());
});

const init = function(element, config) {
  const editorIndex = addEditor(store, config.contentId);
  ReactDOM.render(<Editor store={store} editorIndex={editorIndex} />, element);

  if (config.onChange) {
    store.subscribe(() => {
      config.onChange(store);
    });
  }
  if (config.contentId) {
    store.dispatch(loadContent(editorIndex, config.contentId));
  }
};

window.Perform = {
  richContent: {
    init,
    store
  }
}
