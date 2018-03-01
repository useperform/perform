const reducers = {
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
};
