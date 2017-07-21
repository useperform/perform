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
      blocks: Object.assign({}, state.blocks, action.json.blocks),
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
    const newIndex = newPos[1] < 0 ? 0 : newPos[1];

    let blocks = Object.assign({}, state.blocks);
    let editors = state.editors.map(editor => Object.assign({}, editor));
    let sourceOrder = editors[pos[0]].order.slice();
    let destOrder = newPos[0] === pos[0] ? sourceOrder : editors[newPos[0]].order.slice();
    // blockRef is an array of [block-id, react-id]
    let blockRef = sourceOrder[pos[1]];
    if (!blockRef) {
      return state;
    }
    const blockId = blockRef[0];
    const block = blocks[blockId];

    sourceOrder.splice(pos[1], 1);
    editors[pos[0]].order = sourceOrder;

    // if the block is not used anywhere else, give it a new id, marking it as new.
    // saving the source editor before the destination editor will
    // delete the block from the database, so it needs to be marked as
    // new to allow saving it again.
    if (pos[0] !== newPos[0] && !editorsUseBlock(editors, blockId)) {
      delete blocks[blockId];
      blockRef = ['_'+newId(), blockRef[1]];
      blocks[blockRef[0]] = block;
    }

    destOrder.splice(newIndex, 0, blockRef);
    editors[newPos[0]].order = destOrder;

    return Object.assign({}, state, {
      blocks,
      editors
    });
  },
  BLOCK_REMOVE: function(state, action) {
    let blocks = Object.assign({}, state.blocks);
    let editors = state.editors.map(editor => Object.assign({}, editor));
    const editorIndex = action.position[0];
    const pos = action.position[1];
    const blockId = editors[editorIndex].order[pos][0];

    let order = editors[editorIndex].order;
    editors[editorIndex].order = [
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
