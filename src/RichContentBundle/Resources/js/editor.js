import Editor from './components/Editor';
import ReactDOM from 'react-dom';
import React from 'react';

import {createStore} from 'redux';

const reducer = function (state, action) {
  console.log(state, action);

  if (action.type === 'CONTENT_LOAD') {
    return Object.assign({}, state, {
      blocks: action.json.blocks,
      order: action.json.order,
    });
  }

  return state;
};

const initialState = {
  blocks: {},
  order: [],
};

const store = createStore(reducer, initialState);

const init = function(element, config) {
  ReactDOM.render(<Editor contentId={config.contentId} store={store} />, element);
}

window.Perform = {
  richContent: {
    init,
    store
  }
}
