import Editor from './components/Editor';
import ReactDOM from 'react-dom';
import React from 'react';

import {createStore} from 'redux';

const reducer = function (state, action) {
  console.debug(action);

  if (action.type === 'CONTENT_LOAD') {
    // Associate each ordered block with a random id.
    // This will be used for the key on the react component to keep
    // track of DOM nodes, since we can't use the position in the
    // order array.
    const order = action.json.order.map(id => {
      return [id, Math.random().toString().substring(2)];
    });
    return Object.assign({}, state, {
      blocks: action.json.blocks,
      order: order,
    });
  }
  if (action.type === 'BLOCK_UPDATE') {
    const newBlocks = state.blocks;
    newBlocks[action.id].value = action.value;

    return Object.assign(state, {blocks: newBlocks});
  }
  if (action.type === 'BLOCK_MOVE_UP') {
    const pos = action.currentPosition;

    if (pos === 0) {
      return state;
    }

    const newOrder = [
      ...state.order.slice(0, pos - 1),
      state.order[pos],
      state.order[pos - 1],
      ...state.order.slice(pos + 1),
    ];

    return Object.assign(state, {order: newOrder});
  }
  if (action.type === 'BLOCK_MOVE_DOWN') {
    const pos = action.currentPosition;

    if (pos + 1 === state.order.length) {
      return state;
    }

    const newOrder = [
      ...state.order.slice(0, pos),
      state.order[pos + 1],
      state.order[pos],
      ...state.order.slice(pos + 2),
    ];

    return Object.assign(state, {order: newOrder});
  }
  if (action.type === 'BLOCK_REMOVE') {
    let newOrder = state.order;
    let newBlocks = state.blocks;
    let orderedIds = state.order.map(i => {
      return i[0];
    });

    const pos = action.currentPosition;
    newOrder.splice(pos, 1);

    // also remove the block if it's not used anywhere else
    const id = orderedIds[pos];
    orderedIds.splice(pos, 1);
    if (orderedIds.indexOf(id) === -1) {
      delete newBlocks[id];
    }

    return Object.assign(state, {
      order: newOrder,
      blocks: newBlocks,
    });
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
