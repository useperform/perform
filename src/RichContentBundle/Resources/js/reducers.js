const newId = function() {
  return Math.random().toString().substring(2);
}

const reducers = {
  CONTENT_LOAD: function(state, action) {
    const contentId = action.id;
    const editorIndex = action.editorIndex;
    if (!contentId) {
      return state;
    }

    if (!action.status) {
      //show loading state
      return state;
    }

    // Associate each ordered block with a random id.
    // This will be used for the key on the react component to keep
    // track of DOM nodes, since we can't use the position in the
    // order array.
    const order = action.json.order.map(id => {
      return [id, newId()];
    });
    const editors = state.editors || [];
    editors[editorIndex] = Object.assign(editors[editorIndex] || {}, {
      order,
      contentId,
      loaded: true
    });

    return Object.assign({}, state, {
      blocks: action.json.blocks,
      editors,
    });
  },
  CONTENT_SAVE: function(state, action) {
    if (!action.status) {
      //show loading state
      return state;
    }

    // replace stub ids in blocks with new database ids
    let blocks = state.blocks;
    const newIds = Object.entries(action.json.newBlocks);
    for (let i=0; i < newIds.length; i++) {
      const stubId = newIds[i][0];
      const dbId = newIds[i][1];
      blocks[dbId] = state.blocks[stubId];
      delete blocks[stubId];
    }
    // replace stub ids in the order with new database ids
    const order = state.order.map(item => {
      const dbId = action.json.newBlocks[item[0]];
      if (dbId) {
        return [dbId, item[1]];
      }

      return item
    });

    return Object.assign(state, {
      contentId: action.json.id,
      blocks,
      order
    });
  },
  BLOCK_UPDATE: function(state, action) {
    const blocks = state.blocks;
    blocks[action.id].value = action.value;

    return Object.assign(state, {blocks: blocks});
  },
  BLOCK_MOVE: function(state, action) {
    const pos = action.currentPosition;
    const newPos = action.newPosition;
    const order = state.order;
    const block = order[pos];

    order.splice(pos, 1);
    order.splice(newPos, 0, block);

    return Object.assign(state, {order: order})
  },
  BLOCK_MOVE_UP: function(state, action) {
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
  },
  BLOCK_MOVE_DOWN: function(state, action) {
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
  },
  BLOCK_REMOVE: function(state, action) {
    let order = state.order;
    let blocks = state.blocks;
    let orderedIds = state.order.map(i => {
      return i[0];
    });

    const pos = action.currentPosition;
    order.splice(pos, 1);

    // also remove the block if it's not used anywhere else
    const id = orderedIds[pos];
    orderedIds.splice(pos, 1);
    if (orderedIds.indexOf(id) === -1) {
      delete blocks[id];
    }

    return Object.assign(state, {
      order: order,
      blocks: blocks,
    });
  },
  BLOCK_ADD: function(state, action) {
    // set an arbitrary unique id, since there is no database id for
    // this new block
    const id = '_'+newId();
    let blocks = state.blocks ? state.blocks : {};

    blocks[id] = {
      type: action.blockType,
      value: action.value,
    };
    const order = [
      ...state.order ? state.order : [],
      [id, newId()],
    ];

    return Object.assign(state, {
      blocks,
      order
    })
  },
  EDITOR_ADD: function(state, action) {
    const editors = state.editors;
    editors.push({
      contentId: action.contentId,
      loaded: false,
      order: [],
    });

    return Object.assign(state, {
      editors
    });
  }
};

export default function reducer(state, action) {
  if (reducers[action.type]) {
    return reducers[action.type](state, action);
  }

  if (action.type.substring(0, 7) !== '@@redux') {
    console.warn(`Warning: no reducer found for action "${action.type}"`);
  }
  return state;
}
