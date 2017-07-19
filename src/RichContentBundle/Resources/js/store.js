import {createStore, applyMiddleware} from 'redux';
import reducer from './reducers';

const initialState = {
  contentId: undefined,
  loaded: false,
  blocks: {},
  order: [],
};

const thunk = store => next => action =>
      typeof action === 'function'
      ? action(store.dispatch, store.getState)
      : next(action);

const store = createStore(reducer, initialState, applyMiddleware(thunk));

store.subscribe(function() {
  console.debug('New state: ', store.getState());
});

export default store;
