import 'whatwg-fetch';
import blockTypes from './components/blocktypes';

export function loadContent(editorIndex, id) {
  return function(dispatch) {
    const url = '/admin/_editor/content/get/' + id;
    fetch(url, {
      credentials: 'include',
    }).then(res => {
      return res.json();
    }).then(json => {
      dispatch({
        type: 'CONTENT_LOAD',
        editorIndex: editorIndex,
        id: id,
        status: true,
        json,
      });
    });
  }
}

const handleFetch = function(response) {
  if (!response.ok) {
    throw Error('An error occurred saving this content. Please try again.');
  }

  return response.json();
}

const getPostBody = function(state) {
  const blockIds = Object.keys(state.blocks);
  let currentBlocks = {};
  let newBlocks = {}

  for (let i=0; i < blockIds.length; i++) {
    let id = blockIds[i];
    let block = state.blocks[id];
    // new blocks have a stub id starting with _
    if (id.substring(0, 1) === '_') {
      newBlocks[id] = block;
      continue;
    }
    currentBlocks[id] = block;
  }

  return {
    newBlocks: newBlocks,
    blocks: currentBlocks,
    order: state.order.map(i => {
      // an array with the block id and a unique react key
      // we only want the block id
      return i[0];
    })
  };
}

export function save(onSuccess, onError) {
  return function (dispatch, getState) {
    dispatch({
      type: 'CONTENT_SAVE',
      status: false
    });
    const contentId = getState().contentId;
    const url = contentId ? '/admin/_editor/content/save/' + contentId
          : '/admin/_editor/content/save-new';

    fetch(url, {
      body: JSON.stringify(getPostBody(getState())),
      credentials: 'include',
      method: 'POST'
    }).then(handleFetch)
      .then(json => {
        dispatch({
          type: 'CONTENT_SAVE',
          status: true,
          json: json
        });
        onSuccess(json);
      })
      .catch(error => {
        app.func.showError(error);
        onError(error);
      });
  }
}

export function addBlock(type) {
  return {
    type: 'BLOCK_ADD',
    blockType: type,
    value: blockTypes[type].defaults
  }
}

export function moveBlock(currentPosition, newPosition) {
  return {
    type: 'BLOCK_MOVE',
    currentPosition,
    newPosition,
  }
}
