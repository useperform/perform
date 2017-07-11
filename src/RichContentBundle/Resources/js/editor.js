import Editor from './components/Editor';
import ReactDOM from 'react-dom';
import React from 'react';

import {createStore} from 'redux';

const reducer = function (state, action) {
  console.debug(action);

  if (action.type === 'CONTENT_LOAD') {
    return Object.assign({}, state, {
      blocks: action.json.blocks,
      order: action.json.order,
    });
  }
  if (action.type === 'BLOCK_UPDATE') {
    const newBlocks = state.blocks;
    newBlocks[action.id].value = action.value;

    return Object.assign(state, {blocks: newBlocks});
  }

  return state;
};

const initialState = {
  blocks: {},
  order: [],
};

const store = createStore(reducer, initialState);

store.subscribe(function() {
  console.debug('New state: ', store.getState());
})

const init = function(element, config) {
  ReactDOM.render(<Editor contentId={config.contentId} store={store} />, element);
}

window.Perform = {
  richContent: {
    init,
    store
  }
}
