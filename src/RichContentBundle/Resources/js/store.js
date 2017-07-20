import {createStore, applyMiddleware} from 'redux';
import reducer from './reducers';

const initialState = {
  blocks: {},
  editors: [],
};

const thunk = store => next => action =>
      typeof action === 'function'
      ? action(store.dispatch, store.getState)
      : next(action);

export function newStore() {
  return createStore(reducer, initialState, applyMiddleware(thunk));
}

export function addEditor(store, contentId) {
  store.dispatch({
    type: 'EDITOR_ADD',
    contentId: contentId,
  });
  return store.getState().editors.length - 1;
};
