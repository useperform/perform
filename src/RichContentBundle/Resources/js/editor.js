import Editor from './components/Editor';
import ReactDOM from 'react-dom';
import React from 'react';

import {loadContent} from './actions';
import store from './store';

const init = function(element, config) {
  ReactDOM.render(<Editor store={store} />, element);
  if (config.onChange) {
    store.subscribe(() => {
      config.onChange(store);
    });
  }
  if (config.contentId) {
    store.dispatch(loadContent(config.contentId));
  }
};

window.Perform = {
  richContent: {
    init,
    store
  }
}
