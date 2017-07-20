const newId = function() {
  return Math.random().toString().substring(2);
}

const editorsUseBlock = function(editors, blockId) {
  return editors.some(editor => {
    return editor.order.some(order => {
      return order[0] === blockId;
    });
  });
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
    let blocks = Object.assign({}, state.blocks);
    const newIds = Object.entries(action.json.newBlocks);
    for (let i=0; i < newIds.length; i++) {
      const stubId = newIds[i][0];
      const dbId = newIds[i][1];
      blocks[dbId] = state.blocks[stubId];
      delete blocks[stubId];
    }
    // replace stub ids in the order with new database ids
    const editors = state.editors.map(editor => Object.assign({}, editor));
    const order = editors[action.editorIndex].order.map(item => {
      const dbId = action.json.newBlocks[item[0]];
      if (dbId) {
        return [dbId, item[1]];
      }

      return item
    });
    editors[action.editorIndex].order = order;
    editors[action.editorIndex].contentId = action.json.id;

    return Object.assign({}, state, {
      blocks,
      editors
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

    const editors = state.editors;
    const order = editors[action.editorIndex].order;
    const newOrder = [
      ...order.slice(0, pos - 1),
      order[pos],
      order[pos - 1],
      ...order.slice(pos + 1),
    ];
    editors[action.editorIndex].order = newOrder;

    return Object.assign(state, {editors: editors});
  },
  BLOCK_MOVE_DOWN: function(state, action) {
    const pos = action.currentPosition;

    const editors = state.editors;
    const order = editors[action.editorIndex].order;
    if (pos + 1 === order.length) {
      return state;
    }

    const newOrder = [
      ...order.slice(0, pos),
      order[pos + 1],
      order[pos],
      ...order.slice(pos + 2),
    ];
    editors[action.editorIndex].order = newOrder;

    return Object.assign(state, {editors: editors});
  },
  BLOCK_REMOVE: function(state, action) {
    let blocks = Object.assign({}, state.blocks);
    let editors = state.editors.map(editor => Object.assign({}, editor));
    const pos = action.position;
    const blockId = editors[action.editorIndex].order[pos][0];

    let order = editors[action.editorIndex].order;
    editors[action.editorIndex].order = [
      ...order.slice(0, pos),
      ...order.slice(pos + 1),
    ];

    if (!editorsUseBlock(editors, blockId)) {
      delete blocks[blockId];
    }

    return Object.assign({}, state, {
      blocks,
      editors,
    });
  },
  BLOCK_ADD: function(state, action) {
    // set an arbitrary unique id, since there is no database id for
    // this new block
    const id = '_'+newId();
    let blocks = Object.assign({}, state.blocks);
    blocks[id] = {
      type: action.blockType,
      value: action.value,
    };

    let editors = state.editors.map(editor => Object.assign({}, editor));
    editors[action.editorIndex].order = [
      ...editors[action.editorIndex].order,
      [id, newId()],
    ];

    return Object.assign({}, state, {
      blocks,
      editors
    })
  },
  EDITOR_ADD: function(state, action) {
    let editors = state.editors;
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
