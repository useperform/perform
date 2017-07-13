import Editor from './components/Editor';
import ReactDOM from 'react-dom';
import React from 'react';

import {createStore, applyMiddleware} from 'redux';
import {loadContent} from './actions';
import reducer from './reducers';

const initialState = {
  contentId: undefined,
  loaded: false,
  blocks: {},
  order: [],
  newBlocks: {},
};

const thunk = store => next => action =>
      typeof action === 'function'
      ? action(store.dispatch, store.getState)
      : next(action);

const store = createStore(reducer, initialState, applyMiddleware(thunk));

store.subscribe(function() {
  console.debug('New state: ', store.getState());
});

const init = function(element, config) {
  ReactDOM.render(<Editor store={store} />, element);
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
